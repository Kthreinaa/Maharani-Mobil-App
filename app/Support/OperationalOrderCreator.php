<?php

namespace App\Support;

use App\Models\Car;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Support\TestDriveOrderLinker;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OperationalOrderCreator
{
    public function create(array $data, User $actor): Order
    {
        $car = Car::query()->findOrFail((int) $data['car_id']);
        if ($car->status !== 'available') {
            throw ValidationException::withMessages([
                'car_id' => $car->status === 'sold'
                    ? 'Unit ini sudah terjual dan tidak bisa dibuat transaksi baru.'
                    : 'Unit ini sudah terpesan dan sedang diproses, sehingga tidak bisa dibuat transaksi baru.',
            ]);
        }

        $customer = $this->resolveCustomer($data);
        $channel = (string) ($data['transaction_channel'] ?? 'offline');
        $salesFlow = $channel === 'offline'
            ? 'offline_showroom'
            : (string) ($data['sales_flow'] ?? 'after_test_drive');
        $status = (string) ($data['status'] ?? 'pending');
        $purchaseMethod = (string) ($data['purchase_method'] ?? 'cash');
        $paymentMethod = $data['payment_method'] ?? null;
        $amount = (float) ($data['amount'] ?? $car->harga);
        $creditDpAmount = $purchaseMethod === 'credit'
            ? (float) ($data['credit_dp_amount'] ?? 0)
            : 0.0;
        $leasingSettlementAmount = $purchaseMethod === 'credit'
            ? (float) ($data['leasing_settlement_amount'] ?? 0)
            : 0.0;
        $totalAmount = $purchaseMethod === 'credit'
            ? round($creditDpAmount + $leasingSettlementAmount, 2)
            : $amount;

        if ($purchaseMethod === 'credit' && $totalAmount <= 0) {
            $totalAmount = $amount;
        }

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => $status,
            'total' => $totalAmount,
            'payment_method' => $purchaseMethod,
            'leasing_partner' => $purchaseMethod === 'credit' ? ($data['leasing_partner'] ?? null) : null,
            'credit_dp_percentage' => $purchaseMethod === 'credit' && $totalAmount > 0
                ? round(($creditDpAmount / $totalAmount) * 100, 2)
                : null,
            'credit_dp_amount' => $purchaseMethod === 'credit' ? $creditDpAmount : null,
            'transaction_channel' => $channel,
            'sales_flow' => $salesFlow,
            'notes' => $this->buildNotes($data),
            'cancel_reason' => $status === 'cancelled' ? ($data['cancel_reason'] ?? null) : null,
            'follow_up_status' => $this->followUpStatusFor($status, $data),
            'next_follow_up_at' => $status === 'cancelled' ? ($data['next_follow_up_at'] ?? null) : null,
            'handled_by' => $actor->id,
            'handled_role' => (string) $actor->role,
            'handled_at' => now(),
            'approved_by' => in_array($status, ['confirmed', 'paid', 'completed'], true) ? $actor->id : null,
            'approved_at' => in_array($status, ['confirmed', 'paid', 'completed'], true) ? now() : null,
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        if ($paymentMethod) {
            $paymentAmount = $purchaseMethod === 'credit'
                ? (in_array($status, ['paid', 'completed'], true) ? $totalAmount : $creditDpAmount)
                : $amount;

            Payment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'amount' => $paymentAmount,
                'status' => in_array($status, ['paid', 'completed'], true) ? 'verified' : 'pending',
                'verified_by' => in_array($status, ['paid', 'completed'], true) ? $actor->id : null,
                'verified_at' => in_array($status, ['paid', 'completed'], true) ? now() : null,
                'handled_by' => $actor->id,
                'handled_role' => (string) $actor->role,
                'handled_at' => now(),
            ]);
        }

        if (in_array($status, ['paid', 'completed'], true)) {
            $order->issuePaymentDocuments();
        }

        if ($status === 'completed') {
            $order->issueHandoverDocuments();
        }

        TestDriveOrderLinker::attach($order);
        $this->syncCarStatus($car, $status);

        return $order->fresh(['user', 'car', 'payment']);
    }

    private function resolveCustomer(array $data): User
    {
        $email = filled($data['customer_email'] ?? null)
            ? Str::lower(trim((string) $data['customer_email']))
            : 'offline-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(5)) . '@maharani.local';

        $existingUser = User::query()->where('email', $email)->first();
        if ($existingUser && $existingUser->role !== 'customer') {
            $email = 'offline-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(5)) . '@maharani.local';
        }

        $customer = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => trim((string) $data['customer_name']),
                'phone' => $data['customer_phone'] ?? null,
                'role' => 'customer',
            ]
        );

        $customer->forceFill([
            'name' => trim((string) $data['customer_name']),
            'phone' => $data['customer_phone'] ?? $customer->phone,
            'role' => 'customer',
        ])->save();

        return $customer;
    }

    private function buildNotes(array $data): ?string
    {
        $purchaseMethod = (string) ($data['purchase_method'] ?? 'cash');
        $notes = collect([
            'Input internal: transaksi customer ' . (($data['transaction_channel'] ?? 'offline') === 'offline' ? 'offline showroom' : 'online setelah test drive'),
            filled($data['customer_phone'] ?? null) ? 'WhatsApp: ' . $data['customer_phone'] : null,
            filled($data['customer_city'] ?? null) ? 'Domisili: ' . $data['customer_city'] : null,
            $purchaseMethod === 'credit' ? 'Metode pembelian: kredit leasing' : 'Metode pembelian: cash',
            $purchaseMethod === 'credit' && filled($data['leasing_partner'] ?? null) ? 'Leasing: ' . $data['leasing_partner'] : null,
            $purchaseMethod === 'credit' && filled($data['credit_dp_amount'] ?? null) ? 'DP customer: ' . number_format((float) $data['credit_dp_amount'], 0, ',', '.') : null,
            $purchaseMethod === 'credit' && filled($data['leasing_settlement_amount'] ?? null) ? 'Sisa pelunasan leasing: ' . number_format((float) $data['leasing_settlement_amount'], 0, ',', '.') : null,
            filled($data['notes'] ?? null) ? 'Catatan: ' . $data['notes'] : null,
        ])->filter()->implode("\n");

        return $notes !== '' ? $notes : null;
    }

    private function followUpStatusFor(string $status, array $data): string
    {
        if ($status === 'completed') {
            return 'closed_won';
        }

        if ($status === 'cancelled') {
            return filled($data['next_follow_up_at'] ?? null) ? 'needs_follow_up' : 'closed_lost';
        }

        if (in_array($status, ['confirmed', 'paid'], true)) {
            return 'followed_up';
        }

        return 'new_lead';
    }

    private function syncCarStatus(Car $car, string $status): void
    {
        if ($status === 'completed') {
            $car->update(['status' => 'sold']);
            return;
        }

        if (in_array($status, ['pending', 'confirmed', 'paid'], true) && $car->status !== 'sold') {
            $car->update(['status' => 'reserved']);
        }
    }
}
