<?php

namespace App\Support;

use Illuminate\Support\Collection;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class XlsxReader
{
    private const SS_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const REL_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';
    private const R_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /**
     * @return array<int, array{name:string, path:string}>
     */
    public static function sheets(string $xlsxPath): array
    {
        $zip = self::openZip($xlsxPath);
        try {
            $workbook = self::loadXml($zip, 'xl/workbook.xml');
            $rels = self::loadXml($zip, 'xl/_rels/workbook.xml.rels');

            $workbook->registerXPathNamespace('ss', self::SS_NS);
            $workbook->registerXPathNamespace('r', self::R_NS);
            $rels->registerXPathNamespace('rel', self::REL_NS);

            $idToTarget = [];
            foreach (($rels->xpath('//rel:Relationship') ?: []) as $rel) {
                $idToTarget[(string) $rel['Id']] = (string) $rel['Target'];
            }

            $out = [];
            foreach (($workbook->xpath('//ss:sheets/ss:sheet') ?: []) as $sheet) {
                $name = (string) $sheet['name'];
                $rid = (string) ($sheet->attributes(self::R_NS)['id'] ?? '');
                $target = $idToTarget[$rid] ?? '';
                $target = ltrim($target, '/');
                if ($target !== '' && !str_starts_with($target, 'xl/')) {
                    $target = 'xl/' . $target;
                }

                if ($name !== '' && $target !== '') {
                    $out[] = ['name' => $name, 'path' => $target];
                }
            }

            return $out;
        } finally {
            $zip->close();
        }
    }

    /**
     * Reads the first row as headings and returns remaining rows as associative arrays.
     *
     * @return Collection<int, array<string, string>>
     */
    public static function readSheet(string $xlsxPath, string $sheetPath): Collection
    {
        $zip = self::openZip($xlsxPath);
        try {
            $sharedStrings = self::readSharedStrings($zip);
            $sheetXml = self::loadXml($zip, $sheetPath);

            $sheet = $sheetXml->children(self::SS_NS);
            $sheetData = $sheet->sheetData;
            if (!$sheetData) {
                return collect();
            }

            // Some real-world workbooks add title rows before the actual headings row.
            // We scan for the first row that looks like a header (e.g. contains "tanggal" + "merek/merk").
            $headings = [];
            $rows = collect();

            foreach ($sheetData->row as $row) {
                $cells = self::readRowCells($row, $sharedStrings);
                $cells = self::trimTrailingEmpty($cells);
                if ($cells === [] || self::rowIsEmpty($cells)) {
                    continue;
                }

                if ($headings === []) {
                    $maybe = self::detectHeadingRow($cells);
                    if ($maybe !== []) {
                        $headings = $maybe;
                    }
                    continue;
                }

                // Ignore repeated header rows inside the data region.
                if (self::looksLikeHeadingRepeat($cells, $headings)) {
                    continue;
                }

                $assoc = [];
                $hasAny = false;
                foreach ($headings as $i => $heading) {
                    $value = $cells[$i] ?? '';
                    $value = is_string($value) ? trim($value) : (string) $value;
                    $assoc[$heading] = $value;
                    if ($value !== '') {
                        $hasAny = true;
                    }
                }

                if ($hasAny) {
                    $rows->push($assoc);
                }
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }

    private static function openZip(string $path): ZipArchive
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi ZipArchive belum aktif. File .xlsx tidak bisa dibaca.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File Excel tidak bisa dibuka.');
        }

        return $zip;
    }

    private static function loadXml(ZipArchive $zip, string $name): SimpleXMLElement
    {
        $xml = $zip->getFromName($name);
        if ($xml === false) {
            throw new RuntimeException("Bagian file Excel tidak ditemukan: {$name}");
        }

        $parsed = simplexml_load_string($xml);
        if (!$parsed) {
            throw new RuntimeException("XML tidak valid: {$name}");
        }

        return $parsed;
    }

    /**
     * @return array<int, string>
     */
    private static function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $parsed = simplexml_load_string($xml);
        if (!$parsed) {
            return [];
        }

        $strings = [];
        foreach ($parsed->children(self::SS_NS)->si as $item) {
            $children = $item->children(self::SS_NS);
            if (isset($children->t)) {
                $strings[] = (string) $children->t;
                continue;
            }

            $text = '';
            foreach ($children->r as $run) {
                $text .= (string) $run->children(self::SS_NS)->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    /**
     * @return array<int, string>
     */
    private static function readRowCells(SimpleXMLElement $row, array $sharedStrings): array
    {
        $cells = [];
        $fallbackIndex = 0;
        foreach ($row->children(self::SS_NS)->c as $cell) {
            $attrs = $cell->attributes();
            $ref = (string) ($attrs['r'] ?? '');
            $idx = $ref !== '' ? self::columnIndex($ref) : $fallbackIndex;
            $cells[$idx] = self::cellValue($cell, $sharedStrings);
            $fallbackIndex++;
        }

        if ($cells === []) {
            return [];
        }

        ksort($cells);
        $max = array_key_last($cells);

        $out = [];
        for ($i = 0; $i <= $max; $i++) {
            $out[$i] = (string) ($cells[$i] ?? '');
        }

        return $out;
    }

    private static function cellValue(SimpleXMLElement $cell, array $sharedStrings): string
    {
        $attrs = $cell->attributes();
        $type = (string) ($attrs['t'] ?? '');
        $children = $cell->children(self::SS_NS);

        if (!isset($children->v)) {
            return '';
        }

        $raw = (string) $children->v;
        if ($type === 's') {
            $idx = (int) $raw;
            return $sharedStrings[$idx] ?? $raw;
        }

        if ($type === 'inlineStr') {
            return (string) ($children->is->children(self::SS_NS)->t ?? '');
        }

        return $raw;
    }

    private static function columnIndex(string $reference): int
    {
        $letters = strtoupper(preg_replace('/[^A-Z]/', '', $reference) ?: 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private static function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = str_replace([' ', '-', '.', '/', '\\'], '_', $key);
        $key = preg_replace('/_+/', '_', $key) ?: '';
        return trim($key, '_');
    }

    /**
     * @param array<int, string> $cells
     * @return array<int, string>
     */
    private static function detectHeadingRow(array $cells): array
    {
        $normalized = array_map(fn ($v) => self::normalizeKey((string) $v), $cells);

        $hasTanggal = in_array('tanggal_transaksi', $normalized, true) || in_array('tanggal', $normalized, true);
        $hasMerek = in_array('merek', $normalized, true) || in_array('merk', $normalized, true);

        if (!$hasTanggal || !$hasMerek) {
            return [];
        }

        $headings = [];
        foreach ($cells as $i => $heading) {
            $k = self::normalizeKey((string) $heading);
            if ($k === '') {
                $k = 'col_' . ($i + 1);
            }

            // Normalize common variants so downstream import logic is consistent.
            if ($k === 'merk') $k = 'merek';
            if ($k === 'tahun_mobil' || $k === 'tahun') $k = 'tahun_mobil';
            if ($k === 'harga' || $k === 'harga_jual') $k = 'harga_jual';
            if ($k === 'model' || $k === 'tipe' || $k === 'modeltipe' || $k === 'model_tipe') $k = 'model_tipe';
            if ($k === 'tenor_bulan' || $k === 'tenor__bulan' || $k === 'tenor_(bulan)') $k = 'tenor_(bulan)';

            $headings[$i] = $k;
        }

        return $headings;
    }

    /**
     * @param array<int, string> $cells
     * @param array<int, string> $headings
     */
    private static function looksLikeHeadingRepeat(array $cells, array $headings): bool
    {
        $normalized = array_map(fn ($v) => self::normalizeKey((string) $v), $cells);
        $score = 0;
        foreach ($headings as $i => $h) {
            $cell = $normalized[$i] ?? '';
            if ($cell !== '' && ($cell === $h || ($h === 'merek' && $cell === 'merk'))) {
                $score++;
            }
        }

        // If at least 2/3 of known headings match, treat as a repeated header row.
        $need = max(3, (int) floor(count($headings) * 0.66));
        return $score >= $need;
    }

    /**
     * @param array<int, string> $cells
     */
    private static function rowIsEmpty(array $cells): bool
    {
        foreach ($cells as $v) {
            if (trim((string) $v) !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array<int, string> $cells
     * @return array<int, string>
     */
    private static function trimTrailingEmpty(array $cells): array
    {
        while ($cells !== [] && (string) end($cells) === '') {
            array_pop($cells);
        }
        return $cells;
    }
}
