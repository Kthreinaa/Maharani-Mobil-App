<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$path = 'C:/Users/dell/Downloads/MBKM_2025-2026/PENJUALAN_MAHARANI/2025/Penjualan_2025_Maharani Mobil.xlsx';
$sheets = App\Support\XlsxReader::sheets($path);
$counts = [];
foreach ($sheets as $sheet) {
    $rows = App\Support\XlsxReader::readSheet($path, $sheet['path']);
    foreach ($rows as $row) {
        $raw = trim((string)($row['tanggal_transaksi'] ?? ''));
        $brand = trim((string)($row['merek'] ?? ''));
        $model = trim((string)($row['model_tipe'] ?? ''));
        $price = trim((string)($row['harga_jual'] ?? ''));
        if ($raw === '' || $brand === '' || $model === '' || $price === '' || strtoupper((string)($row['no'] ?? '')) === 'TOTAL') {
            continue;
        }
        if (is_numeric($raw)) {
            $date = Carbon\Carbon::create(1899, 12, 30)->addDays((int)$raw)->format('Y-m-d');
        } elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{3,4})$/', $raw, $m)) {
            $year = strlen($m[3]) === 3 ? 2025 : (int)$m[3];
            $date = sprintf('%04d-%02d-%02d', $year, (int)$m[1], (int)$m[2]);
        } else {
            $date = $raw;
        }
        $process = strtolower(trim((string)($row['proses'] ?? '')));
        $method = $process === '' ? 'blank' : $process;
        $key = $date . ' | ' . $method;
        $counts[$key] = ($counts[$key] ?? 0) + 1;
    }
}
ksort($counts);
foreach ($counts as $key => $count) {
    if (str_starts_with($key, '2025-12')) {
        echo $key . ' | ' . $count . PHP_EOL;
    }
}
