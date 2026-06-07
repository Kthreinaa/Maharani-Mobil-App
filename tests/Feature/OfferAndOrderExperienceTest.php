<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferAndOrderExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_and_marketing_offer_search_and_status_filter_return_matching_data(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'name' => 'Supervisor Maharani',
        ]);

        $marketing = User::factory()->create([
            'role' => 'marketing',
            'name' => 'Marketing Maharani',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Rina Customer',
        ]);

        $toyota = Car::create([
            'kode_unit' => 'OFF-001',
            'merk' => 'Toyota',
            'tipe' => 'Raize',
            'tahun' => 2022,
            'harga' => 250000000,
            'kilometer' => 1200,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $honda = Car::create([
            'kode_unit' => 'OFF-002',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2021,
            'harga' => 180000000,
            'kilometer' => 2100,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        Offer::create([
            'user_id' => $customer->id,
            'car_id' => $toyota->id,
            'offer_price' => 230000000,
            'status' => 'accepted',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
            'notes' => 'Broker: diki',
        ]);

        Offer::create([
            'user_id' => $customer->id,
            'car_id' => $honda->id,
            'offer_price' => 170000000,
            'status' => 'pending',
            'handled_by' => $marketing->id,
            'handled_role' => 'marketing',
            'notes' => 'Follow up customer',
        ]);

        $this->actingAs($supervisor)
            ->get(route('supervisor.offers.index', ['q' => 'Toyota', 'status' => 'accepted']))
            ->assertOk()
            ->assertSee('Toyota Raize')
            ->assertSee('Supervisor')
            ->assertDontSee('Honda Brio')
            ->assertDontSee('Broker: diki');

        $this->actingAs($marketing)
            ->get(route('marketing.offers.index', ['q' => 'Honda', 'status' => 'pending']))
            ->assertOk()
            ->assertSee('Honda Brio')
            ->assertSee('Marketing')
            ->assertDontSee('Toyota Raize');
    }

    public function test_order_detail_shows_clear_photo_warning_when_unit_has_no_images(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $car = Car::create([
            'kode_unit' => 'ORD-001',
            'merk' => 'Toyota',
            'tipe' => 'Corolla Altis',
            'tahun' => 2018,
            'harga' => 315000000,
            'kilometer' => 900,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'completed',
            'total' => 315000000,
            'payment_method' => 'cash',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
        ]);

        $this->actingAs($supervisor)
            ->get(route('supervisor.orders.show', $order))
            ->assertOk()
            ->assertSee('Foto unit belum tersedia')
            ->assertSee('Tambah Foto Unit Sekarang');
    }
}
