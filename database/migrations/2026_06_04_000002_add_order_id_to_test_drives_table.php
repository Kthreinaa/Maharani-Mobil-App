<?php

use App\Support\TestDriveOrderLinker;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_drives', function (Blueprint $table) {
            if (!Schema::hasColumn('test_drives', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('car_id')->constrained('orders')->nullOnDelete();
            }
        });

        TestDriveOrderLinker::backfill();
    }

    public function down(): void
    {
        Schema::table('test_drives', function (Blueprint $table) {
            if (Schema::hasColumn('test_drives', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
        });
    }
};
