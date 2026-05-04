<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('Landing Meta Title') }}</title>
  <meta name="description" content="{{ __('Landing Meta Description') }}" />
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700" rel="stylesheet" />
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet" />
</head>
<body class="bg-white text-slate-900 dark:bg-[#020617] dark:text-slate-100">
  <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur dark:border-white/10 dark:bg-[#020617]/95">
    <nav class="mx-auto flex w-full max-w-[1280px] items-center justify-between px-4 py-4 md:px-6">
      <a class="font-headline text-[22px] font-extrabold tracking-tight text-[#111827] dark:text-white" href="/">MaharaniMobil</a>

      <div class="hidden items-center gap-8 text-[13px] font-medium md:flex">
        <a class="border-b-2 border-[#f5a623] pb-1 text-[#111827] dark:text-white" href="/">{{ __('Home') }}</a>
        <a class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white" href="/about">{{ __('About Us') }}</a>
        <a class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white" href="/financing">{{ __('Financing') }}</a>
      </div>

      <div class="flex items-center gap-3">
        @include('components.nav-tools')

        @auth
          @php
            $dashboardUrl = match(auth()->user()->role) {
              'supervisor' => '/supervisor/dashboard',
              'marketing' => '/marketing/dashboard',
              'owner' => '/owner/dashboard',
              default => '/home',
            };
          @endphp
          <a class="hidden text-[13px] font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white sm:inline" href="{{ $dashboardUrl }}">Dashboard</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-full border border-slate-300 px-4 py-1.5 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">Logout</button>
          </form>
        @else
          <a class="text-[13px] font-medium text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
          <a class="rounded-full bg-[#071b47] px-4 py-1.5 text-[12px] font-bold text-white transition hover:bg-[#0e2d72] dark:bg-[#f5a623] dark:text-[#091226] dark:hover:bg-[#f7bf55]" href="{{ route('register') }}">{{ __('Register') }}</a>
        @endauth
      </div>
    </nav>
  </header>

  <main>
    <section class="landing-hero-surface relative min-h-[650px] overflow-hidden lg:min-h-[760px]">
      <div class="absolute inset-0">
        <img alt="Hero Car" class="h-full w-full object-cover" src="{{ asset('assets/landing-hero.jpg') }}?v={{ filemtime(public_path('assets/landing-hero.jpg')) }}" />
        <div class="landing-hero-overlay absolute inset-0"></div>
      </div>

      <div class="relative mx-auto grid min-h-[650px] w-full max-w-[1280px] content-center grid-cols-1 gap-10 px-4 py-16 md:px-6 lg:min-h-[760px] lg:grid-cols-12 lg:items-center lg:py-20">
        <div class="lg:col-span-7">
          <h1 class="font-headline text-[48px] font-extrabold leading-[0.95] tracking-tight text-white sm:text-[64px] lg:text-[72px]">
            {{ __('The Digital') }}<br />
            <span class="text-[#f5a623]">{{ __('Concierge') }}</span> {{ __('For Your') }}<br />
            {{ __('Next Drive.') }}
          </h1>

          <p class="mt-5 max-w-[560px] text-[16px] leading-relaxed text-slate-200">
            {{ __('Landing Hero Subtitle') }}
          </p>

          <div class="mt-8 flex flex-wrap gap-4">
            <a class="inline-flex items-center rounded-xl bg-[#f5a623] px-7 py-3 text-[13px] font-bold text-[#121826] transition hover:brightness-105" href="/catalog">{{ __('See Catalog') }}</a>
            @auth
              <a class="inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-7 py-3 text-[13px] font-bold text-white transition hover:bg-white/20" href="/test-drive">{{ __('Contact Consultant') }}</a>
            @else
              <a class="js-login-required inline-flex items-center rounded-xl border border-white/25 bg-white/10 px-7 py-3 text-[13px] font-bold text-white transition hover:bg-white/20" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">{{ __('Contact Consultant') }}</a>
            @endauth
          </div>

          <div class="mt-10 flex flex-wrap gap-6 text-[12px] font-semibold text-white/95">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-[#f5a623]" style="font-variation-settings: 'FILL' 1;">verified</span>
              <span>{{ __('Unit Terverifikasi') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-[#f5a623]" style="font-variation-settings: 'FILL' 1;">history</span>
              <span>{{ __('Berpengalaman 10+ Tahun') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-[#f5a623]" style="font-variation-settings: 'FILL' 1;">support_agent</span>
              <span>{{ __('Layanan Purna Jual') }}</span>
            </div>
          </div>
        </div>

        <div class="hero-search-column lg:col-span-5" style="background: transparent !important;">
          <div class="hero-search-card w-full editorial-shadow rounded-[2rem] border border-white/20 p-7 md:p-9 dark:border-white/10">
            <h3 class="font-headline text-[30px] font-extrabold text-[#0b1a40] dark:text-white">{{ __('Cari Kendaraan Anda') }}</h3>

            <form method="GET" action="{{ route('catalog') }}" class="mt-6 space-y-5 text-[12px]">
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-300">{{ __('Brand') }}</label>
                <select name="brand" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-medium text-slate-700 focus:border-[#f5a623] focus:ring-[#f5a623] dark:border-slate-700 dark:bg-[#071538] dark:text-slate-100">
                  <option value="all">{{ __('Semua Merek') }}</option>
                  @foreach(($brandOptions ?? collect()) as $merk)
                    <option value="{{ $merk }}">{{ $merk }}</option>
                  @endforeach
                </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-300">{{ __('Model') }}</label>
                  <input name="q" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-medium text-slate-700 placeholder:text-slate-400 focus:border-[#f5a623] focus:ring-[#f5a623] dark:border-slate-700 dark:bg-[#071538] dark:text-slate-100 dark:placeholder:text-slate-500" placeholder="e.g. Fortuner" />
                </div>
                <div>
                  <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-300">{{ __('Tahun (Min 2010)') }}</label>
                  <input name="year_min" min="2010" inputmode="numeric" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-medium text-slate-700 placeholder:text-slate-400 focus:border-[#f5a623] focus:ring-[#f5a623] dark:border-slate-700 dark:bg-[#071538] dark:text-slate-100 dark:placeholder:text-slate-500" placeholder="2020" />
                </div>
              </div>

              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-300">{{ __('Kilometer & Harga') }}</label>
                <div class="grid grid-cols-2 gap-4">
                  <input name="kilometer" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-medium text-slate-700 placeholder:text-slate-400 focus:border-[#f5a623] focus:ring-[#f5a623] dark:border-slate-700 dark:bg-[#071538] dark:text-slate-100 dark:placeholder:text-slate-500" placeholder="{{ __('Kilometer') }}" inputmode="numeric" />
                  <input name="price_target" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-medium text-slate-700 placeholder:text-slate-400 focus:border-[#f5a623] focus:ring-[#f5a623] dark:border-slate-700 dark:bg-[#071538] dark:text-slate-100 dark:placeholder:text-slate-500" placeholder="{{ __('Harga (contoh: 250 juta)') }}" inputmode="numeric" />
                </div>
              </div>

              <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#071b47] py-3.5 text-[13px] font-bold text-white transition hover:bg-[#0e2d72] dark:bg-[#f5a623] dark:text-[#111827] dark:hover:bg-[#f7bf55]" type="submit">
                <span class="material-symbols-outlined text-[18px]">search</span>
                {{ __('Temukan Unit') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <a class="absolute bottom-6 right-6 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#f5a623] text-[#0a1736] shadow-xl transition hover:scale-105" href="/catalog" data-open-search-modal aria-label="Search">
        <span class="material-symbols-outlined">search</span>
      </a>
    </section>

    <section class="landing-surface-inventory pt-16 pb-10 md:pt-20 md:pb-12">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="mb-12 flex items-end justify-between gap-4">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Our Inventory') }}</p>
            <h2 class="landing-light-heading mt-2 font-headline text-[38px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[48px]">{{ __('Unit Terbaru Pekanbaru') }}</h2>
          </div>
          <a class="inline-flex items-center gap-2 text-[12px] font-semibold text-slate-500 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white" href="/catalog">
            {{ __('Lihat Semua Koleksi') }}
            <span class="material-symbols-outlined text-[17px] text-[#f5a623]">arrow_right_alt</span>
          </a>
        </div>

        <div class="inventory-grid-clean grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3" style="background: transparent !important;">
          @php
            $newestCars = $newestCars ?? collect();
            $fallbackImages = [
              'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?q=80&w=1000&auto=format&fit=crop',
              'https://images.unsplash.com/photo-1632245889029-e406faaa34cd?q=80&w=1000&auto=format&fit=crop',
              'https://images.unsplash.com/photo-1549924231-f129b911e442?q=80&w=1000&auto=format&fit=crop',
              'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1200&auto=format&fit=crop',
            ];

            $formatLandingPrice = function ($value) {
              $price = (float) $value;
              if ($price >= 1000000000) {
                return 'Rp ' . number_format($price / 1000000000, 3, '.', '') . 'M';
              }
              if ($price >= 1000000) {
                return 'Rp ' . number_format($price / 1000000, 0, ',', '.') . 'jt';
              }
              return 'Rp ' . number_format($price, 0, ',', '.');
            };
          @endphp

          @if ($newestCars->isEmpty())
            <div class="rounded-[1.4rem] border border-slate-200 bg-white p-8 text-slate-600 dark:border-white/10 dark:bg-[#0b1120] dark:text-slate-200 md:col-span-2 xl:col-span-3">
              Belum ada unit terbaru yang tersedia. Tambahkan unit dari dashboard supervisor/marketing agar tampil di sini.
            </div>
          @else
            @foreach ($newestCars as $index => $car)
              @php
                $statusKey = strtolower((string) ($car->status ?? 'available'));
                $badgeText = $statusKey === 'reserved' ? 'Reserved' : 'Available';
                $badgeClass = $statusKey === 'reserved'
                  ? 'bg-[#f5a623] text-[#121826]'
                  : 'bg-emerald-400 text-emerald-950';

                $title = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? ''));
                $subtitleParts = array_values(array_filter([
                  $car->transmisi ?? null,
                  $car->warna ?? null,
                ]));
                $subtitle = count($subtitleParts) ? implode(' • ', $subtitleParts) : 'Lihat detail spesifikasi unit';

                $image = (is_array($car->photos) && !empty($car->photos[0]))
                  ? asset('storage/' . $car->photos[0])
                  : $fallbackImages[$index % count($fallbackImages)];

                $isCustomer = auth()->check() && auth()->user()->role === 'customer';
              @endphp

              <article class="landing-inventory-card group overflow-hidden rounded-[1.4rem] border border-slate-200 bg-white editorial-shadow transition duration-500 hover:-translate-y-1 dark:border-white/10 dark:bg-[#0b1120]">
                <div class="relative aspect-[16/9] overflow-hidden">
                  <img alt="{{ $title !== '' ? $title : 'Unit mobil' }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" src="{{ $image }}" />
                  <span class="absolute left-4 top-4 rounded-full px-3 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $badgeText }}</span>
                </div>

                <div class="p-7">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                      <h3 class="landing-inventory-title truncate font-headline text-[22px] font-extrabold leading-[1.15] text-slate-900 dark:text-white sm:text-[26px]">{{ $title !== '' ? $title : ('Unit #' . $car->id) }}</h3>
                      <p class="landing-inventory-meta mt-1 truncate text-[12px] text-slate-500 dark:text-slate-300">{{ $subtitle }}</p>
                    </div>

                    @if ($isCustomer)
                      <form method="POST" action="{{ route('customer.favorites.store', $car->id) }}">
                        @csrf
                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-[#1a376f] dark:text-slate-200" title="Favorit" aria-label="Favorit">
                          <span class="material-symbols-outlined text-[18px]">favorite</span>
                        </button>
                      </form>
                    @elseif(!auth()->check())
                      <a class="js-login-required inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-[#1a376f] dark:text-slate-200" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}" title="Favorit" aria-label="Login untuk favorit">
                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                      </a>
                    @endif
                  </div>

                  <div class="landing-inventory-divider landing-inventory-meta mt-4 grid grid-cols-2 gap-3 border-y border-slate-200 py-3 text-[11px] text-slate-500 dark:border-white/10 dark:text-slate-300">
                    <span class="inline-flex items-center gap-1.5 truncate">
                      <span class="material-symbols-outlined text-[14px]">calendar_today</span>{{ $car->tahun ?? '-' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 truncate">
                      <span class="material-symbols-outlined text-[14px]">speed</span>{{ number_format((int) ($car->kilometer ?? 0), 0, ',', '.') }} KM
                    </span>
                  </div>

                  <div class="mt-5 flex items-end justify-between gap-3">
                    <div class="min-w-0">
                      <p class="landing-inventory-otr text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">HARGA OTR</p>
                      <p class="landing-inventory-price truncate font-headline text-[22px] font-extrabold leading-none text-slate-900 dark:text-white sm:text-[26px]">{{ $formatLandingPrice($car->harga ?? 0) }}</p>
                    </div>
                    <a class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-[#f5a623] text-[#121826] transition hover:scale-110" href="{{ route('cars.show', $car->id) }}" aria-label="Lihat detail">
                      <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                  </div>
                </div>
              </article>
            @endforeach
          @endif
        </div>
      </div>
    </section>

    <section class="landing-surface-main pt-10 pb-16 md:pt-12 md:pb-20">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="mx-auto max-w-[720px] text-center">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('The Maharani Difference') }}</p>
          <h2 class="landing-light-heading mt-3 font-headline text-[40px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[52px]">{{ __('Mengapa Pilih Maharani Mobil?') }}</h2>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-12">
          <article class="relative overflow-hidden rounded-[1.4rem] bg-[#e8eef8] p-8 text-[#0b1a40] dark:bg-[#0f172a] dark:text-white md:col-span-8 md:p-10">
            <img alt="Engine" class="absolute inset-0 h-full w-full object-cover opacity-20" src="https://images.unsplash.com/photo-1487754180451-c456f719a1fc?q=80&w=1200&auto=format&fit=crop" />
            <div class="relative">
              <span class="material-symbols-outlined text-[38px] text-[#f5a623]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
              <h3 class="mt-3 font-headline text-[36px] font-extrabold leading-tight">{{ __('Garansi & Keamanan Unit') }}</h3>
              <p class="mt-4 max-w-[540px] text-[14px] leading-relaxed text-slate-700 dark:text-slate-200">{{ __('Setiap unit melalui 175 titik inspeksi ketat. Kami memberikan jaminan bebas banjir dan bebas tabrak untuk setiap kilometer yang Anda tempuh.') }}</p>
            </div>
          </article>

          <article class="rounded-[1.4rem] border border-slate-200 bg-white p-8 dark:border-white/10 dark:bg-[#0b1120] md:col-span-4 md:p-10">
            <span class="material-symbols-outlined text-[38px] text-[#f5a623]">visibility</span>
            <h3 class="mt-3 font-headline text-[34px] font-extrabold leading-tight text-slate-900 dark:text-white">{{ __('Transparansi Harga') }}</h3>
            <p class="mt-4 text-[14px] leading-relaxed text-slate-500 dark:text-slate-300">{{ __('Tidak ada biaya tersembunyi. Semua riwayat servis dan dokumen kendaraan tersedia untuk Anda tinjau kapan saja.') }}</p>
          </article>

          <article class="rounded-[1.4rem] bg-[#f5a623] p-8 md:col-span-5 md:p-10">
            <span class="material-symbols-outlined text-[38px] text-[#111827]" style="font-variation-settings: 'FILL' 1;">electric_bolt</span>
            <h3 class="mt-3 font-headline text-[34px] font-extrabold leading-tight text-[#111827]">{{ __('Proses Cepat & Mudah') }}</h3>
            <p class="mt-4 text-[14px] leading-relaxed text-[#1f2937]">{{ __('Persetujuan kredit dalam hitungan jam. Kami mengurus semua dokumen dari awal hingga unit terparkir di garasi Anda.') }}</p>
          </article>

          <article class="rounded-[1.4rem] bg-[#e5e7eb] p-8 dark:bg-[#17346d] md:col-span-7 md:p-10">
            <span class="material-symbols-outlined text-[38px] text-slate-400 dark:text-slate-500">directions_car</span>
            <h3 class="mt-3 font-headline text-[34px] font-extrabold leading-tight text-slate-900 dark:text-white">{{ __('Layanan Home Test Drive') }}</h3>
            <p class="mt-4 text-[14px] leading-relaxed text-slate-600 dark:text-slate-300">{{ __('Sibuk? Biarkan kami membawa unit impian langsung ke depan pintu rumah Anda di area Pekanbaru.') }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="landing-surface-testimonials pt-10 pb-20 md:pt-12 md:pb-20">
      <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-10 px-4 md:px-6 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-6">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Testimonials') }}</p>
          <h2 class="landing-light-heading mt-3 font-headline text-[42px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[52px]">{{ __('Apa Kata Pemilik Kendaraan Maharani?') }}</h2>

          <div class="mt-6 flex items-center gap-4">
            <div class="flex -space-x-2">
              <img class="h-8 w-8 rounded-full border-2 border-white object-cover dark:border-[#020617]" src="https://i.pravatar.cc/80?img=11" alt="Client 1" />
              <img class="h-8 w-8 rounded-full border-2 border-white object-cover dark:border-[#020617]" src="https://i.pravatar.cc/80?img=22" alt="Client 2" />
              <img class="h-8 w-8 rounded-full border-2 border-white object-cover dark:border-[#020617]" src="https://i.pravatar.cc/80?img=33" alt="Client 3" />
            </div>
            <p class="landing-light-copy text-[12px] text-slate-500 dark:text-slate-300">{{ __('Bergabunglah dengan 5,000+ pelanggan puas kami.') }}</p>
          </div>
        </div>

        <div class="relative lg:col-span-6">
          <article class="relative z-10 rounded-[2rem] border border-slate-200 bg-white p-8 dark:border-white/10 dark:bg-[#0b1120] md:p-10">
            <span class="material-symbols-outlined absolute right-8 top-6 text-[52px] text-[#f5a623]/25">format_quote</span>

            <div class="mb-5 flex gap-0.5 text-[#f5a623]">
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
            </div>

            <p class="text-[17px] leading-relaxed text-slate-700 dark:text-slate-100">{{ __('Pengalaman membeli mobil bekas yang paling berkelas di Pekanbaru. Sales person sangat informatif dan tidak memaksa. Unit diantar dalam kondisi sangat bersih seperti baru.') }}</p>

            <div class="mt-7 flex items-center gap-3">
              <img class="h-10 w-10 rounded-full object-cover" src="https://i.pravatar.cc/90?img=52" alt="Dr. Andi" />
              <div>
                <p class="text-[14px] font-bold text-slate-900 dark:text-white">Dr. Andi Wijaya</p>
                <p class="text-[12px] text-slate-500 dark:text-slate-300">{{ __('Pemilik Toyota Land Cruiser') }}</p>
              </div>
            </div>
          </article>

          <div class="absolute -bottom-4 -right-4 -z-0 hidden h-full w-full rounded-[2rem] bg-[#f5a623]/20 sm:block"></div>
        </div>
      </div>
    </section>

    <section class="landing-surface-seo py-20">
      <div class="mx-auto w-full max-w-[920px] px-4 md:px-6">
        <h2 class="landing-light-heading text-center font-headline text-[34px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[44px]">{{ __('Pusat Jual Beli Mobil Bekas Berkualitas di Pekanbaru') }}</h2>

        <div class="landing-light-copy mt-8 space-y-5 text-[15px] leading-loose text-slate-600 dark:text-slate-300">
          <p>{{ __('Maharani Mobil Pekanbaru telah berdiri selama lebih dari satu dekade melayani kebutuhan otomotif masyarakat Riau. Sebagai penyedia mobil bekas Pekanbaru yang terpercaya, kami memahami bahwa membeli kendaraan bukan sekadar transaksi, melainkan sebuah investasi jangka panjang.') }}</p>
          <p>{{ __('Kami menyediakan berbagai pilihan kendaraan mulai dari MPV keluarga seperti Toyota Avanza dan Mitsubishi Xpander, hingga unit premium seperti BMW dan Mercedes-Benz. Seluruh inventaris kami telah melewati proses multi-point inspection yang ketat untuk memastikan standar kualitas "The Digital Concierge" tetap terjaga.') }}</p>
          <p>{{ __('Terletak strategis di jantung kota Pekanbaru, showroom kami menawarkan pengalaman belanja yang nyaman dengan fasilitas purna jual yang lengkap. Baik Anda mencari mobil pertama atau ingin melakukan trade-in, tim ahli kami siap membantu Anda menemukan solusi finansial terbaik yang sesuai dengan anggaran Anda.') }}</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="bg-[#03163f] py-14 text-white dark:bg-[#020617]">
    <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-10 px-4 md:grid-cols-4 md:px-6">
      <div>
        <h3 class="font-headline text-[38px] font-extrabold italic">Maharani Mobil.</h3>
        <p class="mt-4 max-w-[260px] text-[12px] leading-relaxed text-slate-300">{{ __('Solusi otomotif premium dan terpercaya di Pekanbaru sejak 2014. Melayani dengan hati, mengantar dengan bangga.') }}</p>
      </div>

      <div>
        <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">QUICK LINKS</h4>
        <ul class="mt-4 space-y-2 text-[12px]">
          <li><a class="underline transition hover:text-[#f5a623]" href="/catalog">{{ __('Catalog') }}</a></li>
          <li><a class="underline transition hover:text-[#f5a623]" href="/privacy">{{ __('Privacy Policy') }}</a></li>
          <li><a class="underline transition hover:text-[#f5a623]" href="/terms">{{ __('Our Showroom') }}</a></li>
          <li><a class="underline transition hover:text-[#f5a623]" href="{{ route('login') }}">{{ __('Contact Support') }}</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">OFFICE</h4>
        <p class="mt-4 text-[12px] leading-relaxed text-slate-300">Jl. Soekarno - Hatta No. 88<br />Marpoyan Damai, Pekanbaru<br />Riau 28282</p>
      </div>

      <div>
        <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">NEWSLETTER</h4>
        <p class="mt-4 text-[11px] text-slate-300">{{ __('DAFTARKAN INFO UNIT TERBARU') }}</p>

        <div class="mt-3 flex overflow-hidden rounded-lg border border-slate-700">
          <input class="w-full bg-transparent px-3 py-2 text-[12px] text-white placeholder:text-slate-400 focus:outline-none" placeholder="{{ __('Your Email') }}" />
          <button class="inline-flex items-center bg-[#f5a623] px-3 text-[#061c4d]" type="button">
            <span class="material-symbols-outlined text-[18px]">send</span>
          </button>
        </div>
      </div>
    </div>

    <div class="mx-auto mt-10 w-full max-w-[1280px] border-t border-white/10 px-4 pt-6 text-center text-[11px] tracking-[0.2em] text-slate-500 md:px-6">
      © 2026 MAHARANI MOBIL PEKANBARU. THE DIGITAL CONCIERGE.
    </div>
  </footer>

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan mobil yang ada di website.'])
  @include('components.search-modal', ['brandOptions' => $brandOptions ?? collect()])
  @include('components.ui-system-footer')
</body>
</html>


