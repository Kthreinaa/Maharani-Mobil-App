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
        private readonly CheckoutDraftService $checkoutDrafts,
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
        return $this->createInvoiceForDraft([
            'token' => 'legacy-order-' . $order->id,
            'user_id' => $order->user_id,
            'car_id' => $order->car_id,
            'total_amount' => (float) $order->total,
        ], $customer, $amount);
    }

    /**
     * @param array<string, mixed> $draft
     * @return array<string, mixed>
     */
    public function createInvoiceForDraft(array $draft, User $customer, float $amount): array
    {
        if (!$this->enabled()) {
            throw new RuntimeException('Xendit belum dikonfigurasi.');
        }

        $token = (string) ($draft['token'] ?? '');
        if ($token === '') {
            throw new RuntimeException('Draft checkout tidak ditemukan.');
        }

        $car = \App\Models\Car::query()->find((int) ($draft['car_id'] ?? 0));
        $totalAmount = (float) ($draft['total_amount'] ?? 0);
        $unitCode = $car?->kode_unit ?: 'UNIT-' . (int) ($draft['car_id'] ?? 0);
        $carName = trim((string) ($car?->merk ?? '') . ' ' . (string) ($car?->tipe ?? '') . ' ' . (string) ($car?->tahun ?? ''));
        $isFullPayment = $amount >= $totalAmount;
        $description = ($isFullPayment ? 'Pelunasan penuh unit ' : 'Booking fee unit ') . $unitCode . ($carName !== '' ? ' - ' . $carName : '');
        $externalId = $this->checkoutDrafts->gatewayExternalId($token);

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
            'success_redirect_url' => route('payment.page', ['draft' => $token, 'gateway' => 'xendit']),
            'failure_redirect_url' => route('payment.page', ['draft' => $token, 'gateway' => 'xendit']),
            'customer' => $customerData,
            'items' => [
                [
                    'name' => $description,
                    'quantity' => 1,
                    'price' => (int) round($amount),
                    'category' => $isFullPayment ? 'Pelunasan Unit' : 'Booking Fee',
                    'url' => route('cars.show', (int) ($draft['car_id'] ?? 0)),
                ],
            ],
            'metadata' => [
                'draft_token' => $token,
                'user_id' => (string) ($draft['user_id'] ?? 0),
                'car_id' => (string) ($draft['car_id'] ?? 0),
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

        $gatewayStatus = Str::upper((string) Arr::get($payload, 'status', 'PENDING'));
        $paidAmount = (float) Arr::get($payload, 'paid_amount', $payment?->amount ?? 0);
        $paidAt = Arr::get($payload, 'paid_at');
        $handledAt = filled($paidAt) ? Carbon::parse((string) $paidAt) : now();

        if (!$payment || !$payment->order) {
            if ($gatewayStatus !== 'PAID') {
                return;
            }

            $token = $this->checkoutDrafts->tokenFromGatewayExternalId($externalId);
            if (!$token) {
                return;
            }

            $draft = $this->checkoutDrafts->find($token);
            if (!$draft) {
                $resolvedOrderId = $this->checkoutDrafts->resolvedOrderId($token);
                if ($resolvedOrderId > 0) {
                    $payment = Payment::query()
                        ->with(['order.car'])
                        ->where('gateway_provider', 'xendit')
                        ->where('gateway_external_id', $externalId)
                        ->first();
                }

                return;
            }

            $user = User::query()->find((int) ($draft['user_id'] ?? 0));
            if (!$user) {
                return;
            }

            $order = $this->checkoutDrafts->finalizePaidDraft($draft, $user, [
                'method' => 'transfer',
                'gateway_provider' => 'xendit',
                'gateway_reference' => (string) Arr::get($payload, 'id', ''),
                'gateway_external_id' => $externalId,
                'gateway_checkout_url' => (string) Arr::get($payload, 'invoice_url', ''),
                'gateway_status' => $gatewayStatus,
                'gateway_channel' => (string) Arr::get($payload, 'payment_channel', Arr::get($payload, 'payment_method', '')),
                'gateway_payload' => $payload,
                'amount' => $paidAmount,
                'status' => 'verified',
                'handled_role' => 'gateway',
                'handled_at' => $handledAt,
                'verified_at' => $handledAt,
                'paid_at' => $handledAt,
            ]);

            $payment = $order->payment;
            if (!$payment) {
                return;
            }
        }

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
                    $payment->order->car->update([
                        'status' => $payment->order->status === 'completed' ? 'sold' : 'reserved',
                    ]);
                }
            }
        });
    }
}
