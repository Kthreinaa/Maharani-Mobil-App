<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class HistoricalUnitCodeSynchronizer
{
    public static function sync(string $startDate = '2021-01-01', string $endDate = '2025-12-31 23:59:59'): int
    {
        $rows = DB::table('orders')
            ->join('cars', 'cars.id', '=', 'orders.car_id')
            ->select('orders.id as order_id', 'orders.created_at as order_date', 'cars.id as car_id', 'cars.tipe', 'cars.tahun')
            ->whereNotNull('orders.import_reference')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderBy('orders.created_at')
            ->orderBy('orders.id')
            ->get()
            ->unique('car_id')
            ->values();

        $targetCarIds = $rows->pluck('car_id')->map(fn ($id) => (int) $id)->all();
        $reservedCodes = DB::table('cars')
            ->whereNotIn('id', $targetCarIds)
            ->whereNotNull('kode_unit')
            ->pluck('id', 'kode_unit')
            ->mapWithKeys(fn ($carId, $code) => [strtoupper((string) $code) => (int) $carId])
            ->all();

        $sequence = 1;
        foreach ($rows as $row) {
            $kodeUnit = self::nextAvailableCode(
                (string) $row->tipe,
                (int) $row->tahun,
                $sequence,
                (int) $row->car_id,
                $reservedCodes
            );

            DB::table('cars')
                ->where('id', $row->car_id)
                ->update([
                    'kode_unit' => $kodeUnit,
                    'updated_at' => now(),
                ]);

            $sequence++;
        }

        return $rows->count();
    }

    /**
     * @param array<string, int> $reservedCodes
     */
    private static function nextAvailableCode(string $type, int $year, int &$sequence, int $carId, array &$reservedCodes): string
    {
        while (true) {
            $candidate = strtoupper(UnitCodeFormatter::compose($type, $year, $sequence));
            $ownerCarId = $reservedCodes[$candidate] ?? null;

            if ($ownerCarId === null || $ownerCarId === $carId) {
                $reservedCodes[$candidate] = $carId;
                return $candidate;
            }

            $sequence++;
        }
    }
}
