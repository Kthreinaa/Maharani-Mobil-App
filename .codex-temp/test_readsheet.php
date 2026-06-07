<?php
require __DIR__ . '/../vendor/autoload.php';
$path='C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_MaharaniMobil_FIX.xlsx';
$r = \App\Support\XlsxReader::readSheet($path, 'xl/worksheets/sheet1.xml');
var_dump($r->first());
