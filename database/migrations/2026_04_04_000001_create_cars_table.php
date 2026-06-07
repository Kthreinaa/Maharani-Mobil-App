<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cars')) {
            Schema::create('cars', function (Blueprint $table) {
                $table->id();
                $table->string('kode_unit')->unique();
                $table->string('merk');
                $table->string('tipe');
                $table->unsignedInteger('tahun');
                $table->decimal('harga', 15, 2);
                $table->unsignedInteger('kilometer')->nullable();
                $table->string('transmisi')->nullable();
                $table->string('warna')->nullable();
                $table->string('bahan_bakar')->nullable();
                $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
                $table->text('deskripsi')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
