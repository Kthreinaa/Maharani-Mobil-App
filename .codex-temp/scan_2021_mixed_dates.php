<?php
require __DIR__ . '/../vendor/autoload.php';
$path = 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2021\\Penjualan_2021_Maharani Mobil.xlsx';
foreach (\App\Support\XlsxReader::sheets($path) as $sheet) {
  $rows = \App\Support\XlsxReader::readSheet($path, $sheet['path']);
  foreach ($rows as $idx => $row) {
    $value = trim((string)($row['tanggal_transaksi'] ?? ''));
    if ($value !== '' && !is_numeric($value) && preg_match('/\d/', $value)) {
      echo $sheet['name'] . ' row ' . ($idx + 2) . ' mixed date candidate: ' . $value . " | brand=" . ($row['merek'] ?? '') . " | model=" . ($row['model_tipe'] ?? '') . "\n";
    }
  }
}
