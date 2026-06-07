<?php
require __DIR__ . '/../vendor/autoload.php';
use Carbon\Carbon;
$path = 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2021\\Penjualan_2021_Maharani Mobil.xlsx';
$yearHint = 2021;
foreach (\App\Support\XlsxReader::sheets($path) as $sheet) {
  $rows = \App\Support\XlsxReader::readSheet($path, $sheet['path']);
  foreach ($rows as $idx => $row) {
    $brand = trim((string)($row['merek'] ?? ''));
    $model = trim((string)($row['model_tipe'] ?? ''));
    $priceRaw = trim((string)($row['harga_jual'] ?? ''));
    $dateRaw = trim((string)($row['tanggal_transaksi'] ?? ''));
    if ($brand === '' || $model === '' || $priceRaw === '') {
      continue;
    }
    $parsed = null;
    if ($dateRaw !== '') {
      if (is_numeric($dateRaw)) {
        $parsed = Carbon::create(1899,12,30)->addDays((int)$dateRaw)->startOfDay()->addHours(12);
      } else {
        try { $parsed = Carbon::parse($dateRaw); } catch (Throwable $e) {}
      }
    }
    if ($parsed && ($parsed->year < 2000 || abs($parsed->year - $yearHint) > 1)) {
      echo $sheet['name'] . ' row ' . ($idx + 2) . ' suspicious imported row: date=' . $dateRaw . ' parsed=' . $parsed->format('Y-m-d H:i:s') . ' brand=' . $brand . ' model=' . $model . ' price=' . $priceRaw . "\n";
    }
  }
}
