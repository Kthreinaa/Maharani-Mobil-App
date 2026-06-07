<?php

namespace App\Support;

use App\Models\Car;

class CarUnitCodeSuggester
{
    public static function nextSequence(): int
    {
        return (int) Car::query()
            ->pluck('kode_unit')
            ->map(fn (?string $code) => self::extractSequence($code))
            ->max() + 1;
    }

    public static function suggest(?string $type, int|string|null $year, ?int $sequence = null): string
    {
        $sequence ??= self::nextSequence();

        return UnitCodeFormatter::compose((string) $type, $year, $sequence);
    }

    public static function extractSequence(?string $code): int
    {
        if (!preg_match('/(\d+)$/', (string) $code, $matches)) {
            return 0;
        }

        return (int) $matches[1];
    }
}
