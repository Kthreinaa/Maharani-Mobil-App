<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Car;
use App\Support\OperationalOrderCreator;
use Illuminate\Http\Request;

class MarketingOrderController extends Controller
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

        return view('marketing.orders.index', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'car', 'payment.handledBy', 'handledBy']);

        return view('marketing.orders.show', compact('order'));
    }

    public function create()
    {
        $cars = Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->orderBy('merk')
            ->orderBy('tipe')
            ->get();

        return view('internal.orders.create-offline', [
            'layout' => 'layouts.marketing',
            'title' => 'Input Transaksi Showroom',
            'pageTitle' => 'Input Transaksi Showroom',
            'cars' => $cars,
            'submitRoute' => route('marketing.orders.store'),
            'backRoute' => route('marketing.orders.index'),
            'workspaceLabel' => 'Marketing',
        ]);
    }

    public function store(Request $request, OperationalOrderCreator $creator)
    {
        $validated = $this->validateInternalOrder($request);
        $order = $creator->create($validated, $request->user());

        return redirect()
            ->route('marketing.orders.show', $order)
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
            $payload['cancel_reason'] = $validated['cancel_reason'] ?? null;
            $payload['follow_up_status'] = !empty($validated['next_follow_up_at']) ? 'needs_follow_up' : 'closed_lost';
            $payload['next_follow_up_at'] = $validated['next_follow_up_at'] ?? null;
        } elseif ($validated['status'] === 'completed') {
            $payload['follow_up_status'] = 'closed_won';
        } elseif (in_array($validated['status'], ['confirmed', 'paid'], true)) {
            $payload['follow_up_status'] = 'followed_up';
        }

        $order->update($this->stampActor($request, $payload));

        if ($validated['status'] === 'completed') {
            $order->issueSettlementDocuments();
        } elseif ($validated['status'] === 'paid') {
            $order->issueSettlementDocuments();
        }

        $order->refresh();
        $this->syncCarStatus($order, (string) $order->status);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function syncPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'method' => ['required', 'in:cash,transfer,credit'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $storedMethod = $validated['method'] === 'credit' ? 'va' : $validated['method'];

        $payment = Payment::updateOrCreate(
            ['order_id' => $order->id],
            $this->stampActor($request, [
                'method' => $storedMethod,
                'amount' => (float) $validated['amount'],
                'proof_file' => $order->payment?->proof_file,
                'status' => $order->payment?->status ?? 'pending',
                'verified_by' => $order->payment?->verified_by,
                'verified_at' => $order->payment?->verified_at,
            ])
        );

        $order->update($this->stampActor($request, [
            'payment_method' => $storedMethod,
            'transaction_channel' => $order->transaction_channel ?? 'online',
        ]));

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

        return back()->with('success', 'Data transaksi berhasil diperbarui oleh marketing.');
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
            'payment_method' => ['nullable', 'in:cash,transfer,credit'],
            'amount' => ['required', 'numeric', 'min:0'],
            'cancel_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
