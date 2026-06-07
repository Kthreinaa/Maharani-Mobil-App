<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('car_id')->constrained()->cascadeOnDelete();
                $table->enum('status', ['pending', 'confirmed', 'paid', 'completed', 'cancelled'])->default('pending');
                $table->decimal('total', 15, 2);
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
