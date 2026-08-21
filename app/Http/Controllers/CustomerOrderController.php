<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('car', 'payment', 'purchaseReview')
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    public function requestCancellation(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);

        if (!$order->can_customer_request_cancellation) {
            return back()->with('error', 'Pesanan ini sudah tidak bisa diajukan pembatalan.');
        }

        $validated = $request->validate([
            'customer_cancellation_reason' => ['nullable', 'string'],
        ]);

        $reason = trim((string) ($validated['customer_cancellation_reason'] ?? ''));

        $order->forceFill([
            'customer_cancellation_reason' => $reason !== '' ? mb_substr($reason, 0, 255) : null,
            'customer_cancellation_requested_at' => now(),
        ])->save();

        return back()->with('success', 'Permintaan pembatalan sudah dikirim dan sedang menunggu persetujuan supervisor.');
    }
}
