<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offer_histories')) {
            Schema::create('offer_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
                $table->string('actor_role', 20);
                $table->string('action', 30);
                $table->decimal('offered_price', 15, 2)->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_histories');
    }
};
