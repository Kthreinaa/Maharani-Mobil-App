<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen Pesanan | Marketing</title>
  <meta name="description" content="Pantau pesanan customer Maharani Mobil dengan data aktual."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4"><a class="text-xl font-bold text-[#1A2B4C]" href="{{ route('marketing.dashboard') }}">Maharani Mobil</a></div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.dashboard') }}"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.products.index') }}"><span class="material-symbols-outlined">directions_car</span>Manajemen Produk</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="{{ route('marketing.orders.index') }}"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.offers.index') }}"><span class="material-symbols-outlined">sell</span>Manajemen Penawaran</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.products.upload') }}"><span class="material-symbols-outlined">upload</span>Upload Produk</a>
    </nav>
    <form class="mt-auto pt-6 border-t border-slate-100" method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="w-full text-left text-slate-500 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" type="submit"><span class="material-symbols-outlined">logout</span>Logout</button>
    </form>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-primary">Manajemen Pesanan</h1>
        <p class="text-on-surface-variant">Pantau order customer dan progress pembayarannya.</p>
      </div>
    </header>

    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
      <div class="px-8 py-6 border-b border-slate-50">
        <form class="flex flex-col md:flex-row md:items-center md:justify-between gap-4" method="GET" action="{{ route('marketing.orders.index') }}">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">search</span>
            <input class="border-none focus:ring-0 text-sm" name="q" placeholder="Cari customer, mobil, atau kode order" type="text" value="{{ request('q') }}"/>
          </div>
          <div class="flex items-center gap-3">
            <select class="rounded-full border border-outline-variant px-4 py-2 text-sm" name="status">
              <option value="all">All Status</option>
              @foreach (['pending', 'confirmed', 'paid', 'completed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
            <button class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white" type="submit">Filter</button>
          </div>
        </form>
      </div>
      <div class="overflow-x-auto">
        <table class="mm-data-table w-full text-left">
          <thead>
            <tr class="text-on-surface-variant text-xs font-black uppercase tracking-widest">
              <th class="px-8 py-4">Kode Order</th>
              <th class="px-8 py-4">Customer</th>
              <th class="px-8 py-4">Unit</th>
              <th class="px-8 py-4">Total</th>
              <th class="px-8 py-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @forelse ($orders as $order)
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-8 py-5 font-semibold text-primary">{{ $order->order_reference }}</td>
                <td class="px-8 py-5">{{ $order->user?->name ?? 'Customer' }}</td>
                <td class="px-8 py-5">{{ $order->car?->merk }} {{ $order->car?->tipe }} {{ $order->car?->tahun }}</td>
                <td class="px-8 py-5">{{ \App\Support\CurrencyFormatter::rupiah($order->total) }}</td>
                <td class="px-8 py-5"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-primary">{{ $order->status }}</span></td>
              </tr>
            @empty
              <tr><td class="px-8 py-8 text-sm text-on-surface-variant" colspan="5">Belum ada data pesanan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
