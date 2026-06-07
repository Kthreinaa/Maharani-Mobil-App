<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class OrderCodeFormatter
{
    private const MODEL_CODE_MAP = [
        'INNOVA' => 'INV',
        'KIJANG' => 'KJG',
        'AVANZA' => 'AVZ',
        'XENIA' => 'XEN',
        'PAJERO' => 'PAJ',
        'FORTUNER' => 'FOR',
        'SIGRA' => 'SIG',
        'CALYA' => 'CLY',
        'BRIO' => 'BRI',
        'HRV' => 'HRV',
        'CRV' => 'CRV',
        'JAZZ' => 'JAZ',
        'AGYA' => 'AGY',
        'ALPHARD' => 'ALP',
        'ERTIGA' => 'ERT',
        'YARIS' => 'YRS',
    ];

    private const GENERIC_TYPE_TOKENS = [
        'AT', 'MT', 'CVT', 'MATIC', 'AUTOMATIC', 'MANUAL',
        'EDITION', 'TYPE', 'SERIES', 'UNIT', 'NEW', 'ALL',
    ];

    public static function forOrderParts(
        ?string $type,
        ?string $brand,
        CarbonInterface $orderedAt,
        ?string $transactionChannel,
        ?string $importSource,
        int $sequence
    ): string {
        return self::compose(
            self::typeCode($type, $brand),
            $orderedAt,
            self::methodCode($transactionChannel, $orderedAt, $importSource),
            $sequence
        );
    }

    public static function compose(string $typeCode, CarbonInterface $orderedAt, string $methodCode, int $sequence): string
    {
        return Str::upper($typeCode)
            . $orderedAt->format('dmy')
            . Str::upper($methodCode)
            . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
    }

    public static function typeCode(?string $type, ?string $brand = null): string
    {
        $brandTokens = self::normalizedTokens($brand);
        $typeTokens = self::normalizedTokens($type);

        foreach ($typeTokens as $token) {
            if (isset(self::MODEL_CODE_MAP[$token])) {
                return self::MODEL_CODE_MAP[$token];
            }
        }

        $filteredTypeTokens = collect($typeTokens)
            ->reject(fn (string $token) => in_array($token, $brandTokens, true))
            ->reject(fn (string $token) => in_array($token, self::GENERIC_TYPE_TOKENS, true))
            ->reject(fn (string $token) => is_numeric($token))
            ->reject(fn (string $token) => strlen($token) < 2)
            ->values();

        $candidate = (string) ($filteredTypeTokens->first() ?? '');

        if ($candidate === '') {
            $candidate = (string) (collect($typeTokens)->first() ?? collect($brandTokens)->first() ?? 'ORD');
        }

        $lettersOnly = preg_replace('/[^A-Z0-9]/', '', $candidate) ?: 'ORD';

        return Str::padRight(Str::upper(Str::substr($lettersOnly, 0, 3)), 3, 'X');
    }

    public static function methodCode(?string $transactionChannel, ?CarbonInterface $orderedAt = null, ?string $importSource = null): string
    {
        if ($importSource && $orderedAt && (int) $orderedAt->format('Y') <= 2025) {
            return 'OFN';
        }

        return $transactionChannel === 'offline' ? 'OFN' : 'OLN';
    }

    /**
     * @return array<int, string>
     */
    private static function normalizedTokens(?string $value): array
    {
        $normalized = Str::upper(Str::ascii(trim((string) $value)));
        if ($normalized === '') {
            return [];
        }

        return collect(preg_split('/[^A-Z0-9]+/', $normalized) ?: [])
            ->filter(fn ($token) => $token !== null && $token !== '')
            ->values()
            ->all();
    }
}
