@php
  /**
   * Halaman booking test drive (customer).
   * Data:
   * - $cars: daftar mobil available
   * - $selectedCar: mobil default/terpilih dari query car_id
   */
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
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <nav class="hidden md:flex items-center gap-8 font-headline tracking-tight">
        <a class="text-slate-500 dark:text-slate-400 hover:text-[#F5A623] transition-colors" href="/catalog">{{ __('Catalog') }}</a>
        <a class="text-slate-500 dark:text-slate-400 hover:text-[#F5A623] transition-colors" href="/about">{{ __('About Us') }}</a>
        <a class="text-slate-500 dark:text-slate-400 hover:text-[#F5A623] transition-colors" href="/financing">{{ __('Financing') }}</a>
      </nav>
      <div class="flex items-center gap-4">
        @include('components.nav-tools')
        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 text-slate-500 dark:text-slate-300 hover:text-primary">Logout</button>
          </form>
        @else
          <a class="px-4 py-2 text-slate-500 dark:text-slate-300 hover:text-primary" href="{{ route('login') }}">{{ __('Login') }}</a>
          <a class="px-6 py-2 bg-primary text-white rounded-full font-bold" href="{{ route('register') }}">{{ __('Register') }}</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    <nav class="flex items-center gap-2 text-on-surface-variant text-sm mb-6" aria-label="Breadcrumb">
      <a class="hover:text-primary" href="/home">Home</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <a class="hover:text-primary" href="/catalog">Catalog</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <span class="text-primary font-bold">Booking Test Drive</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <section class="lg:col-span-2 bg-surface-container-lowest rounded-2xl shadow-xl shadow-blue-900/5 p-8 border border-outline-variant/40">
        <h1 class="text-3xl font-extrabold text-primary mb-2">Buat Janji Test Drive</h1>
        <p class="text-on-surface-variant mb-8">Pilih unit, tanggal, dan jam. Setelah booking, janji akan tersimpan dan tim kami akan menghubungi Anda.</p>

        @if ($errors->any())
          <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            Data booking belum valid. Silakan periksa input Anda.
          </div>
        @endif

        <form method="POST" action="{{ route('customer.test-drive.store') }}" class="space-y-6">
          @csrf

          <div>
            <label for="car_id" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Unit Mobil</label>
            <select id="car_id" name="car_id" required class="w-full rounded-xl border border-outline-variant p-4 bg-surface">
              @foreach($cars as $car)
                @php
                  $label = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
                  $isSelected = (int) old('car_id', optional($selectedCar)->id) === (int) $car->id;
                @endphp
                <option
                  value="{{ $car->id }}"
                  @selected($isSelected)
                  data-price="{{ number_format((float) ($car->harga ?? 0), 0, ',', '.') }}"
                  data-km="{{ number_format((int) ($car->kilometer ?? 0), 0, ',', '.') }}"
                  data-trans="{{ $car->transmisi ?? '-' }}"
                >
                  {{ $label !== '' ? $label : ('Unit #' . $car->id) }}
                </option>
              @endforeach
            </select>
            @error('car_id')
              <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="booking_date" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tanggal</label>
              <input id="booking_date" name="booking_date" value="{{ old('booking_date') }}" class="w-full rounded-xl border border-outline-variant p-4 bg-surface" type="date" required/>
              @error('booking_date')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="booking_time" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Jam</label>
              <select id="booking_time" name="booking_time" class="w-full rounded-xl border border-outline-variant p-4 bg-surface" required>
                @php $timeValue = old('booking_time'); @endphp
                <option value="09:00" @selected($timeValue === '09:00')>09:00</option>
                <option value="11:00" @selected($timeValue === '11:00')>11:00</option>
                <option value="13:00" @selected($timeValue === '13:00')>13:00</option>
                <option value="15:00" @selected($timeValue === '15:00')>15:00</option>
                <option value="17:00" @selected($timeValue === '17:00')>17:00</option>
              </select>
              @error('booking_time')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div>
            <label for="location" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Lokasi</label>
            <input id="location" name="location" value="{{ old('location') }}" class="w-full rounded-xl border border-outline-variant p-4 bg-surface" placeholder="Alamat pengantaran atau showroom" type="text"/>
            @error('location')
              <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="notes" class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Catatan</label>
            <textarea id="notes" name="notes" class="w-full rounded-xl border border-outline-variant p-4 h-28 bg-surface" placeholder="Preferensi lokasi, rute, atau permintaan khusus">{{ old('notes') }}</textarea>
            @error('notes')
              <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit" class="w-full bg-secondary-container text-on-secondary-fixed py-4 rounded-xl font-bold">
            Konfirmasi Booking
          </button>
        </form>
      </section>

      <aside class="space-y-6">
        <div class="bg-primary text-white rounded-2xl p-6 shadow-xl border border-white/10">
          <h2 class="text-lg font-bold mb-4">Ringkasan Unit</h2>
          <div class="space-y-2 text-sm text-blue-100">
            <p id="td-unit-name" class="font-semibold text-white">
              {{ $selectedCar ? trim(($selectedCar->merk ?? '') . ' ' . ($selectedCar->tipe ?? '') . ' ' . ($selectedCar->tahun ?? '')) : 'Pilih unit mobil' }}
            </p>
            <p id="td-unit-meta">
              {{ $selectedCar ? (number_format((int) ($selectedCar->kilometer ?? 0), 0, ',', '.') . ' KM • ' . ($selectedCar->transmisi ?? '-')) : '-' }}
            </p>
            <p id="td-unit-price" class="text-secondary-container font-black text-lg">
              {{ $selectedCar ? ('Rp ' . number_format((float) ($selectedCar->harga ?? 0), 0, ',', '.')) : '-' }}
            </p>
          </div>
        </div>

        <div class="bg-surface-container-low rounded-2xl p-6 border border-outline-variant/40">
          <h3 class="font-bold text-primary mb-3">Reminder</h3>
          <ul class="space-y-2 text-sm text-on-surface-variant">
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">notifications</span> H-1 akan ada pengingat via WhatsApp.</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">verified</span> Bawa SIM & KTP untuk verifikasi.</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">location_on</span> Pilih lokasi yang nyaman untuk Anda.</li>
          </ul>
        </div>
      </aside>
    </div>
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
        const price = opt.getAttribute('data-price') || '0';
        priceEl.textContent = `Rp ${price}`;
      };

      select.addEventListener('change', render);
      render();
    })();
  </script>
</body>
</html>

