<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MarketingCarController extends Controller
{
    public function index(Request $request)
    {
        $status = strtolower(trim((string) $request->query('status', 'all')));
        $source = strtolower(trim((string) $request->query('source', 'all')));
        $dataset = strtolower(trim((string) $request->query('dataset', 'all')));
        $search = trim((string) $request->query('q', ''));
        $allowedStatuses = ['all', 'available', 'reserved', 'sold'];
        $allowedSources = ['all', 'marketing', 'supervisor', 'unknown'];
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
                $sub->where('merk', 'like', '%' . $search . '%')
                    ->orWhere('tipe', 'like', '%' . $search . '%')
                    ->orWhere('kode_unit', 'like', '%' . $search . '%')
                    ->orWhere('bm', 'like', '%' . $search . '%');
            });
        }

        $cars = $this->paginateSortedCars($query->get(), $request);
        $sourceSummary = [
            'all' => Car::managedCatalog()->count(),
            'marketing' => Car::managedCatalog()->whereHas('createdBy', fn ($userQuery) => $userQuery->where('role', 'marketing'))->count(),
            'supervisor' => Car::managedCatalog()->whereHas('createdBy', fn ($userQuery) => $userQuery->where('role', 'supervisor'))->count(),
            'unknown' => Car::managedCatalog()->whereNull('created_by')->count(),
            'available' => Car::managedCatalog()->where('status', 'available')->count(),
            'sold' => Car::managedCatalog()->where('status', 'sold')->count(),
            'archive' => Car::importedArchive()->count(),
            'operational' => Car::operationalDataset()->count(),
        ];

        return view('marketing.cars.index', compact('cars', 'sourceSummary', 'status', 'source', 'dataset', 'search'));
    }

    public function create()
    {
        return $this->denyManagementAccess();
    }

    public function store(Request $request)
    {
        return $this->denyManagementAccess();
    }

    public function edit(Car $car)
    {
        return $this->denyManagementAccess();
    }

    public function update(Request $request, Car $car)
    {
        return $this->denyManagementAccess();
    }

    public function destroyPhoto(Car $car, int $photoIndex)
    {
        return $this->denyManagementAccess();
    }

    public function replacePhoto(Request $request, Car $car, int $photoIndex)
    {
        return $this->denyManagementAccess();
    }

    public function destroy(Car $car)
    {
        return $this->denyManagementAccess();
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

    private function denyManagementAccess()
    {
        return redirect()
            ->route('marketing.products.index')
            ->withErrors([
                'marketing_products_access' => 'Marketing hanya dapat memantau data unit. Pengelolaan data mobil dilakukan oleh supervisor.',
            ]);
    }
}
