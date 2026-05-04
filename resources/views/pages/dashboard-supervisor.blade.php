@extends('layouts.supervisor')

@php
  $title = 'Supervisor Dashboard';
  $pageTitle = 'Dashboard Overview';
@endphp

@section('content')
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Total Mobil Tersedia</p>
      <div class="text-2xl font-bold mt-2">{{ $totalAvailableCars }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Total Mobil Terjual</p>
      <div class="text-2xl font-bold mt-2">{{ $totalSold }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Total Pesanan</p>
      <div class="text-2xl font-bold mt-2">{{ $totalOrders }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Total Customer</p>
      <div class="text-2xl font-bold mt-2">{{ $totalCustomers }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Transaksi Pending</p>
      <div class="text-2xl font-bold mt-2">{{ $pendingPayments }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Transaksi Verified</p>
      <div class="text-2xl font-bold mt-2">{{ $verifiedPayments }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Booking Test Drive</p>
      <div class="text-2xl font-bold mt-2">{{ $totalTestDrives }}</div>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-slate-200">
      <p class="text-xs uppercase text-slate-500">Penawaran Masuk</p>
      <div class="text-2xl font-bold mt-2">{{ $totalOffers }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="col-span-2 rounded-xl bg-white p-6 shadow-sm border border-slate-200">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Tren Penjualan (Unit)</h2>
        <span class="text-xs text-slate-500">Tahun {{ now()->year }}</span>
      </div>
      <canvas id="salesChart" height="120"></canvas>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Status Transaksi</h2>
      </div>
      <canvas id="paymentChart" height="140"></canvas>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
      <h2 class="text-lg font-bold mb-4">Pendapatan Bulanan</h2>
      <canvas id="revenueChart" height="160"></canvas>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 col-span-2">
      <h2 class="text-lg font-bold mb-4">Mobil Paling Laku</h2>
      <canvas id="topCarsChart" height="160"></canvas>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
      <h2 class="text-lg font-bold mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 gap-3 text-sm">
        <a href="{{ route('supervisor.cars.create') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-center">Tambah Mobil</a>
        <a href="{{ route('supervisor.users.create') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-center">Tambah User</a>
        <a href="{{ route('supervisor.orders.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center">Lihat Pesanan</a>
        <a href="{{ route('supervisor.payments.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center">Verifikasi</a>
        <a href="{{ route('supervisor.reports.exportPdf') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center">Export PDF</a>
        <a href="{{ route('supervisor.reports.exportExcel') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-center">Export Excel</a>
      </div>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 col-span-2">
      <h2 class="text-lg font-bold mb-4">Aktivitas Terbaru</h2>
      <ul class="space-y-3 text-sm">
        @forelse($activity as $item)
          <li class="flex items-center justify-between">
            <span class="text-slate-700">{{ $item['label'] }} • {{ $item['detail'] }}</span>
            <span class="text-xs text-slate-500">{{ $item['time']->diffForHumans() }}</span>
          </li>
        @empty
          <li class="text-slate-500">Belum ada aktivitas terbaru.</li>
        @endforelse
      </ul>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 col-span-2">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Pesanan Terbaru</h2>
        <a class="text-sm text-slate-500 hover:underline" href="{{ route('supervisor.orders.index') }}">Lihat Semua</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="text-xs uppercase text-slate-500">
            <tr>
              <th class="py-2 text-left">Kode</th>
              <th class="py-2 text-left">Customer</th>
              <th class="py-2 text-left">Mobil</th>
              <th class="py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($recentOrders as $order)
              <tr>
                <td class="py-2">#{{ $order->id }}</td>
                <td class="py-2">{{ $order->user?->name }}</td>
                <td class="py-2">{{ $order->car?->merk }} {{ $order->car?->tipe }}</td>
                <td class="py-2">{{ $order->status }}</td>
              </tr>
            @empty
              <tr><td class="py-3 text-slate-500" colspan="4">Belum ada pesanan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Pembayaran Terbaru</h2>
        <a class="text-sm text-slate-500 hover:underline" href="{{ route('supervisor.payments.index') }}">Lihat Semua</a>
      </div>
      <ul class="space-y-3 text-sm">
        @forelse($recentPayments as $payment)
          <li class="flex items-center justify-between">
            <span>{{ $payment->order?->user?->name }}</span>
            <span class="text-xs text-slate-500">{{ $payment->status }}</span>
          </li>
        @empty
          <li class="text-slate-500">Belum ada pembayaran.</li>
        @endforelse
      </ul>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const salesLabels = @json($monthlySales->pluck('month'));
    const salesData = @json($monthlySales->pluck('total'));
    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: { labels: salesLabels, datasets: [{ label: 'Unit Terjual', data: salesData, borderColor: '#111827', tension: .35 }] },
      options: { plugins: { legend: { display: false } } }
    });

    const revenueLabels = @json($monthlyRevenue->pluck('month'));
    const revenueData = @json($monthlyRevenue->pluck('total'));
    new Chart(document.getElementById('revenueChart'), {
      type: 'bar',
      data: { labels: revenueLabels, datasets: [{ label: 'Pendapatan', data: revenueData, backgroundColor: '#0f172a' }] },
      options: { plugins: { legend: { display: false } } }
    });

    const paymentLabels = @json($paymentStatus->pluck('status'));
    const paymentData = @json($paymentStatus->pluck('total'));
    new Chart(document.getElementById('paymentChart'), {
      type: 'doughnut',
      data: { labels: paymentLabels, datasets: [{ data: paymentData, backgroundColor: ['#f59e0b','#10b981','#ef4444'] }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    const topCarLabels = @json($topCars->map(fn($row) => $row->car?->merk . ' ' . $row->car?->tipe));
    const topCarData = @json($topCars->pluck('total'));
    new Chart(document.getElementById('topCarsChart'), {
      type: 'bar',
      data: { labels: topCarLabels, datasets: [{ label: 'Total Terjual', data: topCarData, backgroundColor: '#1f2937' }] },
      options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });
  </script>
@endpush
