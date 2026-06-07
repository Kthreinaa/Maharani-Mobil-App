<?php
$path = dirname(__DIR__) . '/.codex-temp/sales-import-test.xlsx';
$zip = new ZipArchive();
$result = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($result !== true) { throw new RuntimeException('zip open failed: '.$result); }
$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>');
$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
$zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>');
$zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
$strings = ['Tanggal','Customer','Mobil','Merk','Tipe','Tahun','Nominal','Metode Pembayaran','Status Pembayaran','Status Order','Kode Unit','Kilometer','Rina Test','Honda HR-V','Honda','HR-V','Transfer','Verified','Completed','TEST-IMPORT-002'];
$si = implode('', array_map(fn($s) => '<si><t>'.htmlspecialchars($s, ENT_XML1).'</t></si>', $strings));
$zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">'.$si.'</sst>');
$cells = '';
for ($i = 0; $i < 12; $i++) { $cells .= '<c r="'.chr(65+$i).'1" t="s"><v>'.$i.'</v></c>'; }
$row2 = '<c r="A2"><v>45333</v></c><c r="B2" t="s"><v>12</v></c><c r="C2" t="s"><v>13</v></c><c r="D2" t="s"><v>14</v></c><c r="E2" t="s"><v>15</v></c><c r="F2"><v>2021</v></c><c r="G2"><v>250000000</v></c><c r="H2" t="s"><v>16</v></c><c r="I2" t="s"><v>17</v></c><c r="J2" t="s"><v>18</v></c><c r="K2" t="s"><v>19</v></c><c r="L2"><v>12000</v></c>';
$zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1">'.$cells.'</row><row r="2">'.$row2.'</row></sheetData></worksheet>');
$zip->close();
