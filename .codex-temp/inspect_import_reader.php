<?php
require __DIR__ . '/../vendor/autoload.php';
$files = [
  '2021' => public_path('imports/PENJUALAN_MAHARANI/2021/Penjualan_2021_Maharani Mobil.xlsx'),
  '2022' => public_path('imports/PENJUALAN_MAHARANI/2022/Penjualan_2022_Maharani mobil.xlsx'),
  '2025' => public_path('imports/PENJUALAN_MAHARANI/2025/Penjualan_2025_Maharani Mobil.xlsx'),
];
foreach ($files as $label => $path) {
  echo "==== $label ====\n";
  $sheets = \App\Support\XlsxReader::sheets($path);
  var_export($sheets);
  echo "\n";
  if ($sheets) {
    $rows = \App\Support\XlsxReader::readSheet($path, $sheets[0]['path']);
    var_export($rows->take(3)->all());
    echo "\n\n";
  }
}
