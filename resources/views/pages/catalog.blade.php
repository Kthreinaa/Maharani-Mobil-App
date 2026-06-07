<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Katalog Mobil') }} | Maharani Mobil</title>
  <meta name="description" content="{{ __('Katalog mobil bekas Maharani Mobil Pekanbaru. Filter merek, tahun, harga, dan status unit.') }}"/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'catalog', 'overlap' => false, 'showCatalog' => true])

  <main class="mx-auto flex-grow w-full max-w-[1380px] px-4 py-8 md:px-6 md:py-10">
    @php
      $brandOptions = $brandOptions ?? collect();
      $selectedBrand = request('brand', 'all');
      $availableCars = $catalogCars ?? collect();
      $catalogState = $catalogState ?? ['sort' => 'latest', 'hasFilters' => false, 'activeFilters' => []];
      $selectedSort = $catalogState['sort'] ?? 'latest';
      $hasFilters = $catalogState['hasFilters'] ?? false;
      $activeFilters = $catalogState['activeFilters'] ?? [];
      $homeUrl = auth()->check() && auth()->user()->role === 'customer'
        ? route('customer.home')
        : route('landing');
      $sortLabels = [
        'latest' => 'Terbaru',
        'oldest' => 'Terlama',
        'price_low' => 'Harga Termurah',
        'price_high' => 'Harga Tertinggi',
        'year_newest' => 'Tahun Terbaru',
        'year_oldest' => 'Tahun Terlama',
        'km_low' => 'Kilometer Terendah',
        'km_high' => 'Kilometer Tertinggi',
      ];
      $filterChips = [];

      if (!empty($activeFilters['brand'])) {
        $filterChips[] = 'Merek: ' . $activeFilters['brand'];
      }
      if (!empty($activeFilters['q'])) {
        $filterChips[] = 'Model: ' . $activeFilters['q'];
      }
      if (!empty($activeFilters['year_min'])) {
        $filterChips[] = 'Tahun >= ' . $activeFilters['year_min'];
      }
      if (!empty($activeFilters['kilometer'])) {
        $filterChips[] = 'KM <= ' . number_format((int) $activeFilters['kilometer'], 0, ',', '.');
      }
      if (!empty($activeFilters['price_max'])) {
        $filterChips[] = 'Maks. ' . \App\Support\CurrencyFormatter::rupiah($activeFilters['price_max']);
      }
      if (!empty($activeFilters['price_target'])) {
        $filterChips[] = 'Target ' . \App\Support\CurrencyFormatter::rupiah($activeFilters['price_target']);
      }

      $catalogImages = [
        'https://lh3.googleusercontent.com/aida-public/AB6AXuAm_kOv_2zpTXV40_mX9vsauOG9S29LdkyOrObzAHCAnfd-I-Ti83HJ93UwDLMoUPnbc2JVG8_apX-UHDJ7eCQ8jwH8-vcMZmoEPc8vUb4NzfKHVcMfeGLHLR44FGU5moEOl3PP4VZdOXEPZQeU0Cm0UUXgIw5GJvykemBUDeqW6hODi4sJA47--ch7UJXeyLSmMWo2b0OZC4f4giImRxiubBxf72b1lj8jSAQScm3diuaZvRoCwszBUhBzBo7R2-zJsKzleZQXmGk',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuB17A4TfIieJ8Xb-A5HJEOSgt20mUJR4tIf2LC7r3sWAOCmk9ByGCMfottc2E0k8uhMCMIZCB-R7SJrST1HN63m58XbPMyw4VMfeLhH6sYnfi3znIynT738YSCvRAJCtSdhs-oz4TlXcGOHC3fP_feLjWGkc7Bv1G7Bc3OUI-VxeLO9Yfbwop0I2UqT_O-mHL__9_hCdsiNxuGzUHGGoXwxeEPXwcvNVoOLqJuvm81OqU-PUKiZJUDzbi0h0X1OwRVcJ_fSdEXB-I4',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuBjjamtnQUvZKurcGVFHyIxqkuzleHgJlLBObX71YO__sxxXbS6oESz92yAK2-FOPJfteqbhOkDs50t9Bb0eJxr4DES59YBpN3oo21iiwKmGVhoCBfU4oAXJprLSsOjymMHYsZqeYUE7YlWlBLUo7Chxt4FELeVDmJqDZgX2UK9Z9q6XIhyu7Tic-194OoIag7E_-xYExNNoiy17bhQ_pRJ_mRXdtycYJ4zOceCzsAWKE1d4jtxdzYsGPy522iDvNRG51bHlPEcEQ8',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuB3VLBiwqSXrHk0fuIH6WaClWvG85BDh7oI0DnPDzLX0jC7jrgNqUzZrCun4ihizPfzpZyNxlxi-q3wOJ_sjD12BRbnkOtJrbxuxJq2B1aEIwYigiu_Q_4p82iUdCsZlI_QCTRp_cM8qw9aykZF_pEiDyPfavgFNEpkmam1KvHYKLbNiHx1cdYtMDeUDcyEpzlHAG-g1TigerlxkZNH2pNM4Jb8rsrX3QD0WzgwXlz20W9x_8CXmFPOf0M4Y8-OcOCS_jOwTqTl4IM',
      ];
    @endphp

    <nav aria-label="Breadcrumb" class="mb-8 flex text-sm font-medium text-slate-500">
      <ol class="flex items-center space-x-2">
        <li><a class="transition-colors hover:text-primary" href="{{ $homeUrl }}">{{ __('Beranda') }}</a></li>
        <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
        <li><span class="font-bold text-primary">{{ __('Katalog') }}</span></li>
      </ol>
    </nav>

    <div class="flex flex-col gap-8 xl:flex-row">
      <aside class="w-full xl:w-[320px] xl:flex-shrink-0">
        <div class="sticky top-28 overflow-hidden rounded-[2rem] border border-white/60 bg-white/90 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur-xl">
          <div class="mb-6 flex items-start justify-between gap-4">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[#f5a623]">{{ __('Filter Katalog') }}</p>
              <h2 class="mt-2 flex items-center gap-2 font-headline text-[28px] font-extrabold text-primary">
                <span class="material-symbols-outlined text-[24px]">tune</span>
                {{ __('Cari Unit') }}
              </h2>
              <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('Atur hasil berdasarkan merek, model, tahun, kilometer, dan harga yang paling cocok.') }}</p>
            </div>
            @if ($hasFilters)
              <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-[#f5a623]/12 px-3 text-xs font-bold text-[#d88a00]">
                {{ count($filterChips) }}
              </span>
            @endif
          </div>

          <form method="GET" action="{{ route('catalog') }}" class="space-y-5">
            <input type="hidden" name="sort" value="{{ $selectedSort }}" />

            <div>
              <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Merek') }}</label>
              <select name="brand" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 shadow-inner shadow-white/50 focus:border-[#0b1a40] focus:ring-0">
                <option value="all">{{ __('Semua Merek') }}</option>
                @foreach($brandOptions as $merk)
                  <option value="{{ $merk }}" @selected($selectedBrand === $merk)>{{ $merk }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Model atau Tipe') }}</label>
              <input name="q" value="{{ request('q') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0" placeholder="{{ __('Contoh: Fortuner, Avanza') }}" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
              <div>
                <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Tahun Minimum') }}</label>
                <input name="year_min" min="2010" inputmode="numeric" value="{{ request('year_min', request('year')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0" placeholder="2018" />
              </div>
              <div>
                <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Kilometer Maksimum') }}</label>
                <input name="kilometer" inputmode="numeric" value="{{ request('kilometer', request('km_max')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0" placeholder="60000" />
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
              <div>
                <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Harga Maksimal') }}</label>
                <input name="price_max" inputmode="numeric" value="{{ request('price_max') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0" placeholder="{{ __('250 (juta)') }}" />
              </div>
              <div>
                <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Harga Target') }}</label>
                <input name="price_target" inputmode="numeric" value="{{ request('price_target', request('price')) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0" placeholder="{{ __('300 (juta)') }}" />
                <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('Jika diisi, hasil akan diprioritaskan ke harga yang paling dekat dengan target Anda.') }}</p>
              </div>
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row xl:flex-col">
              <button class="inline-flex flex-1 items-center justify-center rounded-full bg-[#08132e] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_40px_rgba(8,19,46,0.18)] transition hover:translate-y-[-1px] hover:brightness-110" type="submit">
                {{ __('Terapkan Filter') }}
              </button>
              <a class="inline-flex flex-1 items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" href="{{ route('catalog') }}">
                {{ __('Reset Filter') }}
              </a>
            </div>
          </form>
        </div>
      </aside>

      <section class="min-w-0 flex-1">
        <div class="overflow-hidden rounded-[2.2rem] border border-white/60 bg-white/90 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur-xl md:p-7">
          <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#f5a623]">{{ __('Katalog Maharani Mobil') }}</p>
              <h1 class="mt-2 font-headline text-[34px] font-extrabold tracking-tight text-primary md:text-[42px]">{{ __('Unit Tersedia') }}</h1>
              <p class="mt-2 max-w-[680px] text-sm leading-7 text-slate-500">
                {{ __('Menampilkan :count unit yang siap Anda bandingkan. Gunakan filter di samping untuk mempersempit pencarian tanpa keluar dari halaman.', ['count' => $availableCars->count()]) }}
              </p>
            </div>

            <form method="GET" action="{{ route('catalog') }}" class="w-full rounded-[1.6rem] border border-slate-200 bg-slate-50/90 p-4 shadow-sm lg:w-[280px]">
              <input type="hidden" name="brand" value="{{ request('brand', 'all') }}" />
              <input type="hidden" name="q" value="{{ request('q') }}" />
              <input type="hidden" name="year_min" value="{{ request('year_min', request('year')) }}" />
              <input type="hidden" name="kilometer" value="{{ request('kilometer', request('km_max')) }}" />
              <input type="hidden" name="price_max" value="{{ request('price_max') }}" />
              <input type="hidden" name="price_target" value="{{ request('price_target', request('price')) }}" />

              <label class="mb-3 block text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">{{ __('Urutkan Hasil') }}</label>
              <select
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 focus:border-[#0b1a40] focus:ring-0"
                name="sort"
                onchange="this.form.submit()"
              >
                @foreach ($sortLabels as $value => $label)
                  <option value="{{ $value }}" @selected($selectedSort === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </form>
          </div>

          @if ($hasFilters)
            <div class="mt-5 flex flex-wrap items-center gap-2">
              @foreach ($filterChips as $chip)
                <span class="inline-flex items-center rounded-full bg-[#0b1a40]/6 px-3 py-1.5 text-xs font-semibold text-primary">{{ $chip }}</span>
              @endforeach
            </div>
          @endif
        </div>

        @if ($availableCars->isEmpty())
          <div class="mt-6 rounded-[2rem] border border-dashed border-slate-300 bg-white/90 px-6 py-10 text-center shadow-sm">
            <h2 class="font-headline text-2xl font-bold text-primary">{{ __('Tidak ada unit yang cocok') }}</h2>
            <p class="mx-auto mt-3 max-w-[540px] text-sm leading-7 text-slate-500">
              {{ __('Coba longgarkan filter merek, tahun, kilometer, atau target harga agar sistem bisa menampilkan lebih banyak pilihan yang relevan.') }}
            </p>
            <div class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row">
              <a class="inline-flex items-center justify-center rounded-full bg-[#08132e] px-5 py-3 text-sm font-bold text-white transition hover:brightness-110" href="{{ route('catalog') }}">{{ __('Lihat semua unit') }}</a>
              <a class="inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" href="{{ route('faq') }}">{{ __('Butuh bantuan memilih?') }}</a>
            </div>
          </div>
        @else
          <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 2xl:grid-cols-3">
            @foreach ($availableCars as $index => $car)
              @php
                $fallbackImage = $catalogImages[$index % count($catalogImages)];
                $carImage = (is_array($car->photos) && !empty($car->photos[0]))
                  ? asset('storage/' . $car->photos[0])
                  : $fallbackImage;
              @endphp
              <div class="h-full">
                @include('partials.car-card', [
                  'car' => $car,
                  'imageUrl' => $carImage,
                  'showFavorite' => true,
                ])
              </div>
            @endforeach
          </div>
        @endif
      </section>
    </div>
  </main>

  @include('components.public-site-footer')

  <div class="fixed bottom-8 right-8 z-50 md:hidden">
    <button class="bg-secondary text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center glass-nav ring-4 ring-secondary/20" aria-label="Search">
      <span class="material-symbols-outlined text-3xl">search</span>
    </button>
  </div>
@include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan mobil yang ada di website.'])
@include('components.ui-system-footer')
</body>
</html>
