<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;

class SupervisorActivityController extends Controller
{
    public function index()
    {
        $activity = collect()
            ->merge(Order::latest()->take(20)->get()->map(fn ($item) => [
                'type' => 'order',
                'label' => 'Pesanan baru',
                'detail' => 'Order #' . $item->id,
                'time' => $item->created_at,
            ]))
            ->merge(Payment::latest()->take(20)->get()->map(fn ($item) => [
                'type' => 'payment',
                'label' => 'Pembayaran',
                'detail' => 'Payment #' . $item->id,
                'time' => $item->created_at,
            ]))
            ->merge(User::whereIn('role', ['owner', 'supervisor', 'marketing'])->latest()->take(10)->get()->map(fn ($item) => [
                'type' => 'user',
                'label' => 'User internal',
                'detail' => $item->name,
                'time' => $item->created_at,
            ]))
            ->merge(Car::latest()->take(10)->get()->map(fn ($item) => [
                'type' => 'car',
                'label' => 'Mobil baru',
                'detail' => $item->merk . ' ' . $item->tipe,
                'time' => $item->created_at,
            ]))
            ->merge(TestDrive::latest()->take(10)->get()->map(fn ($item) => [
                'type' => 'testdrive',
                'label' => 'Test drive',
                'detail' => 'Booking #' . $item->id,
                'time' => $item->created_at,
            ]))
            ->merge(Offer::latest()->take(10)->get()->map(fn ($item) => [
                'type' => 'offer',
                'label' => 'Penawaran',
                'detail' => 'Offer #' . $item->id,
                'time' => $item->created_at,
            ]))
            ->sortByDesc('time')
            ->values();

        return view('supervisor.activity.index', compact('activity'));
    }
}
