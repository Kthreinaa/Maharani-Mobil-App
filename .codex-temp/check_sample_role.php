<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = 'Penjualan_2025_Maharani Mobil.xlsx';
$sample = App\Models\Order::where('import_source', $source)->whereDate('created_at', '2025-12-06')->orderBy('id')->get(['id','created_at','payment_method','handled_role']);
foreach ($sample as $order) {
 echo $order->id . ' | ' . $order->created_at->format('Y-m-d') . ' | ' . $order->payment_method . ' | ' . ($order->handled_role ?? 'null') . PHP_EOL;
}
