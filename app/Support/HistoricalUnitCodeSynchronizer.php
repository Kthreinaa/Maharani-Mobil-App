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
            ->get();

        $sequence = 1;
        foreach ($rows as $row) {
            DB::table('cars')
                ->where('id', $row->car_id)
                ->update([
                    'kode_unit' => UnitCodeFormatter::compose((string) $row->tipe, (int) $row->tahun, $sequence),
                    'updated_at' => now(),
                ]);

            $sequence++;
        }

        return $rows->count();
    }
}
