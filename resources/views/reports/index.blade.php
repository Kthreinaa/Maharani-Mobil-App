<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Penjualan</title>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen p-8">
  <h1 class="text-2xl font-bold text-primary mb-6">Laporan Penjualan</h1>
  <div class="mb-4 flex gap-3">
    <a class="px-4 py-2 rounded-lg bg-primary text-white" href="?period=weekly">Mingguan</a>
    <a class="px-4 py-2 rounded-lg bg-primary text-white" href="?period=monthly">Bulanan</a>
    <a class="px-4 py-2 rounded-lg bg-primary text-white" href="?period=yearly">Tahunan</a>
  </div>
  <div class="mb-6 flex gap-3">
    <a class="px-4 py-2 rounded-lg border border-outline-variant" href="{{ route(request()->routeIs('owner.*') ? 'owner.reports.exportPdf' : 'supervisor.reports.exportPdf') }}">Export PDF</a>
    <a class="px-4 py-2 rounded-lg border border-outline-variant" href="{{ route(request()->routeIs('owner.*') ? 'owner.reports.exportExcel' : 'supervisor.reports.exportExcel') }}">Export Excel</a>
  </div>
  <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
    <table class="w-full text-left text-sm">
      <thead class="bg-surface-container-low text-on-surface-variant uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Tanggal</th>
          <th class="px-4 py-3">Customer</th>
          <th class="px-4 py-3">Mobil</th>
          <th class="px-4 py-3">Metode</th>
          <th class="px-4 py-3">Nominal</th>
          <th class="px-4 py-3">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($rows as $row)
          <tr>
            <td class="px-4 py-3">{{ $row->tanggal }}</td>
            <td class="px-4 py-3">{{ $row->customer }}</td>
            <td class="px-4 py-3">{{ $row->mobil }}</td>
            <td class="px-4 py-3">{{ $row->metode_pembayaran }}</td>
            <td class="px-4 py-3">Rp {{ number_format($row->nominal) }}</td>
            <td class="px-4 py-3">{{ $row->status_order }} / {{ $row->status_pembayaran }}</td>
          </tr>
        @empty
          <tr>
            <td class="px-4 py-6 text-center text-on-surface-variant" colspan="6">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</body>
</html>
