<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen Penawaran | Marketing</title>
  <meta name="description" content="Pantau penawaran masuk customer Maharani Mobil."/>
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
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.orders.index') }}"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="{{ route('marketing.offers.index') }}"><span class="material-symbols-outlined">sell</span>Manajemen Penawaran</a>
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
        <h1 class="text-3xl font-extrabold text-primary">Manajemen Penawaran</h1>
        <p class="text-on-surface-variant">Daftar penawaran customer yang masuk dari halaman detail dan form negosiasi.</p>
      </div>
    </header>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      @forelse ($offers as $offer)
        <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
          <div class="flex items-center justify-between mb-4">
            <span class="font-bold text-primary">{{ $offer->car?->merk }} {{ $offer->car?->tipe }} {{ $offer->car?->tahun }}</span>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-primary">{{ $offer->status }}</span>
          </div>
          <p class="text-sm text-on-surface-variant">Customer: {{ $offer->user?->name ?? 'Customer' }}</p>
          <p class="text-sm text-on-surface-variant mt-1">Offer: {{ \App\Support\CurrencyFormatter::rupiah($offer->offer_price) }}</p>
          @if ($offer->notes)
            <p class="mt-3 text-sm text-on-surface-variant">{{ $offer->notes }}</p>
          @endif
        </div>
      @empty
        <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6 text-sm text-on-surface-variant lg:col-span-3">
          Belum ada penawaran customer yang masuk.
        </div>
      @endforelse
    </section>
  </main>
</body>
</html>
