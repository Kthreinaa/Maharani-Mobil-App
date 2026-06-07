<?php
require __DIR__ . '/../vendor/autoload.php';
$files = [
  '2021' => 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2021\\Penjualan_2021_Maharani Mobil.xlsx',
  '2025' => 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_Maharani Mobil.xlsx',
];
foreach ($files as $label => $path) {
  echo "==== $label ====\n";
  foreach (\App\Support\XlsxReader::sheets($path) as $sheet) {
    $rows = \App\Support\XlsxReader::readSheet($path, $sheet['path']);
    foreach ($rows as $idx => $row) {
      $value = trim((string)($row['tanggal_transaksi'] ?? ''));
      if ($value === '') continue;
      if (!is_numeric($value)) {
        echo $sheet['name'] . ' row ' . ($idx + 2) . ' non-numeric date: ' . $value . "\n";
      }
      if (preg_match('/^0\d{3}-\d{2}-\d{2}/', $value)) {
        echo $sheet['name'] . ' row ' . ($idx + 2) . ' suspicious date: ' . $value . "\n";
      }
    }
  }
  echo "\n";
}
