@php
  $customerName = trim((string) (auth()->user()?->name ?? 'Customer'));
  $overviewCars = ($homeOverviewCars ?? collect())->take(6);
  $customerSummary = $customerSummary ?? ['favorites' => 0, 'orders' => 0, 'reviews' => 0, 'test_drives' => 0];
  $recentActivities = $recentActivities ?? collect();
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

    <section class="landing-surface-main pb-8 md:pb-10">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <a href="{{ route('customer.favorites.index') }}" class="group min-h-[180px] rounded-[1.6rem] border border-rose-200 bg-rose-50/70 px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-500">Favorit</p>
                <p class="mt-3 text-[34px] font-extrabold leading-none text-rose-700">{{ number_format((int) ($customerSummary['favorites'] ?? 0)) }}</p>
                <p class="mt-3 max-w-[160px] text-[13px] leading-6 text-rose-600/90">Unit tersimpan untuk Anda pantau lagi.</p>
              </div>
              <span class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-600 material-symbols-outlined text-[28px] font-black" style="font-variation-settings:'FILL' 1,'wght' 700,'GRAD' 0,'opsz' 24;">favorite</span>
            </div>
          </a>

          <a href="{{ route('customer.orders.index') }}" class="group min-h-[180px] rounded-[1.6rem] border border-amber-200 bg-amber-50/75 px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600">Pesanan</p>
                <p class="mt-3 text-[34px] font-extrabold leading-none text-amber-700">{{ number_format((int) ($customerSummary['orders'] ?? 0)) }}</p>
                <p class="mt-3 max-w-[160px] text-[13px] leading-6 text-amber-700/85">Riwayat order yang sudah Anda buat.</p>
              </div>
              <span class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 material-symbols-outlined text-[28px] font-black" style="font-variation-settings:'FILL' 1,'wght' 700,'GRAD' 0,'opsz' 24;">shopping_bag</span>
            </div>
          </a>

          <a href="{{ route('reviews.page') }}" class="group min-h-[180px] rounded-[1.6rem] border border-sky-200 bg-sky-50/75 px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-600">Review</p>
                <p class="mt-3 text-[34px] font-extrabold leading-none text-sky-700">{{ number_format((int) ($customerSummary['reviews'] ?? 0)) }}</p>
                <p class="mt-3 max-w-[160px] text-[13px] leading-6 text-sky-700/85">Jumlah review yang sudah Anda bagikan.</p>
              </div>
              <span class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-100 text-sky-600 material-symbols-outlined text-[28px] font-black" style="font-variation-settings:'FILL' 1,'wght' 700,'GRAD' 0,'opsz' 24;">rate_review</span>
            </div>
          </a>

          <a href="{{ route('customer.test-drives.index') }}" class="group min-h-[180px] rounded-[1.6rem] border border-emerald-200 bg-emerald-50/75 px-5 py-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">Test Drive</p>
                <p class="mt-3 text-[34px] font-extrabold leading-none text-emerald-700">{{ number_format((int) ($customerSummary['test_drives'] ?? 0)) }}</p>
                <p class="mt-3 max-w-[160px] text-[13px] leading-6 text-emerald-700/85">Jadwal test drive yang pernah Anda ajukan.</p>
              </div>
              <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 material-symbols-outlined text-[28px] font-black" style="font-variation-settings:'FILL' 1,'wght' 700,'GRAD' 0,'opsz' 24;">directions_car</span>
            </div>
          </a>
        </div>
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

    <section class="landing-surface-testimonials pt-6 pb-16 md:pt-8 md:pb-20">
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

        <div class="relative lg:col-span-6 lg:pt-4 xl:pt-6">
          @if (($featuredReviews ?? collect())->isNotEmpty())
            @php
              $latestReview = ($featuredReviews ?? collect())->first();
              $overviewReviewPhotos = ($featuredReviews ?? collect())
                  ->flatMap(fn ($review) => collect($review->review_photos ?? []))
                  ->filter(fn ($photo) => filled($photo))
                  ->take(8)
                  ->values();
              $latestReviewRating = max(0, min(5, (int) round((float) ($latestReview?->rating ?? 0))));
            @endphp
            <article class="relative z-10 overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
              <div class="border-b border-slate-200 bg-[linear-gradient(135deg,#08132e_0%,#0d214f_100%)] px-6 py-5 text-white md:px-7">
                <div class="flex flex-col gap-3">
                  <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Review Terbaru</p>
                    <h3 class="mt-2 font-headline text-[24px] font-extrabold">Ringkasan Pengalaman Customer</h3>
                    <p class="mt-2 max-w-[520px] text-sm leading-6 text-slate-200">Komentar terbaru customer tampil di satu box overview, sementara dokumentasi foto customer lain tetap berjajar rapi di bawahnya.</p>
                  </div>
                </div>
              </div>

              <div class="px-6 py-6 md:px-7">
                <article>
                  <div class="flex items-start justify-between gap-4">
                    <div>
                      <p class="text-[15px] font-bold text-slate-900">{{ $latestReview->user?->name }}</p>
                      <p class="mt-1 text-[12px] text-slate-500">{{ $latestReview->car?->merk }} {{ $latestReview->car?->tipe }} {{ $latestReview->car?->tahun }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-right">
                      @for ($i = 1; $i <= 5; $i++)
                        <svg aria-hidden="true" class="h-[18px] w-[18px] {{ $i <= $latestReviewRating ? 'text-[#f5a623]' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                      @endfor
                    </div>
                  </div>

                  <p class="mt-4 text-[15px] leading-7 text-slate-700">{{ \Illuminate\Support\Str::limit($latestReview->review_text, 170) }}</p>

                  @if ($overviewReviewPhotos->isNotEmpty())
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                      @foreach ($overviewReviewPhotos as $photo)
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-100">
                          <img alt="Foto dokumentasi review customer" class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . $photo) }}"/>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </article>
              </div>
            </article>
          @else
            <article class="relative z-10 rounded-[2rem] border border-slate-200 bg-white p-8 md:p-10">
              <p class="text-[17px] leading-relaxed text-slate-700">
                Review customer Maharani Mobil akan tampil di sini setelah customer membagikan pengalaman mereka tentang unit dan layanan showroom.
              </p>
            </article>
          @endif
        </div>
      </div>
    </section>

    <section class="landing-surface-main pb-16 md:pb-20">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 bg-[linear-gradient(135deg,#071b47_0%,#12306a_100%)] px-6 py-5 text-white md:px-7">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Aktivitas Terbaru</p>
                <h2 class="mt-2 font-headline text-[26px] font-extrabold">Riwayat Aktivitas</h2>
                <p class="mt-2 max-w-[620px] text-sm leading-6 text-slate-200">Pantau kembali aktivitas terakhir Anda, mulai dari menyimpan unit favorit, membuat pesanan, menjadwalkan test drive, hingga mengirim review.</p>
              </div>
              <div class="rounded-full bg-white/10 px-4 py-2 text-[12px] font-semibold text-slate-100 backdrop-blur-sm">
                {{ $recentActivities instanceof \Illuminate\Support\Collection ? $recentActivities->count() : count($recentActivities) }} aktivitas terbaru
              </div>
            </div>
          </div>

          <div class="px-6 py-6 md:px-7">
            @if (($recentActivities instanceof \Illuminate\Support\Collection ? $recentActivities->isNotEmpty() : !empty($recentActivities)))
              <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach ($recentActivities as $activity)
                  <a href="{{ $activity['href'] }}" class="group rounded-[1.4rem] border border-slate-200 bg-slate-50/60 px-5 py-4 transition hover:border-slate-300 hover:bg-white">
                    <div class="flex items-start gap-4">
                      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#071b47] text-white">
                        <span class="material-symbols-outlined text-[22px]">{{ $activity['icon'] }}</span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                          <div>
                            <p class="text-[15px] font-bold text-slate-900">{{ $activity['title'] }}</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">{{ $activity['detail'] }}</p>
                          </div>
                          <span class="shrink-0 text-[12px] font-semibold text-slate-400">{{ $activity['time_label'] }}</span>
                        </div>
                        <div class="mt-3 inline-flex items-center gap-2 text-[12px] font-semibold text-[#0d2a67]">
                          Lihat detail
                          <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </div>
                      </div>
                    </div>
                  </a>
                @endforeach
              </div>
            @else
              <div class="rounded-[1.5rem] border border-dashed border-slate-200 bg-slate-50 px-5 py-6 text-sm leading-7 text-slate-500">
                Aktivitas terbaru Anda akan tampil di sini setelah mulai menyimpan favorit, membuat pesanan, menjadwalkan test drive, atau mengirim review customer.
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin dibantu memilih unit mobil yang cocok.'])
  @include('components.ui-system-footer')
</body>
</html>
