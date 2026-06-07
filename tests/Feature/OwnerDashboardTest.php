<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_uses_selected_year_for_top_brand_data(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner-dashboard@example.com',
        ]);

        $this->seedCompletedOrder('customer-owner-2025-a@example.com', 'Toyota', 'Fortuner', '2025-03-10 10:00:00', 540000000);
        $this->seedCompletedOrder('customer-owner-2025-b@example.com', 'Toyota', 'Avanza', '2025-06-12 12:00:00', 280000000);
        $this->seedCompletedOrder('customer-owner-2026@example.com', 'Honda', 'Brio', '2026-02-15 09:00:00', 210000000);

        $response = $this
            ->actingAs($owner)
            ->get(route('owner.dashboard', ['year' => 2025]));

        $response->assertOk();
        $response->assertViewHas('selectedYear', 2025);
        $response->assertViewHas('topBrands', function ($topBrands) {
            return $topBrands->isNotEmpty()
                && $topBrands->first()['brand'] === 'Toyota'
                && (int) $topBrands->first()['units'] === 2;
        });
    }

    public function test_owner_dashboard_json_sync_returns_year_specific_payload(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner-dashboard-sync@example.com',
        ]);

        $this->seedCompletedOrder('customer-owner-sync-2024@example.com', 'Toyota', 'Rush', '2024-01-10 08:00:00', 310000000);
        $this->seedCompletedOrder('customer-owner-sync-2025@example.com', 'Mitsubishi', 'Pajero', '2025-04-14 11:30:00', 620000000);
        $this->seedCompletedOrder('customer-owner-sync-2025-b@example.com', 'Mitsubishi', 'Xpander', '2025-09-21 15:45:00', 330000000);

        $this->actingAs($owner)
            ->getJson(route('owner.dashboard', ['year' => 2025]))
            ->assertOk()
            ->assertJsonPath('selected_year', 2025)
            ->assertJsonPath('top_brands.items.0.brand', 'Mitsubishi')
            ->assertJsonCount(12, 'sales_overview_chart.labels')
            ->assertJsonCount(3, 'strategic_insights')
            ->assertJsonStructure([
                'selected_year',
                'strategic_insights',
                'dashboard_notifications' => [
                    'count',
                    'items',
                    'empty_text',
                ],
            ]);
    }

    public function test_owner_can_access_and_update_settings_page(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner-settings@example.com',
            'phone' => '0812345678',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.settings.edit'))
            ->assertOk()
            ->assertSee('Kelola Akun Owner');

        $response = $this->actingAs($owner)->put(route('owner.settings.update'), [
            'name' => 'Owner Maharani',
            'email' => 'owner-settings@example.com',
            'phone' => '0898888888',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('owner.settings.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'name' => 'Owner Maharani',
            'phone' => '0898888888',
        ]);
    }

    private function seedCompletedOrder(string $customerEmail, string $brand, string $type, string $createdAt, int $total): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => $customerEmail,
        ]);

        $car = Car::create([
            'kode_unit' => strtoupper(substr($brand, 0, 3)) . '-' . strtoupper(substr($type, 0, 3)) . '-' . substr($createdAt, 2, 2),
            'merk' => $brand,
            'tipe' => $type,
            'tahun' => (int) substr($createdAt, 0, 4),
            'harga' => $total,
            'kilometer' => 4000,
            'transmisi' => 'Automatic',
            'warna' => 'Hitam',
            'bahan_bakar' => 'Bensin',
            'status' => 'sold',
            'deskripsi' => 'Unit untuk pengujian dashboard owner.',
            'photos' => [],
        ]);

        $order = new Order([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'completed',
            'total' => $total,
            'payment_method' => 'transfer',
        ]);
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt;
        $order->save();
    }
}
