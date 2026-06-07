<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = 'Penjualan_2025_Maharani Mobil.xlsx';
$orders = App\Models\Order::where('import_source', $source)
    ->whereDate('created_at', '2025-12-06')
    ->orderBy('id')
    ->get(['id','payment_method','notes']);
foreach ($orders as $order) {
 echo 'ID=' . $order->id . PHP_EOL;
 echo 'METHOD=' . $order->payment_method . PHP_EOL;
 echo 'NOTES=' . str_replace(["\r","\n"], ['',' || '], (string)$order->notes) . PHP_EOL;
 echo '---' . PHP_EOL;
}
