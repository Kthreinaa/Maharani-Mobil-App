<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('handled_by')->nullable()->after('import_reference')->constrained('users')->nullOnDelete();
            $table->string('handled_role')->nullable()->after('handled_by');
            $table->timestamp('handled_at')->nullable()->after('handled_role');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('handled_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->string('handled_role')->nullable()->after('handled_by');
            $table->timestamp('handled_at')->nullable()->after('handled_role');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('handled_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->string('handled_role')->nullable()->after('handled_by');
            $table->timestamp('handled_at')->nullable()->after('handled_role');
        });

        Schema::table('test_drives', function (Blueprint $table) {
            $table->foreignId('handled_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->string('handled_role')->nullable()->after('handled_by');
            $table->timestamp('handled_at')->nullable()->after('handled_role');
        });
    }

    public function down(): void
    {
        Schema::table('test_drives', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by');
            $table->dropColumn(['handled_role', 'handled_at']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by');
            $table->dropColumn(['handled_role', 'handled_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by');
            $table->dropColumn(['handled_role', 'handled_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by');
            $table->dropColumn(['handled_role', 'handled_at']);
        });
    }
};
