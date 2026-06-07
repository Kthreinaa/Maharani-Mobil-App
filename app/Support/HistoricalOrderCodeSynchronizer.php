<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoricalOrderCodeSynchronizer
{
    public static function sync(?string $startDate = null, ?string $endDate = null): int
    {
        $rows = DB::table('orders')
            ->join('cars', 'cars.id', '=', 'orders.car_id')
            ->select(
                'orders.id',
                'orders.created_at',
                'orders.transaction_channel',
                'orders.sales_flow',
                'orders.import_source',
                'cars.merk',
                'cars.tipe'
            )
            ->when($startDate, fn ($query) => $query->where('orders.created_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->where('orders.created_at', '<=', $endDate))
            ->orderBy('orders.created_at')
            ->orderBy('orders.id')
            ->get();

        $globalSequence = 0;

        foreach ($rows as $row) {
            $orderedAt = Carbon::parse((string) $row->created_at);
            $forceOfflineLegacy = !empty($row->import_source) && (int) $orderedAt->format('Y') <= 2025;
            $channel = $forceOfflineLegacy ? 'offline' : (string) ($row->transaction_channel ?: 'online');
            $baseCode = OrderCodeFormatter::forOrderParts(
                (string) $row->tipe,
                (string) $row->merk,
                $orderedAt,
                $channel,
                $forceOfflineLegacy ? (string) $row->import_source : null,
                0
            );
            $baseCode = substr($baseCode, 0, -2);
            $globalSequence++;
            $orderCode = $baseCode . str_pad((string) $globalSequence, 2, '0', STR_PAD_LEFT);

            $payload = [
                'order_code' => $orderCode,
            ];

            if ($forceOfflineLegacy) {
                $payload['transaction_channel'] = 'offline';
                $payload['sales_flow'] = 'offline_showroom';
            }

            DB::table('orders')
                ->where('id', $row->id)
                ->update($payload);
        }

        return $rows->count();
    }
}
