<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_access_dashboard_customer_and_settings_pages(): void
    {
        $marketing = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-workspace@example.com',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-marketing-workspace@example.com',
        ]);
        $trendDate = now()->setYear(2025)->setMonth(4)->setDay(12)->setTime(10, 0);

        $car = Car::create([
            'kode_unit' => 'MKT-ANALYSIS-001',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => 2025,
            'harga' => 210000000,
            'kilometer' => 1000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $offer = Offer::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'offer_price' => 200000000,
            'status' => 'pending',
            'handled_by' => $marketing->id,
            'handled_role' => 'marketing',
        ]);
        $offer->forceFill([
            'created_at' => $trendDate->copy(),
            'updated_at' => $trendDate->copy(),
        ])->save();

        Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'completed',
            'total' => 210000000,
            'payment_method' => 'cash',
            'handled_by' => $marketing->id,
            'handled_role' => 'marketing',
        ]);

        $this->actingAs($marketing)
            ->get(route('marketing.dashboard', ['signal_year' => 2025, 'signal_month' => 4]))
            ->assertOk()
            ->assertSee('Aktivitas Customer', false)
            ->assertDontSee('Import Excel')
            ->assertDontSee('Test Drive')
            ->assertDontSee('Buka Analisis Pemasaran')
            ->assertSee('Menampilkan rincian harian untuk');

        $this->actingAs($marketing)
            ->get(route('marketing.customers.index'))
            ->assertOk()
            ->assertSee($customer->name);

        $this->actingAs($marketing)
            ->get(route('marketing.customers.show', $customer))
            ->assertOk()
            ->assertSee($customer->email);

        $this->actingAs($marketing)
            ->get(route('marketing.settings.edit'))
            ->assertOk()
            ->assertSee('Kelola Akun Marketing');

        $this->actingAs($marketing)
            ->getJson(route('marketing.dashboard', ['panel' => 'signals', 'signal_year' => 2025, 'signal_month' => 4]))
            ->assertOk()
            ->assertJsonPath('chart.labels.0', '01')
            ->assertJsonPath('period_label', 'Menampilkan rincian harian untuk April 2025.');
    }

    public function test_marketing_can_update_own_settings(): void
    {
        $marketing = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-settings@example.com',
            'phone' => '08123456789',
        ]);

        $response = $this->actingAs($marketing)->put(route('marketing.settings.update'), [
            'name' => 'Marketing Baru',
            'email' => 'marketing-baru@example.com',
            'phone' => '08999999999',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response
            ->assertRedirect(route('marketing.settings.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $marketing->id,
            'name' => 'Marketing Baru',
            'email' => 'marketing-baru@example.com',
            'phone' => '08999999999',
        ]);
    }
}
