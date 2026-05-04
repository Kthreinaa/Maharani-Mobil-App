<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favoriteCars()->latest()->get();
        return view('customer.favorites', compact('favorites'));
    }

    public function store(Request $request, Car $car)
    {
        $request->user()->favoriteCars()->syncWithoutDetaching([$car->id]);
        return back()->with('success', 'Mobil ditambahkan ke favorit.');
    }

    public function destroy(Request $request, Car $car)
    {
        $request->user()->favoriteCars()->detach($car->id);
        return back()->with('success', 'Mobil dihapus dari favorit.');
    }
}
