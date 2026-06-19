<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\CheckoutDraftService;
use App\Services\XenditPaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function store(Request $request, XenditPaymentLinkService $xendit, CheckoutDraftService $checkoutDrafts)
    {
        $validated = $request->validate([
            'order_id' => ['nullable', 'integer'],
            'draft_token' => ['nullable', 'string'],
            'method' => ['required', 'in:cash,transfer,va,credit'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'payment_plan' => ['nullable', 'in:booking,full'],
        ]);

        if (!empty($validated['draft_token'])) {
            $draft = $checkoutDrafts->findForUser((string) $validated['draft_token'], (int) $request->user()->id);
            abort_if(!$draft, 404);

            $paymentPlan = $validated['payment_plan']
                ?? $this->resolveDraftPaymentPlan($draft);
            $paymentAmount = $this->paymentAmountFromDraft($draft, $paymentPlan);

            if ($validated['method'] === 'transfer' && $xendit->enabled()) {
                $invoice = $xendit->createInvoiceForDraft($draft, $request->user(), $paymentAmount);

                return redirect()->away((string) $invoice['invoice_url']);
            }

            return back()->with('error', 'Metode pembayaran ini harus diselesaikan dari halaman pembayaran otomatis.');
        }

        $order = Order::query()
            ->with('payment')
            ->where('id', $validated['order_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $storedMethod = $validated['method'];
        $paymentPlan = $validated['payment_plan']
            ?? $this->resolveCashPaymentPlan($order);

        $paymentAmount = $order->is_credit_purchase
            ? (float) ($order->credit_dp_amount ?? 0)
            : ($order->transaction_channel === 'online'
                ? ($paymentPlan === 'full'
                    ? (float) $order->total
                    : min((float) $order->total, (float) config('payments.booking_fee', 2500000)))
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
                && (float) $existingPayment->amount === $paymentAmount
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
                    ->filter(fn ($line) => filled($line)
                        && !str_starts_with((string) $line, 'Bank tujuan pembayaran:')
                        && !str_starts_with((string) $line, 'Pilihan pembayaran customer:'))
                    ->values();
                $lines->push('Bank tujuan pembayaran: ' . $bankLabel);
                $lines->push('Pilihan pembayaran customer: ' . ($order->is_credit_purchase
                    ? 'DP Kredit'
                    : ($paymentPlan === 'full' ? 'Bayar Lunas' : 'Booking Fee')));
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
            'order_id' => ['nullable', 'integer'],
            'draft_token' => ['nullable', 'string'],
        ]);

        if (!empty($validated['draft_token'])) {
            return $this->completeDraftPayment($request, (string) $validated['draft_token'], 'local_demo', 'PAID');
        }

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

    private function resolveCashPaymentPlan(Order $order): string
    {
        $paidAmount = (float) ($order->payment?->amount ?? 0);
        if ($paidAmount >= (float) $order->total && (float) $order->total > 0) {
            return 'full';
        }

        return str_contains((string) ($order->notes ?? ''), 'Pilihan pembayaran cash online: Bayar Lunas Full')
            ? 'full'
            : 'booking';
    }

public function checkout()
{
    $params = [
        'transaction_details' => [
            'order_id' => 'ORDER-' . time(),
            'gross_amount' => 100000,
        ],
        'customer_details' => [
            'first_name' => 'Rina',
            'email' => 'rina@example.com',
        ]
    ];

    $snapToken = Snap::getSnapToken($params);

    return response()->json([
        'token' => $snapToken
    ]);
}

public function snapToken(Request $request, CheckoutDraftService $checkoutDrafts)
{
    $validated = $request->validate([
        'draft_token' => ['required', 'string'],
    ]);

    $draft = $checkoutDrafts->findForUser((string) $validated['draft_token'], (int) $request->user()->id);
    abort_if(!$draft, 404);

\Midtrans\Config::$curlOptions = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [],
];
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $paymentPlan = $this->resolveDraftPaymentPlan($draft);
    $paymentAmount = $this->paymentAmountFromDraft($draft, $paymentPlan);
    $car = \App\Models\Car::query()->find((int) ($draft['car_id'] ?? 0));
    $carName = trim((string) ($car?->merk ?? '') . ' ' . (string) ($car?->tipe ?? '') . ' ' . (string) ($car?->tahun ?? ''));

    $params = [
        'transaction_details' => [
            'order_id' => 'CHK-' . substr(sha1((string) $validated['draft_token']), 0, 20),
            'gross_amount' => (int) $paymentAmount,
        ],
        'customer_details' => [
            'first_name' => $request->user()->name,
            'email' => $request->user()->email,
        ],
        'item_details' => [
            [
                'id' => (string) ($draft['car_id'] ?? 0),
                'price' => (int) $paymentAmount,
                'quantity' => 1,
                'name' => $carName !== '' ? $carName : 'Pembayaran Maharani Mobil',
            ],
        ],
    ];

    $snapToken = Snap::getSnapToken($params);

    return response()->json([
        'token' => $snapToken,
    ]);
}
public function completePayment(Request $request)
{
    $validated = $request->validate([
        'draft_token' => ['required', 'string'],
    ]);

    return $this->completeDraftPayment($request, (string) $validated['draft_token'], 'midtrans', 'PAID');
}

    private function completeDraftPayment(Request $request, string $draftToken, string $gatewayProvider, string $gatewayStatus)
    {
        $checkoutDrafts = app(CheckoutDraftService::class);
        $draft = $checkoutDrafts->findForUser($draftToken, (int) $request->user()->id);

        if (!$draft) {
            $resolvedOrder = $checkoutDrafts->resolvedOrderForUser($draftToken, (int) $request->user()->id);
            if ($resolvedOrder) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('order.tracking', ['order' => $resolvedOrder->id]),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Draft pembayaran tidak ditemukan atau sudah kedaluwarsa.',
            ], 422);
        }

        $paymentPlan = $this->resolveDraftPaymentPlan($draft);
        $paymentAmount = $this->paymentAmountFromDraft($draft, $paymentPlan);
        $order = $checkoutDrafts->finalizePaidDraft($draft, $request->user(), [
            'method' => 'transfer',
            'gateway_provider' => $gatewayProvider,
            'gateway_reference' => strtoupper($gatewayProvider) . '-' . now()->timestamp,
            'gateway_external_id' => $draftToken,
            'gateway_status' => $gatewayStatus,
            'gateway_channel' => 'BANK_TRANSFER',
            'amount' => $paymentAmount,
            'status' => 'verified',
            'handled_role' => 'gateway',
            'handled_at' => now(),
            'verified_at' => now(),
            'paid_at' => now(),
        ]);

        $request->session()->forget('active_checkout_draft');
        $request->session()->put('active_order_id', $order->id);

        return response()->json([
            'success' => true,
            'redirect' => route('order.tracking', ['order' => $order->id]),
        ]);
    }

    /**
     * @param array<string, mixed> $draft
     */
    private function resolveDraftPaymentPlan(array $draft): string
    {
        return (string) ($draft['payment_plan'] ?? 'booking');
    }

    /**
     * @param array<string, mixed> $draft
     */
    private function paymentAmountFromDraft(array $draft, string $paymentPlan): float
    {
        $totalAmount = (float) ($draft['total_amount'] ?? 0);

        return $paymentPlan === 'full'
            ? $totalAmount
            : min($totalAmount, (float) config('payments.booking_fee', 2500000));
    }
}
