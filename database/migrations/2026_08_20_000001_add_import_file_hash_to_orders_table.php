<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'import_file_hash')) {
                $table->string('import_file_hash', 64)->nullable()->after('import_reference');
                $table->index('import_file_hash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'import_file_hash')) {
                $table->dropIndex(['import_file_hash']);
                $table->dropColumn('import_file_hash');
            }
        });
    }
};
