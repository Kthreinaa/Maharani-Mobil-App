<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Order;
use App\Support\CarUnitCodeSuggester;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SupervisorCarController extends Controller
{
    public function index(Request $request)
    {
        $status = strtolower(trim((string) $request->query('status', 'all')));
        $source = strtolower(trim((string) $request->query('source', 'all')));
        $dataset = strtolower(trim((string) $request->query('dataset', 'all')));
        $search = trim((string) $request->query('q', ''));
        $allowedStatuses = ['all', 'available', 'reserved', 'sold'];
        $allowedSources = ['all', 'supervisor', 'unknown'];
        $allowedDatasets = ['all', 'archive', 'operational'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        if (!in_array($source, $allowedSources, true)) {
            $source = 'all';
        }

        if (!in_array($dataset, $allowedDatasets, true)) {
            $dataset = 'all';
        }

        $query = Car::query()
            ->managedCatalog()
            ->with('createdBy:id,name,role')
            ->select('cars.*')
            ->selectSub(
                Order::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('car_id', 'cars.id'),
                'latest_transaction_at'
            )
            ->selectRaw(
                "CASE WHEN cars.status = 'sold' AND cars.deskripsi IN (?, ?) AND EXISTS (SELECT 1 FROM orders archive_orders WHERE archive_orders.car_id = cars.id AND archive_orders.import_reference IS NOT NULL) THEN 1 ELSE 0 END as is_import_archive",
                Car::IMPORT_ARCHIVE_DESCRIPTIONS
            );

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($source !== 'all') {
            if ($source === 'unknown') {
                $query->whereNull('created_by');
            } else {
                $query->whereHas('createdBy', function ($userQuery) use ($source) {
                    if ($source === 'supervisor') {
                        $userQuery->whereIn('role', ['supervisor', 'marketing']);
                        return;
                    }

                    $userQuery->where('role', $source);
                });
            }
        }

        if ($dataset !== 'all') {
            if ($dataset === 'archive') {
                $query->importedArchive();
            } else {
                $query->operationalDataset();
            }
        }

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('merk', 'like', "%{$search}%")
                    ->orWhere('tipe', 'like', "%{$search}%")
                    ->orWhere('kode_unit', 'like', "%{$search}%");

                if (Car::hasBmColumn()) {
                    $sub->orWhere('bm', 'like', "%{$search}%");
                }
            });
        }

        $cars = $this->paginateSortedCars($query->get(), $request);
        $sourceSummary = [
            'all' => Car::managedCatalog()->count(),
            'supervisor' => Car::managedCatalog()->whereHas('createdBy', fn ($userQuery) => $userQuery->whereIn('role', ['supervisor', 'marketing']))->count(),
            'unknown' => Car::managedCatalog()->whereNull('created_by')->count(),
            'archive' => Car::importedArchive()->count(),
            'operational' => Car::operationalDataset()->count(),
            'available' => Car::managedCatalog()->where('status', 'available')->count(),
            'reserved' => Car::managedCatalog()->where('status', 'reserved')->count(),
            'sold' => Car::managedCatalog()->where('status', 'sold')->count(),
        ];

        return view('supervisor.cars.index', compact('cars', 'sourceSummary', 'status', 'source', 'dataset', 'search'));
    }

    public function create()
    {
        return view('supervisor.cars.create', [
            'nextUnitSequence' => CarUnitCodeSuggester::nextSequence(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCar($request);

        $photos = $validated['photos'];
        unset($validated['photos']);

        if (!Car::hasBmColumn()) {
            unset($validated['bm']);
        }

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
        $validated = $this->validateCar($request, $car);

        $newPhotos = null;
        if (array_key_exists('photos', $validated)) {
            $newPhotos = $validated['photos'];
            unset($validated['photos']);
        }

        if (!Car::hasBmColumn()) {
            unset($validated['bm']);
        }

        $car->update($validated);

        if ($newPhotos) {
            $storedPhotos = is_array($car->photos) ? array_values($car->photos) : [];
            foreach ($newPhotos as $photo) {
                $storedPhotos[] = $photo->store("cars/{$car->id}", 'public');
            }
            $car->update(['photos' => $storedPhotos]);

            return redirect()
                ->to(route('supervisor.cars.edit', $car) . '#stored-photos')
                ->with('success', 'Foto unit berhasil ditambahkan.');
        }

        return redirect()->route('supervisor.cars.edit', $car)->with('success', 'Mobil berhasil diperbarui.');
    }

    public function destroyPhoto(Car $car, int $photoIndex)
    {
        $photos = is_array($car->photos) ? array_values($car->photos) : [];

        if (!array_key_exists($photoIndex, $photos)) {
            return back()->withErrors(['photos' => 'Foto mobil yang dipilih tidak ditemukan.']);
        }

        if (count($photos) <= 5) {
            return back()->withErrors(['photos' => 'Foto mobil minimal harus tersisa 5 foto. Tambahkan foto baru terlebih dahulu sebelum menghapus foto ini.']);
        }

        Storage::disk('public')->delete($photos[$photoIndex]);
        unset($photos[$photoIndex]);

        $car->update([
            'photos' => array_values($photos),
        ]);

        return back()->with('success', 'Foto mobil berhasil dihapus.');
    }

    public function replacePhoto(Request $request, Car $car, int $photoIndex)
    {
        $photos = is_array($car->photos) ? array_values($car->photos) : [];

        if (!array_key_exists($photoIndex, $photos)) {
            return back()->withErrors(['photos' => 'Foto mobil yang dipilih tidak ditemukan.']);
        }

        $validated = $request->validate([
            'photo' => ['required', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(4096)],
        ], [
            'photo.required' => 'Pilih foto baru yang ingin digunakan.',
            'photo.image' => 'File pengganti harus berupa gambar yang valid.',
            'photo.max' => 'Ukuran foto pengganti maksimal 4MB.',
        ]);

        Storage::disk('public')->delete($photos[$photoIndex]);
        $photos[$photoIndex] = $validated['photo']->store("cars/{$car->id}", 'public');

        $car->update([
            'photos' => array_values($photos),
        ]);

        return back()->with('success', 'Foto mobil berhasil diganti.');
    }

    public function destroy(Car $car)
    {
        if (is_array($car->photos)) {
            Storage::disk('public')->delete($car->photos);
        }

        $car->delete();
        return redirect()->route('supervisor.cars.index')->with('success', 'Mobil berhasil dihapus.');
    }

    private function paginateSortedCars($cars, Request $request): LengthAwarePaginator
    {
        $sortedCars = $cars->sort(function (Car $left, Car $right) {
            $leftArchive = (int) ($left->is_import_archive ?? 0);
            $rightArchive = (int) ($right->is_import_archive ?? 0);

            if ($leftArchive !== $rightArchive) {
                return $leftArchive <=> $rightArchive;
            }

            if ($left->unit_code_sequence !== $right->unit_code_sequence) {
                return $right->unit_code_sequence <=> $left->unit_code_sequence;
            }

            $leftCreatedAt = optional($left->created_at)?->getTimestamp() ?? 0;
            $rightCreatedAt = optional($right->created_at)?->getTimestamp() ?? 0;
            if ($leftCreatedAt !== $rightCreatedAt) {
                return $rightCreatedAt <=> $leftCreatedAt;
            }

            return $right->id <=> $left->id;
        })->values();

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedCars->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $sortedCars->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function validateCar(Request $request, ?Car $car = null): array
    {
        if (Car::hasBmColumn()) {
            $request->merge([
                'bm' => Car::normalizeBm($request->input('bm')),
            ]);
        }

        $photoRules = $car
            ? ['nullable', 'array']
            : ['required', 'array', 'min:5'];

        $photoItemRules = $car
            ? ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096']
            : ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

        $rules = [
            'kode_unit' => ['required', 'string', 'max:50', 'unique:cars,kode_unit,' . $car?->id],
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
            'photos' => $photoRules,
            'photos.*' => $photoItemRules,
        ];

        if (Car::hasBmColumn()) {
            $rules['bm'] = ['required', 'string', 'max:50', Rule::unique('cars', 'bm')->ignore($car?->id)];
        } else {
            $rules['bm'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules, [
            'bm.required' => 'BM unit wajib diisi agar tiap mobil punya identitas unik.',
            'bm.unique' => 'BM unit sudah dipakai oleh mobil lain. Gunakan BM yang berbeda.',
            'photos.required' => 'Foto mobil wajib diunggah minimal 5 foto.',
            'photos.min' => 'Foto mobil minimal harus berjumlah 5 foto.',
            'photos.*.image' => 'Setiap file foto mobil harus berupa gambar yang valid.',
            'photos.*.mimes' => 'Format foto mobil hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max' => 'Ukuran setiap foto mobil maksimal 4MB.',
        ]);

        return Car::hasBmColumn()
            ? $validated
            : Arr::except($validated, ['bm']);
    }
}
