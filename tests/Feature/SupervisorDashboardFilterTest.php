<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorDashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_brand_leaderboard_follows_selected_year_filter(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-dashboard@example.com',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-dashboard@example.com',
        ]);

        $toyotaCar = Car::create([
            'kode_unit' => 'TY-2021-001',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2021,
            'harga' => 200000000,
            'kilometer' => 1000,
            'status' => 'sold',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $hondaCar = Car::create([
            'kode_unit' => 'HN-2022-001',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2022,
            'harga' => 210000000,
            'kilometer' => 1500,
            'status' => 'sold',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $this->createCompletedOrder($customer->id, $toyotaCar->id, '2021-06-10 10:00:00');
        $this->createCompletedOrder($customer->id, $hondaCar->id, '2022-03-15 10:00:00');
        $this->createCompletedOrder($customer->id, $hondaCar->id, '2022-07-20 10:00:00');

        $response2021 = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.dashboard', ['report_range' => 'yearly', 'year' => 2021]));

        $response2021->assertOk();
        $topBrands2021 = $response2021->viewData('topBrands');
        $this->assertCount(1, $topBrands2021);
        $this->assertSame('Toyota', $topBrands2021->first()->merk);
        $this->assertSame(1, (int) $topBrands2021->first()->total);

        $response2022 = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.dashboard', ['report_range' => 'yearly', 'year' => 2022]));

        $response2022->assertOk();
        $topBrands2022 = $response2022->viewData('topBrands');
        $this->assertCount(1, $topBrands2022);
        $this->assertSame('Honda', $topBrands2022->first()->merk);
        $this->assertSame(2, (int) $topBrands2022->first()->total);

        $this->actingAs($supervisor)
            ->getJson(route('supervisor.dashboard', ['panel' => 'sales-report', 'report_range' => 'yearly', 'year' => 2022]))
            ->assertOk()
            ->assertJsonPath('top_brands.labels.0', 'Honda')
            ->assertJsonPath('top_brands.totals.0', 2);

        $this->actingAs($supervisor)
            ->getJson(route('supervisor.dashboard', ['panel' => 'sales-trend', 'year' => 2022]))
            ->assertOk()
            ->assertJsonStructure([
                'sales_chart' => ['labels', 'totals'],
                'revenue_chart' => ['labels', 'totals'],
            ]);
    }

    private function createCompletedOrder(int $userId, int $carId, string $createdAt): void
    {
        $order = new Order([
            'user_id' => $userId,
            'car_id' => $carId,
            'status' => 'completed',
            'total' => 200000000,
            'payment_method' => 'cash',
        ]);
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt;
        $order->save();
    }
}
