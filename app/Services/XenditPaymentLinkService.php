<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class XenditPaymentLinkService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    public function enabled(): bool
    {
        return filled(config('services.xendit.secret_key'));
    }

    public function settlementAccount(): array
    {
        return [
            'bank' => (string) config('payments.settlement.bank', 'Bank Mandiri'),
            'account_name' => (string) config('payments.settlement.account_name', 'Diki Susanto'),
            'account_number' => (string) config('payments.settlement.account_number', '1080093012152'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function createInvoice(Order $order, User $customer, float $amount): array
    {
        if (!$this->enabled()) {
            throw new RuntimeException('Xendit belum dikonfigurasi.');
        }

        $car = $order->car;
        $unitCode = $car?->kode_unit ?: 'UNIT-' . $order->car_id;
        $carName = trim((string) ($car?->merk ?? '') . ' ' . (string) ($car?->tipe ?? '') . ' ' . (string) ($car?->tahun ?? ''));
        $description = 'Booking fee unit ' . $unitCode . ($carName !== '' ? ' - ' . $carName : '');
        $externalId = 'mm-order-' . $order->id . '-' . Str::lower((string) Str::ulid());

        $customerData = array_filter([
            'given_names' => (string) $customer->name,
            'email' => (string) ($customer->email ?? ''),
            'mobile_number' => (string) ($customer->phone ?? ''),
        ], fn ($value) => $value !== '');

        $payload = [
            'external_id' => $externalId,
            'amount' => (int) round($amount),
            'description' => $description,
            'invoice_duration' => 86400,
            'currency' => 'IDR',
            'success_redirect_url' => route('order.tracking', ['order' => $order->id]),
            'failure_redirect_url' => route('payment.page', ['order' => $order->id]),
            'customer' => $customerData,
            'items' => [
                [
                    'name' => $description,
                    'quantity' => 1,
                    'price' => (int) round($amount),
                    'category' => 'Booking Fee',
                    'url' => route('cars.show', $order->car_id),
                ],
            ],
            'metadata' => [
                'order_id' => (string) $order->id,
                'user_id' => (string) $order->user_id,
                'car_id' => (string) $order->car_id,
                'unit_code' => $unitCode,
            ],
        ];

        $response = $this->http
            ->baseUrl((string) config('services.xendit.base_url', 'https://api.xendit.co'))
            ->withBasicAuth((string) config('services.xendit.secret_key'), '')
            ->acceptJson()
            ->post('/v2/invoices', $payload)
            ->throw()
            ->json();

        if (!is_array($response) || !filled($response['invoice_url'] ?? null) || !filled($response['id'] ?? null)) {
            throw new RuntimeException('Respons invoice Xendit tidak lengkap.');
        }

        return $response;
    }

    public function hasValidWebhookToken(Request $request): bool
    {
        $expectedToken = (string) config('services.xendit.webhook_token', '');

        if ($expectedToken === '') {
            return false;
        }

        return hash_equals($expectedToken, (string) $request->header('x-callback-token', ''));
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function handleInvoiceWebhook(array $payload): void
    {
        $externalId = (string) Arr::get($payload, 'external_id', '');
        if ($externalId === '') {
            return;
        }

        /** @var Payment|null $payment */
        $payment = Payment::query()
            ->with(['order.car'])
            ->where('gateway_provider', 'xendit')
            ->where('gateway_external_id', $externalId)
            ->first();

        if (!$payment || !$payment->order) {
            return;
        }

        $gatewayStatus = Str::upper((string) Arr::get($payload, 'status', 'PENDING'));
        $paidAmount = (float) Arr::get($payload, 'paid_amount', $payment->amount ?? 0);
        $paidAt = Arr::get($payload, 'paid_at');
        $handledAt = filled($paidAt) ? Carbon::parse((string) $paidAt) : now();

        DB::transaction(function () use ($payment, $payload, $gatewayStatus, $paidAmount, $handledAt): void {
            $payment->fill([
                'method' => 'transfer',
                'amount' => $paidAmount > 0 ? $paidAmount : $payment->amount,
                'gateway_status' => $gatewayStatus,
                'gateway_reference' => (string) Arr::get($payload, 'id', $payment->gateway_reference),
                'gateway_channel' => (string) Arr::get($payload, 'payment_channel', Arr::get($payload, 'payment_method', $payment->gateway_channel)),
                'gateway_payload' => $payload,
                'handled_role' => 'gateway',
                'handled_at' => $handledAt,
            ]);

            if ($gatewayStatus === 'PAID') {
                $payment->fill([
                    'status' => 'verified',
                    'verified_at' => $handledAt,
                    'paid_at' => $handledAt,
                ]);
            }

            $payment->save();

            if ($gatewayStatus === 'PAID' && $payment->order) {
                if ((float) $payment->amount >= (float) $payment->order->total) {
                    $payment->order->update(['status' => 'paid']);
                    $payment->order->issueSettlementDocuments();
                } else {
                    $payment->order->update(['status' => 'confirmed']);
                    $payment->order->resetTransactionDocuments();
                }

                if ($payment->order->car && $payment->order->car->status !== 'sold') {
                    $payment->order->car->update(['status' => 'reserved']);
                }
            }
        });
    }
}
