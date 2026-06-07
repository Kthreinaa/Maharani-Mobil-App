@php
  $carName = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
  $unitCode = $car->kode_unit ?: 'Tanpa kode unit';
  $carImage = is_array($car->photos ?? null) && !empty($car->photos[0])
    ? asset('storage/' . $car->photos[0])
    : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1400&auto=format&fit=crop';
  $sourceOffer = $sourceOffer ?? null;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesan Online | Maharani Mobil</title>
  <meta name="description" content="Pesan online unit Maharani Mobil dengan booking fee dan pembayaran ke rekening resmi showroom."/>
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
        <p class="text-xs uppercase tracking-[0.24em] text-slate-400 font-semibold">Pembelian Online Maharani Mobil</p>
        <h1 class="text-4xl font-extrabold text-primary mt-2">Pesan Online</h1>
      </div>
      <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('cars.show', $car->id) }}">Kembali ke detail mobil</a>
    </div>

    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        Mohon centang persetujuan biaya booking sebelum melanjutkan ke pembayaran.
      </div>
    @endif

    @if (session('success'))
      <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1.55fr)_minmax(430px,0.95fr)]">
      <section class="rounded-[2rem] bg-white p-6 md:p-8 shadow-xl shadow-blue-900/5">
        <form id="online-booking-form" method="POST" action="{{ route('customer.orders.store') }}" class="space-y-8">
          @csrf
          <input type="hidden" name="car_id" value="{{ $car->id }}"/>
          <input type="hidden" name="payment_method" value="transfer"/>
          <input type="hidden" name="sales_flow" value="direct_purchase"/>
          @if ($sourceOffer)
            <input type="hidden" name="offer_id" value="{{ $sourceOffer->id }}"/>
          @endif

          @if ($sourceOffer)
            <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50/80 p-5">
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Harga hasil negosiasi</p>
              <p class="mt-2 text-lg font-extrabold text-emerald-900">Unit {{ $unitCode }} sudah disetujui supervisor dengan harga hasil negosiasi.</p>
              <p class="mt-2 text-sm leading-6 text-emerald-800">
                Pesan online ini menggunakan harga final hasil negosiasi yang sudah disepakati di sistem.
              </p>
            </div>
          @endif

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">1. Bayar Biaya Pemesanan Saja</h2>
            <p class="mt-3 text-[15px] leading-7 text-on-surface-variant">
              Pembelian online Maharani Mobil menggunakan skema harga cash. Setelah biaya booking dibayarkan, customer akan diarahkan ke rekening resmi Maharani Mobil untuk proses pembayaran.
            </p>

            <div class="mt-6 grid grid-cols-1 md:max-w-[460px]">
              <div class="rounded-[1.35rem] border-2 border-[#4a86d9] bg-white px-6 py-5 shadow-sm">
                <p class="text-sm text-on-surface-variant">Harga Cash</p>
                <p class="mt-2 text-[2rem] font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</p>
              </div>
            </div>
          </div>

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">2. Rincian Harga</h2>
            <div class="mt-6 space-y-5">
              <div class="flex items-center justify-between gap-4 text-lg font-bold text-primary">
                <span>Harga Mobil (Harga Cash)</span>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 text-base font-semibold text-on-surface-variant">
                <span>Biaya Booking</span>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($bookingFee) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-lg font-bold text-primary">
                <span>Sisa Pembayaran</span>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($remainingBalance) }}</span>
              </div>
            </div>
          </div>

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">3. Lokasi Mobil</h2>
            <div class="mt-5 rounded-[1.3rem] bg-slate-50 px-5 py-4 text-sm leading-7 text-on-surface-variant">
              Unit berada di showroom Maharani Mobil Pekanbaru. Setelah pembayaran booking fee berhasil diproses, tim Maharani akan melanjutkan konfirmasi pesanan dan penjadwalan serah terima unit.
            </div>
          </div>
        </form>
      </section>

      <aside class="w-full max-w-[520px] xl:justify-self-end">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-blue-900/5">
          <div class="grid grid-cols-[112px_minmax(0,1fr)] items-start gap-5 p-5 md:grid-cols-[128px_minmax(0,1fr)] md:p-6">
            <img alt="{{ $carName }}" class="h-20 w-28 rounded-2xl object-cover md:h-24 md:w-32" src="{{ $carImage }}"/>
            <div class="min-w-0">
              <h2 class="text-[1.45rem] font-extrabold leading-tight text-primary md:text-[1.7rem]">{{ $carName }}</h2>
              <p class="mt-2 text-sm font-semibold text-slate-500">Kode Unit: {{ $unitCode }}</p>
              <p class="mt-3 text-[14px] leading-7 text-on-surface-variant">
                Mobil dipesan hanya untuk Anda. Selesaikan pembayaran booking fee untuk melanjutkan proses pemesanan online.
              </p>
            </div>
          </div>

          <div class="border-t border-slate-200 px-5 py-5 md:px-6">
            <div class="space-y-5 text-primary">
              <div class="flex items-center justify-between gap-4 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p>Booking Fee</p>
                  <span class="mt-2 inline-flex rounded-full bg-[#ffe8a6] px-3 py-1 text-sm font-semibold text-[#8a4d12]">Bayar Sekarang</span>
                </div>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($bookingFee) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p>Sisa Pembayaran</p>
                  <span class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-500">Dibayar Nanti</span>
                </div>
                <span>{{ \App\Support\CurrencyFormatter::rupiah($remainingBalance) }}</span>
              </div>
            </div>

            <div class="mt-6 border-t border-slate-200 pt-6">
              <div class="flex items-start justify-between gap-4 text-primary">
                <div class="text-[1.05rem] font-bold md:text-[1.15rem]">
                  <p>Pembayaran Biaya</p>
                  <p>Pemesanan</p>
                </div>
                <div class="text-right text-[1.05rem] font-bold md:text-[1.15rem]">
                  <p>{{ \App\Support\CurrencyFormatter::rupiah($bookingFee) }}</p>
                </div>
              </div>

              <div class="mt-6">
                <button
                  class="inline-flex w-full items-center justify-center rounded-[1rem] bg-[#ffcf33] px-5 py-4 text-lg font-bold text-primary disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                  type="submit"
                  form="online-booking-form"
                  id="online-booking-submit"
                  disabled
                >
                  Bayar Sekarang
                </button>
              </div>

              <label class="mt-4 flex items-start gap-3 text-sm leading-7 text-on-surface-variant">
                <input
                  class="mt-1 h-5 w-5 rounded border-slate-300 text-[#4a86d9] focus:ring-[#4a86d9]"
                  type="checkbox"
                  name="booking_fee_agreement"
                  value="1"
                  form="online-booking-form"
                  id="booking-fee-agreement"
                  @checked(old('booking_fee_agreement'))
                />
                <span>Dengan memilih kotak ini, saya mengonfirmasikan bahwa saya telah membaca, memahami, dan setuju untuk membayar biaya booking sebelum melanjutkan Transaksi pesanan.</span>
              </label>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya sedang memesan online untuk unit ' . $carName . '.'])
  @include('components.ui-system-footer')
  <script>
    (() => {
      const agreement = document.getElementById('booking-fee-agreement');
      const submit = document.getElementById('online-booking-submit');

      const syncSubmitState = () => {
        if (!agreement || !submit) {
          return;
        }

        submit.disabled = !agreement.checked;
      };

      if (agreement) {
        agreement.addEventListener('change', syncSubmitState);
      }
      syncSubmitState();
    })();
  </script>
</body>
</html>
