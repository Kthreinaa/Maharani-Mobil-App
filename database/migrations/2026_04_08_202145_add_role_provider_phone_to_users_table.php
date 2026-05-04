<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom role/provider/provider_id/phone ke tabel users bila belum ada.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['customer', 'supervisor', 'marketing', 'owner'])->default('customer');
            }

            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable();
            }

            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable();
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });
    }

    /**
     * Tidak menghapus kolom saat rollback untuk menghindari menghapus kolom bawaan schema existing.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
