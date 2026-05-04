<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Katalog Mobil | Maharani Mobil</title>
  <meta name="description" content="Katalog mobil bekas Maharani Mobil Pekanbaru. Filter merek, tahun, harga, dan status unit."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body min-h-screen flex flex-col">
  <!-- TopNavBar -->
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl docked full-width top-0 sticky z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <nav class="hidden md:flex items-center space-x-8 font-headline tracking-tight">
        <a class="text-[#1A2B4C] font-bold border-b-2 border-[#F5A623] pb-1" href="/catalog">{{ __('Catalog') }}</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="/about">{{ __('About Us') }}</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="/financing">{{ __('Financing') }}</a>
      </nav>
      <div class="flex items-center space-x-4">
        @include('components.nav-tools')
        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-slate-500 font-medium px-4 py-2 hover:text-[#1A2B4C] transition-colors">Logout</button>
          </form>
        @else
          <a class="text-slate-500 font-medium px-4 py-2 hover:text-[#1A2B4C] transition-colors" href="/login">{{ __('Login') }}</a>
          <a class="bg-primary text-white font-bold px-6 py-2 rounded-lg hover:scale-95 transition-transform duration-200" href="/register">{{ __('Register') }}</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="flex-grow max-w-screen-2xl mx-auto w-full px-8 py-8">
    <nav aria-label="Breadcrumb" class="flex mb-8 text-sm font-medium text-on-surface-variant/60">
      <ol class="flex items-center space-x-2">
        <li><a class="hover:text-primary transition-colors" href="/">Home</a></li>
        <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
        <li><a class="text-primary font-bold" href="/catalog">Catalog</a></li>
      </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
      <aside class="w-full lg:w-72 flex-shrink-0">
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm sticky top-24">
          <h2 class="font-headline font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">filter_list</span>
            Filters
          </h2>
          @php
            /**
             * Sidebar filter katalog yang benar-benar fungsional.
             * Parameter GET yang dipakai sama dengan form search di landing:
             * - brand, q, year_min, km_max, price_target
             */
            $brandOptions = $brandOptions ?? collect();
            $selectedBrand = request('brand', 'all');
          @endphp

          <form method="GET" action="{{ route('catalog') }}" class="space-y-6">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">{{ __('Brand') }}</label>
              <select name="brand" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20">
                <option value="all">{{ __('Semua Merek') }}</option>
                @foreach($brandOptions as $merk)
                  <option value="{{ $merk }}" @selected($selectedBrand === $merk)>{{ $merk }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">{{ __('Model') }}</label>
              <input name="q" value="{{ request('q') }}" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="e.g. Fortuner" />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">{{ __('Tahun (Min 2010)') }}</label>
              <input name="year_min" min="2010" inputmode="numeric" value="{{ request('year_min', request('year')) }}" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="2020" />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">{{ __('Kilometer') }}</label>
              <input name="kilometer" inputmode="numeric" value="{{ request('kilometer', request('km_max')) }}" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="e.g. 60000" />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">{{ __('Harga (Rekomendasi Terdekat)') }}</label>
              <input name="price_target" inputmode="numeric" value="{{ request('price_target', request('price')) }}" class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="e.g. 250 (juta)" />
              <p class="mt-2 text-xs text-on-surface-variant/70">
                {{ __('Jika diisi, katalog akan diurutkan dari harga yang paling mendekati.') }}
              </p>
            </div>

            <button class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary-container transition-colors mt-2" type="submit">
              {{ __('Terapkan Filter') }}
            </button>
          </form>
        </div>
      </aside>

      <div class="flex-grow">
        @php
          // Sumber stok katalog: mobil dengan status available (sama dengan overview di home).
          $availableCars = $catalogCars ?? collect();
          $catalogImages = [
            'https://lh3.googleusercontent.com/aida-public/AB6AXuAm_kOv_2zpTXV40_mX9vsauOG9S29LdkyOrObzAHCAnfd-I-Ti83HJ93UwDLMoUPnbc2JVG8_apX-UHDJ7eCQ8jwH8-vcMZmoEPc8vUb4NzfKHVcMfeGLHLR44FGU5moEOl3PP4VZdOXEPZQeU0Cm0UUXgIw5GJvykemBUDeqW6hODi4sJA47--ch7UJXeyLSmMWo2b0OZC4f4giImRxiubBxf72b1lj8jSAQScm3diuaZvRoCwszBUhBzBo7R2-zJsKzleZQXmGk',
            'https://lh3.googleusercontent.com/aida-public/AB6AXuB17A4TfIieJ8Xb-A5HJEOSgt20mUJR4tIf2LC7r3sWAOCmk9ByGCMfottc2E0k8uhMCMIZCB-R7SJrST1HN63m58XbPMyw4VMfeLhH6sYnfi3znIynT738YSCvRAJCtSdhs-oz4TlXcGOHC3fP_feLjWGkc7Bv1G7Bc3OUI-VxeLO9Yfbwop0I2UqT_O-mHL__9_hCdsiNxuGzUHGGoXwxeEPXwcvNVoOLqJuvm81OqU-PUKiZJUDzbi0h0X1OwRVcJ_fSdEXB-I4',
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBjjamtnQUvZKurcGVFHyIxqkuzleHgJlLBObX71YO__sxxXbS6oESz92yAK2-FOPJfteqbhOkDs50t9Bb0eJxr4DES59YBpN3oo21iiwKmGVhoCBfU4oAXJprLSsOjymMHYsZqeYUE7YlWlBLUo7Chxt4FELeVDmJqDZgX2UK9Z9q6XIhyu7Tic-194OoIag7E_-xYExNNoiy17bhQ_pRJ_mRXdtycYJ4zOceCzsAWKE1d4jtxdzYsGPy522iDvNRG51bHlPEcEQ8',
            'https://lh3.googleusercontent.com/aida-public/AB6AXuB3VLBiwqSXrHk0fuIH6WaClWvG85BDh7oI0DnPDzLX0jC7jrgNqUzZrCun4ihizPfzpZyNxlxi-q3wOJ_sjD12BRbnkOtJrbxuxJq2B1aEIwYigiu_Q_4p82iUdCsZlI_QCTRp_cM8qw9aykZF_pEiDyPfavgFNEpkmam1KvHYKLbNiHx1cdYtMDeUDcyEpzlHAG-g1TigerlxkZNH2pNM4Jb8rsrX3QD0WzgwXlz20W9x_8CXmFPOf0M4Y8-OcOCS_jOwTqTl4IM',
          ];
        @endphp

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 class="font-headline font-extrabold text-3xl text-primary tracking-tight">Available</h1>
            <p class="text-on-surface-variant text-sm mt-1">Showing {{ $availableCars->count() }} available vehicles</p>
          </div>
          <div class="flex items-center gap-4 bg-surface-container-lowest p-2 rounded-xl shadow-sm">
            <span class="text-xs font-bold uppercase tracking-widest pl-2">Sort By</span>
            <select class="bg-transparent border-none text-sm font-semibold focus:ring-0" disabled>
              <option>Newest Listed</option>
            </select>
          </div>
        </div>

        @if ($availableCars->isEmpty())
          <div class="bg-surface-container-lowest rounded-xl p-8 text-on-surface-variant">
            Stok mobil pada katalog belum tersedia. Tambahkan data mobil terlebih dahulu dari panel supervisor.
          </div>
        @else
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
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

        <section class="mt-16">
          <h2 class="font-headline font-extrabold text-2xl text-primary mb-6">UI States</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Loading</p>
              <div class="space-y-3">
                <div class="h-4 bg-surface-container-high rounded-full w-3/4 animate-pulse"></div>
                <div class="h-4 bg-surface-container-high rounded-full w-1/2 animate-pulse"></div>
                <div class="h-32 bg-surface-container-high rounded-xl animate-pulse"></div>
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Error</p>
              <div class="flex items-center gap-3 text-error">
                <span class="material-symbols-outlined">error</span>
                <span class="font-semibold">Gagal memuat data. Coba lagi.</span>
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Empty</p>
              <div class="text-on-surface-variant text-sm">
                Belum ada unit sesuai filter Anda. Reset filter untuk melihat semua unit.
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30 relative">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Toast</p>
              <div class="absolute right-4 bottom-4 bg-primary text-white text-xs font-bold px-3 py-2 rounded-full shadow-lg">
                Unit ditambahkan ke keranjang
              </div>
              <div class="text-on-surface-variant text-sm">Contoh notifikasi toast pada aksi cepat.</div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <footer class="bg-[#031636] dark:bg-[#03163f] w-full py-12 mt-auto">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-12 w-full max-w-screen-2xl mx-auto">
      <div class="space-y-4">
        <div class="text-white font-black italic text-2xl tracking-tighter">Maharani Mobil</div>
        <p class="text-slate-400 text-xs uppercase tracking-widest leading-loose font-label">© 2026 Maharani Mobil Pekanbaru.<br/>The Digital Concierge.</p>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Navigation</h5>
        <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/catalog">Catalog</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/financing">Financing</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/about">About Us</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/test-drive">Contact</a></li>
        </ul>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Support</h5>
        <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/privacy">Privacy Policy</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/terms">Terms of Service</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/faq">Cookie Settings</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="/test-drive">Contact Support</a></li>
        </ul>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Connect</h5>
        <div class="flex gap-4">
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">share</span>
          </a>
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">alternate_email</span>
          </a>
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">call</span>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <div class="fixed bottom-8 right-8 z-50 md:hidden">
    <button class="bg-secondary text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center glass-nav ring-4 ring-secondary/20" aria-label="Search">
      <span class="material-symbols-outlined text-3xl">search</span>
    </button>
  </div>
@include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan mobil yang ada di website.'])
@include('components.ui-system-footer')
</body>
</html>
