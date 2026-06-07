<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$path = 'C:/Users/dell/Downloads/MBKM_2025-2026/PENJUALAN_MAHARANI/2025/Penjualan_2025_Maharani Mobil.xlsx';
foreach (App\Support\XlsxReader::sheets($path) as $sheet) {
    if ($sheet['name'] !== 'Desember') continue;
    $rows = App\Support\XlsxReader::readSheet($path, $sheet['path']);
    foreach ($rows as $row) {
        $raw = trim((string)($row['tanggal_transaksi'] ?? ''));
        $brand = trim((string)($row['merek'] ?? ''));
        $model = trim((string)($row['model_tipe'] ?? ''));
        $price = trim((string)($row['harga_jual'] ?? ''));
        if ($raw === '' || $brand === '' || $model === '' || $price === '' || strtoupper((string)($row['no'] ?? '')) === 'TOTAL') continue;
        echo ($row['no'] ?? '-') . ' | ' . $raw . ' | ' . $brand . ' | ' . $model . ' | proses=' . ($row['proses'] ?? '') . PHP_EOL;
    }
}
