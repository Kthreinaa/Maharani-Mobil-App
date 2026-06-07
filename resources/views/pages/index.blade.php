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
<body class="overflow-x-hidden bg-white text-slate-900 dark:bg-[#020617] dark:text-slate-100">
  @php
    $customerFavoriteCarIds = auth()->check() && auth()->user()->role === 'customer'
      ? auth()->user()->favoriteCars()->pluck('cars.id')->map(fn ($id) => (int) $id)->all()
      : [];
  @endphp
  @include('components.public-site-header', ['active' => 'home'])

  <main>
    <section class="landing-hero-surface relative min-h-[650px] overflow-hidden lg:min-h-[760px]">
      <div class="absolute inset-0">
        <img alt="Hero Car" class="h-full w-full object-cover" src="{{ asset('assets/landing-hero.jpg') }}?v={{ filemtime(public_path('assets/landing-hero.jpg')) }}" />
        <div class="landing-hero-overlay absolute inset-0"></div>
      </div>

      <div class="relative mx-auto grid min-h-[650px] w-full max-w-[1280px] content-center grid-cols-1 gap-10 px-4 py-16 md:px-6 lg:min-h-[760px] lg:grid-cols-12 lg:items-center lg:py-20">
        <div class="lg:col-span-7">
          <h1 class="font-headline text-[48px] font-extrabold leading-[0.95] tracking-tight text-white sm:text-[64px] lg:text-[72px]">
            Temukan Mobil<br />
            Impian Anda Bersama<br />
            <span class="text-[#f5a623]">Maharani Mobil</span>
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
            <h3 class="font-headline text-[30px] font-extrabold text-white">{{ __('Cari Kendaraan Anda') }}</h3>

            <form method="GET" action="{{ route('catalog') }}" class="mt-6 space-y-5 text-[12px]">
              <div>
                <label class="hero-search-label mb-2 block text-[10px] font-bold uppercase tracking-[0.18em]">{{ __('Brand') }}</label>
                <select name="brand" class="hero-search-field w-full rounded-xl px-4 py-3 text-[13px] font-medium">
                  <option value="all">{{ __('Semua Merek') }}</option>
                  @foreach(($brandOptions ?? collect()) as $merk)
                    <option value="{{ $merk }}">{{ $merk }}</option>
                  @endforeach
                </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="hero-search-label mb-2 block text-[10px] font-bold uppercase tracking-[0.18em]">{{ __('Model') }}</label>
                  <input name="q" class="hero-search-field w-full rounded-xl px-4 py-3 text-[13px] font-medium" placeholder="e.g. Fortuner" />
                </div>
                <div>
                  <label class="hero-search-label mb-2 block text-[10px] font-bold uppercase tracking-[0.18em]">{{ __('Tahun (Min 2010)') }}</label>
                  <input name="year_min" min="2010" inputmode="numeric" class="hero-search-field w-full rounded-xl px-4 py-3 text-[13px] font-medium" placeholder="2020" />
                </div>
              </div>

              <div>
                <label class="hero-search-label mb-2 block text-[10px] font-bold uppercase tracking-[0.18em]">{{ __('Kilometer & Harga') }}</label>
                <div class="grid grid-cols-2 gap-4">
                  <input name="kilometer" class="hero-search-field w-full rounded-xl px-4 py-3 text-[13px] font-medium" placeholder="{{ __('Kilometer') }}" inputmode="numeric" />
                  <input name="price_target" class="hero-search-field w-full rounded-xl px-4 py-3 text-[13px] font-medium" placeholder="{{ __('Harga (contoh: 250 juta)') }}" inputmode="numeric" />
                </div>
              </div>

              <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#f5a623] py-3.5 text-[13px] font-bold text-[#111827] shadow-[0_18px_40px_rgba(245,166,35,0.26)] transition hover:brightness-105 dark:bg-[#f5a623] dark:text-[#111827] dark:hover:bg-[#f7bf55]" type="submit">
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

            $formatLandingPrice = fn ($value) => \App\Support\CurrencyFormatter::rupiah($value);
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
                $detailUrl = route('cars.show', $car->id);
                $shareTitle = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
                $shareUrl = $detailUrl;
                $shareText = 'Lihat unit ' . ($shareTitle !== '' ? $shareTitle : 'Maharani Mobil') . ' di Maharani Mobil.';

                $image = (is_array($car->photos) && !empty($car->photos[0]))
                  ? asset('storage/' . $car->photos[0])
                  : $fallbackImages[$index % count($fallbackImages)];

                $isCustomer = auth()->check() && auth()->user()->role === 'customer';
                $isFavorite = $isCustomer && in_array((int) $car->id, $customerFavoriteCarIds, true);
              @endphp

              <article class="landing-inventory-card group overflow-hidden rounded-[1.4rem] border border-slate-200 bg-white editorial-shadow transition duration-500 hover:-translate-y-1 dark:border-white/10 dark:bg-[#0b1120]">
                <a class="relative block aspect-[16/9] overflow-hidden" href="{{ $detailUrl }}" aria-label="Lihat detail {{ $title !== '' ? $title : ('Unit #' . $car->id) }}">
                  <img alt="{{ $title !== '' ? $title : 'Unit mobil' }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" src="{{ $image }}" />
                  <span class="absolute left-4 top-4 rounded-full px-3 py-1 text-[10px] font-bold {{ $badgeClass }}">{{ $badgeText }}</span>
                </a>

                <div class="p-7">
                  <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                      <h3 class="landing-inventory-title truncate font-headline text-[18px] font-extrabold leading-[1.15] text-slate-900 dark:text-white sm:text-[20px]">{{ $title !== '' ? $title : ('Unit #' . $car->id) }}</h3>
                      <p class="landing-inventory-meta mt-1 truncate text-[11px] text-slate-500 dark:text-slate-300">{{ $subtitle }}</p>
                    </div>

                    <div class="flex items-center gap-1.5">
                      <button
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-[#1a376f] dark:text-slate-200"
                        data-share-car
                        data-share-url="{{ $shareUrl }}"
                        data-share-title="{{ $shareTitle }}"
                        data-share-text="{{ $shareText }}"
                        data-share-image="{{ $image }}"
                        title="Bagikan"
                        aria-label="Bagikan unit"
                      >
                        <span class="material-symbols-outlined text-[15px]">share</span>
                      </button>

                      @if ($isCustomer)
                        <button
                          type="button"
                          class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 transition hover:bg-slate-200 dark:bg-[#1a376f]"
                          data-favorite-toggle
                          data-store-url="{{ route('customer.favorites.store', $car->id) }}"
                          data-destroy-url="{{ route('customer.favorites.destroy', $car->id) }}"
                          data-is-favorite="{{ $isFavorite ? '1' : '0' }}"
                          title="Favorit"
                          aria-label="{{ $isFavorite ? 'Favorit tersimpan' : 'Tambah favorit' }}"
                          aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
                        >
                          <span
                            class="material-symbols-outlined text-[15px] {{ $isFavorite ? 'text-rose-500' : 'text-slate-700 dark:text-slate-200' }}"
                            data-favorite-icon
                            style="font-variation-settings: 'FILL' {{ $isFavorite ? 1 : 0 }}, 'wght' 500, 'GRAD' 0, 'opsz' 24;"
                          >favorite</span>
                        </button>
                      @elseif(!auth()->check())
                        <a class="js-login-required inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-[#1a376f] dark:text-slate-200" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}" title="Favorit" aria-label="Login untuk favorit">
                          <span class="material-symbols-outlined text-[15px]">favorite</span>
                        </a>
                      @endif
                    </div>
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
                    <a class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-[#f5a623] text-[#121826] transition hover:scale-110" href="{{ $detailUrl }}" aria-label="Lihat detail">
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

    <section class="landing-surface-testimonials py-16 md:py-20">
      <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-10 px-4 md:px-6 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-6">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">Overview Customer</p>
          <h2 class="landing-light-heading mt-3 font-headline text-[42px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[52px]">Apa kata Customer Maharani?</h2>

          <div class="mt-6 flex items-center gap-4">
            <div class="rounded-full bg-white px-4 py-2 text-[12px] font-semibold text-slate-700 shadow-sm dark:bg-[#0b1120] dark:text-slate-200">
              {{ number_format((float) ($featuredReviewAverage ?? 0), 1) }}/5
            </div>
            <p class="landing-light-copy text-[12px] text-slate-500 dark:text-slate-300">{{ ($featuredReviewCount ?? 0) }} review customer sudah tayang.</p>
          </div>

          <p class="mt-5 max-w-[520px] text-[15px] font-medium leading-7 dark:text-slate-200" style="color:#071b47;opacity:1;">
            Dokumentasi review ini menampilkan pengalaman langsung customer terhadap kondisi unit dan pelayanan Maharani Mobil.
          </p>

          <a class="mt-6 inline-flex items-center gap-2 rounded-full bg-[#071b47] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#0d2a67]" href="{{ route('reviews.page') }}">
            Lihat Semua Review Customer
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </a>
        </div>

        <div class="relative lg:col-span-6">
          @if (($featuredReviews ?? collect())->isNotEmpty())
            <div class="grid gap-4">
              @foreach (($featuredReviews ?? collect()) as $review)
                @php
                  $photos = collect($review->review_photos ?? []);
                @endphp
                <article class="relative z-10 rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-[#0b1120] md:p-7">
                  <div class="flex items-start justify-between gap-4">
                    <div>
                      <p class="text-[15px] font-bold text-slate-900 dark:text-white">{{ $review->user?->name }}</p>
                      <p class="mt-1 text-[12px] text-slate-500 dark:text-slate-300">{{ $review->car?->merk }} {{ $review->car?->tipe }} {{ $review->car?->tahun }}</p>
                    </div>
                    <div class="text-right text-[#f5a623]">
                      @for ($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $i <= (int) $review->rating ? 1 : 0 }};">star</span>
                      @endfor
                    </div>
                  </div>

                  <p class="mt-4 text-[15px] leading-7 text-slate-700 dark:text-slate-100">{{ \Illuminate\Support\Str::limit($review->review_text, 170) }}</p>

                  @if ($photos->isNotEmpty())
                    <div class="mt-4 grid grid-cols-3 gap-3">
                      @foreach ($photos->take(3) as $photo)
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                          <img alt="Foto review {{ $review->user?->name }}" class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . $photo) }}"/>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </article>
              @endforeach
            </div>
          @else
            <article class="relative z-10 rounded-[2rem] border border-slate-200 bg-white p-8 dark:border-white/10 dark:bg-[#0b1120] md:p-10">
              <p class="text-[17px] leading-relaxed text-slate-700 dark:text-slate-100">
                Review customer Maharani Mobil akan tampil di sini setelah customer membagikan pengalaman mereka tentang unit dan layanan showroom.
              </p>
            </article>
          @endif
        </div>
      </div>
    </section>

    <section class="landing-surface-faq py-16 md:py-20">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <div class="mx-auto max-w-[760px] text-center">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">FAQ</p>
          <h2 class="landing-light-heading mt-3 font-headline text-[38px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[48px]">Pertanyaan Yang Sering Ditanyakan</h2>
          <p class="landing-light-copy mt-4 text-[15px] leading-7 text-slate-600 dark:text-slate-300">
            Ringkasan jawaban penting untuk membantu Anda memahami proses pembelian mobil di Maharani Mobil sebelum melakukan transaksi.
          </p>
        </div>

        <div class="mx-auto mt-10 max-w-[920px] space-y-4">
          @foreach (collect($faqItems ?? [])->take(4) as $item)
            <details class="group rounded-[1.5rem] border border-slate-200 bg-white p-6 editorial-shadow transition dark:border-white/10 dark:bg-[#0b1120]">
              <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-headline text-[20px] font-bold text-slate-900 dark:text-white">
                <span>{{ $item['question'] }}</span>
                <span class="material-symbols-outlined text-[#f5a623] transition duration-300 group-open:rotate-45">add</span>
              </summary>

              <div class="mt-4 space-y-4 text-[14px] leading-7 text-slate-600 dark:text-slate-300">
                @foreach ($item['paragraphs'] ?? [] as $paragraph)
                  <p>{{ $paragraph }}</p>
                @endforeach

                @if (!empty($item['bullets']))
                  <ul class="space-y-2 pl-5">
                    @foreach ($item['bullets'] as $bullet)
                      <li class="list-disc">{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>
            </details>
          @endforeach
        </div>

        <div class="mt-8 text-center">
          <a class="inline-flex items-center gap-2 rounded-full bg-[#071b47] px-6 py-3 text-[13px] font-bold text-white transition hover:bg-[#0d2a67]" href="{{ route('faq') }}">
            Lihat FAQ lainnya
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </a>
        </div>
      </div>
    </section>

    <section class="landing-surface-seo overflow-hidden pb-20 pt-10 md:pt-12">
      <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-12 px-4 md:px-6 lg:grid-cols-[0.92fr_1.08fr] lg:items-center lg:gap-20">
        <div class="max-w-[620px]">
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Trusted Since 2017') }}</p>
          <h2 class="landing-light-heading mt-3 text-left font-headline text-[34px] font-extrabold leading-tight text-slate-900 dark:text-white sm:text-[44px]">{{ __('Pusat Jual Beli Mobil Bekas Berkualitas di Pekanbaru') }}</h2>

          <div class="landing-light-copy mt-8 space-y-5 text-left text-[15px] leading-loose text-slate-600 dark:text-slate-300">
            <p>{{ __('Maharani Mobil Pekanbaru telah berdiri selama lebih dari satu dekade melayani kebutuhan otomotif masyarakat Riau. Sebagai penyedia mobil bekas Pekanbaru yang terpercaya, kami memahami bahwa membeli kendaraan bukan sekadar transaksi, melainkan sebuah investasi jangka panjang.') }}</p>
            <p>{{ __('Kami menyediakan berbagai pilihan kendaraan mulai dari MPV keluarga seperti Toyota Avanza dan Mitsubishi Xpander, hingga unit premium seperti BMW dan Mercedes-Benz. Seluruh inventaris kami telah melewati proses multi-point inspection yang ketat untuk memastikan standar kualitas "The Digital Concierge" tetap terjaga.') }}</p>
            <p>{{ __('Terletak strategis di jantung kota Pekanbaru, showroom kami menawarkan pengalaman belanja yang nyaman dengan fasilitas purna jual yang lengkap. Baik Anda mencari mobil pertama atau ingin melakukan trade-in, tim ahli kami siap membantu Anda menemukan solusi finansial terbaik yang sesuai dengan anggaran Anda.') }}</p>
          </div>
        </div>

        <div class="relative flex justify-center lg:justify-end lg:translate-x-[max(24px,calc((100vw-1280px)/2+24px))]">
          <img
            alt="Supervisor dan teknisi Maharani Mobil"
            class="relative z-10 h-auto w-full max-w-[760px] -scale-x-100 object-contain"
            src="{{ asset('assets/landing-supervisor-technician-transparent.png') }}?v={{ filemtime(public_path('assets/landing-supervisor-technician-transparent.png')) }}"
          />
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan mobil yang ada di website.'])
  @include('components.search-modal', ['brandOptions' => $brandOptions ?? collect()])
  @include('components.ui-system-footer')

  @include('partials.favorite-toggle-script')

  <script>
    (() => {
      const copyText = async (value) => {
        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(value);
          return;
        }

        const textarea = document.createElement('textarea');
        textarea.value = value;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
      };

      const buildShareFiles = async (imageUrl, title) => {
        if (!imageUrl || !navigator.canShare) {
          return [];
        }

        try {
          const response = await fetch(imageUrl, { mode: 'cors' });
          if (!response.ok) {
            return [];
          }

          const blob = await response.blob();
          const extension = blob.type.includes('png') ? 'png' : blob.type.includes('webp') ? 'webp' : 'jpg';
          const safeTitle = (title || 'produk-maharani-mobil')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '') || 'produk-maharani-mobil';
          const file = new File([blob], `${safeTitle}.${extension}`, { type: blob.type || 'image/jpeg' });

          return navigator.canShare({ files: [file] }) ? [file] : [];
        } catch (error) {
          return [];
        }
      };

      document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-share-car]');
        if (!button) {
          return;
        }

        const url = button.getAttribute('data-share-url') || window.location.href;
        const title = button.getAttribute('data-share-title') || document.title;
        const text = button.getAttribute('data-share-text') || '';
        const imageUrl = button.getAttribute('data-share-image') || '';

        if (navigator.share) {
          try {
            const files = await buildShareFiles(imageUrl, title);
            const payload = files.length ? { title, text, url, files } : { title, text, url };
            await navigator.share(payload);
            return;
          } catch (error) {
            if (error?.name === 'AbortError') {
              return;
            }
          }
        }

        try {
          await copyText(url);
          button.classList.add('bg-emerald-100', 'text-emerald-700');
          setTimeout(() => {
            button.classList.remove('bg-emerald-100', 'text-emerald-700');
          }, 1600);
        } catch (error) {
          console.error('Gagal menyalin link unit.', error);
        }
      });
    })();
  </script>
</body>
</html>


