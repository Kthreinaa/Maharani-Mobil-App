<?php

namespace App\Support;

use App\Models\Order;
use App\Models\TestDrive;

class TestDriveOrderLinker
{
    public static function attach(Order $order): void
    {
        if (!$order->user_id || !$order->car_id) {
            return;
        }

        if ($order->sales_flow === 'direct_purchase') {
            self::removeStandaloneForDirectPurchase($order);
            return;
        }

        if ($order->sales_flow !== 'after_test_drive') {
            return;
        }

        $orderedDate = optional($order->created_at)->toDateString() ?? now()->toDateString();

        $testDrive = TestDrive::query()
            ->whereNull('order_id')
            ->where('user_id', $order->user_id)
            ->where('car_id', $order->car_id)
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->whereDate('booking_date', '<=', $orderedDate)
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->first();

        if (!$testDrive) {
            return;
        }

        $testDrive->forceFill([
            'order_id' => $order->id,
            'follow_up_status' => $testDrive->follow_up_status === 'closed_lost'
                ? $testDrive->follow_up_status
                : 'closed_won',
        ])->saveQuietly();
    }

    public static function backfill(): int
    {
        $orders = Order::query()
            ->whereIn('sales_flow', ['after_test_drive', 'direct_purchase'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($orders as $order) {
            self::attach($order);
        }

        return $orders->count();
    }

    public static function removeStandaloneForDirectPurchase(Order $order): void
    {
        TestDrive::query()
            ->whereNull('order_id')
            ->where('user_id', $order->user_id)
            ->where('car_id', $order->car_id)
            ->delete();
    }
}
