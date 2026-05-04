<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Offer;
use App\Models\Car;

class MarketingDashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalSold = Car::where('status', 'sold')->count();
        $incomingOffers = Offer::where('status', 'pending')->count();
        $topInterested = Car::withCount('offers')->orderByDesc('offers_count')->take(5)->get();

        return view('pages.dashboard-marketing', compact(
            'totalOrders',
            'totalSold',
            'incomingOffers',
            'topInterested'
        ));
    }
}
