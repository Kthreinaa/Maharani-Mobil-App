<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Favorit Saya') }} | Maharani Mobil</title>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'favorites', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Customer Area') }}</p>
          <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">{{ __('Mobil Favorit') }}</h1>
          <p class="mt-3 max-w-[620px] text-sm leading-7 text-on-surface-variant">{{ __('Simpan unit yang menarik, lalu bandingkan sebelum lanjut ke detail atau checkout.') }}</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-5 py-3 text-[13px] font-bold text-white transition hover:brightness-110" href="{{ route('catalog') }}">
          <span class="material-symbols-outlined text-[18px]">search</span>
          {{ __('Buka katalog') }}
        </a>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($favorites as $car)
          @php
            $imageUrl = is_array($car->photos ?? null) && !empty($car->photos[0])
              ? asset('storage/' . $car->photos[0])
              : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1200&auto=format&fit=crop';
          @endphp
          <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <img alt="{{ $car->merk }} {{ $car->tipe }}" class="h-56 w-full object-cover" src="{{ $imageUrl }}"/>
            <div class="p-5">
              <h3 class="font-headline text-[24px] font-extrabold text-primary">{{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}</h3>
              <p class="mt-2 text-sm text-on-surface-variant">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</p>
              <div class="mt-5 flex gap-3">
                <a class="flex-1 rounded-xl bg-primary px-4 py-3 text-center text-sm font-bold text-white transition hover:brightness-110" href="{{ route('cars.show', $car) }}">{{ __('Lihat Detail') }}</a>
                <form method="POST" action="{{ route('customer.favorites.destroy', $car) }}">
                  @csrf
                  @method('DELETE')
                  <button class="rounded-xl border border-outline-variant px-4 py-3 text-sm font-bold text-primary transition hover:bg-slate-50" type="submit">{{ __('Hapus') }}</button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="rounded-[1.5rem] border border-slate-200 bg-white p-8 text-sm text-on-surface-variant shadow-sm md:col-span-2 xl:col-span-3">
            {{ __('Belum ada favorit. Simpan unit dari katalog atau halaman detail mobil agar mudah Anda bandingkan nanti.') }}
          </div>
        @endforelse
      </div>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin dibantu memilih unit dari daftar favorit saya.'])
  @include('components.ui-system-footer')
</body>
</html>
