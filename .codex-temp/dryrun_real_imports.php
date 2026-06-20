<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$supervisor = \App\Models\User::firstOrCreate(
    ['email' => 'codex-dryrun-import@example.com'],
    ['name' => 'Codex Dry Run', 'password' => bcrypt('password'), 'role' => 'supervisor']
);

$files = [
  '2021' => public_path('imports/PENJUALAN_MAHARANI/2021/Penjualan_2021_Maharani Mobil.xlsx'),
  '2025' => public_path('imports/PENJUALAN_MAHARANI/2025/Penjualan_2025_Maharani Mobil.xlsx'),
];
foreach ($files as $label => $path) {
  echo "==== $label ====\n";
  try {
    $stats = \App\Support\MaharaniSalesImporter::importSalesWorkbook($path, $supervisor->id, true, basename($path));
    var_export($stats);
    echo "\n";
  } catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
  }
  echo "\n";
}
