<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Order::create([
            'user_id' => $request->user()->id,
            'car_id' => $validated['car_id'],
            'total' => $validated['total'],
            'payment_method' => $validated['payment_method'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('customer.orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }
}
