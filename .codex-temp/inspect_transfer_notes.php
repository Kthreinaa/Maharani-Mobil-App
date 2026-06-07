<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = 'Penjualan_2025_Maharani Mobil.xlsx';
$orders = App\Models\Order::where('import_source', $source)
    ->where('payment_method', 'transfer')
    ->limit(5)
    ->get(['id','created_at','notes']);
foreach ($orders as $order) {
 echo 'ID=' . $order->id . ' DATE=' . $order->created_at->format('Y-m-d') . PHP_EOL;
 echo 'NOTES=' . str_replace(["\r","\n"], ['',' || '], (string)$order->notes) . PHP_EOL;
 echo '---' . PHP_EOL;
}
