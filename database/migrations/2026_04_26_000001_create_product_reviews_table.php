<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->enum('source_type', ['purchase', 'test_drive']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('review_text');
            $table->string('media_path')->nullable();
            $table->string('embed_url')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['car_id', 'status']);
            $table->unique(['user_id', 'car_id', 'source_type', 'source_id'], 'product_reviews_unique_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
