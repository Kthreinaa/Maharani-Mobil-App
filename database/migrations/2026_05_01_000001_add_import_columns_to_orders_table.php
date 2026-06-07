<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'import_source')) {
                $table->string('import_source')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('orders', 'import_reference')) {
                $table->string('import_reference')->nullable()->unique()->after('import_source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'import_reference')) {
                $table->dropUnique(['import_reference']);
                $table->dropColumn('import_reference');
            }

            if (Schema::hasColumn('orders', 'import_source')) {
                $table->dropColumn('import_source');
            }
        });
    }
};
