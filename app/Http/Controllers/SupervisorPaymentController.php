<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class SupervisorPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'order.car'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $payments = $query->paginate(10);

        return view('supervisor.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.car']);
        return view('supervisor.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $payment->update([
            'status' => 'rejected',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
