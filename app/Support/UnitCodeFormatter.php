<?php

namespace App\Support;

use Illuminate\Support\Str;

class UnitCodeFormatter
{
    private const TYPE_CODE_MAP = [
        'ACCORD' => 'ACD',
        'AGYA' => 'AGY',
        'ALVEZ' => 'ALV',
        'APV' => 'APV',
        'AVANZA' => 'AVZ',
        'AYLA' => 'AYL',
        'BALENO' => 'BLN',
        'BR-V' => 'BRV',
        'BRIO' => 'BRI',
        'CALYA' => 'CLY',
        'CAMRY' => 'CMR',
        'CAPTIVA' => 'CPT',
        'CARRY' => 'CRY',
        'CITY' => 'CTY',
        'CIVIC' => 'CVC',
        'CONFERO' => 'CFR',
        'COROLLA' => 'CRL',
        'CR-V' => 'CRV',
        'CX-3' => 'CX3',
        'ERTIGA' => 'ERT',
        'ETIOS' => 'ETS',
        'FORTUNER' => 'FRT',
        'FREED' => 'FRD',
        'GRAN' => 'GRN',
        'GRAND' => 'GRD',
        'HILUX' => 'HLX',
        'HR-V' => 'HRV',
        'INNOVA' => 'INV',
        'JAZZ' => 'JZZ',
        'JUKE' => 'JUK',
        'KARIMUN' => 'KRM',
        'KIJANG' => 'KJG',
        'L300' => 'L30',
        'LIVINA' => 'LVN',
        'LUXIO' => 'LXI',
        'MOBILIO' => 'MBL',
        'OUTLANDER' => 'OTL',
        'PAJERO' => 'PJR',
        'RAIZE' => 'RAZ',
        'ROCKY' => 'RCK',
        'RUSH' => 'RSH',
        'SERENA' => 'SRN',
        'SIENTA' => 'SNT',
        'SIGRA' => 'SGR',
        'SIRION' => 'SRI',
        'SWIFT' => 'SWF',
        'SX4' => 'SX4',
        'TERIOS' => 'TRS',
        'TRITON' => 'TRT',
        'VELOZ' => 'VLZ',
        'VIOS' => 'VIS',
        'X-TRAIL' => 'XTR',
        'XENIA' => 'XEN',
        'XL7' => 'XL7',
        'XPANDER' => 'XPD',
        'YARIS' => 'YRS',
    ];

    public static function compose(string $type, int|string|null $year, int $sequence): string
    {
        return sprintf(
            'MM-%s-%s-%s',
            self::typeCode($type),
            self::yearCode($year),
            str_pad((string) $sequence, 2, '0', STR_PAD_LEFT)
        );
    }

    public static function typeCode(string $type): string
    {
        $primaryToken = self::primaryToken($type);

        if (isset(self::TYPE_CODE_MAP[$primaryToken])) {
            return self::TYPE_CODE_MAP[$primaryToken];
        }

        $letters = preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::ascii($primaryToken)));
        if ($letters === '') {
            return 'UNK';
        }

        if (strlen($letters) >= 3) {
            return substr($letters, 0, 3);
        }

        return str_pad($letters, 3, 'X');
    }

    public static function yearCode(int|string|null $year): string
    {
        $yearNumber = (int) $year;

        if ($yearNumber <= 0) {
            return '000';
        }

        return str_pad((string) ($yearNumber % 1000), 3, '0', STR_PAD_LEFT);
    }

    private static function primaryToken(string $type): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim(Str::upper(Str::ascii($type))));
        if ($normalized === null || $normalized === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $normalized) ?: [];

        return $parts[0] ?? $normalized;
    }
}
