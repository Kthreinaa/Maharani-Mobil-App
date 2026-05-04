<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home | Maharani Mobil</title>
  <meta name="description" content="Beranda customer Maharani Mobil: rekomendasi unit dan akses cepat."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col dark:bg-slate-900">
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <nav class="hidden md:flex items-center gap-8 font-headline tracking-tight">
        <a class="text-[#1A2B4C] font-bold border-b-2 border-[#F5A623] pb-1" href="/home">{{ __('Home') }}</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/catalog">{{ __('Catalog') }}</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/about">{{ __('About Us') }}</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/financing">{{ __('Financing') }}</a>
      </nav>
      <div class="flex items-center gap-4">
        @include('components.nav-tools')
        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 text-slate-500 hover:text-primary">Logout</button>
          </form>
        @else
          <a class="px-4 py-2 text-slate-500 hover:text-primary" href="/login">{{ __('Login') }}</a>
          <a class="px-6 py-2 bg-primary text-white rounded-full font-bold" href="/register">{{ __('Register') }}</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    @if (session('success'))
      <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
      </div>
    @endif

    <section class="bg-primary rounded-3xl p-8 md:p-12 text-white mb-10">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h1 class="text-4xl md:text-5xl font-extrabold">{{ __('Welcome Back') }}</h1>
          <p class="text-blue-100 mt-2">{{ __('Quick access for test drive, offers, and order tracking.') }}</p>
        </div>
        <div class="flex gap-3">
          @auth
            <a class="bg-secondary-container text-on-secondary-fixed px-6 py-3 rounded-xl font-bold" href="/test-drive">{{ __('Booking Test Drive') }}</a>
          @else
            <a class="js-login-required bg-secondary-container text-on-secondary-fixed px-6 py-3 rounded-xl font-bold" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">{{ __('Booking Test Drive') }}</a>
          @endauth
          <a class="bg-white/10 border border-white/20 px-6 py-3 rounded-xl font-bold" href="/order-tracking">{{ __('Track Order') }}</a>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div class="bg-white rounded-2xl p-6 shadow-xl shadow-blue-900/5">
        <p class="text-xs uppercase tracking-widest text-on-surface-variant">Rekomendasi</p>
        <p class="text-xl font-bold text-primary mt-2">Toyota Fortuner 2022</p>
        <p class="text-sm text-on-surface-variant">Rp 545jt • 14.200 KM</p>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-xl shadow-blue-900/5">
        <p class="text-xs uppercase tracking-widest text-on-surface-variant">Urgency</p>
        <p class="text-xl font-bold text-primary mt-2">Unit Tersisa 1</p>
        <p class="text-sm text-on-surface-variant">Toyota Camry 2022</p>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-xl shadow-blue-900/5">
        <p class="text-xs uppercase tracking-widest text-on-surface-variant">Aksi Cepat</p>
        <p class="text-xl font-bold text-primary mt-2">{{ __('Submit Offer') }}</p>
        @auth
          <a class="text-secondary font-bold" href="/offers">{{ __('Start now') }} →</a>
        @else
          <a class="js-login-required text-secondary font-bold" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">{{ __('Start now') }} →</a>
        @endauth
      </div>
    </section>

    <section>
      @php
        // Menyamakan sumber data overview home dengan stok tersedia di katalog.
        $overviewCars = ($homeOverviewCars ?? $cars ?? collect())->take(6);
        // Placeholder foto ketika data mobil belum punya kolom gambar.
        $overviewImages = [
          'https://lh3.googleusercontent.com/aida-public/AB6AXuDJWY7D72T0YnbrV7D2n-6KfDmwMocsfTyDthwraZt3spp2IM1JjG_pWptdLbGc-vaH42NWoZslEq8CLQ0N39GaWA_-3EAF762tRGUxcNeFwbHmqXsiln7hEIpbjWkKxzrdXcw5SInqe1JxKze0moIYLz2f-NhDaYM5YUuC1Vzc1qS7792dLv3P1gqnwX3-AAezA7Y_MVrTZ8VlRGgV_uLk5KMaoN7WbFg1ON97mmRbRlW536dLH2ACDLQRAPvQ_EPXRXfWkygBgpk',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuB36HJqCR3wyD7bTSSQoSLk9jp_Xayq9466uxS8Ulwp5bicKy9CIRhnt3cDu5yQFtY2faDMQLEVBKvnP-20zlfBb2Gf6MHy6mHBjQfr4853HXkFu6tLmOoGvNbYy8QQx6gXxGdn6iVJUPEpjn-sN-r-SBXj-nWQ6kjUc8gY4As78a7q-TSHFd3bB3IEzsTWuZWsfU6LV36LQJ7-QowQXXQQF0vjJeT72f1LJk-4i-Ng94WLB3VT-u8U8QHVnH3rHVy4Fcj3BiNXt4c',
          'https://lh3.googleusercontent.com/aida-public/AB6AXuD-XQJDRAiht3jmmKlm03BD8d8fkgdRLKKVuVoo9XK2ncsmiR5ElEMj_lTdRbdC9Hf5ABOnHCVnDBnx7vlta4kcEOIeznNW0Ez0L-FuaQabOpkXFLl0yDbHQP-gIJdP0F4mhoV9a_6Vjd1XYqj7Fl3Y-3I9meK4X-DIkTETybpZJb63w0kt-LAkuuV6dYIto14uGMO7d1Mbd9yAkQGBfw9WgGTxwD86KauPENfiFyjQyuk7ZV938YjqEsG_8Az-m4wgyxVl3k6wE6g',
        ];
      @endphp

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-extrabold text-primary">Unit Tersedia</h2>
        <a class="text-secondary font-bold" href="/catalog">Lihat Katalog</a>
      </div>

      @if ($overviewCars->isEmpty())
        <div class="bg-white rounded-2xl p-6 shadow-xl shadow-blue-900/5 text-on-surface-variant">
          Stok mobil tersedia belum ada. Silakan cek kembali setelah data katalog ditambahkan.
        </div>
      @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
          @foreach ($overviewCars as $index => $car)
            @php
              $fallbackImage = $overviewImages[$index % count($overviewImages)];
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
  </main>

  <footer class="bg-[#031636] w-full py-10 mt-auto text-white text-xs uppercase tracking-widest">
    <div class="max-w-screen-2xl mx-auto px-8 flex flex-col md:flex-row justify-between gap-4">
      <span>© 2026 Maharani Mobil Pekanbaru</span>
      <div class="flex gap-6">
        <a class="hover:text-secondary-container" href="/privacy">Privacy</a>
        <a class="hover:text-secondary-container" href="/terms">Terms</a>
      </div>
    </div>
  </footer>
@include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan mobil yang ada di website.'])
@include('components.ui-system-footer')
</body>
</html>


