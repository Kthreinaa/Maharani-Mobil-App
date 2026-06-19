<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Support\TestDriveOrderLinker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutDraftService
{
    private const DRAFT_TTL_SECONDS = 86400;
    private const RESOLVED_TTL_SECONDS = 86400;

    /**
     * @param array<string, mixed> $draft
     */
    public function store(User $user, array $draft, ?string $token = null): string
    {
        $token ??= Str::lower((string) Str::ulid());

        $payload = array_merge($draft, [
            'token' => $token,
            'user_id' => $user->id,
            'updated_at' => now()->toIso8601String(),
        ]);

        Cache::put($this->draftKey($token), $payload, self::DRAFT_TTL_SECONDS);

        return $token;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $token): ?array
    {
        $draft = Cache::get($this->draftKey($token));

        return is_array($draft) ? $draft : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findForUser(string $token, int $userId): ?array
    {
        $draft = $this->find($token);

        if (!$draft || (int) ($draft['user_id'] ?? 0) !== $userId) {
            return null;
        }

        return $draft;
    }

    public function forget(string $token): void
    {
        Cache::forget($this->draftKey($token));
    }

    public function rememberResolvedOrder(string $token, Order $order): void
    {
        Cache::put($this->resolvedKey($token), [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
        ], self::RESOLVED_TTL_SECONDS);
    }

    public function resolvedOrderForUser(string $token, int $userId): ?Order
    {
        $resolved = Cache::get($this->resolvedKey($token));
        if (!is_array($resolved) || (int) ($resolved['user_id'] ?? 0) !== $userId) {
            return null;
        }

        return Order::query()
            ->with(['car', 'payment'])
            ->whereKey((int) ($resolved['order_id'] ?? 0))
            ->where('user_id', $userId)
            ->first();
    }

    public function resolvedOrderId(string $token): int
    {
        $resolved = Cache::get($this->resolvedKey($token));

        return is_array($resolved) ? (int) ($resolved['order_id'] ?? 0) : 0;
    }

    public function gatewayExternalId(string $token): string
    {
        return 'mm-checkout-' . $token;
    }

    public function tokenFromGatewayExternalId(string $externalId): ?string
    {
        $prefix = 'mm-checkout-';

        if (!str_starts_with($externalId, $prefix)) {
            return null;
        }

        $token = substr($externalId, strlen($prefix));

        return $token !== '' ? $token : null;
    }

    /**
     * @param array<string, mixed> $draft
     * @param array<string, mixed> $paymentAttributes
     */
    public function finalizePaidDraft(array $draft, User $user, array $paymentAttributes): Order
    {
        $token = (string) ($draft['token'] ?? '');
        if ($token === '') {
            throw ValidationException::withMessages([
                'payment' => 'Draft checkout tidak valid.',
            ]);
        }

        $resolvedOrder = $this->resolvedOrderForUser($token, $user->id);
        if ($resolvedOrder) {
            return $resolvedOrder;
        }

        return DB::transaction(function () use ($draft, $user, $paymentAttributes, $token) {
            $sourceOffer = null;
            $offerId = (int) ($draft['offer_id'] ?? 0);

            if ($offerId > 0) {
                $sourceOffer = Offer::query()
                    ->where('id', $offerId)
                    ->where('user_id', $user->id)
                    ->where('status', 'accepted')
                    ->firstOrFail();
            }

            $lockedCar = Car::query()
                ->whereKey((int) $draft['car_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedCar->status !== 'available') {
                throw ValidationException::withMessages([
                    'car_id' => $this->unavailableCarMessage($lockedCar),
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'car_id' => $lockedCar->id,
                'total' => (float) ($draft['total_amount'] ?? $lockedCar->harga),
                'payment_method' => (string) ($draft['order_payment_method'] ?? 'transfer'),
                'transaction_channel' => 'online',
                'sales_flow' => (string) ($draft['sales_flow'] ?? 'direct_purchase'),
                'notes' => (string) ($draft['notes'] ?? '') !== '' ? (string) $draft['notes'] : null,
                'follow_up_status' => 'new_lead',
                'document_status' => Order::pendingDocumentStatuses(),
                'status' => 'pending',
            ]);

            TestDriveOrderLinker::attach($order);

            if ($sourceOffer) {
                $sourceOffer->update([
                    'follow_up_status' => 'closed_won',
                    'final_price' => $sourceOffer->negotiated_price,
                ]);
            }

            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                array_merge($paymentAttributes, [
                    'order_id' => $order->id,
                    'method' => (string) ($paymentAttributes['method'] ?? 'transfer'),
                    'amount' => (float) ($paymentAttributes['amount'] ?? 0),
                    'status' => (string) ($paymentAttributes['status'] ?? 'pending'),
                ])
            );

            if ($payment->status === 'verified') {
                if ((float) $payment->amount >= (float) $order->total) {
                    $order->update([
                        'payment_method' => (string) ($draft['order_payment_method'] ?? $payment->method),
                        'status' => 'paid',
                    ]);
                    $order->issueSettlementDocuments();
                } else {
                    $order->update([
                        'payment_method' => (string) ($draft['order_payment_method'] ?? $payment->method),
                        'status' => 'confirmed',
                    ]);
                    $order->resetTransactionDocuments();
                }
            }

            if ($order->car && $order->car->status !== 'sold') {
                $order->car->update([
                    'status' => $order->status === 'completed' ? 'sold' : 'reserved',
                ]);
            }

            $this->rememberResolvedOrder($token, $order);
            $this->forget($token);

            return $order->fresh(['car', 'payment']);
        });
    }

    private function draftKey(string $token): string
    {
        return 'checkout_draft:' . $token;
    }

    private function resolvedKey(string $token): string
    {
        return 'checkout_draft_resolved:' . $token;
    }

    private function unavailableCarMessage(Car $car): string
    {
        return $car->status === 'sold'
            ? 'Unit ini sudah terjual dan tidak bisa dipesan lagi.'
            : 'Unit ini sedang diproses customer lain dan tidak bisa dipesan lagi.';
    }
}
