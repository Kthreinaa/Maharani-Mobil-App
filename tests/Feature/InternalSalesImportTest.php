<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class InternalSalesImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_import_sales_workbook_from_web_ui(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-import@example.com',
        ]);

        $response = $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload('supervisor-sales.xlsx'),
            ]);

        $response
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats');

        $this->assertDatabaseCount('cars', 1);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);

        $car = Car::firstOrFail();
        $order = Order::firstOrFail();
        $payment = Payment::firstOrFail();

        $this->assertSame($supervisor->id, $car->created_by);
        $this->assertSame('sold', $car->status);
        $this->assertSame($car->id, $order->car_id);
        $this->assertSame($supervisor->id, $payment->verified_by);
        $this->assertNotNull($order->import_reference);
        $this->assertSame('supervisor-sales.xlsx', $order->import_source);
        $this->assertSame(0, Offer::count());
        $this->assertSame(0, TestDrive::count());
    }

    public function test_marketing_cannot_access_internal_sales_import_workspace(): void
    {
        $marketing = User::factory()->create([
            'role' => 'marketing',
            'email' => 'marketing-import@example.com',
        ]);

        $this->actingAs($marketing)
            ->get(route('marketing.imports.sales.create'))
            ->assertForbidden();

        $this->actingAs($marketing)
            ->post(route('marketing.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload('marketing-sales.xlsx'),
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('cars', 0);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    public function test_supervisor_can_purge_all_imported_excel_data_without_touching_manual_data(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-purge@example.com',
        ]);

        $manualCustomer = User::factory()->create([
            'role' => 'customer',
            'email' => 'manual-customer@example.com',
        ]);

        Car::create([
            'kode_unit' => 'MANUAL-001',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2024,
            'harga' => 190000000,
            'kilometer' => 1500,
            'status' => 'available',
            'deskripsi' => 'Data manual showroom.',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload('purge-sales.xlsx'),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'));

        $this->assertDatabaseCount('cars', 2);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);

        $response = $this
            ->actingAs($supervisor)
            ->delete(route('supervisor.imports.sales.destroy'));

        $response
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('purge_stats');

        $this->assertDatabaseCount('cars', 1);
        $this->assertDatabaseMissing('cars', ['deskripsi' => 'Unit arsip hasil import penjualan 2025.']);
        $this->assertDatabaseHas('cars', ['kode_unit' => 'MANUAL-001']);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
        $this->assertDatabaseHas('users', ['email' => $manualCustomer->email]);
        $this->assertSame(0, User::where('email', 'like', 'customer-%@import.maharanimobil.local')->count());
    }

    public function test_supervisor_can_purge_imported_excel_data_for_a_specific_year_and_file(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-yearly-purge@example.com',
        ]);

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'sales-2021.xlsx',
                    ['date' => '2021-04-10', 'brand' => 'Toyota', 'model' => 'Avanza G', 'car_year' => 2021]
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'));

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'sales-2022.xlsx',
                    ['date' => '2022-06-18', 'brand' => 'Honda', 'model' => 'Brio RS', 'car_year' => 2022]
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'));

        $createResponse = $this
            ->actingAs($supervisor)
            ->get(route('supervisor.imports.sales.create'));

        $createResponse
            ->assertOk()
            ->assertSee('sales-2021.xlsx')
            ->assertSee('Tahun 2021')
            ->assertSee('sales-2022.xlsx')
            ->assertSee('Tahun 2022');

        $this->assertDatabaseCount('cars', 2);
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('payments', 2);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);

        $response = $this
            ->actingAs($supervisor)
            ->delete(route('supervisor.imports.sales.destroy'), [
                'import_year' => 2021,
                'import_source' => 'sales-2021.xlsx',
            ]);

        $response
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('purge_stats', function (array $stats) {
                return (int) ($stats['cars'] ?? 0) === 1
                    && (int) ($stats['orders'] ?? 0) === 1
                    && (int) ($stats['payments'] ?? 0) === 1;
            });

        $this->assertDatabaseCount('cars', 1);
        $this->assertDatabaseHas('cars', ['merk' => 'Honda']);
        $this->assertDatabaseMissing('cars', ['merk' => 'Toyota']);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', ['import_source' => 'sales-2022.xlsx']);
        $this->assertDatabaseMissing('orders', ['import_source' => 'sales-2021.xlsx']);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    public function test_import_repairs_short_year_dates_for_2021_and_2025_workbooks(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-short-year@example.com',
        ]);

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'Penjualan_2021_Maharani Mobil.xlsx',
                    ['date' => '1/2/202', 'brand' => 'Toyota', 'model' => 'Innova 2.0 G', 'car_year' => 2011]
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats');

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'Penjualan_2025_Maharani Mobil.xlsx',
                    ['date' => '8/20/202', 'brand' => 'Honda', 'model' => 'CR-V', 'car_year' => 2015]
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats');

        $order2021 = Order::where('import_source', 'Penjualan_2021_Maharani Mobil.xlsx')->firstOrFail();
        $order2025 = Order::where('import_source', 'Penjualan_2025_Maharani Mobil.xlsx')->firstOrFail();

        $this->assertSame('2021-01-02', $order2021->created_at->format('Y-m-d'));
        $this->assertSame('2025-08-20', $order2025->created_at->format('Y-m-d'));
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('payments', 2);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    public function test_import_skips_rows_with_dates_far_outside_the_workbook_year(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-invalid-date@example.com',
        ]);

        $response = $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'Penjualan_2021_Maharani Mobil.xlsx',
                    ['date' => '28', 'brand' => 'Honda', 'model' => 'Mobilio RS CVT', 'car_year' => 2019]
                ),
            ]);

        $response
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats', function (array $stats) {
                return (int) ($stats['orders_created'] ?? 0) === 0
                    && (int) ($stats['cars_created'] ?? 0) === 0
                    && (int) ($stats['skipped'] ?? 0) === 1;
            });

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('cars', 0);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    public function test_import_prefers_explicit_payment_method_column_from_workbook(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-payment-method@example.com',
        ]);

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUpload(
                    'sales-credit.xlsx',
                    ['payment_method' => 'Credit', 'process' => '']
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats');

        $order = Order::where('import_source', 'sales-credit.xlsx')->firstOrFail();
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        $this->assertSame('va', $order->payment_method);
        $this->assertSame('va', $payment->method);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    public function test_import_keeps_multiple_transactions_on_the_same_day_and_defaults_blank_method_to_cash(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'email' => 'supervisor-same-day@example.com',
        ]);

        $this
            ->actingAs($supervisor)
            ->post(route('supervisor.imports.sales.store'), [
                'sales_workbook' => $this->makeWorkbookUploadWithRows(
                    'sales-same-day.xlsx',
                    [
                        ['date' => '2025-12-28', 'brand' => 'Toyota', 'model' => 'Rush TRD', 'car_year' => 2015, 'payment_method' => ''],
                        ['date' => '2025-12-28', 'brand' => 'Honda', 'model' => 'Mobilio RS CVT', 'car_year' => 2018, 'payment_method' => 'Credit'],
                    ]
                ),
            ])
            ->assertRedirect(route('supervisor.imports.sales.create'))
            ->assertSessionHas('import_stats');

        $orders = Order::where('import_source', 'sales-same-day.xlsx')->orderBy('id')->get();

        $this->assertCount(2, $orders);
        $this->assertSame('2025-12-28', $orders[0]->created_at->format('Y-m-d'));
        $this->assertSame('2025-12-28', $orders[1]->created_at->format('Y-m-d'));
        $this->assertSame('cash', $orders[0]->payment_method);
        $this->assertSame('va', $orders[1]->payment_method);
        $this->assertSame('supervisor', $orders[0]->handled_role);
        $this->assertSame('supervisor', $orders[1]->handled_role);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('test_drives', 0);
    }

    /**
     * @param array{date?:string,brand?:string,model?:string,car_year?:int,payment_method?:string,process?:string} $overrides
     */
    private function makeWorkbookUpload(string $clientName, array $overrides = []): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'sales-xlsx-');
        if ($path === false) {
            $this->fail('Gagal membuat file sementara untuk workbook test.');
        }

        $xlsxPath = $path . '.xlsx';
        rename($path, $xlsxPath);

        $this->writeMinimalWorkbook($xlsxPath, $overrides);

        return new UploadedFile(
            $xlsxPath,
            $clientName,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    /**
     * @param array<int, array{date?:string,brand?:string,model?:string,car_year?:int,payment_method?:string,process?:string}> $rows
     */
    private function makeWorkbookUploadWithRows(string $clientName, array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'sales-xlsx-multi-');
        if ($path === false) {
            $this->fail('Gagal membuat file sementara untuk workbook multi-row test.');
        }

        $xlsxPath = $path . '.xlsx';
        rename($path, $xlsxPath);

        $this->writeWorkbookWithRows($xlsxPath, $rows);

        return new UploadedFile(
            $xlsxPath,
            $clientName,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    /**
     * @param array{date?:string,brand?:string,model?:string,car_year?:int,payment_method?:string,process?:string} $overrides
     */
    private function writeMinimalWorkbook(string $xlsxPath, array $overrides = []): void
    {
        $date = $overrides['date'] ?? '2025-02-14';
        $brand = $overrides['brand'] ?? 'Toyota';
        $model = $overrides['model'] ?? 'Avanza G';
        $carYear = $overrides['car_year'] ?? 2022;
        $paymentMethod = $overrides['payment_method'] ?? 'Cash';
        $process = $overrides['process'] ?? 'Cash';

        $zip = new ZipArchive();
        $opened = $zip->open($xlsxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            $this->fail('Gagal membuka file zip sementara untuk workbook test.');
        }

        $sharedStrings = [
            'No',
            'Tanggal Transaksi',
            'Merek',
            'Model Tipe',
            'Tahun Mobil',
            'Warna',
            'Transmisi',
            'Harga Jual',
            'Metode Pembayaran',
            'Proses',
            'Broker',
            'Leasing',
            '1',
            $date,
            $brand,
            $model,
            'Hitam',
            'AT',
            $paymentMethod,
            $process,
            'Broker A',
        ];

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>
XML);

        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);

        $zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Januari 2025" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML);

        $zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>
XML);

        $zip->addFromString('xl/sharedStrings.xml', $this->buildSharedStringsXml($sharedStrings));
        $zip->addFromString('xl/worksheets/sheet1.xml', <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
      <c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c>
      <c r="G1" t="s"><v>6</v></c>
      <c r="H1" t="s"><v>7</v></c>
      <c r="I1" t="s"><v>8</v></c>
      <c r="J1" t="s"><v>9</v></c>
      <c r="K1" t="s"><v>10</v></c>
      <c r="L1" t="s"><v>11</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>12</v></c>
      <c r="B2" t="s"><v>13</v></c>
      <c r="C2" t="s"><v>14</v></c>
      <c r="D2" t="s"><v>15</v></c>
      <c r="E2"><v>{$carYear}</v></c>
      <c r="F2" t="s"><v>16</v></c>
      <c r="G2" t="s"><v>17</v></c>
      <c r="H2"><v>275000000</v></c>
      <c r="I2" t="s"><v>18</v></c>
      <c r="J2" t="s"><v>19</v></c>
      <c r="K2" t="s"><v>20</v></c>
    </row>
  </sheetData>
</worksheet>
XML);

        $zip->close();
    }

    /**
     * @param array<int, array{date?:string,brand?:string,model?:string,car_year?:int,payment_method?:string,process?:string}> $rows
     */
    private function writeWorkbookWithRows(string $xlsxPath, array $rows): void
    {
        $zip = new ZipArchive();
        $opened = $zip->open($xlsxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            $this->fail('Gagal membuka file zip sementara untuk workbook multi-row test.');
        }

        $sharedStrings = [
            'No',
            'Tanggal Transaksi',
            'Merek',
            'Model Tipe',
            'Tahun Mobil',
            'Warna',
            'Transmisi',
            'Harga Jual',
            'Metode Pembayaran',
            'Proses',
            'Broker',
            'Leasing',
            'Hitam',
            'AT',
            'Broker A',
        ];

        $rowsXml = '';
        $rowNumber = 2;
        foreach ($rows as $row) {
            $date = $row['date'] ?? '2025-12-28';
            $brand = $row['brand'] ?? 'Toyota';
            $model = $row['model'] ?? 'Avanza G';
            $carYear = $row['car_year'] ?? 2022;
            $paymentMethod = $row['payment_method'] ?? '';
            $process = $row['process'] ?? '';

            $baseIndex = count($sharedStrings);
            array_push($sharedStrings, $date, $brand, $model, $paymentMethod, $process);
            $dateIndex = $baseIndex;
            $brandIndex = $baseIndex + 1;
            $modelIndex = $baseIndex + 2;
            $paymentMethodIndex = $baseIndex + 3;
            $processIndex = $baseIndex + 4;

            $rowsXml .= <<<XML
    <row r="{$rowNumber}">
      <c r="A{$rowNumber}"><v>{$rowNumber}</v></c>
      <c r="B{$rowNumber}" t="s"><v>{$dateIndex}</v></c>
      <c r="C{$rowNumber}" t="s"><v>{$brandIndex}</v></c>
      <c r="D{$rowNumber}" t="s"><v>{$modelIndex}</v></c>
      <c r="E{$rowNumber}"><v>{$carYear}</v></c>
      <c r="F{$rowNumber}" t="s"><v>12</v></c>
      <c r="G{$rowNumber}" t="s"><v>13</v></c>
      <c r="H{$rowNumber}"><v>275000000</v></c>
      <c r="I{$rowNumber}" t="s"><v>{$paymentMethodIndex}</v></c>
      <c r="J{$rowNumber}" t="s"><v>{$processIndex}</v></c>
      <c r="K{$rowNumber}" t="s"><v>14</v></c>
    </row>
XML;
            $rowNumber++;
        }

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>
XML);

        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);

        $zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Desember 2025" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML);

        $zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>
XML);

        $zip->addFromString('xl/sharedStrings.xml', $this->buildSharedStringsXml($sharedStrings));
        $zip->addFromString('xl/worksheets/sheet1.xml', <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
      <c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c>
      <c r="G1" t="s"><v>6</v></c>
      <c r="H1" t="s"><v>7</v></c>
      <c r="I1" t="s"><v>8</v></c>
      <c r="J1" t="s"><v>9</v></c>
      <c r="K1" t="s"><v>10</v></c>
      <c r="L1" t="s"><v>11</v></c>
    </row>
{$rowsXml}
  </sheetData>
</worksheet>
XML);

        $zip->close();
    }

    /**
     * @param array<int, string> $strings
     */
    private function buildSharedStringsXml(array $strings): string
    {
        $items = '';

        foreach ($strings as $string) {
            $items .= '<si><t>' . htmlspecialchars($string, ENT_XML1) . '</t></si>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">'
            . $items
            . '</sst>';
    }
}
