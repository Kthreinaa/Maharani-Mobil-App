<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('maharani:import-sales-2025 {path} {--dry-run}', function () {
    $path = (string) $this->argument('path');
    $dryRun = (bool) $this->option('dry-run');

    $supervisor = \App\Models\User::where('role', 'supervisor')->first();
    $supervisorId = (int) ($supervisor?->id ?? 0);

    if ($supervisorId <= 0) {
        $this->error('Tidak ada user supervisor di database. Buat akun supervisor dulu.');
        return 1;
    }

    $stats = \App\Support\MaharaniSalesImporter::importSalesWorkbook($path, $supervisorId, $dryRun, basename($path));
    $this->info('Import selesai.');
    foreach ($stats as $key => $value) {
        $this->line($key . ': ' . (is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
    }

    return 0;
})->purpose('Import penjualan 2025 dari Excel (xlsx) ke sistem');
