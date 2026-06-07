@php
  $selectedCar = $selectedCar ?? ($cars->first() ?? null);
  $myOffers = $myOffers ?? collect();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajukan Penawaran | Maharani Mobil</title>
  <meta name="description" content="Ajukan penawaran harga untuk unit pilihan Anda di Maharani Mobil."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <div class="flex items-center gap-4">
        @include('components.nav-tools')
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="px-4 py-2 text-slate-500 dark:text-slate-300 hover:text-primary">Logout</button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    <div class="flex items-center justify-between gap-4 mb-8">
      <div>
        <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-semibold">Negosiasi Online</p>
        <h1 class="text-4xl font-extrabold text-primary mt-2">Ajukan Penawaran Harga</h1>
      </div>
      <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('catalog') }}">Kembali ke katalog</a>
    </div>

    @if (session('success'))
      <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        Mohon cek kembali nominal atau respons negosiasi yang Anda kirim.
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <section class="lg:col-span-2 rounded-[2rem] bg-white p-6 md:p-8 shadow-xl shadow-blue-900/5">
        <form method="POST" action="{{ route('customer.offers.store') }}" class="space-y-6">
          @csrf

          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Pilih Unit</label>
            <select id="offer-car-select" class="w-full rounded-xl border border-outline-variant p-4" name="car_id">
              @foreach ($cars as $car)
                @php
                  $label = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
                @endphp
                <option
                  value="{{ $car->id }}"
                  data-price="{{ number_format((float) ($car->harga ?? 0), 0, ',', '.') }}"
                  @selected((int) old('car_id', $selectedCar?->id) === (int) $car->id)
                >
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Harga Penawaran</label>
            <input class="w-full rounded-xl border border-outline-variant p-4" name="offer_price" placeholder="Contoh: 520000000" type="number" value="{{ old('offer_price') }}"/>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Catatan Penawaran</label>
            <textarea class="w-full rounded-xl border border-outline-variant p-4 h-28" name="notes" placeholder="Alasan penawaran, rencana pembayaran, atau catatan untuk supervisor">{{ old('notes') }}</textarea>
          </div>

          <div class="flex justify-end">
            <button class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-4 text-sm font-bold text-white" type="submit">
              Kirim Penawaran
            </button>
          </div>
        </form>
      </section>

      <aside class="space-y-6">
        <div class="rounded-[2rem] bg-primary p-6 text-white shadow-xl shadow-blue-900/20">
          <h2 class="text-lg font-bold mb-4">Estimasi Unit</h2>
          <p id="offer-car-name" class="font-bold text-white">
            {{ $selectedCar ? trim(($selectedCar->merk ?? '') . ' ' . ($selectedCar->tipe ?? '') . ' ' . ($selectedCar->tahun ?? '')) : '-' }}
          </p>
          <p class="mt-3 text-sm text-blue-100">Harga katalog saat ini</p>
          <p id="offer-car-price" class="mt-1 text-3xl font-extrabold text-white">
            {{ $selectedCar ? ('Rp ' . number_format((float) ($selectedCar->harga ?? 0), 0, ',', '.')) : '-' }}
          </p>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-xl shadow-blue-900/5">
          <h3 class="text-lg font-bold text-primary">Bagaimana Penawaran dilakukan?</h3>
          <ul class="mt-4 space-y-3 text-sm text-on-surface-variant">
            <li>1.Customer mengajukan harga tawaran langsung melalui sistem.</li>
            <li>2.Supervisor dapat menerima, menolak, atau memberi tawar balik.</li>
            <li>3.Jika ada tawar balik, customer bisa setuju, menolak, atau kirim harga baru lagi.</li>
            <li>4.Setelah deal, customer checkout memakai harga final hasil negosiasi.</li>
          </ul>
        </div>
      </aside>
    </div>

    <section class="mt-10 rounded-[2rem] bg-white p-6 md:p-8 shadow-xl shadow-blue-900/5">
      <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-semibold">Riwayat Negosiasi</p>
          <h2 class="mt-2 text-2xl font-extrabold text-primary">Penawaran Saya</h2>
        </div>
        <p class="text-sm text-on-surface-variant">Riwayat tawar-menawar tersimpan otomatis agar harga final tidak membingungkan kedua pihak.</p>
      </div>

      <div class="mt-6 grid gap-5">
        @forelse ($myOffers as $offer)
          <article class="rounded-[1.7rem] border border-slate-200 bg-slate-50/70 p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
              <div>
                <h3 class="text-xl font-extrabold text-primary">{{ $offer->car?->merk }} {{ $offer->car?->tipe }} {{ $offer->car?->tahun }}</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span class="rounded-full px-3 py-1 text-xs font-bold {{ $offer->status_badge_classes }}">{{ $offer->status_label }}</span>
                  <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">Ronde {{ $offer->negotiation_round ?? 1 }}</span>
                  <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $offer->follow_up_status_label }}</span>
                </div>
              </div>
              @if ($offer->can_checkout)
                <a class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white" href="{{ route('checkout.cash', ['offer_id' => $offer->id, 'car_id' => $offer->car_id]) }}">
                  Lanjut Checkout Harga Deal
                </a>
              @endif
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-4">
              <div class="rounded-[1.1rem] border border-slate-200 bg-white p-4">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Harga Katalog</p>
                <p class="mt-2 text-base font-extrabold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($offer->car?->harga ?? 0) }}</p>
              </div>
              <div class="rounded-[1.1rem] border border-slate-200 bg-white p-4">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Tawaran Customer</p>
                <p class="mt-2 text-base font-extrabold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($offer->offer_price) }}</p>
              </div>
              <div class="rounded-[1.1rem] border border-slate-200 bg-white p-4">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Tawar Balik</p>
                <p class="mt-2 text-base font-extrabold text-slate-900">{{ $offer->counter_price ? \App\Support\CurrencyFormatter::rupiah($offer->counter_price) : '-' }}</p>
              </div>
              <div class="rounded-[1.1rem] border border-slate-200 bg-white p-4">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Harga Final</p>
                <p class="mt-2 text-base font-extrabold text-emerald-700">{{ $offer->final_price ? \App\Support\CurrencyFormatter::rupiah($offer->final_price) : '-' }}</p>
              </div>
            </div>

            @if ($offer->notes)
              <div class="mt-4 rounded-[1.1rem] border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-600">
                {{ $offer->notes }}
              </div>
            @endif

            <div class="mt-5 rounded-[1.1rem] border border-slate-200 bg-white p-4">
              <p class="text-sm font-bold text-primary">Riwayat Nego</p>
              <div class="mt-3 space-y-3">
                @foreach ($offer->histories->sortBy('created_at') as $history)
                  <div class="flex flex-col gap-1 rounded-xl bg-slate-50 px-4 py-3 text-sm">
                    <p class="font-semibold text-slate-900">{{ $history->action_label }}</p>
                    <p class="text-slate-600">{{ $history->offered_price ? \App\Support\CurrencyFormatter::rupiah($history->offered_price) : '-' }}</p>
                    @if ($history->note)
                      <p class="text-slate-500">{{ $history->note }}</p>
                    @endif
                    <p class="text-xs text-slate-400">{{ $history->created_at?->translatedFormat('d M Y H:i') }}</p>
                  </div>
                @endforeach
              </div>
            </div>

            @if ($offer->status === 'countered')
              <form class="mt-5 grid gap-3 rounded-[1.2rem] border border-sky-200 bg-sky-50/80 p-4" method="POST" action="{{ route('customer.offers.respond', $offer) }}">
                @csrf
                @method('PATCH')
                <p class="text-sm font-bold text-sky-900">Supervisor memberi tawar balik. Anda bisa setuju, menolak, atau kirim tawaran baru.</p>
                <input class="rounded-xl border border-sky-200 bg-white" type="number" name="offer_price" min="0" step="0.01" placeholder="Isi jika ingin mengirim tawaran baru" />
                <textarea class="rounded-xl border border-sky-200 bg-white h-24" name="notes" placeholder="Catatan respons untuk supervisor"></textarea>
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white" type="submit" name="response_action" value="accept_counter">Setuju Harga Deal</button>
                  <button class="rounded-xl bg-[#08132e] px-4 py-3 text-sm font-bold text-white" type="submit" name="response_action" value="new_offer">Ajukan Tawaran Baru</button>
                  <button class="rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white" type="submit" name="response_action" value="reject_counter">Tolak / Batalkan</button>
                </div>
              </form>
            @endif
          </article>
        @empty
          <div class="rounded-[1.5rem] border border-dashed border-slate-200 px-5 py-8 text-sm text-slate-500">
            Belum ada penawaran yang Anda kirim.
          </div>
        @endforelse
      </div>
    </section>
  </main>

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin negosiasi harga unit di website.'])
  @include('components.ui-system-footer')

  <script>
    (() => {
      const select = document.getElementById('offer-car-select');
      const nameEl = document.getElementById('offer-car-name');
      const priceEl = document.getElementById('offer-car-price');
      if (!select || !nameEl || !priceEl) return;

      const render = () => {
        const option = select.options[select.selectedIndex];
        if (!option) return;
        nameEl.textContent = option.textContent.trim();
        priceEl.textContent = `Rp ${option.getAttribute('data-price') || '0'}`;
      };

      select.addEventListener('change', render);
      render();
    })();
  </script>
</body>
</html>
