<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalAvailableCars = Car::where('status', 'available')->count();
        $totalSold = Car::where('status', 'sold')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $verifiedPayments = Payment::where('status', 'verified')->count();
        $completedPayments = Payment::where('status', 'verified')->count();
        $totalTestDrives = TestDrive::count();
        $totalOffers = Offer::count();

        $year = now()->year;
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : "MONTH(created_at)";
        $yearExpr = $driver === 'sqlite'
            ? "strftime('%Y', created_at)"
            : "YEAR(created_at)";

        $monthlySales = Order::select(DB::raw("$monthExpr as month"), DB::raw('COUNT(*) as total'))
            ->whereRaw("$yearExpr = ?", [$year])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = Order::select(DB::raw("$monthExpr as month"), DB::raw('SUM(total) as total'))
            ->whereRaw("$yearExpr = ?", [$year])
            ->whereIn('status', ['paid', 'completed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $paymentStatus = Payment::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $topCars = Order::select('car_id', DB::raw('COUNT(*) as total'))
            ->with('car')
            ->groupBy('car_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $recentOrders = Order::with(['user', 'car'])->latest()->take(5)->get();
        $recentPayments = Payment::with(['order.user', 'order.car'])->latest()->take(5)->get();
        $recentTestDrives = TestDrive::with(['user', 'car'])->latest()->take(5)->get();

        $activity = collect()
            ->merge(Order::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'order',
                    'label' => 'Pesanan baru masuk',
                    'detail' => 'Order #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Payment::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'payment',
                    'label' => 'Pembayaran baru',
                    'detail' => 'Payment #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(User::whereIn('role', ['owner', 'supervisor', 'marketing'])->latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'user',
                    'label' => 'User internal baru',
                    'detail' => $item->name,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Car::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'car',
                    'label' => 'Mobil baru ditambahkan',
                    'detail' => $item->merk . ' ' . $item->tipe,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(TestDrive::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'testdrive',
                    'label' => 'Booking test drive',
                    'detail' => 'ID #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Offer::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'offer',
                    'label' => 'Penawaran baru',
                    'detail' => 'ID #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return view('pages.dashboard-supervisor', compact(
            'totalCustomers',
            'totalOrders',
            'totalAvailableCars',
            'totalSold',
            'pendingPayments',
            'verifiedPayments',
            'completedPayments',
            'totalTestDrives',
            'totalOffers',
            'monthlySales',
            'monthlyRevenue',
            'paymentStatus',
            'topCars',
            'recentOrders',
            'recentPayments',
            'recentTestDrives',
            'activity'
        ));
    }
}
