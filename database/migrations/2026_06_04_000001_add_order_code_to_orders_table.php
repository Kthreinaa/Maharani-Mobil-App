<?php

use App\Support\HistoricalOrderCodeSynchronizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_code')->nullable()->after('id');
            });
        }

        HistoricalOrderCodeSynchronizer::sync();

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_code');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['order_code']);
                $table->dropColumn('order_code');
            });
        }
    }
};
