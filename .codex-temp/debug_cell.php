<?php
$zip=new ZipArchive();
$zip->open('C:\\Users\\dell\\Downloads\\MBKM_2025-2026\\PENJUALAN_MAHARANI\\2025\\Penjualan_2025_MaharaniMobil_FIX.xlsx');
$xml=$zip->getFromName('xl/worksheets/sheet1.xml');
$zip->close();
$sheet=simplexml_load_string($xml);
$ns='http://schemas.openxmlformats.org/spreadsheetml/2006/main';
$sheet->registerXPathNamespace('ss',$ns);
$row=$sheet->xpath('//ss:sheetData/ss:row')[0];
$c=$row->children($ns)->c[0];
echo "ref=".(string)$c['r']." t=".(string)$c['t']."\n";
$children=$c->children($ns);
echo "v=".(string)$children->v."\n";
