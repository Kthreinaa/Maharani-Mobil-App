<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorOperationalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_manage_order_payment_offer_and_test_drive_with_role_tracking(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-ops@example.com',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-ops@example.com',
        ]);

        $car = Car::create([
            'kode_unit' => 'OPS-001',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2022,
            'harga' => 210000000,
            'kilometer' => 1000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 210000000,
            'payment_method' => null,
        ]);

        $offer = Offer::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'offer_price' => 200000000,
            'status' => 'pending',
        ]);

        $testDrive = TestDrive::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => '10:00:00',
            'status' => 'pending',
        ]);

        $this->actingAs($supervisor)
            ->patch(route('supervisor.orders.updateStatus', $order), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this->actingAs($supervisor)
            ->patch(route('supervisor.orders.syncPayment', $order), [
                'method' => 'credit',
                'amount' => 210000000,
            ])
            ->assertRedirect();

        $payment = Payment::firstOrFail();

        $this->actingAs($supervisor)
            ->patch(route('supervisor.offers.updateStatus', $offer), [
                'status' => 'accepted',
            ])
            ->assertRedirect();

        $this->actingAs($supervisor)
            ->patch(route('supervisor.testdrives.updateStatus', $testDrive), [
                'status' => 'approved',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
            'payment_method' => 'va',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'order_id' => $order->id,
            'method' => 'va',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
        ]);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'status' => 'accepted',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
        ]);

        $this->assertDatabaseHas('test_drives', [
            'id' => $testDrive->id,
            'status' => 'approved',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
        ]);
    }

    public function test_supervisor_can_update_account_settings(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-settings@example.com',
            'phone' => '0811111111',
        ]);

        $response = $this->actingAs($supervisor)->put(route('supervisor.settings.update'), [
            'name' => 'Supervisor Maharani',
            'email' => 'supervisor-settings@example.com',
            'phone' => '0822222222',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('supervisor.settings.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $supervisor->id,
            'name' => 'Supervisor Maharani',
            'phone' => '0822222222',
        ]);
    }
}
