@php
  $car = $order->car;
  $carName = trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '') . ' ' . ($car?->tahun ?? ''));
  $selectedBankCode = old('bank_account', 'mandiri');
  $gatewayCheckoutUrl = $payment?->gateway_checkout_url;
  $gatewayPending = filled($gatewayCheckoutUrl) && ($payment?->status ?? 'pending') !== 'verified';
  $isCreditPurchase = $order->is_credit_purchase;
  $localSimulationEnabled = app()->environment(['local', 'testing']) && ! $xenditEnabled && ! $isCreditPurchase;
  $selectedPaymentPlan = $selectedPaymentPlan ?? 'booking';
  $fullPaymentAmount = (float) $order->total;
  $bookingPaymentAmount = (float) $bookingFee;
  $activePaymentAmount = $isCreditPurchase
    ? (float) $bookingFee
    : ($selectedPaymentPlan === 'full' ? $fullPaymentAmount : $bookingPaymentAmount);
  $activeRemainingBalance = max((float) $order->total - $activePaymentAmount, 0);
  $paymentLabel = $isCreditPurchase
    ? 'Pembayaran DP Kredit'
    : ($selectedPaymentPlan === 'full' ? 'Transfer Bayar Lunas' : 'Transfer Booking Fee');
  $paymentDescription = $isCreditPurchase
    ? 'Lanjutkan pembayaran DP ke pihak showroom setelah pengajuan kredit disetujui oleh supervisor.'
    : ($selectedPaymentPlan === 'full'
      ? 'Lanjutkan pembayaran lunas sesuai pilihan customer pada halaman pesan online.'
      : 'Lanjutkan pembayaran booking fee sesuai pilihan customer pada halaman pesan online.');
  $paymentMethodInput = $isCreditPurchase ? 'credit' : 'transfer';
  $cashStepLabel = $selectedPaymentPlan === 'full' ? 'Step 2 - Transfer Bayar Lunas' : 'Step 2 - Transfer Booking Fee';
  $cashPaymentTypeLabel = $selectedPaymentPlan === 'full' ? 'Pembayaran Lunas Full' : 'Booking Fee';
  $cashBalanceLabel = 'Sisa Pembayaran';
  $paymentStatus = (string) ($payment?->status ?? 'pending');
  $paidAmount = (float) ($payment?->amount ?? 0);
  $orderTotalAmount = (float) $order->total;

  if ($isCreditPurchase && $paymentStatus === 'verified' && $paidAmount < $orderTotalAmount) {
      $statusLabel = 'DP KREDIT TERVERIFIKASI';
  } elseif ($paymentStatus === 'verified' && $paidAmount >= $orderTotalAmount) {
      $statusLabel = 'LUNAS TERVERIFIKASI';
  } else {
      $statusLabel = strtoupper($paymentStatus);
  }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $paymentLabel }} | Maharani Mobil</title>
  <meta name="description" content="{{ $paymentDescription }}"/>
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
        <p id="payment-step-label" class="text-xs uppercase tracking-[0.24em] text-slate-400 font-semibold">{{ $isCreditPurchase ? 'Step 2 - Pembayaran DP Kredit' : $cashStepLabel }}</p>
        <h1 class="text-4xl font-extrabold text-primary mt-2">{{ $paymentLabel }}</h1>
      </div>
      <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('checkout.cash', ['car_id' => $order->car_id]) }}">Kembali ke pesan online</a>
    </div>

    @if (session('success'))
      <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
      </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
      <section class="lg:col-span-2 rounded-[2rem] bg-white p-6 md:p-8 shadow-xl shadow-blue-900/5">
        <div class="mb-8 rounded-[1.5rem] border border-slate-100 p-5">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-400 font-semibold">Pesanan aktif</p>
          <div class="mt-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <h2 class="text-2xl font-extrabold text-primary">{{ $carName }}</h2>
              <p class="mt-1 text-sm text-on-surface-variant">{{ $order->order_reference }} / {{ $isCreditPurchase ? 'Pembelian kredit leasing' : 'Pembelian online cash' }}</p>
            </div>
            <div class="text-right">
              <p id="active-payment-type-badge" class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $isCreditPurchase ? 'Nominal DP Kredit' : $cashPaymentTypeLabel }}</p>
              <p id="active-payment-amount" class="mt-1 text-3xl font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($activePaymentAmount) }}</p>
            </div>
          </div>
        </div>

        @if ($xenditEnabled)
          <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50/70 p-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Pembayaran Otomatis</p>
            <h3 class="mt-2 text-2xl font-extrabold text-emerald-900">Bayar lewat halaman aman Xendit</h3>
            <p class="mt-3 text-sm leading-7 text-emerald-800">
              Customer akan diarahkan ke halaman pembayaran aman Xendit untuk memilih metode yang paling nyaman, termasuk transfer bank, virtual account, QRIS, dan e-wallet. Setelah pembayaran berhasil, status di sistem Maharani Mobil akan terupdate otomatis melalui webhook.
            </p>

            @if ($gatewayPending)
              <div class="mt-5 rounded-[1.2rem] border border-emerald-200 bg-white/90 px-4 py-3 text-sm text-emerald-800">
                Link pembayaran Anda sudah dibuat sebelumnya dan masih aktif. Anda bisa lanjutkan dari link yang sama tanpa membuat ulang invoice.
              </div>
            @endif
          </div>

          <form method="POST" action="{{ route('customer.payments.store') }}" class="mt-5 space-y-5">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}"/>
            <input type="hidden" name="method" value="{{ $paymentMethodInput }}"/>

            <div class="rounded-[1.5rem] border border-slate-100 p-6">
            <h3 class="text-2xl font-extrabold text-primary">Rekening Settlement Showroom</h3>
            <p class="mt-3 text-sm leading-7 text-on-surface-variant">
              Dana transaksi online akan diproses melalui halaman aman Xendit dan disalurkan ke rekening resmi showroom yang telah dikonfigurasi di sistem pembayaran.
            </p>

            <div class="mt-5 rounded-[1.4rem] border border-outline-variant bg-slate-50 px-5 py-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $settlementAccount['bank'] ?? 'Bank Mandiri' }}</p>
              <p class="mt-2 text-sm font-semibold text-slate-700">a.n. {{ $settlementAccount['account_name'] ?? 'Diki Susanto' }}</p>
              <p class="mt-1 text-xl font-extrabold text-primary">{{ $settlementAccount['account_number'] ?? '1080093012152' }}</p>
            </div>

            @if (($payment?->status ?? null) === 'verified')
              <div class="mt-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ (float) ($payment?->amount ?? 0) >= (float) $order->total
                  ? 'Pembayaran lunas untuk order ini sudah diterima. Dokumen transaksi digital sekarang dapat dilihat dari halaman tracking.'
                  : 'Pembayaran booking fee untuk order ini sudah diterima. Anda bisa kembali ke halaman tracking untuk melihat progres pesanan.' }}
              </div>
            @else
              <div class="mt-6 flex justify-end">
                <button class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-4 text-sm font-bold text-white" type="submit">
                  Lanjutkan Pembayaran
                </button>
              </div>
            @endif
            </div>
          </form>
        @else
          <form method="POST" action="{{ route('customer.payments.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}"/>
            <input type="hidden" name="method" value="{{ $paymentMethodInput }}"/>

            <div class="rounded-[1.5rem] border border-slate-100 p-6">
              <h3 class="text-2xl font-extrabold text-primary">Pilih Rekening Tujuan Maharani Mobil</h3>
              <p class="mt-3 text-sm leading-7 text-on-surface-variant">
                {{ $isCreditPurchase
                  ? 'Pembayaran DP kredit dilakukan ke rekening resmi showroom berikut. Setelah dana DP diverifikasi supervisor, showroom dapat melanjutkan proses kredit sampai pelunasan leasing dan serah terima unit.'
                  : ($selectedPaymentPlan === 'full'
                    ? 'Integrasi pembayaran otomatis belum diaktifkan. Untuk sementara, customer dapat mencatat pembayaran lunas full ke rekening resmi showroom berikut agar divalidasi internal.'
                    : 'Integrasi pembayaran otomatis belum diaktifkan. Untuk sementara, customer dapat mencatat pembayaran booking fee ke rekening resmi showroom berikut agar divalidasi internal.') }}
              </p>

              <div class="mt-6 space-y-4">
                @foreach ($bankAccounts as $bank)
                  <label class="flex cursor-pointer gap-4 rounded-[1.4rem] border border-outline-variant p-5">
                    <input type="radio" name="bank_account" value="{{ $bank['code'] }}" @checked($selectedBankCode === $bank['code']) />
                    <div class="flex-1">
                      <div class="flex items-center justify-between gap-3">
                        <h4 class="text-lg font-bold text-primary">{{ $bank['bank'] }}</h4>
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Rekening resmi</span>
                      </div>
                      <p class="mt-2 text-sm font-semibold text-slate-700">a.n. {{ $bank['account_name'] }}</p>
                      <p class="mt-1 text-base font-extrabold text-primary">{{ $bank['account_number'] }}</p>
                    </div>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="flex justify-end">
              <button class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-4 text-sm font-bold text-white" type="submit">
                Lanjutkan Pembayaran
              </button>
            </div>
          </form>

          @if ($localSimulationEnabled)
            <div class="mt-5 rounded-[1.5rem] border border-sky-200 bg-sky-50/80 p-5">
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-700">Mode Uji Lokal</p>
              <p class="mt-2 text-sm leading-7 text-sky-800">
                Karena gateway real belum aktif di `.env`, pembayaran bank belum bisa benar-benar diproses dari tombol di atas. Untuk melihat hasil faktur, kwitansi digital, dan BAST sekarang juga, gunakan simulasi pelunasan test berikut.
              </p>
              <form method="POST" action="{{ route('customer.payments.simulate-success') }}" class="mt-4 flex justify-end">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}"/>
                <button class="inline-flex items-center justify-center rounded-xl border border-sky-200 bg-white px-6 py-4 text-sm font-bold text-sky-800" type="submit">
                  Simulasikan Pembayaran Berhasil
                </button>
              </form>
            </div>
          @endif
        @endif
      </section>

      <aside class="space-y-6">
        <div class="rounded-[2rem] bg-primary p-6 text-white shadow-xl shadow-blue-900/20">
          <h2 class="mb-4 text-lg font-bold">Ringkasan Pembayaran</h2>
          <div class="space-y-3 text-sm text-blue-100">
            <div class="flex justify-between">
              <span id="summary-payment-label">{{ $isCreditPurchase ? 'Nominal DP' : $cashPaymentTypeLabel }}</span>
              <span id="summary-payment-amount">{{ \App\Support\CurrencyFormatter::rupiah($activePaymentAmount) }}</span>
            </div>
            <div class="flex justify-between">
              <span id="summary-remaining-label">{{ $isCreditPurchase ? 'Pelunasan Leasing' : $cashBalanceLabel }}</span>
              <span id="summary-remaining-amount">{{ \App\Support\CurrencyFormatter::rupiah($activeRemainingBalance) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Metode</span>
              <span>{{ $isCreditPurchase ? 'DP KREDIT SHOWROOM' : ($xenditEnabled ? 'XENDIT HOSTED PAYMENT' : 'TRANSFER BANK') }}</span>
            </div>
            <div class="border-t border-white/15 pt-3 flex justify-between font-bold text-white">
              <span>Status</span>
              <span>{{ $statusLabel }}</span>
            </div>
            @if ($payment?->gateway_channel)
              <div class="flex justify-between">
                <span>Channel</span>
                <span>{{ strtoupper($payment->gateway_channel_label) }}</span>
              </div>
            @endif
          </div>
        </div>

        <div class="rounded-[2rem] bg-white p-6 shadow-xl shadow-blue-900/5">
          <h3 class="text-lg font-bold text-primary">Informasi Penting</h3>
          <p class="mt-3 text-sm leading-7 text-on-surface-variant">
            @if ($isCreditPurchase)
              Customer tidak perlu upload bukti pembayaran. DP kredit yang sudah dicatat akan divalidasi oleh supervisor. Setelah DP diterima, pelunasan utama tetap dilakukan oleh leasing sesuai approval pembiayaan.
            @elseif ($xenditEnabled)
              Customer tidak perlu mengunggah bukti pembayaran. Setelah menyelesaikan pembayaran di halaman Xendit, sistem Maharani Mobil akan menerima update otomatis. Jika pembayaran yang dipilih adalah booking fee, sisa pelunasan tetap dilanjutkan di showroom. Jika pembayaran yang dipilih adalah lunas full, dokumen transaksi digital akan langsung siap setelah pembayaran terverifikasi.
            @else
              Customer tidak perlu mengunggah bukti pembayaran. Data transaksi akan otomatis tercatat dalam sistem dan diverifikasi oleh supervisor berdasarkan mutasi rekening resmi Maharani Mobil.
            @endif
          </p>
        </div>
      </aside>
    </div>
  </main>

  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan pembayaran untuk order ' . $order->order_reference . '.'])
  @include('components.ui-system-footer')
</body>
</html>
