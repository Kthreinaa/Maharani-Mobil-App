@php
  $pageTitle = __('Keranjang') . ' | Maharani Mobil';
  $carName = $car ? trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? '')) : null;
  $carImage = $car && is_array($car->photos) && !empty($car->photos[0])
    ? asset('storage/' . $car->photos[0])
    : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?q=80&w=1600&auto=format&fit=crop';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $pageTitle }}</title>
  <meta name="description" content="{{ __('Keranjang pembelian unit Maharani Mobil sebelum checkout.') }}"/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'catalog', 'utilityActive' => 'cart', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <div class="mb-8 flex items-center justify-between gap-4">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Customer Purchase Flow') }}</p>
          <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">{{ __('Keranjang Unit') }}</h1>
        </div>
        <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('catalog') }}">{{ __('Kembali ke katalog') }}</a>
      </div>

      @if (session('error'))
        <div class="mb-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ session('error') }}
        </div>
      @endif

      @if (!$car)
        <section class="rounded-[2rem] border border-slate-200 bg-white p-10 text-center shadow-sm">
          <span class="material-symbols-outlined text-[52px] text-slate-300">shopping_cart</span>
          <h2 class="mt-4 text-2xl font-extrabold text-primary">{{ __('Belum ada unit di keranjang') }}</h2>
          <p class="mx-auto mt-3 max-w-xl text-on-surface-variant">{{ __('Pilih satu unit dari katalog terlebih dahulu, lalu lanjutkan ke proses pembelian, pembayaran, dan tracking pesanan.') }}</p>
          <a class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 text-sm font-bold text-white" href="{{ route('catalog') }}">{{ __('Jelajahi katalog') }}</a>
        </section>
      @else
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <section class="lg:col-span-2 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-6 md:flex-row">
              <div class="overflow-hidden rounded-[1.5rem] bg-slate-100 md:w-80">
                <img alt="{{ $carName }}" class="h-full w-full object-cover" src="{{ $carImage }}"/>
              </div>
              <div class="flex-1">
                <div class="mb-4 flex flex-wrap items-center gap-3">
                  <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ __('READY FOR CHECKOUT') }}</span>
                  <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('1 unit dipilih') }}</span>
                </div>
                <h2 class="text-3xl font-extrabold leading-tight text-primary">{{ $carName }}</h2>
                <p class="mt-3 text-on-surface-variant">{{ $car->transmisi ?? '-' }} • {{ number_format((int) ($car->kilometer ?? 0), 0, ',', '.') }} KM • {{ $car->warna ?? '-' }}</p>
                <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4">
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-widest text-slate-400">Tahun</p>
                    <p class="mt-1 font-bold text-primary">{{ $car->tahun }}</p>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-widest text-slate-400">{{ __('Fuel') }}</p>
                    <p class="mt-1 font-bold text-primary">{{ $car->bahan_bakar ?? '-' }}</p>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-widest text-slate-400">{{ __('Kode') }}</p>
                    <p class="mt-1 font-bold text-primary">{{ $car->kode_unit ?? '-' }}</p>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-[11px] uppercase tracking-widest text-slate-400">Status</p>
                    <p class="mt-1 font-bold uppercase text-primary">{{ $car->status }}</p>
                  </div>
                </div>
                <div class="mt-6 border-t border-slate-100 pt-6">
                  <p class="text-xs uppercase tracking-[0.18em] text-slate-400">{{ __('Harga Unit') }}</p>
                  <p class="mt-1 text-4xl font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</p>
                </div>
              </div>
            </div>
          </section>

          <aside class="space-y-6">
            <div class="rounded-[2rem] bg-primary p-6 text-white shadow-[0_20px_50px_rgba(8,19,46,0.18)]">
              <h3 class="text-xl font-bold">{{ __('Ringkasan') }}</h3>
              <div class="mt-5 space-y-3 text-sm text-blue-100">
                <div class="flex justify-between">
                  <span>{{ __('Harga unit') }}</span>
                  <span>{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>{{ __('Booking fee') }}</span>
                  <span>{{ \App\Support\CurrencyFormatter::rupiah(0) }}</span>
                </div>
                <div class="flex justify-between border-t border-white/15 pt-3 text-base font-bold text-white">
                  <span>Total</span>
                  <span>{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</span>
                </div>
              </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
              <h3 class="text-lg font-bold text-primary">{{ __('Langkah berikutnya') }}</h3>
              <ol class="mt-4 space-y-3 text-sm text-on-surface-variant">
                <li>{{ __('1. Konfirmasi data pembeli di halaman checkout.') }}</li>
                <li>{{ __('2. Pilih metode pembayaran lalu tunggu validasi supervisor.') }}</li>
                <li>{{ __('3. Pantau verifikasi lewat tracking pesanan.') }}</li>
              </ol>

              @auth
                @if (auth()->user()->role === 'customer')
                  <a class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-secondary-container px-5 py-4 text-sm font-bold text-on-secondary-fixed" href="{{ route('checkout.cash', ['car_id' => $car->id]) }}">{{ __('Lanjut Checkout') }}</a>
                @else
                  <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ __('Checkout hanya tersedia untuk akun customer.') }}</p>
                @endif
              @else
                <a class="js-login-required mt-6 inline-flex w-full items-center justify-center rounded-xl bg-secondary-container px-5 py-4 text-sm font-bold text-on-secondary-fixed" href="{{ route('login') }}" data-popup-message="{{ __('Silakan login sebagai customer untuk melanjutkan checkout.') }}">{{ __('Login untuk checkout') }}</a>
              @endauth

              <a class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-200 px-5 py-4 text-sm font-semibold text-primary" href="{{ route('cars.show', $car->id) }}">{{ __('Lihat detail unit') }}</a>
            </div>
          </aside>
        </div>
      @endif
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan unit ' . ($carName ?? 'yang ada di website') . '.'])
  @include('components.ui-system-footer')
</body>
</html>
