<?php
require __DIR__ . '/../vendor/autoload.php';
$zip = new ZipArchive();
$zip->open(__DIR__ . '/sales-import-test.xlsx');
$ns = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
$shared = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
var_dump(count($shared->children($ns)->si));
foreach ($shared->children($ns)->si as $item) { $ch=$item->children($ns); echo (string)$ch->t."|"; }
echo "\n";
$xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
foreach ($xml->children($ns)->sheetData->row as $row) {
  foreach ($row->children($ns)->c as $cell) {
    $ch=$cell->children($ns); echo (string)$cell['r'].':'.(string)$cell['t'].'='.(string)$ch->v.';';
  }
  echo "\n";
}
