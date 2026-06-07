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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'added',
                'is_favorite' => true,
                'message' => 'Mobil ditambahkan ke favorit.',
            ]);
        }

        return back()->with('success', 'Mobil ditambahkan ke favorit.');
    }

    public function destroy(Request $request, Car $car)
    {
        $request->user()->favoriteCars()->detach($car->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'removed',
                'is_favorite' => false,
                'message' => 'Mobil dihapus dari favorit.',
            ]);
        }

        return back()->with('success', 'Mobil dihapus dari favorit.');
    }
}
