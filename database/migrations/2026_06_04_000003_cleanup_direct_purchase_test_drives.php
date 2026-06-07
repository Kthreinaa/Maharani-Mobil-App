<?php

use App\Support\TestDriveOrderLinker;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        TestDriveOrderLinker::backfill();
    }

    public function down(): void
    {
        // Data cleanup is not restored on rollback.
    }
};
