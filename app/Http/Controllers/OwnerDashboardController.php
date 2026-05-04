<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::whereIn('status', ['paid', 'completed'])->sum('total');
        $totalUnits = Order::whereIn('status', ['paid', 'completed'])->count();

        $monthlySales = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total) as total')
        )->whereIn('status', ['paid', 'completed'])
         ->groupBy('month')
         ->orderBy('month')
         ->get();

        $yearlySales = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total')
        )->whereIn('status', ['paid', 'completed'])
         ->groupBy('year')
         ->orderBy('year')
         ->get();

        $topBrand = Car::select('merk', DB::raw('COUNT(*) as total'))
            ->groupBy('merk')
            ->orderByDesc('total')
            ->first();

        $topType = Car::select('tipe', DB::raw('COUNT(*) as total'))
            ->groupBy('tipe')
            ->orderByDesc('total')
            ->first();

        return view('pages.dashboard-owner', compact(
            'totalRevenue',
            'totalUnits',
            'monthlySales',
            'yearlySales',
            'topBrand',
            'topType'
        ));
    }
}
