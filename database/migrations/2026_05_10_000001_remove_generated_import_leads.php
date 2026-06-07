<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const IMPORT_ARCHIVE_DESCRIPTION = 'Unit arsip hasil import penjualan Excel.';
    private const IMPORT_ARCHIVE_DESCRIPTION_LEGACY = 'Unit arsip hasil import penjualan 2025.';

    public function up(): void
    {
        $importedCarIds = DB::table('cars')
            ->whereIn('deskripsi', [
                self::IMPORT_ARCHIVE_DESCRIPTION,
                self::IMPORT_ARCHIVE_DESCRIPTION_LEGACY,
            ])
            ->pluck('id');

        if ($importedCarIds->isEmpty()) {
            return;
        }

        DB::table('offers')->whereIn('car_id', $importedCarIds)->delete();
        DB::table('test_drives')->whereIn('car_id', $importedCarIds)->delete();
    }

    public function down(): void
    {
        // Data fabricated by older imports should not be restored.
    }
};
