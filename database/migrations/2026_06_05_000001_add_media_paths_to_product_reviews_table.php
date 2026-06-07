<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->json('media_paths')->nullable()->after('media_path');
        });

        DB::table('product_reviews')
            ->select(['id', 'media_path'])
            ->orderBy('id')
            ->get()
            ->each(function ($review) {
                $paths = [];

                if (!empty($review->media_path)) {
                    $paths[] = $review->media_path;
                }

                DB::table('product_reviews')
                    ->where('id', $review->id)
                    ->update([
                        'media_paths' => !empty($paths) ? json_encode($paths, JSON_UNESCAPED_SLASHES) : null,
                        'status' => 'approved',
                        'admin_notes' => null,
                        'verified_at' => DB::raw('COALESCE(verified_at, created_at)'),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn('media_paths');
        });
    }
};
