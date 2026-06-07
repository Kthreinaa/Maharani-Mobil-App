<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen Produk | Marketing</title>
  <meta name="description" content="Kelola daftar produk mobil bekas Maharani Mobil dengan data aktual."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4">
      <a class="text-xl font-bold text-[#1A2B4C]" href="{{ route('marketing.dashboard') }}">Maharani Mobil</a>
    </div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.dashboard') }}"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="{{ route('marketing.products.index') }}"><span class="material-symbols-outlined">directions_car</span>Manajemen Produk</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.orders.index') }}"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
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
        <h1 class="text-3xl font-extrabold text-primary">Manajemen Produk</h1>
        <p class="text-on-surface-variant">Cari, filter, dan pantau semua unit yang tampil di katalog.</p>
      </div>
      <a class="px-6 py-2 rounded-full bg-primary text-white font-semibold" href="{{ route('marketing.products.upload') }}">Tambah Unit</a>
    </header>

    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
      <div class="px-8 py-6 border-b border-slate-50">
        <form class="flex flex-col md:flex-row md:items-center md:justify-between gap-4" method="GET" action="{{ route('marketing.products.index') }}">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">search</span>
            <input class="border-none focus:ring-0 text-sm" name="q" placeholder="Cari nama mobil atau nomor unit" type="text" value="{{ request('q') }}"/>
          </div>
          <div class="flex items-center gap-3 text-sm text-on-surface-variant">
            <span>Status</span>
            <select class="rounded-full border border-outline-variant px-4 py-2" name="status">
              <option value="all">All</option>
              <option value="available" @selected(request('status') === 'available')>Available</option>
              <option value="reserved" @selected(request('status') === 'reserved')>Reserved</option>
              <option value="sold" @selected(request('status') === 'sold')>Sold</option>
            </select>
            <button class="rounded-full bg-primary px-4 py-2 font-semibold text-white" type="submit">Filter</button>
          </div>
        </form>
      </div>
      <div class="overflow-x-auto">
        <table class="mm-data-table w-full text-left">
          <thead>
            <tr class="text-on-surface-variant text-xs font-black uppercase tracking-widest">
              <th class="px-8 py-4">Unit</th>
              <th class="px-8 py-4">Harga</th>
              <th class="px-8 py-4">KM</th>
              <th class="px-8 py-4">Status</th>
              <th class="px-8 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @forelse ($cars as $car)
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-8 py-5">
                  <p class="font-bold text-primary">{{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}</p>
                  <p class="text-xs text-on-surface-variant">{{ $car->kode_unit }}</p>
                </td>
                <td class="px-8 py-5">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</td>
                <td class="px-8 py-5">{{ number_format((int) $car->kilometer, 0, ',', '.') }} KM</td>
                <td class="px-8 py-5"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-primary">{{ $car->status }}</span></td>
                <td class="px-8 py-5 text-right">
                  <a class="px-3 py-1 rounded-lg border border-outline-variant text-xs font-bold" href="{{ route('cars.show', $car->id) }}">Preview</a>
                </td>
              </tr>
            @empty
              <tr>
                <td class="px-8 py-8 text-sm text-on-surface-variant" colspan="5">Belum ada produk yang cocok dengan filter saat ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
