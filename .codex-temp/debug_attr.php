<?php
$zip=new ZipArchive();
$zip->open('C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_MaharaniMobil_FIX.xlsx');
$xml=$zip->getFromName('xl/worksheets/sheet1.xml');
$zip->close();
$sheet=simplexml_load_string($xml);
$ns='http://schemas.openxmlformats.org/spreadsheetml/2006/main';
$row=$sheet->children($ns)->sheetData->row[0];
$c=$row->children($ns)->c[0];
var_dump($c->attributes());
var_dump((string)$c['r'], (string)$c['t'], (string)$c['s']);
