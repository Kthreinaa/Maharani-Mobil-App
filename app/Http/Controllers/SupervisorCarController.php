<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupervisorCarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('merk', 'like', "%{$q}%")
                    ->orWhere('tipe', 'like', "%{$q}%")
                    ->orWhere('kode_unit', 'like', "%{$q}%");
            });
        }

        $cars = $query->paginate(10);
        return view('supervisor.cars.index', compact('cars'));
    }

    public function create()
    {
        return view('supervisor.cars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50', 'unique:cars,kode_unit'],
            'merk' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'harga' => ['required', 'numeric', 'min:0'],
            'kilometer' => ['required', 'numeric', 'min:0'],
            'transmisi' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:50'],
            'bahan_bakar' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,reserved,sold'],
            'deskripsi' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $photos = $validated['photos'];
        unset($validated['photos']);

        $validated['created_by'] = $request->user()->id;
        $car = Car::create($validated);

        $storedPhotos = [];
        foreach ($photos as $photo) {
            $storedPhotos[] = $photo->store("cars/{$car->id}", 'public');
        }
        $car->update(['photos' => $storedPhotos]);

        return redirect()->route('supervisor.cars.index')->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function edit(Car $car)
    {
        return view('supervisor.cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50', 'unique:cars,kode_unit,' . $car->id],
            'merk' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'harga' => ['required', 'numeric', 'min:0'],
            'kilometer' => ['required', 'numeric', 'min:0'],
            'transmisi' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:50'],
            'bahan_bakar' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,reserved,sold'],
            'deskripsi' => ['nullable', 'string'],
            'photos' => ['sometimes', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required_with:photos', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $newPhotos = null;
        if (array_key_exists('photos', $validated)) {
            $newPhotos = $validated['photos'];
            unset($validated['photos']);
        }

        $car->update($validated);

        if ($newPhotos) {
            if (is_array($car->photos)) {
                Storage::disk('public')->delete($car->photos);
            }

            $storedPhotos = [];
            foreach ($newPhotos as $photo) {
                $storedPhotos[] = $photo->store("cars/{$car->id}", 'public');
            }
            $car->update(['photos' => $storedPhotos]);
        }

        return redirect()->route('supervisor.cars.index')->with('success', 'Mobil berhasil diperbarui.');
    }

    public function destroy(Car $car)
    {
        if (is_array($car->photos)) {
            Storage::disk('public')->delete($car->photos);
        }

        $car->delete();
        return redirect()->route('supervisor.cars.index')->with('success', 'Mobil berhasil dihapus.');
    }
}
