<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarManagementFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_car_filters_apply_search_status_and_source_correctly(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-car-filter@example.com',
        ]);

        $marketing = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-car-filter@example.com',
        ]);

        Car::create([
            'kode_unit' => 'SUP-001',
            'merk' => 'Toyota',
            'tipe' => 'Avanza G',
            'tahun' => 2022,
            'harga' => 210000000,
            'kilometer' => 1000,
            'status' => 'sold',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        Car::create([
            'kode_unit' => 'MKT-001',
            'merk' => 'Honda',
            'tipe' => 'Brio RS',
            'tahun' => 2023,
            'harga' => 205000000,
            'kilometer' => 2000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        Car::create([
            'kode_unit' => 'OLD-001',
            'merk' => 'Suzuki',
            'tipe' => 'Ertiga',
            'tahun' => 2021,
            'harga' => 180000000,
            'kilometer' => 3000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => null,
        ]);

        $response = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.cars.index', [
                'q' => 'Toyota',
                'status' => 'sold',
                'source' => 'supervisor',
            ]));

        $response->assertOk();
        $cars = $response->viewData('cars');
        $this->assertSame(1, $cars->total());
        $this->assertSame('SUP-001', $cars->items()[0]->kode_unit);

        $resetResponse = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.cars.index', [
                'q' => '',
                'status' => 'all',
                'source' => 'all',
            ]));

        $resetResponse->assertOk();
        $resetCars = $resetResponse->viewData('cars');
        $this->assertSame(3, $resetCars->total());
    }

    public function test_marketing_car_filters_return_all_data_when_search_is_cleared(): void
    {
        $marketing = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-products-filter@example.com',
        ]);

        Car::create([
            'kode_unit' => 'MKT-101',
            'merk' => 'Toyota',
            'tipe' => 'Raize',
            'tahun' => 2024,
            'harga' => 255000000,
            'kilometer' => 1200,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        Car::create([
            'kode_unit' => 'MKT-202',
            'merk' => 'Daihatsu',
            'tipe' => 'Terios',
            'tahun' => 2022,
            'harga' => 198000000,
            'kilometer' => 2400,
            'status' => 'sold',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $filteredResponse = $this
            ->actingAs($marketing)
            ->get(route('marketing.products.index', [
                'q' => 'Raize',
                'status' => 'available',
            ]));

        $filteredResponse->assertOk();
        $filteredCars = $filteredResponse->viewData('cars');
        $this->assertSame(1, $filteredCars->total());
        $this->assertSame('MKT-101', $filteredCars->items()[0]->kode_unit);

        $clearedResponse = $this
            ->actingAs($marketing)
            ->get(route('marketing.products.index', [
                'q' => '',
                'status' => 'all',
            ]));

        $clearedResponse->assertOk();
        $clearedCars = $clearedResponse->viewData('cars');
        $this->assertSame(2, $clearedCars->total());
    }

    public function test_supervisor_operational_units_are_sorted_by_latest_unit_code_sequence(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-car-sort@example.com',
        ]);

        Car::create([
            'kode_unit' => 'MM-PJR-014-520',
            'merk' => 'Mitsubishi',
            'tipe' => 'Pajero Dakar',
            'tahun' => 2014,
            'harga' => 305000000,
            'kilometer' => 13000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $supervisor->id,
            'created_at' => now()->subMinutes(3),
        ]);

        Car::create([
            'kode_unit' => 'MM-MRC-015-521',
            'merk' => 'Mercedes Benz',
            'tipe' => 'E250',
            'tahun' => 2015,
            'harga' => 285000000,
            'kilometer' => 10000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $supervisor->id,
            'created_at' => now()->subMinutes(2),
        ]);

        Car::create([
            'kode_unit' => 'MM-PJR-014-524',
            'merk' => 'Mitsubishi',
            'tipe' => 'Pajero Dakar',
            'tahun' => 2014,
            'harga' => 315000000,
            'kilometer' => 6000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $supervisor->id,
            'created_at' => now()->subMinute(),
        ]);

        $response = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.cars.index', [
                'dataset' => 'operational',
            ]));

        $response->assertOk();
        $cars = $response->viewData('cars');

        $this->assertSame('MM-PJR-014-524', $cars->items()[0]->kode_unit);
        $this->assertSame('MM-MRC-015-521', $cars->items()[1]->kode_unit);
        $this->assertSame('MM-PJR-014-520', $cars->items()[2]->kode_unit);
    }
}
