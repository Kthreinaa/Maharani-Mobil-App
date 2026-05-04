<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class SupervisorOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'car'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $orders = $query->paginate(10);

        return view('supervisor.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'car', 'payment']);
        return view('supervisor.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,paid,completed,cancelled'],
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
