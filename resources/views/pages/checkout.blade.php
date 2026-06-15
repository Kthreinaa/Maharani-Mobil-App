@php
  $carName = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
  $unitCode = $car->kode_unit ?: 'Tanpa kode unit';
  $carImage = is_array($car->photos ?? null) && !empty($car->photos[0])
    ? asset('storage/' . $car->photos[0])
    : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1400&auto=format&fit=crop';
  $sourceOffer = $sourceOffer ?? null;
  $selectedPaymentPlan = old('payment_plan', 'booking');
  $payNowAmount = $selectedPaymentPlan === 'full' ? (float) $cashPrice : (float) $bookingFee;
  $payLaterAmount = $selectedPaymentPlan === 'full' ? 0 : (float) $remainingBalance;
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

    <div class="grid grid-cols-1 items-start gap-8 xl:grid-cols-[minmax(0,1.58fr)_minmax(430px,0.92fr)]">
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
            <h2 class="text-[2rem] font-extrabold text-primary">1. Pilih Jenis Pembayaran</h2>
            <p class="mt-3 text-[15px] leading-7 text-on-surface-variant">
              Pembelian online Maharani Mobil menggunakan skema harga cash. Customer bisa memilih bayar booking fee terlebih dahulu untuk mengamankan unit, atau langsung bayar lunas full seharga mobil.
            </p>

            <div class="mt-6 flex w-full flex-col gap-4">
              <label class="checkout-plan-card flex w-full cursor-pointer flex-col rounded-[1.5rem] border-2 px-7 py-6 shadow-sm transition" data-payment-plan-card="booking">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="text-[1.85rem] font-extrabold text-primary leading-tight">Bayar Booking Dulu</p>
                    <p class="mt-3 max-w-3xl text-[1.05rem] leading-8 text-on-surface-variant">Customer cukup bayar biaya pemesanan sekarang, lalu sisa pelunasan dilanjutkan nanti setelah proses pemesanan dikonfirmasi showroom.</p>
                  </div>
                  <input type="radio" name="payment_plan" value="booking" class="mt-1" @checked($selectedPaymentPlan === 'booking') />
                </div>
                <div class="mt-7 flex items-end justify-between gap-6 border-t border-slate-200/80 pt-5">
                  <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Bayar sekarang</p>
                    <p class="mt-3 whitespace-nowrap text-[2rem] font-extrabold tracking-tight text-primary md:text-[2.2rem]">{{ \App\Support\CurrencyFormatter::rupiah($bookingFee) }}</p>
                  </div>
                  <span class="inline-flex rounded-full bg-amber-50 px-4 py-2 text-sm font-bold text-amber-700">Aman untuk booking unit</span>
                </div>
              </label>

              <label class="checkout-plan-card flex w-full cursor-pointer flex-col rounded-[1.5rem] border-2 px-7 py-6 shadow-sm transition" data-payment-plan-card="full">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="text-[1.85rem] font-extrabold text-primary leading-tight">Bayar Lunas Full</p>
                    <p class="mt-3 max-w-3xl text-[1.05rem] leading-8 text-on-surface-variant">Customer langsung menyelesaikan seluruh harga mobil pada pembayaran tahap berikutnya agar transaksi lebih cepat selesai.</p>
                  </div>
                  <input type="radio" name="payment_plan" value="full" class="mt-1" @checked($selectedPaymentPlan === 'full') />
                </div>
                <div class="mt-7 flex items-end justify-between gap-6 border-t border-slate-200/80 pt-5">
                  <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Bayar sekarang</p>
                    <p class="mt-3 whitespace-nowrap text-[2rem] font-extrabold tracking-tight text-primary md:text-[2.2rem]">{{ \App\Support\CurrencyFormatter::rupiah($cashPrice) }}</p>
                  </div>
                  <span class="inline-flex rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">Transaksi langsung lunas</span>
                </div>
              </label>
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
                <span id="checkout-now-label">{{ $selectedPaymentPlan === 'full' ? 'Pembayaran Lunas Full' : 'Biaya Booking' }}</span>
                <span id="checkout-now-amount">{{ \App\Support\CurrencyFormatter::rupiah($payNowAmount) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-lg font-bold text-primary">
                <span id="checkout-later-label">{{ $selectedPaymentPlan === 'full' ? 'Sisa Pembayaran' : 'Sisa Pembayaran' }}</span>
                <span id="checkout-later-amount">{{ \App\Support\CurrencyFormatter::rupiah($payLaterAmount) }}</span>
              </div>
            </div>
          </div>

          <div class="rounded-[1.6rem] border border-slate-100 p-6">
            <h2 class="text-[2rem] font-extrabold text-primary">3. Lokasi Mobil</h2>
            <div id="checkout-location-copy" class="mt-5 rounded-[1.3rem] bg-slate-50 px-5 py-4 text-sm leading-7 text-on-surface-variant">
              @if ($selectedPaymentPlan === 'full')
                Unit berada di showroom Maharani Mobil Pekanbaru. Setelah pembayaran lunas full berhasil diproses, tim Maharani akan melanjutkan konfirmasi transaksi dan menyiapkan dokumen digital serta serah terima unit.
              @else
                Unit berada di showroom Maharani Mobil Pekanbaru. Setelah pembayaran booking fee berhasil diproses, tim Maharani akan melanjutkan konfirmasi pesanan dan penjadwalan serah terima unit.
              @endif
            </div>
          </div>
        </form>
      </section>

      <aside class="w-full max-w-[520px] self-start xl:justify-self-end">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-blue-900/5">
          <div class="grid grid-cols-[112px_minmax(0,1fr)] items-start gap-5 p-5 md:grid-cols-[128px_minmax(0,1fr)] md:p-6">
            <img alt="{{ $carName }}" class="h-20 w-28 rounded-2xl object-cover md:h-24 md:w-32" src="{{ $carImage }}"/>
            <div class="min-w-0">
              <h2 class="text-[1.45rem] font-extrabold leading-tight text-primary md:text-[1.7rem]">{{ $carName }}</h2>
              <p class="mt-2 text-sm font-semibold text-slate-500">Kode Unit: {{ $unitCode }}</p>
              <p id="checkout-summary-copy" class="mt-3 text-[14px] leading-7 text-on-surface-variant">
                @if ($selectedPaymentPlan === 'full')
                  Mobil dipesan hanya untuk Anda. Lanjutkan ke pembayaran lunas full untuk menyelesaikan proses pemesanan online.
                @else
                  Mobil dipesan hanya untuk Anda. Selesaikan pembayaran booking fee untuk melanjutkan proses pemesanan online.
                @endif
              </p>
            </div>
          </div>

          <div class="border-t border-slate-200 px-5 py-5 md:px-6">
            <div class="space-y-5 text-primary">
              <div class="flex items-center justify-between gap-4 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p id="checkout-summary-now-label">{{ $selectedPaymentPlan === 'full' ? 'Bayar Lunas Full' : 'Booking Fee' }}</p>
                  <span id="checkout-summary-now-badge" class="mt-2 inline-flex rounded-full bg-[#ffe8a6] px-3 py-1 text-sm font-semibold text-[#8a4d12]">Bayar Sekarang</span>
                </div>
                <span id="checkout-summary-now-amount">{{ \App\Support\CurrencyFormatter::rupiah($payNowAmount) }}</span>
              </div>
              <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-[1.05rem] font-bold md:text-[1.15rem]">
                <div>
                  <p>Sisa Pembayaran</p>
                  <span id="checkout-summary-later-badge" class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-500">{{ $selectedPaymentPlan === 'full' ? 'Tidak Ada Sisa' : 'Dibayar Nanti' }}</span>
                </div>
                <span id="checkout-summary-later-amount">{{ \App\Support\CurrencyFormatter::rupiah($payLaterAmount) }}</span>
              </div>
            </div>

            <div class="mt-6 border-t border-slate-200 pt-6">
              <div class="flex items-start justify-between gap-4 text-primary">
                <div class="text-[1.05rem] font-bold md:text-[1.15rem]">
                  <p id="checkout-submit-amount-label-top">{{ $selectedPaymentPlan === 'full' ? 'Pembayaran' : 'Pembayaran Biaya' }}</p>
                  <p id="checkout-submit-amount-label-bottom">{{ $selectedPaymentPlan === 'full' ? 'Lunas Full' : 'Pemesanan' }}</p>
                </div>
                <div class="text-right text-[1.05rem] font-bold md:text-[1.15rem]">
                  <p id="checkout-submit-amount">{{ \App\Support\CurrencyFormatter::rupiah($payNowAmount) }}</p>
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
                  <span id="checkout-submit-text">{{ $selectedPaymentPlan === 'full' ? 'Bayar Lunas Sekarang' : 'Bayar Booking Sekarang' }}</span>
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
                <span id="checkout-agreement-copy">
                  {{ $selectedPaymentPlan === 'full'
                    ? 'Dengan memilih kotak ini, saya mengonfirmasikan bahwa saya telah membaca, memahami, dan setuju untuk membayar lunas penuh sesuai harga mobil sebelum melanjutkan transaksi pesanan.'
                    : 'Dengan memilih kotak ini, saya mengonfirmasikan bahwa saya telah membaca, memahami, dan setuju untuk membayar biaya booking sebelum melanjutkan transaksi pesanan.' }}
                </span>
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
      const paymentPlanInputs = Array.from(document.querySelectorAll('input[name="payment_plan"]'));

      const formatRupiah = (amount) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount);

      const bookingAmount = {{ json_encode((float) $bookingFee) }};
      const fullAmount = {{ json_encode((float) $cashPrice) }};
      const remainingAmount = {{ json_encode((float) $remainingBalance) }};

      const syncSubmitState = () => {
        if (!agreement || !submit) {
          return;
        }

        submit.disabled = !agreement.checked;
      };

      if (agreement) {
        agreement.addEventListener('change', syncSubmitState);
      }

      const renderPaymentPlan = (plan) => {
        const isFull = plan === 'full';
        const currentPayNow = isFull ? fullAmount : bookingAmount;
        const currentPayLater = isFull ? 0 : remainingAmount;

        document.querySelectorAll('[data-payment-plan-card]').forEach((card) => {
          const active = card.getAttribute('data-payment-plan-card') === plan;
          card.classList.toggle('border-[#4a86d9]', active);
          card.classList.toggle('bg-blue-50/50', active);
          card.classList.toggle('border-slate-200', !active);
        });

        const setText = (id, value) => {
          const element = document.getElementById(id);
          if (element) {
            element.textContent = value;
          }
        };

        setText('checkout-now-label', isFull ? 'Pembayaran Lunas Full' : 'Biaya Booking');
        setText('checkout-now-amount', formatRupiah(currentPayNow));
        setText('checkout-later-amount', formatRupiah(currentPayLater));
        setText('checkout-summary-copy', isFull
          ? 'Mobil dipesan hanya untuk Anda. Lanjutkan ke pembayaran lunas full untuk menyelesaikan proses pemesanan online.'
          : 'Mobil dipesan hanya untuk Anda. Selesaikan pembayaran booking fee untuk melanjutkan proses pemesanan online.');
        setText('checkout-summary-now-label', isFull ? 'Bayar Lunas Full' : 'Booking Fee');
        setText('checkout-summary-now-amount', formatRupiah(currentPayNow));
        setText('checkout-summary-later-amount', formatRupiah(currentPayLater));
        setText('checkout-submit-amount-label-top', isFull ? 'Pembayaran' : 'Pembayaran Biaya');
        setText('checkout-submit-amount-label-bottom', isFull ? 'Lunas Full' : 'Pemesanan');
        setText('checkout-submit-amount', formatRupiah(currentPayNow));
        setText('checkout-submit-text', isFull ? 'Bayar Lunas Sekarang' : 'Bayar Booking Sekarang');
        setText('checkout-agreement-copy', isFull
          ? 'Dengan memilih kotak ini, saya mengonfirmasikan bahwa saya telah membaca, memahami, dan setuju untuk membayar lunas penuh sesuai harga mobil sebelum melanjutkan transaksi pesanan.'
          : 'Dengan memilih kotak ini, saya mengonfirmasikan bahwa saya telah membaca, memahami, dan setuju untuk membayar biaya booking sebelum melanjutkan transaksi pesanan.');
        setText('checkout-location-copy', isFull
          ? 'Unit berada di showroom Maharani Mobil Pekanbaru. Setelah pembayaran lunas full berhasil diproses, tim Maharani akan melanjutkan konfirmasi transaksi dan menyiapkan dokumen digital serta serah terima unit.'
          : 'Unit berada di showroom Maharani Mobil Pekanbaru. Setelah pembayaran booking fee berhasil diproses, tim Maharani akan melanjutkan konfirmasi pesanan dan penjadwalan serah terima unit.');
        setText('checkout-summary-later-badge', isFull ? 'Tidak Ada Sisa' : 'Dibayar Nanti');
      };

      paymentPlanInputs.forEach((input) => {
        input.addEventListener('change', () => renderPaymentPlan(input.value));
      });

      const initialPlan = paymentPlanInputs.find((input) => input.checked)?.value ?? 'booking';
      renderPaymentPlan(initialPlan);
      syncSubmitState();
    })();
  </script>
</body>
</html>
