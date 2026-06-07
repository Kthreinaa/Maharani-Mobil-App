<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class MarketingTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()
            ->with(['order.user', 'order.car', 'handledBy', 'verifier'])
            ->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('method') && $request->input('method') !== 'all') {
            $method = (string) $request->input('method');
            $query->whereHas('order', function ($orderQuery) use ($method) {
                if ($method === 'credit') {
                    $orderQuery->whereIn('payment_method', ['credit', 'va']);
                    return;
                }

                $orderQuery->where('payment_method', 'cash');
            });
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('order.user', fn ($user) => $user->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('order.car', fn ($car) => $car->where('merk', 'like', '%' . $q . '%')->orWhere('tipe', 'like', '%' . $q . '%'))
                    ->orWhereHas('order', fn ($order) => $order->where('id', $q)->orWhere('order_code', 'like', '%' . $q . '%'));
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('marketing.transactions.index', compact('payments'));
    }
}
