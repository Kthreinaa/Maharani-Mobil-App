<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$path = 'C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_Maharani Mobil.xlsx';
$spreadsheet = IOFactory::load($path);
foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
  $title = $sheet->getTitle();
  $highestRow = min($sheet->getHighestRow(), 10);
  echo "==== $title ====\n";
  for ($row = 1; $row <= $highestRow; $row++) {
    $cell = $sheet->getCell('B'.$row);
    echo 'B'.$row.' raw=';
    var_export($cell->getValue());
    echo ' formatted=';
    var_export($cell->getFormattedValue());
    echo "\n";
  }
}
