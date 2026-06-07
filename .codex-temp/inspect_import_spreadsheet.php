<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$files = [
  '2021' => 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2021\\Penjualan_2021_Maharani Mobil.xlsx',
  '2022' => 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2022\\Penjualan_2022_Maharani mobil.xlsx',
  '2025' => 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_Maharani Mobil.xlsx',
];
foreach ($files as $label => $path) {
  echo "==== $label ====\n";
  $spreadsheet = IOFactory::load($path);
  $sheet = $spreadsheet->getSheet(0);
  for ($row = 1; $row <= 5; $row++) {
    $vals = [];
    foreach (range('A','F') as $col) {
      $cell = $sheet->getCell($col.$row);
      $vals[$col] = [
        'formatted' => $cell->getFormattedValue(),
        'raw' => $cell->getValue(),
      ];
    }
    var_export($vals);
    echo "\n";
  }
  echo "\n";
}
