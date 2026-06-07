<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\User;
use App\Support\SalesReportBuilder;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_export_sales_report_to_excel(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-export@example.com',
        ]);

        $this->seedReportOrder('customer-supervisor@example.com', 'supervisor');

        $response = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.reports.exportExcel'));

        $response->assertOk();
        $response->assertDownload('sales-report.xlsx');
    }

    public function test_owner_can_export_sales_report_to_excel(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner-export@example.com',
        ]);

        $this->seedReportOrder('customer-owner@example.com', 'owner');

        $response = $this
            ->actingAs($owner)
            ->get(route('owner.reports.exportExcel'));

        $response->assertOk();
        $response->assertDownload('owner-sales-report.xlsx');
    }

    public function test_supervisor_report_page_can_filter_transactions_by_year(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-report-year@example.com',
        ]);

        $this->seedReportOrder('customer-2025@example.com', 'year2025', '2025-06-15 09:30:00', 225000000);
        $this->seedReportOrder('customer-2024@example.com', 'year2024', '2024-03-10 11:00:00', 180000000);

        $response = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.reports.index', [
                'period' => 'yearly',
                'year' => 2025,
            ]));

        $response
            ->assertOk()
            ->assertSee('Laporan tahun 2025')
            ->assertSee('YEAR2025-EXPORT-001')
            ->assertDontSee('YEAR2024-EXPORT-001');
    }

    public function test_sales_report_builder_respects_report_range_year_alias_for_export_context(): void
    {
        $this->seedReportOrder('customer-alias-2025@example.com', 'alias2025', '2025-06-15 09:30:00', 225000000);
        $this->seedReportOrder('customer-alias-2026@example.com', 'alias2026', '2026-02-10 10:00:00', 280000000);

        $report = SalesReportBuilder::build(Request::create('/supervisor/reports/pdf', 'GET', [
            'report_range' => 'yearly',
            'year' => 2025,
        ]));

        $this->assertSame('yearly', $report['period']);
        $this->assertSame(2025, $report['selected_year']);
        $this->assertSame('Laporan tahun 2025', $report['range_label']);
        $this->assertTrue($report['rows']->pluck('kode_unit')->contains('ALIAS2025-EXPORT-001'));
        $this->assertFalse($report['rows']->pluck('kode_unit')->contains('ALIAS2026-EXPORT-001'));
    }

    public function test_pdf_sales_view_includes_brand_and_purchase_activity_labels_for_printed_report(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer-pdf-brand@example.com',
        ]);

        $handler = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-pdf-brand@example.com',
            'name' => 'Marketing Maharani',
        ]);

        $car = Car::create([
            'kode_unit' => 'PDF-BRAND-001',
            'merk' => 'Toyota',
            'tipe' => 'Fortuner',
            'tahun' => 2025,
            'harga' => 350000000,
            'kilometer' => 2000,
            'transmisi' => 'Automatic',
            'warna' => 'Hitam',
            'bahan_bakar' => 'Diesel',
            'status' => 'sold',
            'deskripsi' => 'Unit untuk pengujian tampilan PDF.',
            'photos' => [],
        ]);

        $order = new Order([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'completed',
            'total' => 350000000,
            'payment_method' => 'transfer',
            'handled_role' => 'marketing',
            'handled_by' => $handler->id,
        ]);
        $order->created_at = '2025-08-11 10:30:00';
        $order->updated_at = '2025-08-11 10:30:00';
        $order->save();

        $report = SalesReportBuilder::build(Request::create('/supervisor/reports/pdf', 'GET', [
            'period' => 'yearly',
            'year' => 2025,
        ]));

        $html = View::make('reports.pdf.sales', compact('report'))->render();

        $this->assertStringContainsString('Merk Mobil Paling Laris', $html);
        $this->assertStringContainsString('Toyota', $html);
        $this->assertStringContainsString('Ringkasan Aktivitas Pembelian', $html);
        $this->assertStringContainsString('Metode Cash', $html);
        $this->assertStringContainsString('Transaksi Input Supervisor', $html);
    }

    private function seedReportOrder(string $customerEmail, string $prefix, string $createdAt = '2025-06-15 09:30:00', int $total = 225000000): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email' => $customerEmail,
        ]);

        $car = Car::create([
            'kode_unit' => strtoupper($prefix) . '-EXPORT-001',
            'merk' => 'Toyota',
            'tipe' => 'Avanza',
            'tahun' => (int) substr($createdAt, 0, 4),
            'harga' => $total,
            'kilometer' => 5000,
            'transmisi' => 'Automatic',
            'warna' => 'Hitam',
            'bahan_bakar' => 'Bensin',
            'status' => 'sold',
            'deskripsi' => 'Unit untuk pengujian export laporan.',
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
