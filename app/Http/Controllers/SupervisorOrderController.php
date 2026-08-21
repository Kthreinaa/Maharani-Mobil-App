<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Car;
use App\Support\CreditSimulationCatalog;
use App\Support\OperationalOrderCreator;
use Illuminate\Http\Request;

class SupervisorOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->filled('status') ? (string) $request->input('status') : 'all';
        if (!in_array($status, ['all', 'pending', 'confirmed', 'paid', 'completed', 'cancelled'], true)) {
            $status = 'all';
        }

        $query = Order::query()
            ->with(['user', 'car', 'payment', 'handledBy'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', fn ($user) => $user->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('car', fn ($car) => $car->where('merk', 'like', '%' . $q . '%')->orWhere('tipe', 'like', '%' . $q . '%'))
                    ->orWhere('id', $q)
                    ->orWhere('order_code', 'like', '%' . $q . '%');
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('supervisor.orders.index', compact('orders', 'status'));
    }

    public function show(Request $request, Order $order)
    {
        if ($order->status === 'pending' && !$order->handled_by) {
            $order->forceFill([
                'handled_by' => $request->user()->id,
                'handled_role' => (string) $request->user()->role,
                'handled_at' => now(),
            ])->save();
        }

        $order->load(['user', 'car', 'payment.handledBy', 'handledBy']);
        $leasingPartners = collect(CreditSimulationCatalog::partners())->pluck('name')->values();

        return view('supervisor.orders.show', compact('order', 'leasingPartners'));
    }

    public function create()
    {
        $cars = Car::query()
            ->where('status', 'available')
            ->orderBy('merk')
            ->orderBy('tipe')
            ->get();

        return view('internal.orders.create-offline', [
            'layout' => 'layouts.supervisor',
            'title' => 'Input Transaksi Showroom',
            'pageTitle' => 'Input Transaksi Showroom',
            'cars' => $cars,
            'leasingPartners' => collect(CreditSimulationCatalog::partners())->pluck('name')->values(),
            'submitRoute' => route('supervisor.payments.store'),
            'backRoute' => route('supervisor.payments.index'),
            'workspaceLabel' => 'Supervisor',
        ]);
    }

    public function store(Request $request, OperationalOrderCreator $creator)
    {
        $validated = $this->validateInternalOrder($request);
        $creditValidation = $this->validateCreditBreakdown($validated, true);

        if ($creditValidation !== null) {
            return $creditValidation;
        }

        $order = $creator->create($validated, $request->user());
        $payment = $order->payment;

        if ($payment) {
            return redirect()
                ->route('supervisor.payments.show', $payment)
                ->with('success', 'Transaksi showroom berhasil dicatat ke sistem.');
        }

        return redirect()
            ->route('supervisor.payments.index')
            ->with('success', 'Transaksi showroom berhasil dicatat ke sistem.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,paid,completed,cancelled'],
            'cancel_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $payload = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'cancelled') {
            $payload['cancel_reason'] = $validated['cancel_reason'] ?: $order->customer_cancellation_reason;
            $payload['follow_up_status'] = !empty($validated['next_follow_up_at']) ? 'needs_follow_up' : 'closed_lost';
            $payload['next_follow_up_at'] = $validated['next_follow_up_at'] ?? null;
            $payload['customer_cancellation_reason'] = null;
            $payload['customer_cancellation_requested_at'] = null;
        } elseif ($validated['status'] === 'completed') {
            $payload['follow_up_status'] = 'closed_won';
            $payload['approved_by'] = $request->user()->id;
            $payload['approved_at'] = now();
        } elseif (in_array($validated['status'], ['confirmed', 'paid'], true)) {
            $payload['follow_up_status'] = 'followed_up';
            $payload['approved_by'] = $request->user()->id;
            $payload['approved_at'] = now();
        }

        $order->update($this->stampActor($request, $payload));

        if ($validated['status'] === 'completed') {
            $order->issueSettlementDocuments();
        } elseif ($validated['status'] === 'paid') {
            $order->issueSettlementDocuments();
        }

        $order->refresh();
        $this->syncCarStatus($order, (string) $order->status);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function approveCancellation(Request $request, Order $order)
    {
        if (!$order->has_pending_cancellation_request) {
            return back()->with('error', 'Tidak ada permintaan pembatalan yang menunggu persetujuan.');
        }

        $order->update($this->stampActor($request, [
            'status' => 'cancelled',
            'cancel_reason' => $order->customer_cancellation_reason,
            'customer_cancellation_reason' => null,
            'customer_cancellation_requested_at' => null,
            'follow_up_status' => 'closed_lost',
            'next_follow_up_at' => null,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]));

        $order->refresh();
        $this->syncCarStatus($order, (string) $order->status);

        return back()->with('success', 'Pembatalan pesanan disetujui. Status order dibatalkan dan stok mobil sudah dikembalikan.');
    }

    public function syncPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'purchase_method' => ['required', 'in:cash,credit'],
            'payment_method' => ['required', 'in:cash,transfer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'leasing_partner' => ['nullable', 'string', 'max:120'],
            'credit_dp_amount' => ['nullable', 'numeric', 'min:0'],
            'leasing_settlement_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $purchaseMethod = (string) $validated['purchase_method'];
        $paymentMethod = (string) $validated['payment_method'];
        $paymentAmount = (float) $validated['amount'];
        $orderUpdates = [
            'payment_method' => $purchaseMethod,
            'transaction_channel' => $order->transaction_channel ?? 'online',
        ];

        if ($purchaseMethod === 'credit') {
            $creditValidation = $this->validateCreditBreakdown($validated, false);

            if ($creditValidation !== null) {
                return $creditValidation;
            }

            $leasingPartner = trim((string) ($validated['leasing_partner'] ?? ''));
            $creditDpAmount = (float) ($validated['credit_dp_amount'] ?? 0);
            $leasingSettlementAmount = (float) ($validated['leasing_settlement_amount'] ?? 0);
            $totalAmount = round($creditDpAmount + $leasingSettlementAmount, 2);

            $orderUpdates['total'] = $totalAmount;
            $orderUpdates['leasing_partner'] = $leasingPartner;
            $orderUpdates['credit_dp_amount'] = $creditDpAmount;
            $orderUpdates['credit_dp_percentage'] = $totalAmount > 0
                ? round(($creditDpAmount / $totalAmount) * 100, 2)
                : null;
        } else {
            $orderUpdates['leasing_partner'] = null;
            $orderUpdates['credit_dp_amount'] = null;
            $orderUpdates['credit_dp_percentage'] = null;
            $orderUpdates['credit_tenor_months'] = null;
            $orderUpdates['credit_monthly_installment'] = null;
            $orderUpdates['credit_interest_rate'] = null;
        }

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            $this->stampActor($request, [
                'method' => $paymentMethod,
                'amount' => $paymentAmount,
                'proof_file' => $order->payment?->proof_file,
                'status' => $order->payment?->status ?? 'pending',
                'verified_by' => $order->payment?->verified_by,
                'verified_at' => $order->payment?->verified_at,
            ])
        );

        $order->update($this->stampActor($request, $orderUpdates));
        $order->refresh()->load('payment');

        if (($payment->status ?? 'pending') === 'verified') {
            if ((float) $payment->amount >= (float) $order->total) {
                $order->update(['status' => 'paid']);
                $order->issueSettlementDocuments();
                $order->refresh();
                $this->syncCarStatus($order, (string) $order->status);
            } elseif (!in_array($order->status, ['completed', 'cancelled'], true)) {
                $order->update(['status' => 'confirmed']);
                $order->resetTransactionDocuments();
                $order->refresh();
                $this->syncCarStatus($order, (string) $order->status);
            }
        }

        return back()->with('success', 'Data transaksi berhasil diperbarui oleh supervisor.');
    }

    private function syncCarStatus(Order $order, string $status): void
    {
        if (!$order->car) {
            return;
        }

        if ($status === 'completed') {
            $order->car->update(['status' => 'sold']);
            return;
        }

        if (in_array($status, ['pending', 'confirmed', 'paid'], true)) {
            $order->car->update(['status' => 'reserved']);
            return;
        }

        if ($status === 'cancelled') {
            $hasOtherActiveOrders = Order::query()
                ->where('car_id', $order->car_id)
                ->where('id', '!=', $order->id)
                ->whereIn('status', ['pending', 'confirmed', 'paid', 'completed'])
                ->exists();

            if (!$hasOtherActiveOrders) {
                $order->car->update(['status' => 'available']);
            }
        }
    }

    private function stampActor(Request $request, array $payload): array
    {
        $payload['handled_by'] = $request->user()->id;
        $payload['handled_role'] = (string) $request->user()->role;
        $payload['handled_at'] = now();

        return $payload;
    }

    private function validateInternalOrder(Request $request): array
    {
        return $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_city' => ['nullable', 'string', 'max:100'],
            'car_id' => ['required', 'exists:cars,id'],
            'transaction_channel' => ['required', 'in:online,offline'],
            'sales_flow' => ['nullable', 'in:after_test_drive,direct_purchase'],
            'status' => ['required', 'in:pending,confirmed,paid,completed,cancelled'],
            'purchase_method' => ['required', 'in:cash,credit'],
            'payment_method' => ['required', 'in:cash,transfer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'leasing_partner' => ['nullable', 'string', 'max:120'],
            'credit_dp_amount' => ['nullable', 'numeric', 'min:0'],
            'leasing_settlement_amount' => ['nullable', 'numeric', 'min:0'],
            'cancel_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function validateCreditBreakdown(array $validated, bool $requireAmountMatch): ?\Illuminate\Http\RedirectResponse
    {
        if (($validated['purchase_method'] ?? 'cash') !== 'credit') {
            return null;
        }

        $allowedPartners = collect(CreditSimulationCatalog::partners())->pluck('name')->all();
        $leasingPartner = trim((string) ($validated['leasing_partner'] ?? ''));
        $creditDpAmount = (float) ($validated['credit_dp_amount'] ?? 0);
        $leasingSettlementAmount = (float) ($validated['leasing_settlement_amount'] ?? 0);
        $amount = (float) ($validated['amount'] ?? 0);

        if ($leasingPartner === '' || !in_array($leasingPartner, $allowedPartners, true)) {
            return back()
                ->withErrors(['leasing_partner' => 'Pilih leasing yang dipakai untuk transaksi kredit.'])
                ->withInput();
        }

        if ($creditDpAmount <= 0) {
            return back()
                ->withErrors(['credit_dp_amount' => 'Input DP customer untuk transaksi kredit.'])
                ->withInput();
        }

        if ($leasingSettlementAmount <= 0) {
            return back()
                ->withErrors(['leasing_settlement_amount' => 'Input sisa pelunasan oleh leasing untuk transaksi kredit.'])
                ->withInput();
        }

        $totalAmount = round($creditDpAmount + $leasingSettlementAmount, 2);

        if ($requireAmountMatch && abs($amount - $totalAmount) > 0.01) {
            return back()
                ->withErrors(['amount' => 'Nominal transaksi kredit harus sama dengan total DP customer ditambah sisa pelunasan leasing.'])
                ->withInput();
        }

        if (!$requireAmountMatch && $amount > $totalAmount) {
            return back()
                ->withErrors(['amount' => 'Nominal pembayaran tidak boleh melebihi total harga unit.'])
                ->withInput();
        }

        return null;
    }
}
