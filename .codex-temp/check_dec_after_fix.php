<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = 'Penjualan_2025_Maharani Mobil.xlsx';
$rows = App\Models\Order::where('import_source', $source)
    ->selectRaw('DATE(created_at) as d, payment_method, count(*) as total')
    ->groupBy('d', 'payment_method')
    ->orderBy('d')
    ->get();
foreach ($rows as $row) {
    if (str_starts_with($row->d, '2025-12')) {
        echo $row->d . ' | ' . ($row->payment_method ?? 'null') . ' | ' . $row->total . PHP_EOL;
    }
}
