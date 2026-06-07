@php
  $customerName = trim((string) (auth()->user()?->name ?? 'Customer'));
  $overviewCars = ($homeOverviewCars ?? collect())->take(6);
  $overviewImages = [
    'https://lh3.googleusercontent.com/aida-public/AB6AXuDJWY7D72T0YnbrV7D2n-6KfDmwMocsfTyDthwraZt3spp2IM1JjG_pWptdLbGc-vaH42NWoZslEq8CLQ0N39GaWA_-3EAF762tRGUxcNeFwbHmqXsiln7hEIpbjWkKxzrdXcw5SInqe1JxKze0moIYLz2f-NhDaYM5YUuC1Vzc1qS7792dLv3P1gqnwX3-AAezA7Y_MVrTZ8VlRGgV_uLk5KMaoN7WbFg1ON97mmRbRlW536dLH2ACDLQRAPvQ_EPXRXfWkygBgpk',
    'https://lh3.googleusercontent.com/aida-public/AB6AXuB36HJqCR3wyD7bTSSQoSLk9jp_Xayq9466uxS8Ulwp5bicKy9CIRhnt3cDu5yQFtY2faDMQLEVBKvnP-20zlfBb2Gf6MHy6mHBjQfr4853HXkFu6tLmOoGvNbYy8QQx6gXxGdn6iVJUPEpjn-sN-r-SBXj-nWQ6kjUc8gY4As78a7q-TSHFd3bB3IEzsTWuZWsfU6LV36LQJ7-QowQXXQQF0vjJeT72f1LJk-4i-Ng94WLB3VT-u8U8QHVnH3rHVy4Fcj3BiNXt4c',
    'https://lh3.googleusercontent.com/aida-public/AB6AXuD-XQJDRAiht3jmmKlm03BD8d8fkgdRLKKVuVoo9XK2ncsmiR5ElEMj_lTdRbdC9Hf5ABOnHCVnDBnx7vlta4kcEOIeznNW0Ez0L-FuaQabOpkXFLl0yDbHQP-gIJdP0F4mhoV9a_6Vjd1XYqj7Fl3Y-3I9meK4X-DIkTETybpZJb63w0kt-LAkuuV6dYIto14uGMO7d1Mbd9yAkQGBfw9WgGTxwD86KauPENfiFyjQyuk7ZV938YjqEsG_8Az-m4wgyxVl3k6wE6g',
  ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Home Customer') }} | Maharani Mobil</title>
  <meta name="description" content="{{ __('Halaman customer Maharani Mobil untuk melihat unit mobil pilihan yang tersedia.') }}"/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'home', 'overlap' => false])

  <main class="flex-grow">
    <section class="landing-surface-main pb-8 md:pb-10">
      <div class="w-full">
        @if (session('success'))
          <div class="px-4 pt-4 md:px-6 md:pt-5">
            <div class="mb-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
              {{ session('success') }}
            </div>
          </div>
        @endif

        @if (session('error'))
          <div class="px-4 pt-4 md:px-6 md:pt-5">
            <div class="mb-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
              {{ session('error') }}
            </div>
          </div>
        @endif

        <section class="relative -mt-4 overflow-hidden rounded-b-[2.2rem] border-x-0 border-b border-t-0 border-white/10 bg-[linear-gradient(135deg,#08132e_0%,#102a63_58%,#f5a623_130%)] px-4 pb-10 pt-12 text-white shadow-[0_24px_70px_rgba(8,19,46,0.22)] md:-mt-5 md:px-6 md:pb-12 md:pt-16">
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_34%),radial-gradient(circle_at_bottom_left,rgba(245,166,35,0.22),transparent_28%)]"></div>
          <div class="relative mx-auto w-full max-w-[1280px] lg:px-6">
            <div class="max-w-[920px] lg:pr-[340px] xl:max-w-[980px] xl:pr-[380px]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f7c35f]">{{ __('Layanan Customer') }}</p>
              <h1 class="mt-3 font-headline text-[38px] font-extrabold leading-tight sm:text-[48px]">Selamat Datang Kembali di Aplikasi Maharani Mobil, {{ $customerName }}</h1>
              <p class="mt-4 max-w-[640px] text-[15px] leading-7 text-slate-200">
                Temukan mobil pilihan Anda dengan kualitas yang baik, dan rasakan pengalaman dengan pelayanan yang nyaman.
              </p>
            </div>

            <div class="pointer-events-none absolute bottom-[-2px] right-3 hidden lg:block xl:right-6">
              <img
                src="{{ asset('assets/customer-mascot-welcome.png') }}?v={{ filemtime(public_path('assets/customer-mascot-welcome.png')) }}"
                alt="Maskot layanan customer Maharani Mobil"
                class="block h-[255px] w-auto object-contain xl:h-[280px]"
                loading="eager"
              />
            </div>
          </div>
        </section>
      </div>
    </section>

    <section class="landing-surface-inventory pt-8 pb-16 md:pt-10 md:pb-20">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="mb-8 flex items-end justify-between gap-4">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Unit Pilihan') }}</p>
            <h2 class="landing-light-heading mt-2 font-headline text-[34px] font-extrabold leading-tight text-slate-900 sm:text-[42px]">{{ __('Pilih Unit Mobil Terbaik Pilihan Anda') }}</h2>
          </div>
          <a class="inline-flex items-center gap-2 text-[12px] font-semibold text-slate-500 transition hover:text-slate-900" href="{{ route('catalog') }}">
            {{ __('Lihat katalog lengkap') }}
            <span class="material-symbols-outlined text-[17px] text-[#f5a623]">arrow_right_alt</span>
          </a>
        </div>

        @if ($overviewCars->isEmpty())
          <div class="rounded-[1.5rem] border border-slate-200 bg-white p-8 text-sm text-on-surface-variant shadow-sm">
            Stok mobil tersedia belum ada. Silakan cek kembali setelah unit baru ditambahkan oleh tim Maharani Mobil.
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
      </div>
    </section>

    <section class="landing-surface-testimonials pb-16 md:pb-20">
      <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-10 px-4 md:px-6 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-6">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">Overview Customer</p>
          <h2 class="landing-light-heading mt-3 font-headline text-[38px] font-extrabold leading-tight text-slate-900 sm:text-[46px]">Apa kata Customer Maharani?</h2>
          <div class="mt-6 flex items-center gap-4">
            <div class="rounded-full bg-white px-4 py-2 text-[12px] font-semibold text-slate-700 shadow-sm">
              {{ number_format((float) ($featuredReviewAverage ?? 0), 1) }}/5
            </div>
            <p class="landing-light-copy text-[12px] text-slate-500">{{ ($featuredReviewCount ?? 0) }} review customer sudah tayang.</p>
          </div>
          <p class="mt-5 max-w-[520px] text-[15px] leading-7 text-slate-600">
            Lihat dokumentasi pengalaman customer Maharani Mobil terhadap produk dan pelayanan showroom secara langsung.
          </p>
          <a class="mt-6 inline-flex items-center gap-2 rounded-full bg-[#071b47] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#0d2a67]" href="{{ route('reviews.page') }}">
            Lihat Semua Review Customer
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </a>
        </div>

        <div class="lg:col-span-6 lg:pt-6 xl:pt-8">
          @if (($featuredReviews ?? collect())->isNotEmpty())
            <div class="grid gap-4">
              @foreach (($featuredReviews ?? collect()) as $review)
                @php
                  $photos = collect($review->review_photos ?? []);
                @endphp
                <article class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
                  <div class="flex items-start justify-between gap-4">
                    <div>
                      <p class="text-[15px] font-bold text-slate-900">{{ $review->user?->name }}</p>
                      <p class="mt-1 text-[12px] text-slate-500">{{ $review->car?->merk }} {{ $review->car?->tipe }} {{ $review->car?->tahun }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-right">
                      @for ($i = 1; $i <= 5; $i++)
                        <span class="text-[18px] leading-none {{ $i <= (int) $review->rating ? 'text-[#f5a623]' : 'text-slate-300' }}">&#9733;</span>
                      @endfor
                    </div>
                  </div>
                  <p class="mt-4 text-[15px] leading-7 text-slate-700">{{ \Illuminate\Support\Str::limit($review->review_text, 160) }}</p>
                    @if ($photos->isNotEmpty())
                      <div class="mt-4 grid grid-cols-3 gap-3">
                        @foreach ($photos->take(3) as $photo)
                          <div class="flex justify-start">
                            <img alt="Foto review {{ $review->user?->name }}" class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . $photo) }}"/>
                          </div>
                        @endforeach
                      </div>
                    @endif
                </article>
              @endforeach
            </div>
          @else
            <div class="rounded-[1.8rem] border border-slate-200 bg-white p-6 text-sm leading-7 text-slate-600 shadow-sm">
              Review customer akan tampil di sini setelah customer membagikan pengalaman mereka tentang unit dan layanan Maharani Mobil.
            </div>
          @endif
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin dibantu memilih unit mobil yang cocok.'])
  @include('components.ui-system-footer')
</body>
</html>
