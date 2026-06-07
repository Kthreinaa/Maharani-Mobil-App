<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('car_id')->constrained()->cascadeOnDelete();
                $table->decimal('offer_price', 15, 2);
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
