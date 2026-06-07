@php
  $cars = $cars ?? collect();
  $selectedCar = $selectedCar ?? ($cars->first() ?? null);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Test Drive | Maharani Mobil</title>
  <meta name="description" content="Jadwalkan test drive mobil pilihan Anda di Maharani Mobil Pekanbaru."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'test-drives', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <nav class="mb-6 flex items-center gap-2 text-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a class="hover:text-primary" href="{{ route('customer.home') }}">Home</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <a class="hover:text-primary" href="{{ route('customer.test-drives.index') }}">Test Drive</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="font-bold text-primary">Booking</span>
      </nav>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
          <h1 class="font-headline text-[34px] font-extrabold text-primary">Buat Janji Test Drive</h1>
          <p class="mt-2 text-sm leading-7 text-on-surface-variant">Pilih unit, tanggal, dan jam kunjungan. Setelah dikirim, tim kami akan meninjau dan menghubungi Anda untuk konfirmasi.</p>

          @if ($errors->any())
            <div class="mt-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
              Data booking belum valid. Silakan periksa input Anda.
            </div>
          @endif

          <form method="POST" action="{{ route('customer.test-drive.store') }}" class="mt-8 space-y-6">
            @csrf

            <div>
              <label for="car_id" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Unit Mobil</label>
              <select id="car_id" name="car_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0">
                @foreach($cars as $car)
                  @php
                    $label = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
                    $isSelected = (int) old('car_id', optional($selectedCar)->id) === (int) $car->id;
                  @endphp
                  <option
                    value="{{ $car->id }}"
                    @selected($isSelected)
                    data-price="{{ number_format((float) ($car->harga ?? 0), 2, '.', ',') }}"
                    data-km="{{ number_format((int) ($car->kilometer ?? 0), 0, ',', '.') }}"
                    data-trans="{{ $car->transmisi ?? '-' }}"
                  >
                    {{ $label !== '' ? $label : ('Unit #' . $car->id) }}
                  </option>
                @endforeach
              </select>
              @error('car_id')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label for="booking_date" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal</label>
                <input id="booking_date" name="booking_date" value="{{ old('booking_date') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" type="date" required/>
                @error('booking_date')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="booking_time" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Jam</label>
                <select id="booking_time" name="booking_time" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required>
                  @php $timeValue = old('booking_time'); @endphp
                  <option value="09:00" @selected($timeValue === '09:00')>09:00</option>
                  <option value="11:00" @selected($timeValue === '11:00')>11:00</option>
                  <option value="13:00" @selected($timeValue === '13:00')>13:00</option>
                  <option value="15:00" @selected($timeValue === '15:00')>15:00</option>
                  <option value="17:00" @selected($timeValue === '17:00')>17:00</option>
                </select>
                @error('booking_time')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div>
              <label for="location" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</label>
              <input id="location" name="location" value="{{ old('location') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" placeholder="Alamat pengantaran atau showroom" type="text"/>
              @error('location')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="notes" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Catatan</label>
              <textarea id="notes" name="notes" class="h-28 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" placeholder="Preferensi lokasi, rute, atau permintaan khusus">{{ old('notes') }}</textarea>
              @error('notes')
                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
              @enderror
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#08132e] px-6 py-3.5 text-[13px] font-bold text-white transition hover:brightness-110">
              <span class="material-symbols-outlined text-[18px]">event_available</span>
              Konfirmasi Booking
            </button>
          </form>
        </section>

        <aside class="space-y-6">
          <div class="rounded-[2rem] bg-[#08132e] p-6 text-white shadow-[0_20px_50px_rgba(8,19,46,0.18)]">
            <h2 class="text-lg font-bold">Ringkasan Unit</h2>
            <div class="mt-4 space-y-2 text-sm text-slate-200">
              <p id="td-unit-name" class="font-semibold text-white">
                {{ $selectedCar ? trim(($selectedCar->merk ?? '') . ' ' . ($selectedCar->tipe ?? '') . ' ' . ($selectedCar->tahun ?? '')) : 'Pilih unit mobil' }}
              </p>
              <p id="td-unit-meta">
                {{ $selectedCar ? (number_format((int) ($selectedCar->kilometer ?? 0), 0, ',', '.') . ' KM • ' . ($selectedCar->transmisi ?? '-')) : '-' }}
              </p>
              <p id="td-unit-price" class="text-lg font-black text-[#f7c35f]">
                {{ $selectedCar ? \App\Support\CurrencyFormatter::rupiah($selectedCar->harga ?? 0) : '-' }}
              </p>
            </div>
          </div>

          <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-primary">Reminder</h3>
            <ul class="mt-4 space-y-3 text-sm text-on-surface-variant">
              <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[#f5a623]">notifications</span> H-1 akan ada pengingat via WhatsApp.</li>
              <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[#f5a623]">verified</span> Bawa SIM & KTP untuk verifikasi.</li>
              <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[#f5a623]">location_on</span> Pilih lokasi yang nyaman untuk Anda.</li>
            </ul>
          </div>
        </aside>
      </div>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin booking test drive.'])
  @include('components.ui-system-footer')

  <script>
    (() => {
      const select = document.getElementById('car_id');
      const nameEl = document.getElementById('td-unit-name');
      const metaEl = document.getElementById('td-unit-meta');
      const priceEl = document.getElementById('td-unit-price');
      if (!select || !nameEl || !metaEl || !priceEl) return;

      const render = () => {
        const opt = select.options[select.selectedIndex];
        if (!opt) return;
        nameEl.textContent = opt.textContent.trim();
        const km = opt.getAttribute('data-km') || '-';
        const trans = opt.getAttribute('data-trans') || '-';
        metaEl.textContent = `${km} KM • ${trans}`;
        const price = opt.getAttribute('data-price') || '0.00';
        priceEl.textContent = `Rp. ${price}`;
      };

      select.addEventListener('change', render);
      render();
    })();
  </script>
</body>
</html>
