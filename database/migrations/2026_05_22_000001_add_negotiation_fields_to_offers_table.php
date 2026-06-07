<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'counter_price')) {
                $table->decimal('counter_price', 15, 2)->nullable()->after('offer_price');
            }

            if (!Schema::hasColumn('offers', 'final_price')) {
                $table->decimal('final_price', 15, 2)->nullable()->after('counter_price');
            }

            if (!Schema::hasColumn('offers', 'negotiation_round')) {
                $table->unsignedInteger('negotiation_round')->default(1)->after('final_price');
            }

            if (!Schema::hasColumn('offers', 'last_offer_by')) {
                $table->string('last_offer_by', 20)->nullable()->after('negotiation_round');
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("UPDATE offers SET status = 'pending' WHERE status NOT IN ('pending','accepted','rejected','countered')");
        }

        Schema::table('offers', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->dropColumn('status');
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('offers', function (Blueprint $table) {
                $table->enum('status', ['pending', 'countered', 'accepted', 'rejected'])->default('pending')->after('offer_price');
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('offers', function (Blueprint $table) {
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->after('offer_price');
            });
        }

        Schema::table('offers', function (Blueprint $table) {
            foreach (['last_offer_by', 'negotiation_round', 'final_price', 'counter_price'] as $column) {
                if (Schema::hasColumn('offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
