@php
  $selectedCarLabel = $selectedCar
      ? trim(($selectedCar->merk ?? '') . ' ' . ($selectedCar->tipe ?? '') . ' ' . ($selectedCar->tahun ?? ''))
      : 'Semua Unit';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ulasan Customer | Maharani Mobil</title>
  <meta name="description" content="Review customer Maharani Mobil berisi pengalaman pembelian mobil bekas, pelayanan showroom, dan dokumentasi foto review langsung dari customer."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'reviews'])

  <main class="mx-auto w-full max-w-[1280px] flex-grow px-4 pb-10 pt-32 md:px-6 md:pt-28">
    <section class="overflow-hidden rounded-[2rem] bg-[#031636] px-6 py-8 text-white shadow-[0_20px_60px_rgba(3,22,54,0.18)] md:px-8 md:py-10">
      <div class="max-w-[780px]">
        <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">Overview Customer</p>
        <h1 class="mt-3 font-headline text-[32px] font-extrabold leading-tight sm:text-[40px]">Ulasan Customer Maharani Mobil</h1>
        <p class="mt-4 text-[15px] leading-7 text-slate-200">
          Lihat review langsung dari customer Maharani Mobil tentang kondisi unit, pengalaman pembelian, dan pelayanan showroom. Semua review di halaman ini berasal dari customer terverifikasi.
        </p>
        <p class="mt-3 text-[13px] leading-6 text-slate-200/90">
          Customer hanya bisa mengirim review maksimal 7 hari setelah pembelian selesai atau test drive disetujui.
        </p>
      </div>
    </section>

    @if (session('success'))
      <div class="mt-5 rounded-[1.25rem] border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mt-5 rounded-[1.25rem] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        Mohon cek kembali form review Anda. Pastikan semua input sudah valid.
      </div>
    @endif

    <div class="mt-4 text-sm text-slate-500">
      <a class="hover:text-primary" href="/">Beranda</a>
      <span> &gt; </span>
      <span>Ulasan Customer</span>
      @if ($selectedCar)
        <span> &gt; </span>
        <span class="font-semibold text-primary">{{ $selectedCarLabel }}</span>
      @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-[1.55fr_1fr]">
      <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_rgba(7,27,71,0.06)]">
        <h2 class="font-headline text-[24px] font-extrabold text-slate-900">Ringkasan Ulasan</h2>
        <p class="mt-2 text-sm text-slate-500">Rating rata-rata</p>
        <div class="mt-2 flex items-end gap-3">
          <p class="font-headline text-3xl font-extrabold text-[#071b47]">{{ number_format($averageRating, 1) }}/5</p>
          <p class="pb-1 text-sm text-slate-500">({{ $totalReviews }} ulasan)</p>
        </div>
        <p class="mt-3 text-sm text-slate-600">Menampilkan review customer untuk {{ $selectedCarLabel }}.</p>
      </section>

      <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_rgba(7,27,71,0.06)]">
        <h2 class="font-headline text-[24px] font-extrabold text-slate-900">Tulis Review Anda</h2>

        @auth
          @if (auth()->user()->role === 'customer' && $eligibleCars->isNotEmpty())
            <form class="mt-4 space-y-3" method="POST" action="{{ route('customer.reviews.store') }}" enctype="multipart/form-data">
              @csrf
              <div>
                <label class="mb-1 block text-sm font-semibold text-slate-600">Unit</label>
                <select class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="car_id">
                  @foreach ($eligibleCars as $option)
                    <option value="{{ $option['car']->id }}" @selected((int) old('car_id', $selectedCarId) === (int) $option['car']->id)>
                      {{ $option['car']->merk }} {{ $option['car']->tipe }} {{ $option['car']->tahun }} - {{ $option['label'] }}
                    </option>
                  @endforeach
                </select>
                <p class="mt-2 text-xs text-slate-500">Review hanya tersedia selama 7 hari sejak transaksi atau test drive selesai.</p>
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-slate-600">Rating</label>
                <select class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="rating">
                  @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected((int) old('rating', 5) === $i)>{{ $i }} / 5</option>
                  @endfor
                </select>
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-slate-600">Review</label>
                <textarea class="h-28 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="review_text" placeholder="Bagikan pengalaman Anda saat membeli mobil atau menerima layanan dari Maharani Mobil...">{{ old('review_text') }}</textarea>
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-slate-600">Foto Review</label>
                <input class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="media[]" type="file" accept=".jpg,.jpeg,.png,.webp" multiple/>
                <p class="mt-2 text-xs text-slate-500">Maksimal 5 foto. Format JPG, PNG, atau WEBP.</p>
              </div>
              <button class="w-full rounded-xl bg-[#071b47] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0d2a67]" type="submit">Kirim Review</button>
            </form>
          @elseif (auth()->user()->role === 'customer')
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-sm text-amber-700">
              Anda baru bisa mengirim review setelah pembelian selesai atau test drive disetujui, dan review hanya tersedia selama 7 hari.
            </div>
          @else
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-600">
              Form review hanya tersedia untuk akun customer.
            </div>
          @endif
        @else
          <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-600">
            Masuk terlebih dahulu sebagai customer untuk menambahkan review.
          </div>
        @endauth
      </section>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[1.55fr_1fr]">
      <section class="space-y-4">
        <div class="flex flex-col gap-3 rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_rgba(7,27,71,0.06)] md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-700">Filter &amp; Urutkan</p>
            <p class="text-xs text-slate-500">Pilih unit tertentu atau tampilkan review terbaru lebih dulu.</p>
          </div>
          <form class="flex flex-col gap-2 sm:flex-row" method="GET" action="{{ route('reviews.page') }}">
            <select class="rounded-xl border border-slate-300 px-3 py-2 text-sm" name="car">
              <option value="0">Semua Unit</option>
              @foreach ($allCars as $car)
                <option value="{{ $car->id }}" @selected($selectedCarId === (int) $car->id)>{{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}</option>
              @endforeach
            </select>
            <select class="rounded-xl border border-slate-300 px-3 py-2 text-sm" name="sort">
              <option value="latest" @selected($sort === 'latest')>Terbaru</option>
              <option value="highest" @selected($sort === 'highest')>Rating Tertinggi</option>
            </select>
            <button class="rounded-xl bg-[#071b47] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#0d2a67]" type="submit">Terapkan</button>
          </form>
        </div>

        <div class="relative h-[360px] overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-[0_18px_45px_rgba(7,27,71,0.06)] md:h-[390px] lg:h-[410px]">
          <div class="pointer-events-none absolute bottom-6 right-3 z-10 flex flex-col items-center gap-2 rounded-full bg-[#071b47]/92 px-3 py-4 text-white shadow-lg">
            <span class="material-symbols-outlined text-[22px]">south</span>
            <span class="writing-mode-vertical text-[10px] font-bold uppercase tracking-[0.24em] [writing-mode:vertical-rl] [text-orientation:mixed]">Scroll</span>
          </div>

          <div class="h-full space-y-4 overflow-y-auto overscroll-contain scroll-smooth pr-8 [scroll-snap-type:y_mandatory] [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar]:w-2">
          @forelse ($reviews as $review)
            @php
              $photos = collect($review->review_photos ?? []);
              $reviewRating = max(0, min(5, (int) round((float) ($review->rating ?? 0))));
            @endphp
            <article class="min-h-full snap-start rounded-[1.5rem] border border-slate-200 bg-white p-5">
              <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                <div>
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="font-headline text-[24px] font-extrabold text-slate-900">{{ $review->user?->name }}</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $review->source_type === 'purchase' ? 'Pembelian selesai' : 'Test drive selesai' }}</span>
                  </div>
                  <p class="mt-1 text-sm text-slate-500">{{ $review->car?->merk }} {{ $review->car?->tipe }} {{ $review->car?->tahun }}</p>
                </div>
                <div class="text-right">
                  <div class="flex items-center justify-end gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                      <svg aria-hidden="true" class="h-[18px] w-[18px] {{ $i <= $reviewRating ? 'text-[#f5a623]' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                    @endfor
                  </div>
                  <p class="mt-1 text-xs text-slate-500">{{ optional($review->created_at)->format('d M Y') }}</p>
                </div>
              </div>

              @if ($photos->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-3">
                  @foreach ($photos as $photo)
                    <div class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-50">
                      <img alt="Foto review {{ $review->user?->name }}" class="h-28 w-28 object-cover" src="{{ asset('storage/' . $photo) }}"/>
                    </div>
                  @endforeach
                </div>
              @endif

              <p class="mt-4 text-[15px] leading-7 text-slate-700">{{ $review->review_text }}</p>
            </article>
          @empty
            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 text-sm text-slate-500">
              Belum ada review customer yang tampil untuk filter ini.
            </div>
          @endforelse
          </div>
        </div>

        @if ($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div>{{ $reviews->links() }}</div>
        @endif
      </section>

      <aside class="space-y-4">
        <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_18px_45px_rgba(7,27,71,0.06)]">
          <h2 class="font-headline text-[24px] font-extrabold text-slate-900">Tentang Review Customer</h2>
          <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
            <li>Review di halaman ini berasal dari customer yang sudah membeli unit atau sudah menyelesaikan test drive.</li>
            <li>Customer dapat menambahkan foto review agar calon pembeli lain bisa melihat dokumentasi pengalaman membeli mobil bekas di Maharani.</li>
            <li>Batas waktu pengiriman review adalah maksimal 7 hari setelah pembelian selesai atau test drive disetujui.</li>
          </ul>
        </section>
      </aside>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin melihat review customer atau membagikan pengalaman saya.'])
  @include('components.ui-system-footer')
</body>
</html>
