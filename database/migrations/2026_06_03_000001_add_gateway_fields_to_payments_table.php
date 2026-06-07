<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'gateway_provider')) {
                $table->string('gateway_provider', 40)->nullable()->after('method');
            }

            if (!Schema::hasColumn('payments', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('gateway_provider');
            }

            if (!Schema::hasColumn('payments', 'gateway_external_id')) {
                $table->string('gateway_external_id')->nullable()->after('gateway_reference');
            }

            if (!Schema::hasColumn('payments', 'gateway_checkout_url')) {
                $table->text('gateway_checkout_url')->nullable()->after('gateway_external_id');
            }

            if (!Schema::hasColumn('payments', 'gateway_status')) {
                $table->string('gateway_status', 40)->nullable()->after('gateway_checkout_url');
            }

            if (!Schema::hasColumn('payments', 'gateway_channel')) {
                $table->string('gateway_channel', 80)->nullable()->after('gateway_status');
            }

            if (!Schema::hasColumn('payments', 'gateway_payload')) {
                $table->json('gateway_payload')->nullable()->after('gateway_channel');
            }

            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('gateway_payload');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['paid_at', 'gateway_payload', 'gateway_channel', 'gateway_status', 'gateway_checkout_url', 'gateway_external_id', 'gateway_reference', 'gateway_provider'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
