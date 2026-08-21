<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_cancellation_reason')) {
                $table->string('customer_cancellation_reason', 255)->nullable()->after('cancel_reason');
            }

            if (!Schema::hasColumn('orders', 'customer_cancellation_requested_at')) {
                $table->timestamp('customer_cancellation_requested_at')->nullable()->after('customer_cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'customer_cancellation_reason',
                'customer_cancellation_requested_at',
            ];

            $existing = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('orders', $column)));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
