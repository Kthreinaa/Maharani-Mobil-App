<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $orders = DB::table('orders')
            ->whereNotNull('import_reference')
            ->get(['id', 'payment_method', 'notes']);

        foreach ($orders as $order) {
            $method = $this->resolveMethodFromNotes((string) ($order->notes ?? ''));

            if ($method === ($order->payment_method ?? null)) {
                continue;
            }

            DB::table('orders')
                ->where('id', $order->id)
                ->update(['payment_method' => $method]);

            DB::table('payments')
                ->where('order_id', $order->id)
                ->update(['method' => $method]);
        }
    }

    public function down(): void
    {
        // Data normalization is intentionally not reversed.
    }

    private function resolveMethodFromNotes(string $notes): string
    {
        $normalized = strtolower(trim($notes));

        if ($normalized === '') {
            return 'cash';
        }

        if (str_contains($normalized, 'proses: cash') || str_contains($normalized, 'proses: tunai')) {
            return 'cash';
        }

        if (
            str_contains($normalized, 'proses: kredit')
            || str_contains($normalized, 'proses: credit')
            || str_contains($normalized, 'leasing:')
            || str_contains($normalized, 'dp:')
            || str_contains($normalized, 'tenor:')
            || str_contains($normalized, 'angsuran/bulan:')
            || str_contains($normalized, 'approval kredit:')
            || str_contains($normalized, 'aproval kredit:')
            || str_contains($normalized, 'status survey:')
            || str_contains($normalized, 'persen leasing:')
        ) {
            return 'va';
        }

        return 'cash';
    }
};
