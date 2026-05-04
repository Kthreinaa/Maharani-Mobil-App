<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan Saya - Maharani Mobil</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
  <div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold mb-6">Pesanan Saya</h1>

    @if (session('success'))
      <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-100 text-xs uppercase tracking-wider text-slate-500">
          <tr>
            <th class="px-6 py-4">Tanggal</th>
            <th class="px-6 py-4">Mobil</th>
            <th class="px-6 py-4">Total</th>
            <th class="px-6 py-4">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse ($orders as $order)
            <tr>
              <td class="px-6 py-4">{{ $order->created_at->format('d M Y') }}</td>
              <td class="px-6 py-4">{{ $order->car?->merk }} {{ $order->car?->tipe }}</td>
              <td class="px-6 py-4">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
              <td class="px-6 py-4">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase">{{ $order->status }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-6 text-center text-slate-500">Belum ada pesanan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
