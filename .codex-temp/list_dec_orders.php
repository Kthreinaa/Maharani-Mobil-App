<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = 'Penjualan_2025_Maharani Mobil.xlsx';
$rows = App\Models\Order::where('import_source', $source)
    ->whereBetween('created_at', ['2025-12-01 00:00:00', '2025-12-31 23:59:59'])
    ->orderBy('created_at')
    ->get(['id','created_at','payment_method','handled_role']);
foreach ($rows as $row) {
 echo $row->id . ' | ' . $row->created_at->format('Y-m-d') . ' | ' . $row->payment_method . ' | ' . ($row->handled_role ?? 'null') . PHP_EOL;
}
