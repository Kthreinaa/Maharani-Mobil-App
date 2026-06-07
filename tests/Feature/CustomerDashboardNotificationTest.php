<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_dashboard_shows_notification_center_with_actionable_updates(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-notification@example.com',
        ]);

        $car = Car::create([
            'kode_unit' => 'CUST-NOTIF-001',
            'merk' => 'Toyota',
            'tipe' => 'Rush',
            'tahun' => 2024,
            'harga' => 275000000,
            'kilometer' => 12000,
            'transmisi' => 'Automatic',
            'warna' => 'Hitam',
            'bahan_bakar' => 'Bensin',
            'status' => 'available',
            'deskripsi' => 'Unit untuk pengujian dashboard customer.',
            'photos' => [],
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 275000000,
            'payment_method' => 'transfer',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'transfer',
            'amount' => 275000000,
            'status' => 'pending',
        ]);

        TestDrive::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'booking_date' => now()->addDays(2)->toDateString(),
            'booking_time' => '10:00:00',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($customer)
            ->get(route('customer.home'));

        $response
            ->assertOk()
            ->assertSee('Notifikasi Customer')
            ->assertSee('Pesanan sedang diproses')
            ->assertSee('Update test drive');
    }
}
