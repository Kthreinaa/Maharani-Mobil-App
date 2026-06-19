<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Review Customer | Maharani Mobil</title>
  <meta name="description" content="Halaman customer Maharani Mobil untuk menulis review pembelian atau test drive beserta foto review."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'customer-reviews', 'overlap' => false])

  <main class="flex-grow">
    <section class="landing-surface-main py-10 md:py-12">
      <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
        <section class="relative overflow-hidden rounded-[2.2rem] border border-white/10 bg-[linear-gradient(135deg,#08132e_0%,#102a63_58%,#f5a623_130%)] p-7 text-white shadow-[0_24px_70px_rgba(8,19,46,0.22)] md:p-10">
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_34%),radial-gradient(circle_at_bottom_left,rgba(245,166,35,0.22),transparent_28%)]"></div>
          <div class="relative grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
            <div>
              <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f7c35f]">Customer Review</p>
              <h1 class="mt-3 font-headline text-[36px] font-extrabold leading-tight sm:text-[46px]">Bagikan pengalaman Anda bersama Maharani Mobil.</h1>
              <p class="mt-4 max-w-[640px] text-[15px] leading-7 text-slate-200">
                Tulis review jujur tentang kondisi unit, pelayanan showroom, dan pengalaman transaksi Anda. Review akan langsung tampil di halaman ulasan customer.
              </p>
              <div class="mt-7 flex flex-wrap gap-3">
                <a class="inline-flex items-center gap-2 rounded-full bg-[#f5a623] px-5 py-3 text-[13px] font-bold text-[#111827] shadow-[0_14px_34px_rgba(245,166,35,0.24)] transition hover:brightness-105" href="{{ route('reviews.page') }}">
                  <span class="material-symbols-outlined text-[18px]">visibility</span>
                  Lihat Ulasan Customer
                </a>
                <a class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-5 py-3 text-[13px] font-semibold text-white backdrop-blur-xl transition hover:bg-white/15" href="{{ route('catalog') }}">
                  <span class="material-symbols-outlined text-[18px]">directions_car</span>
                  Kembali ke Katalog
                </a>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
              <article class="rounded-[1.6rem] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-300">Unit Bisa Direview</p>
                <p class="mt-2 text-[34px] font-extrabold">{{ $eligibleCars->count() }}</p>
                <p class="mt-2 text-[13px] text-slate-200">Unit yang bisa Anda review berdasarkan pembelian selesai atau test drive yang disetujui.</p>
              </article>
              <article class="rounded-[1.6rem] border border-white/12 bg-white/10 p-5 backdrop-blur-xl">
                <p class="text-[11px] uppercase tracking-[0.22em] text-slate-300">Review Terkirim</p>
                <p class="mt-2 text-[34px] font-extrabold">{{ $submittedReviews->count() }}</p>
                <p class="mt-2 text-[13px] text-slate-200">Semua review yang Anda kirim akan langsung tampil sebagai dokumentasi pengalaman customer.</p>
              </article>
            </div>
          </div>
        </section>

        @if (session('success'))
          <div class="mt-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mt-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            Mohon cek kembali form review Anda. Pastikan semua input sudah sesuai.
          </div>
        @endif

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[1.08fr_0.92fr]">
          <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">Form Review</p>
                <h2 class="mt-2 font-headline text-[28px] font-extrabold text-primary">Tulis review baru</h2>
                <p class="mt-3 max-w-[560px] text-sm leading-7 text-on-surface-variant">
                  Ceritakan pengalaman Anda secara jujur. Anda bisa menambahkan rating dan maksimal 5 foto review agar dokumentasinya lebih jelas.
                </p>
              </div>
              @if ($selectedCarOption)
                <span class="hidden rounded-full bg-[#eef7ff] px-3 py-1 text-xs font-bold text-primary sm:inline-flex">
                  {{ $selectedCarOption['label'] }}
                </span>
              @endif
            </div>

            @if ($eligibleCars->isNotEmpty())
              <form class="mt-6 space-y-5" method="POST" action="{{ route('customer.reviews.store') }}" enctype="multipart/form-data">
                @csrf
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Unit yang direview</label>
                  <select class="w-full rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#071b47] focus:ring-0" name="car_id">
                    @foreach ($eligibleCars as $option)
                      <option value="{{ $option['car']->id }}" @selected((int) old('car_id', $selectedCarId ?: ($selectedCarOption['car']->id ?? 0)) === (int) $option['car']->id)>
                        {{ $option['car']->merk }} {{ $option['car']->tipe }} {{ $option['car']->tahun }} - {{ $option['label'] }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Rating</label>
                  <select class="w-full rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#071b47] focus:ring-0" name="rating">
                    @for ($i = 5; $i >= 1; $i--)
                      <option value="{{ $i }}" @selected((int) old('rating', 5) === $i)>{{ $i }} / 5</option>
                    @endfor
                  </select>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Review Anda</label>
                  <textarea class="h-36 w-full rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#071b47] focus:ring-0" name="review_text" placeholder="Tulis pengalaman Anda saat membeli mobil atau menerima layanan dari Maharani Mobil.">{{ old('review_text') }}</textarea>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Foto review</label>
                  <input id="review-photo-input" class="w-full rounded-[1.2rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 file:mr-4 file:rounded-full file:border-0 file:bg-[#071b47] file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-[#0d2a67]" name="media[]" type="file" accept=".jpg,.jpeg,.png,.webp" multiple/>
                  <p class="mt-2 text-xs text-slate-500">Maksimal 5 foto. Format yang didukung: JPG, PNG, atau WEBP.</p>
                  <div id="review-photo-preview" class="mt-4 hidden grid grid-cols-2 gap-3 md:grid-cols-3"></div>
                </div>

                <button class="inline-flex items-center justify-center rounded-full bg-[#071b47] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#0d2a67]" type="submit">
                  Kirim Review
                </button>
              </form>
            @else
              <div class="mt-6 rounded-[1.4rem] border border-amber-200 bg-amber-50 px-5 py-5 text-sm leading-7 text-amber-700">
                Semua unit yang bisa direview sudah Anda review.
              </div>
            @endif
          </section>

          <aside class="space-y-6">
            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
              <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">Panduan Review</p>
              <h2 class="mt-2 font-headline text-[26px] font-extrabold text-primary">Agar review lebih membantu</h2>
              <ul class="mt-4 space-y-3 text-sm leading-7 text-on-surface-variant">
                <li>Ceritakan kondisi unit saat diterima atau setelah test drive.</li>
                <li>Jelaskan pelayanan tim showroom, proses transaksi, dan kesesuaian informasi unit.</li>
                <li>Tambahkan foto review agar calon pembeli lain bisa melihat dokumentasi pengalaman Anda.</li>
              </ul>
            </section>

            <section class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
              <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">Review Saya</p>
              <h2 class="mt-2 font-headline text-[26px] font-extrabold text-primary">Review yang sudah diupload</h2>
              <div class="mt-5 space-y-4">
                @forelse ($submittedReviews as $review)
                  @php
                    $photos = collect($review->review_photos ?? []);
                  @endphp
                  <article class="rounded-[1.4rem] border border-slate-200 bg-slate-50 px-4 py-4">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <p class="font-semibold text-slate-900">{{ $review->car?->merk }} {{ $review->car?->tipe }} {{ $review->car?->tahun }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ optional($review->created_at)->format('d M Y') }}</p>
                      </div>
                      <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold uppercase text-emerald-700">Tayang</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($review->review_text, 120) }}</p>
                    @if ($photos->isNotEmpty())
                      <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach ($photos->take(3) as $photo)
                          <img alt="Foto review {{ $review->user?->name }}" class="h-20 w-full rounded-[0.9rem] object-cover" src="{{ asset('storage/' . $photo) }}"/>
                        @endforeach
                      </div>
                    @endif
                  </article>
                @empty
                  <div class="rounded-[1.4rem] border border-dashed border-slate-200 px-4 py-5 text-sm leading-7 text-slate-500">
                    Anda belum pernah mengirim review. Setelah mengirim dari form di samping, review Anda akan tampil di sini.
                  </div>
                @endforelse
              </div>
            </section>
          </aside>
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menulis review untuk unit yang sudah saya beli atau test drive.'])
  @include('components.ui-system-footer')

  <script>
    (() => {
      const input = document.getElementById('review-photo-input');
      const preview = document.getElementById('review-photo-preview');

      if (!input || !preview) {
        return;
      }

      input.addEventListener('change', () => {
        preview.innerHTML = '';
        const files = Array.from(input.files || []).slice(0, 5);

        if (!files.length) {
          preview.classList.add('hidden');
          return;
        }

        preview.classList.remove('hidden');

        files.forEach((file, index) => {
          const objectUrl = URL.createObjectURL(file);
          const card = document.createElement('div');
          card.className = 'overflow-hidden rounded-[1rem] border border-slate-200 bg-slate-50';
          card.innerHTML = `<img class="h-28 w-full rounded-[1rem] object-cover" src="${objectUrl}" alt="Preview foto review ${index + 1}">`;
          preview.appendChild(card);
        });
      });
    })();
  </script>
</body>
</html>
