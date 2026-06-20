<?php
require __DIR__ . '/../vendor/autoload.php';
$files = [
  '2021' => public_path('imports/PENJUALAN_MAHARANI/2021/Penjualan_2021_Maharani Mobil.xlsx'),
  '2022' => public_path('imports/PENJUALAN_MAHARANI/2022/Penjualan_2022_Maharani mobil.xlsx'),
  '2025' => public_path('imports/PENJUALAN_MAHARANI/2025/Penjualan_2025_Maharani Mobil.xlsx'),
];
foreach ($files as $label => $path) {
  echo "==== $label ====\n";
  foreach (\App\Support\XlsxReader::sheets($path) as $sheet) {
    $rows = \App\Support\XlsxReader::readSheet($path, $sheet['path']);
    foreach ($rows as $idx => $row) {
      $value = trim((string)($row['tanggal_transaksi'] ?? ''));
      if ($value !== '' && preg_match('/^\d{1,2}\/\d{1,2}\/\d{3}$/', $value)) {
        echo $sheet['name'] . ' row ' . ($idx + 2) . ' short-year date: ' . $value . "\n";
      }
      if ($value !== '' && preg_match('/^\d{1,2}-\d{1,2}-\d{3}$/', $value)) {
        echo $sheet['name'] . ' row ' . ($idx + 2) . ' short-year dash date: ' . $value . "\n";
      }
    }
  }
  echo "\n";
}
