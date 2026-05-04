<?php

namespace App\Http\Controllers;

use App\Models\Car;

class CustomerDashboardController extends Controller
{
    /**
     * Menampilkan home khusus customer dengan sumber data mobil yang sama seperti katalog (stok available).
     */
    public function index()
    {
        $catalogCars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $homeOverviewCars = $catalogCars->take(6);

        return view('pages.home', compact('catalogCars', 'homeOverviewCars'));
    }
}
