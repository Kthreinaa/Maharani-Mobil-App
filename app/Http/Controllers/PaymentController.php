<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\XenditPaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PaymentController extends Controller
{
    public function store(Request $request, XenditPaymentLinkService $xendit)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer'],
            'method' => ['required', 'in:cash,transfer,va,credit'],
            'bank_account' => ['nullable', 'string', 'max:50'],
        ]);

        $order = Order::query()
            ->with('payment')
            ->where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $storedMethod = $validated['method'];

        $paymentAmount = $order->is_credit_purchase
            ? (float) ($order->credit_dp_amount ?? 0)
            : ($order->sales_flow === 'direct_purchase' && $order->transaction_channel === 'online'
                ? min((float) $order->total, (float) config('payments.booking_fee', 2500000))
                : (float) $order->total);

        if ($validated['method'] === 'transfer' && $xendit->enabled()) {
            $existingPayment = $order->payment;

            if ($existingPayment && $existingPayment->status === 'verified') {
                return redirect()
                    ->route('order.tracking', ['order' => $order->id])
                    ->with('success', 'Pembayaran untuk order ini sudah diterima oleh sistem.');
            }

            if (
                $existingPayment
                && $existingPayment->gateway_provider === 'xendit'
                && $existingPayment->status !== 'verified'
                && filled($existingPayment->gateway_checkout_url)
                && !in_array((string) $existingPayment->gateway_status, ['EXPIRED', 'FAILED'], true)
            ) {
                return redirect()->away((string) $existingPayment->gateway_checkout_url);
            }

            $invoice = $xendit->createInvoice($order->loadMissing('car'), $request->user(), $paymentAmount);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'method' => 'transfer',
                    'gateway_provider' => 'xendit',
                    'gateway_reference' => (string) ($invoice['id'] ?? ''),
                    'gateway_external_id' => (string) ($invoice['external_id'] ?? ''),
                    'gateway_checkout_url' => (string) ($invoice['invoice_url'] ?? ''),
                    'gateway_status' => (string) ($invoice['status'] ?? 'PENDING'),
                    'gateway_payload' => $invoice,
                    'amount' => $paymentAmount,
                    'proof_file' => $existingPayment?->proof_file,
                    'status' => $existingPayment?->status === 'verified' ? 'verified' : 'pending',
                ]
            );

            $order->update([
                'payment_method' => 'transfer',
                'status' => 'confirmed',
            ]);

            return redirect()->away((string) $invoice['invoice_url']);
        }

        if (!empty($validated['bank_account'])) {
            $bankLabel = match ($validated['bank_account']) {
                'mandiri' => sprintf(
                    '%s - %s - %s',
                    (string) config('payments.settlement.bank', 'Bank Mandiri'),
                    (string) config('payments.settlement.account_name', 'Diki Susanto'),
                    (string) config('payments.settlement.account_number', '1080093012152')
                ),
                default => null,
            };

            if ($bankLabel) {
                $lines = collect(preg_split("/\r\n|\n|\r/", (string) $order->notes))
                    ->filter(fn ($line) => filled($line) && !str_starts_with((string) $line, 'Bank tujuan pembayaran:'))
                    ->values();
                $lines->push('Bank tujuan pembayaran: ' . $bankLabel);
                $order->notes = $lines->implode("\n");
            }
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => $storedMethod,
                'amount' => $paymentAmount,
                'proof_file' => $order->payment?->proof_file,
                'status' => $order->payment?->status === 'verified' ? 'verified' : 'pending',
            ]
        );

        $order->update([
            'payment_method' => $order->is_credit_purchase ? 'credit' : $validated['method'],
            'status' => 'confirmed',
            'notes' => $order->notes,
        ]);

        return redirect()
            ->route('order.tracking', ['order' => $order->id])
            ->with('success', match ($validated['method']) {
                'cash' => 'Metode pembayaran cash berhasil dicatat. Supervisor akan memvalidasi transaksi Anda di sistem.',
                'credit' => 'Pembayaran DP kredit berhasil dicatat. Supervisor akan memvalidasi DP yang Anda kirim sebelum proses dilanjutkan bersama showroom dan leasing.',
                default => 'Metode pembayaran transfer berhasil dicatat. Supervisor akan memvalidasi pembayaran Anda di sistem.',
            });
    }

    public function upload(Request $request)
    {
        $orderId = (int) $request->input('order_id', 0);

        $order = Order::query()
            ->where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return redirect()
            ->route('order.tracking', ['order' => $order->id])
            ->with('error', 'Customer tidak perlu mengunggah bukti bayar. Validasi pembayaran dilakukan oleh supervisor melalui sistem.');
    }

    public function simulateSuccess(Request $request)
    {
        abort_unless(App::environment(['local', 'testing']), 404);

        $validated = $request->validate([
            'order_id' => ['required', 'integer'],
        ]);

        $order = Order::query()
            ->with(['payment', 'car'])
            ->where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'transfer',
                'gateway_provider' => 'local_demo',
                'gateway_reference' => 'LOCAL-DEMO-' . $order->id,
                'gateway_external_id' => 'local-demo-order-' . $order->id,
                'gateway_status' => 'PAID',
                'gateway_channel' => 'BANK_TRANSFER',
                'amount' => (float) $order->total,
                'status' => 'verified',
                'handled_role' => 'gateway',
                'handled_at' => now(),
                'verified_at' => now(),
                'paid_at' => now(),
            ]
        );

        $order->update([
            'payment_method' => 'transfer',
            'status' => 'paid',
        ]);
        $order->issueSettlementDocuments();

        if ($order->car && $order->car->status !== 'sold') {
            $order->car->update(['status' => 'reserved']);
        }

        return redirect()
            ->route('order.tracking', ['order' => $order->id])
            ->with('success', 'Simulasi pembayaran lokal berhasil. Faktur, kwitansi digital, dan BAST sekarang sudah aktif untuk order ini.');
    }
}
