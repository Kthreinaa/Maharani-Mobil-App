<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $importedOrders = DB::table('orders')
            ->whereNotNull('import_reference')
            ->whereNull('handled_by')
            ->get(['id', 'created_at']);

        foreach ($importedOrders as $order) {
            $payment = DB::table('payments')
                ->where('order_id', $order->id)
                ->first(['verified_by', 'verified_at']);

            $handledBy = (int) ($payment->verified_by ?? 0);
            if ($handledBy <= 0) {
                continue;
            }

            $role = DB::table('users')->where('id', $handledBy)->value('role') ?: 'supervisor';

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'handled_by' => $handledBy,
                    'handled_role' => $role,
                    'handled_at' => $payment->verified_at ?? $order->created_at,
                ]);

            DB::table('payments')
                ->where('order_id', $order->id)
                ->whereNull('handled_by')
                ->update([
                    'handled_by' => $handledBy,
                    'handled_role' => $role,
                    'handled_at' => $payment->verified_at ?? $order->created_at,
                ]);

            $carId = DB::table('orders')->where('id', $order->id)->value('car_id');
            if ($carId) {
                DB::table('offers')
                    ->where('car_id', $carId)
                    ->whereNull('handled_by')
                    ->update([
                        'handled_by' => $handledBy,
                        'handled_role' => $role,
                        'handled_at' => $payment->verified_at ?? $order->created_at,
                    ]);

                DB::table('test_drives')
                    ->where('car_id', $carId)
                    ->whereNull('handled_by')
                    ->update([
                        'handled_by' => $handledBy,
                        'handled_role' => $role,
                        'handled_at' => $payment->verified_at ?? $order->created_at,
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Historical backfill is intentionally not reversed.
    }
};
