<?php

namespace App\Support;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MaharaniSalesImporter
{
    private const IMPORT_ARCHIVE_DESCRIPTION = 'Unit arsip hasil import penjualan Excel.';
    private const IMPORT_ARCHIVE_DESCRIPTION_LEGACY = 'Unit arsip hasil import penjualan 2025.';
    private const IMPORT_REFERENCE_PREFIX = 'xlsx-import-';
    private const IMPORT_REFERENCE_PREFIX_LEGACY = 'xlsx2025-';
    private const IMPORT_CUSTOMER_EMAIL_PREFIX = 'customer-import-';
    private const IMPORT_CUSTOMER_EMAIL_PREFIX_LEGACY = 'customer-2025-';
    private const IMPORT_CUSTOMER_EMAIL_DOMAIN = '@import.maharanimobil.local';

    public static function importSalesWorkbook(string $xlsxPath, int $internalUserId, bool $dryRun = false, ?string $importSourceName = null): array
    {
        if (!is_file($xlsxPath)) {
            throw new RuntimeException("File tidak ditemukan: {$xlsxPath}");
        }

        $sheets = XlsxReader::sheets($xlsxPath);
        if ($sheets === []) {
            throw new RuntimeException('Sheet Excel tidak ditemukan.');
        }

        $stats = [
            'cars_created' => 0,
            'customers_created' => 0,
            'orders_created' => 0,
            'payments_created' => 0,
            'offers_created' => 0,
            'test_drives_created' => 0,
            'skipped' => 0,
            'skipped_fingerprint_duplicates' => 0,
        ];

        $sourceName = $importSourceName ?: basename($xlsxPath);
        $fileHash = self::computeWorkbookHash($xlsxPath);
        self::guardAgainstDuplicateWorkbook($sourceName, $fileHash);
        $workbookYearHint = self::inferWorkbookYear($sourceName, $xlsxPath);

        $runner = function () use ($xlsxPath, $internalUserId, $sheets, $sourceName, $fileHash, $workbookYearHint, &$stats) {
            self::backfillMissingBmFromExistingData();

            foreach ($sheets as $sheet) {
                $sheetName = $sheet['name'];
                $rows = XlsxReader::readSheet($xlsxPath, $sheet['path']);

                foreach ($rows as $rowIndex => $row) {
                    $reference = self::buildReference($sheetName, (int) $rowIndex, $row);
                    if (Order::where('import_reference', $reference)->exists()) {
                        $stats['skipped']++;
                        continue;
                    }

                    $brand = trim((string) ($row['merek'] ?? ''));
                    $modelType = trim((string) ($row['model_tipe'] ?? ''));
                    $year = (int) self::parseNumber($row['tahun_mobil'] ?? ($row['tahun mobil'] ?? ''));
                    $price = self::parseMoney($row['harga_jual'] ?? ($row['harga jual'] ?? ''));

                    $date = WorkbookSheetDateResolver::alignToSheetMonth(
                        self::parseExcelDate($row['tanggal_transaksi'] ?? '', $workbookYearHint),
                        $sheetName
                    );
                    $color = trim((string) ($row['warna'] ?? '')) ?: null;
                    $transmission = trim((string) ($row['transmisi'] ?? '')) ?: null;

                    if (!$date || $brand === '' || $modelType === '' || $price <= 0) {
                        $stats['skipped']++;
                        continue;
                    }

                    $effectiveYear = $year > 1900 ? $year : (int) $date->year;
                    $bm = self::extractBm($row);
                    if ($bm !== null && Car::where('bm', $bm)->exists()) {
                        $stats['skipped']++;
                        continue;
                    }

                    if ($bm === null && self::hasImportedArchiveFingerprintDuplicate($brand, $modelType, $effectiveYear, $price)) {
                        $stats['skipped']++;
                        $stats['skipped_fingerprint_duplicates']++;
                        continue;
                    }

                    $paymentMethod = self::classifyPaymentMethod($row);
                    $broker = trim((string) ($row['broker'] ?? ''));
                    $process = trim((string) ($row['proses'] ?? ''));
                    $leasing = trim((string) ($row['leasing'] ?? ($row['Leasing'] ?? '')));

                    $customer = self::resolveCustomer($sheetName, $rowIndex, $date);
                    if ($customer->wasRecentlyCreated) {
                        $stats['customers_created']++;
                    }

                    $kodeUnit = 'MM25-' . strtoupper(substr(sha1($reference), 0, 10));
                    $carCreatedAt = $date->copy()->subDays(self::stableRand($reference, 18, 75));

                    $car = Car::create([
                        'kode_unit' => $kodeUnit,
                        'bm' => $bm,
                        'merk' => $brand,
                        'tipe' => $modelType,
                        'tahun' => $effectiveYear,
                        'harga' => $price,
                        'kilometer' => null,
                        'transmisi' => $transmission,
                        'warna' => $color,
                        'bahan_bakar' => null,
                        'status' => 'sold',
                        'deskripsi' => self::IMPORT_ARCHIVE_DESCRIPTION,
                        'photos' => [],
                        'created_by' => $internalUserId,
                        'created_at' => $carCreatedAt,
                        'updated_at' => $carCreatedAt,
                    ]);
                    $stats['cars_created']++;

                    $notes = self::buildOrderNotes($row, $broker, $process, $leasing);

                    $order = new Order([
                        'user_id' => $customer->id,
                        'car_id' => $car->id,
                        'status' => 'completed',
                        'total' => $price,
                        'payment_method' => $paymentMethod,
                        'transaction_channel' => 'offline',
                        'sales_flow' => 'offline_showroom',
                        'notes' => $notes,
                        'follow_up_status' => 'closed_won',
                        'import_source' => $sourceName,
                        'import_reference' => $reference,
                        'import_file_hash' => $fileHash,
                        'handled_by' => $internalUserId,
                        'handled_role' => 'supervisor',
                        'handled_at' => $date,
                        'approved_by' => $internalUserId,
                        'approved_at' => $date,
                    ]);
                    $order->created_at = $date;
                    $order->updated_at = $date;
                    $order->save();
                    $stats['orders_created']++;

                    $payment = new Payment([
                        'order_id' => $order->id,
                        'method' => $paymentMethod,
                        'amount' => $price,
                        'proof_file' => null,
                        'status' => 'verified',
                        'verified_by' => $internalUserId,
                        'verified_at' => $date,
                        'handled_by' => $internalUserId,
                        'handled_role' => 'supervisor',
                        'handled_at' => $date,
                    ]);
                    $payment->created_at = $date;
                    $payment->updated_at = $date;
                    $payment->save();
                    $stats['payments_created']++;
                }
            }

            HistoricalUnitCodeSynchronizer::sync();
            HistoricalOrderCodeSynchronizer::sync();
        };

        if ($dryRun) {
            DB::transaction(function () use ($runner) {
                $runner();
                DB::rollBack();
            });
            return $stats + ['dry_run' => true];
        }

        DB::transaction(function () use ($runner) {
            $runner();
        });

        return $stats + ['dry_run' => false];
    }

    private static function computeWorkbookHash(string $xlsxPath): string
    {
        $hash = hash_file('sha256', $xlsxPath);

        if (!is_string($hash) || $hash === '') {
            throw new RuntimeException('File Excel gagal dibaca untuk proses validasi import.');
        }

        return $hash;
    }

    private static function guardAgainstDuplicateWorkbook(string $sourceName, string $fileHash): void
    {
        $normalizedSourceName = trim($sourceName);
        if ($normalizedSourceName !== '' && self::importedOrdersQuery()->where('import_source', $normalizedSourceName)->exists()) {
            throw new RuntimeException("Import tidak dapat diproses karena nama file {$normalizedSourceName} sudah pernah digunakan pada data import sebelumnya.");
        }

        if (self::importedOrdersQuery()->where('import_file_hash', $fileHash)->exists()) {
            throw new RuntimeException('Import tidak dapat diproses karena isi file Excel yang diunggah terdeteksi sama dengan data import yang sudah tersimpan sebelumnya.');
        }
    }

    public static function bmCoverageSnapshot(): array
    {
        return [
            'total_units' => Car::count(),
            'units_with_bm' => Car::whereNotNull('bm')->count(),
            'units_without_bm' => Car::whereNull('bm')->count(),
            'imported_units_without_bm' => self::importedCarsQuery()->whereNull('bm')->count(),
        ];
    }

    public static function backfillMissingBmFromExistingData(): array
    {
        $stats = [
            'checked' => 0,
            'updated' => 0,
            'conflicts' => 0,
            'unresolved' => 0,
        ];

        Car::query()
            ->whereNull('bm')
            ->with(['orders:id,car_id,notes'])
            ->orderBy('id')
            ->chunkById(100, function ($cars) use (&$stats) {
                foreach ($cars as $car) {
                    $stats['checked']++;
                    $bm = null;

                    foreach (self::backfillBmSources($car) as $source) {
                        $bm = self::extractBmFromText($source);
                        if ($bm !== null) {
                            break;
                        }
                    }

                    if ($bm === null) {
                        $stats['unresolved']++;
                        continue;
                    }

                    $hasConflict = Car::query()
                        ->where('bm', $bm)
                        ->where('id', '!=', $car->id)
                        ->exists();

                    if ($hasConflict) {
                        $stats['conflicts']++;
                        continue;
                    }

                    $car->update(['bm' => $bm]);
                    $stats['updated']++;
                }
            });

        return $stats;
    }

    private static function extractBm(array $row): ?string
    {
        foreach ([
            'bm',
            'plat',
            'plat_nomor',
            'plat_no',
            'nopol',
            'no_polisi',
            'nomor_polisi',
            'nomor_plat',
        ] as $key) {
            $value = Car::normalizeBm($row[$key] ?? null);
            if ($value !== null) {
                return $value;
            }
        }

        foreach (self::prioritizedBmTextSources($row) as $text) {
            $value = self::extractBmFromText($text);
            if ($value !== null) {
                return $value;
            }
        }

        foreach ($row as $value) {
            $candidate = self::extractBmFromText((string) $value);
            if ($candidate !== null) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private static function backfillBmSources(Car $car): array
    {
        $sources = [];

        if (filled($car->deskripsi)) {
            $sources[] = (string) $car->deskripsi;
        }

        foreach ($car->orders as $order) {
            if (filled($order->notes)) {
                $sources[] = (string) $order->notes;
            }
        }

        return $sources;
    }

    /**
     * @return array<int, string>
     */
    private static function prioritizedBmTextSources(array $row): array
    {
        $sources = [];

        foreach ([
            'keterangan',
            'catatan',
            'deskripsi',
            'notes',
            'note',
            'remarks',
            'remark',
            'detail',
            'informasi_tambahan',
        ] as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            if ($value !== '') {
                $sources[] = $value;
            }
        }

        return $sources;
    }

    private static function extractBmFromText(string $text): ?string
    {
        $text = strtoupper(trim($text));
        if ($text === '') {
            return null;
        }

        $keywordPatterns = [
            '/(?:PLAT|NOPOL|NO\.?\s*POL(?:ISI)?|NOMOR\s*POLISI|NO\.?\s*POLISI)\s*[:\-]?\s*([A-Z]{1,2}\s*\d{1,4}\s*[A-Z]{0,3})\b/u',
            '/\bBM\s*[:\-]?\s*([A-Z]{1,2}\s*\d{1,4}\s*[A-Z]{0,3})\b/u',
        ];

        foreach ($keywordPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches) === 1) {
                return Car::normalizeBm($matches[1] ?? null);
            }
        }

        if (preg_match('/\b([A-Z]{1,2}\s*\d{1,4}\s*[A-Z]{0,3})\b/u', $text, $matches) === 1) {
            return Car::normalizeBm($matches[1] ?? null);
        }

        return null;
    }

    private static function hasImportedArchiveFingerprintDuplicate(string $brand, string $modelType, int $year, float $price): bool
    {
        return self::importedCarsQuery()
            ->whereRaw('LOWER(merk) = ?', [self::normalizeFingerprintText($brand)])
            ->whereRaw('LOWER(tipe) = ?', [self::normalizeFingerprintText($modelType)])
            ->where('tahun', $year)
            ->where('harga', $price)
            ->exists();
    }

    private static function normalizeFingerprintText(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value) ?: '';

        return $value;
    }

    public static function importedDataSnapshot(): array
    {
        $importedCarIds = self::importedCarsQuery()->pluck('id');
        $importedOrderIds = self::importedOrdersQuery()->pluck('id');

        return [
            'cars' => $importedCarIds->count(),
            'orders' => $importedOrderIds->count(),
            'payments' => Payment::whereIn('order_id', $importedOrderIds)->count(),
            'offers' => Offer::whereIn('car_id', $importedCarIds)->count(),
            'test_drives' => TestDrive::whereIn('car_id', $importedCarIds)->count(),
            'customers' => self::importedCustomersQuery()->count(),
        ];
    }

    /**
     * @return array<int, array{
     *     source_name:string,
     *     year:int,
     *     imported_at:?Carbon,
     *     cars:int,
     *     orders:int,
     *     payments:int,
     *     offers:int,
     *     test_drives:int,
     *     customers:int
     * }>
     */
    public static function importHistory(): array
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER)"
            : 'YEAR(created_at)';

        $groups = self::importedOrdersQuery()
            ->selectRaw("import_source, {$yearExpr} as import_year, COUNT(*) as total_orders, MIN(created_at) as imported_at")
            ->groupBy('import_source', 'import_year')
            ->orderByDesc('import_year')
            ->orderByDesc('imported_at')
            ->get();

        return $groups->map(function ($group) {
            $sourceName = (string) ($group->import_source ?: 'Workbook tanpa nama');
            $year = (int) $group->import_year;

            $carIds = self::importedOrdersQuery()
                ->where('import_source', $sourceName)
                ->whereYear('created_at', $year)
                ->pluck('car_id')
                ->filter()
                ->unique()
                ->values();

            $orderIds = self::importedOrdersQuery()
                ->where('import_source', $sourceName)
                ->whereYear('created_at', $year)
                ->pluck('id');

            $customerIds = self::importedOrdersQuery()
                ->where('import_source', $sourceName)
                ->whereYear('created_at', $year)
                ->pluck('user_id')
                ->filter()
                ->unique()
                ->values();

            return [
                'source_name' => $sourceName,
                'year' => $year,
                'imported_at' => $group->imported_at ? Carbon::parse($group->imported_at) : null,
                'cars' => $carIds->count(),
                'orders' => (int) $group->total_orders,
                'payments' => Payment::whereIn('order_id', $orderIds)->count(),
                'offers' => Offer::whereIn('car_id', $carIds)->count(),
                'test_drives' => TestDrive::whereIn('car_id', $carIds)->count(),
                'customers' => self::importedCustomersQuery()->whereIn('id', $customerIds)->count(),
            ];
        })->all();
    }

    public static function purgeImportedSalesData(?int $year = null, ?string $sourceName = null): array
    {
        $filters = self::normalizePurgeFilters($year, $sourceName);
        $snapshot = self::importedDataSnapshotForScope($filters['year'], $filters['source_name']);

        DB::transaction(function () use ($filters) {
            $importedCarIds = self::importedCarsQueryForScope($filters['year'], $filters['source_name'])->pluck('id');

            if ($importedCarIds->isNotEmpty()) {
                Offer::whereIn('car_id', $importedCarIds)->delete();
                TestDrive::whereIn('car_id', $importedCarIds)->delete();
                Car::whereIn('id', $importedCarIds)->delete();
            }

            self::importedCustomersQuery()
                ->where('role', 'customer')
                ->doesntHave('orders')
                ->doesntHave('offers')
                ->doesntHave('testDrives')
                ->doesntHave('productReviews')
                ->delete();
        });

        return $snapshot;
    }

    public static function importedDataSnapshotForScope(?int $year = null, ?string $sourceName = null): array
    {
        $filters = self::normalizePurgeFilters($year, $sourceName);
        $importedCarIds = self::importedCarsQueryForScope($filters['year'], $filters['source_name'])->pluck('id');
        $importedOrderIds = self::importedOrdersQueryForScope($filters['year'], $filters['source_name'])->pluck('id');
        $importedCustomerIds = self::importedOrdersQueryForScope($filters['year'], $filters['source_name'])
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values();

        return [
            'cars' => $importedCarIds->count(),
            'orders' => $importedOrderIds->count(),
            'payments' => Payment::whereIn('order_id', $importedOrderIds)->count(),
            'offers' => Offer::whereIn('car_id', $importedCarIds)->count(),
            'test_drives' => TestDrive::whereIn('car_id', $importedCarIds)->count(),
            'customers' => self::importedCustomersQuery()->whereIn('id', $importedCustomerIds)->count(),
        ];
    }

    private static function resolveCustomer(string $sheetName, int $rowIndex, Carbon $date): User
    {
        $seed = sha1($sheetName . '|' . $rowIndex . '|' . $date->format('Y-m-d'));
        $email = self::IMPORT_CUSTOMER_EMAIL_PREFIX . substr($seed, 0, 10) . self::IMPORT_CUSTOMER_EMAIL_DOMAIN;
        $name = 'Customer ' . strtoupper(substr($sheetName, 0, 3)) . ' ' . str_pad((string) ($rowIndex + 1), 3, '0', STR_PAD_LEFT);

        return User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'role' => 'customer']
        );
    }

    private static function buildReference(string $sheetName, int $rowIndex, array $row): string
    {
        $parts = [
            'xlsx-import',
            Str::slug($sheetName),
            (string) ($row['no'] ?? ($rowIndex + 1)),
            (string) ($row['tanggal_transaksi'] ?? ''),
            (string) ($row['merek'] ?? ''),
            (string) ($row['model_tipe'] ?? ''),
            (string) ($row['tahun_mobil'] ?? ($row['tahun mobil'] ?? '')),
            (string) ($row['harga_jual'] ?? ($row['harga jual'] ?? '')),
        ];

        return self::IMPORT_REFERENCE_PREFIX . sha1(implode('|', $parts));
    }

    private static function parseExcelDate(string $value, ?int $yearHint = null): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            // Excel serial date: day 0 = 1899-12-30.
            $date = Carbon::create(1899, 12, 30)->addDays((int) $value)->startOfDay()->addHours(self::stableRand($value, 9, 16));

            return self::dateMatchesWorkbookYear($date, $yearHint) ? $date : null;
        }

        if ($yearHint !== null) {
            $repaired = self::repairShortYearDate($value, $yearHint);
            if ($repaired !== null) {
                return self::dateMatchesWorkbookYear($repaired, $yearHint) ? $repaired : null;
            }
        }

        try {
            $date = Carbon::parse($value);

            return self::dateMatchesWorkbookYear($date, $yearHint) ? $date : null;
        } catch (\Throwable $e) {
            // Non-date values can appear if a sheet contains a title row or repeated headings.
            return null;
        }
    }

    private static function repairShortYearDate(string $value, int $yearHint): ?Carbon
    {
        if (!preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{3})$/', $value, $matches)) {
            return null;
        }

        $month = (int) $matches[1];
        $day = (int) $matches[2];
        $shortYear = $matches[3];
        $yearHintString = (string) $yearHint;

        if (!str_starts_with($yearHintString, $shortYear) || strlen($yearHintString) !== 4) {
            return null;
        }

        try {
            return Carbon::createFromDate($yearHint, $month, $day)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function inferWorkbookYear(string $sourceName, string $xlsxPath): ?int
    {
        $candidates = [
            $sourceName,
            basename($xlsxPath),
            $xlsxPath,
        ];

        foreach ($candidates as $candidate) {
            if (preg_match('/(?:19|20)\d{2}/', $candidate, $matches) === 1) {
                $year = (int) $matches[0];
                if ($year >= 1900 && $year <= 2100) {
                    return $year;
                }
            }
        }

        return null;
    }

    private static function dateMatchesWorkbookYear(Carbon $date, ?int $yearHint): bool
    {
        if ($yearHint === null) {
            return true;
        }

        return abs($date->year - $yearHint) <= 1;
    }

    private static function parseMoney(string $value): float
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $raw = strtolower($value);
        $raw = str_replace(['rp', 'idr', ' '], '', $raw);
        $raw = preg_replace('/[^0-9,.\-]/', '', $raw) ?: '0';
        $lastComma = strrpos($raw, ',');
        $lastDot = strrpos($raw, '.');

        if ($lastComma !== false && $lastDot !== false) {
            $raw = $lastComma > $lastDot
                ? str_replace(',', '.', str_replace('.', '', $raw))
                : str_replace(',', '', $raw);
        } elseif ($lastComma !== false) {
            $raw = preg_match('/,\d{1,2}$/', $raw) ? str_replace(',', '.', $raw) : str_replace(',', '', $raw);
        } elseif ($lastDot !== false && !preg_match('/\.\d{1,2}$/', $raw)) {
            $raw = str_replace('.', '', $raw);
        }

        return (float) $raw;
    }

    private static function parseNumber(string $value): float
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $raw = preg_replace('/[^0-9.\-]/', '', $value) ?: '0';
        return (float) $raw;
    }

    private static function classifyPaymentMethod(array $row): string
    {
        $explicitMethod = self::normalizePaymentMethodValue(
            $row['metode_pembayaran']
                ?? $row['metode_bayar']
                ?? $row['cara_bayar']
                ?? $row['payment_method']
                ?? $row['metode']
                ?? ''
        );

        if ($explicitMethod !== null) {
            return $explicitMethod;
        }

        $process = strtolower(trim((string) ($row['proses'] ?? '')));
        $leasing = strtolower(trim((string) ($row['leasing'] ?? ($row['Leasing'] ?? ''))));
        $dp = trim((string) ($row['dp'] ?? ''));
        $tenor = trim((string) ($row['tenor_(bulan)'] ?? ($row['tenor'] ?? '')));

        if (str_contains($process, 'cash')) {
            return 'cash';
        }

        if (str_contains($process, 'credit') || str_contains($process, 'kredit') || $leasing !== '' || $dp !== '' || $tenor !== '') {
            return 'va';
        }

        return 'cash';
    }

    private static function normalizePaymentMethodValue(string $value): ?string
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, 'cash') || str_contains($normalized, 'tunai')) {
            return 'cash';
        }

        if (str_contains($normalized, 'credit') || str_contains($normalized, 'kredit')) {
            return 'va';
        }

        if (str_contains($normalized, 'transfer')) {
            return 'transfer';
        }

        if (str_contains($normalized, 'virtual account') || $normalized === 'va') {
            return 'va';
        }

        return null;
    }

    private static function buildOrderNotes(array $row, string $broker, string $process, string $leasing): ?string
    {
        $lines = [];
        if ($broker !== '') $lines[] = "Broker: {$broker}";
        if ($process !== '') $lines[] = "Proses: {$process}";
        if ($leasing !== '') $lines[] = "Leasing: {$leasing}";

        $dp = trim((string) ($row['dp'] ?? ''));
        $tenor = trim((string) ($row['tenor_(bulan)'] ?? ''));
        $installment = trim((string) ($row['angsuran/bulan'] ?? ''));
        $approval = trim((string) ($row['aproval_kredit'] ?? ''));
        $survey = trim((string) ($row['status_survey'] ?? ''));

        if ($dp !== '') $lines[] = "DP: {$dp}";
        if ($tenor !== '') $lines[] = "Tenor: {$tenor}";
        if ($installment !== '') $lines[] = "Angsuran/bulan: {$installment}";
        if ($approval !== '') $lines[] = "Approval kredit: {$approval}";
        if ($survey !== '') $lines[] = "Status survey: {$survey}";

        $percent = trim((string) ($row['persen_leasing'] ?? ''));
        if ($percent !== '') $lines[] = "Persen leasing: {$percent}";

        return $lines ? implode("\n", $lines) : null;
    }

    private static function stableRand(string $seed, int $min, int $max): int
    {
        $hash = hexdec(substr(sha1($seed), 0, 8));
        $range = max(1, $max - $min + 1);
        return $min + ($hash % $range);
    }

    private static function importedCarsQuery()
    {
        return Car::query()->whereIn('deskripsi', [
            self::IMPORT_ARCHIVE_DESCRIPTION,
            self::IMPORT_ARCHIVE_DESCRIPTION_LEGACY,
        ]);
    }

    private static function importedCarsQueryForScope(?int $year = null, ?string $sourceName = null)
    {
        $carIds = self::importedOrdersQueryForScope($year, $sourceName)
            ->pluck('car_id')
            ->filter()
            ->unique()
            ->values();

        return self::importedCarsQuery()->whereIn('id', $carIds);
    }

    private static function importedOrdersQuery()
    {
        return Order::query()
            ->whereNotNull('import_reference')
            ->where(function ($query) {
                $query
                    ->where('import_reference', 'like', self::IMPORT_REFERENCE_PREFIX . '%')
                    ->orWhere('import_reference', 'like', self::IMPORT_REFERENCE_PREFIX_LEGACY . '%');
            });
    }

    private static function importedOrdersQueryForScope(?int $year = null, ?string $sourceName = null)
    {
        $query = self::importedOrdersQuery();

        if ($year !== null) {
            $query->whereYear('created_at', $year);
        }

        if ($sourceName !== null && $sourceName !== '') {
            $query->where('import_source', $sourceName);
        }

        return $query;
    }

    private static function importedCustomersQuery()
    {
        return User::query()
            ->where(function ($query) {
                $query
                    ->where('email', 'like', self::IMPORT_CUSTOMER_EMAIL_PREFIX . '%' . self::IMPORT_CUSTOMER_EMAIL_DOMAIN)
                    ->orWhere('email', 'like', self::IMPORT_CUSTOMER_EMAIL_PREFIX_LEGACY . '%' . self::IMPORT_CUSTOMER_EMAIL_DOMAIN);
            });
    }

    /**
     * @return array{year:?int,source_name:?string}
     */
    private static function normalizePurgeFilters(?int $year, ?string $sourceName): array
    {
        return [
            'year' => $year && $year > 0 ? $year : null,
            'source_name' => ($sourceName !== null && trim($sourceName) !== '') ? trim($sourceName) : null,
        ];
    }
}
