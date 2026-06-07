<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;

class WorkbookSheetDateResolver
{
    private const SHEET_MONTHS = [
        'JANUARI' => 1,
        'FEBRUARI' => 2,
        'MARET' => 3,
        'APRIL' => 4,
        'MEI' => 5,
        'JUNI' => 6,
        'JULI' => 7,
        'AGUSTUS' => 8,
        'SEPTEMBER' => 9,
        'OKTOBER' => 10,
        'NOVEMBER' => 11,
        'DESEMBER' => 12,
    ];

    public static function alignToSheetMonth(?Carbon $date, string $sheetName): ?Carbon
    {
        if (!$date) {
            return null;
        }

        $sheetMonth = self::sheetMonth($sheetName);
        if ($sheetMonth === null || $sheetMonth === (int) $date->month) {
            return $date;
        }

        $adjusted = $date->copy()->month($sheetMonth);

        if ((int) $adjusted->day !== (int) $date->day) {
            return $date;
        }

        return $adjusted;
    }

    public static function sheetMonth(string $sheetName): ?int
    {
        $normalized = Str::upper(Str::ascii(trim($sheetName)));

        return self::SHEET_MONTHS[$normalized] ?? null;
    }
}
