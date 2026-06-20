@php
  $car = $car ?? $order?->car;
  $carName = trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '') . ' ' . ($car?->tahun ?? ''));
  $carPhotos = is_array($car?->photos ?? null) ? array_values(array_filter($car->photos ?? [])) : [];
  $carImage = !empty($carPhotos[0])
    ? asset('storage/' . $carPhotos[0])
    : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1400&auto=format&fit=crop';
  $selectedBankCode = old('bank_account', 'mandiri');
  $gatewayCheckoutUrl = $payment?->gateway_checkout_url ?? null;
  $gatewayPending = filled($gatewayCheckoutUrl) && ($payment?->status ?? 'pending') !== 'verified';
  $isCreditPurchase = $isCreditPurchase ?? ($order?->is_credit_purchase ?? false);
  $localSimulationEnabled = app()->environment(['local', 'testing']) && ! $xenditEnabled && ! $isCreditPurchase;
  $selectedPaymentPlan = $selectedPaymentPlan ?? 'booking';
  $fullPaymentAmount = (float) ($orderTotal ?? ($order?->total ?? 0));
  $bookingPaymentAmount = (float) $bookingFee;
  $activePaymentAmount = $isCreditPurchase
    ? (float) $bookingFee
    : ($selectedPaymentPlan === 'full' ? $fullPaymentAmount : $bookingPaymentAmount);
  $activeRemainingBalance = max($fullPaymentAmount - $activePaymentAmount, 0);
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
  $paymentStatus = (string) ($payment?->status ?? 'pending');
  $paidAmount = (float) ($payment?->amount ?? 0);
  $orderTotalAmount = $fullPaymentAmount;
  $draftToken = $draftToken ?? null;
  $showPaymentSuccess = $paymentStatus === 'verified';
  $statusLabel = match (true) {
      $isCreditPurchase && $paymentStatus === 'verified' && $paidAmount < $orderTotalAmount => 'DP KREDIT TERVERIFIKASI',
      $paymentStatus === 'verified' && $paidAmount >= $orderTotalAmount => 'LUNAS TERVERIFIKASI',
      default => strtoupper($paymentStatus),
  };
  $statusBadgeClass = match ($paymentStatus) {
      'verified' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
      'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
      default => 'border-amber-200 bg-amber-50 text-amber-700',
  };
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
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-background text-on-background">
  @include('components.public-site-header', ['active' => 'orders', 'overlap' => false, 'showLogout' => false])

  <main class="landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <section class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#08132e_0%,#13254f_62%,#1b356d_100%)] px-6 py-7 text-white shadow-[0_20px_60px_rgba(8,19,46,0.18)] md:px-8">
        <div class="absolute inset-y-0 right-0 w-[38%] bg-[radial-gradient(circle_at_top,rgba(245,166,35,0.22),transparent_58%)]"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
          <div class="max-w-3xl">
            <p id="payment-step-label" class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#f7c35f]">{{ $isCreditPurchase ? 'Step 2 - Pembayaran DP Kredit' : $cashStepLabel }}</p>
            <h1 class="mt-3 font-headline text-[34px] font-extrabold leading-tight md:text-[42px]">{{ $paymentLabel }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-200">
              {{ $isCreditPurchase
                ? 'Selesaikan pembayaran DP ke rekening resmi showroom. Setelah pembayaran masuk, pesanan akan tercatat dan supervisor dapat melanjutkan proses kredit Anda.'
                : ($selectedPaymentPlan === 'full'
                  ? 'Pembayaran lunas akan langsung mencatat pesanan ke sistem setelah berhasil. Semua informasi di halaman ini dibuat agar customer mudah mengikuti langkahnya.'
                  : '') }}
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-100">
              {{ $order ? 'Pesanan aktif' : 'Draft pembayaran' }}
            </span>
            <a class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/16" href="{{ route('checkout.cash', ['car_id' => $car?->id]) }}">
              Kembali ke pesan online
            </a>
          </div>
        </div>
      </section>

      @if (session('success'))
        <div class="mt-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="mt-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ session('error') }}
        </div>
      @endif

      <div class="mt-8 grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1.42fr)_390px] xl:items-stretch">
        <section class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-sm">
              <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Referensi</p>
              <p class="mt-3 text-lg font-extrabold text-primary">{{ $displayReference }}</p>
              <p class="mt-2 text-sm leading-6 text-on-surface-variant">Nomor ini akan aktif sebagai pesanan setelah pembayaran berhasil.</p>
            </div>
            <div class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-sm">
              <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Bayar Sekarang</p>
              <p class="mt-3 text-lg font-extrabold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($activePaymentAmount) }}</p>
              <p class="mt-2 text-sm leading-6 text-on-surface-variant">{{ $isCreditPurchase ? 'Nominal DP kredit yang perlu dibayar.' : ($selectedPaymentPlan === 'full' ? 'Pembayaran penuh sesuai harga unit.' : 'Biaya booking untuk mengamankan proses pembelian.') }}</p>
            </div>
          </div>

          <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div>
              <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#f5a623]">Panduan Pembayaran</p>
                <h2 class="mt-2 font-headline text-[30px] font-extrabold text-primary"></h2>
              </div>
              <div class="mt-4 inline-flex max-w-full items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px] text-primary">verified_user</span>
                <span>Customer tidak perlu upload bukti pembayaran. Sistem akan mencatat pesanan setelah pembayaran berhasil.</span>
              </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
              <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-5">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#08132e] text-sm font-extrabold text-white">1</span>
                <h3 class="mt-4 text-lg font-extrabold text-primary">Periksa unit & nominal</h3>
                <p class="mt-2 text-sm leading-7 text-on-surface-variant">Pastikan nama unit, jenis pembayaran, dan nominal yang akan dibayar sudah sesuai.</p>
              </div>
              <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-5">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#08132e] text-sm font-extrabold text-white">2</span>
                <h3 class="mt-4 text-lg font-extrabold text-primary">Lanjutkan pembayaran</h3>
                <p class="mt-2 text-sm leading-7 text-on-surface-variant">Lakukan pembayaran melalui rekening resmi atau halaman pembayaran resmi Maharani Mobil sesuai metode yang dipilih.</p>
              </div>
              <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-5">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#08132e] text-sm font-extrabold text-white">3</span>
                <h3 class="mt-4 text-lg font-extrabold text-primary">Pesanan masuk otomatis</h3>
                <p class="mt-2 text-sm leading-7 text-on-surface-variant">Setelah dana berhasil diterima, pesanan langsung tercatat dan masuk ke proses supervisor.</p>
              </div>
            </div>
          </div>

          <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div>
              <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#f5a623]">Form Pembayaran</p>
                <h2 class="mt-2 font-headline text-[30px] font-extrabold text-primary">{{ $xenditEnabled ? 'Lanjut ke pembayaran aman' : 'Pilih rekening resmi showroom' }}</h2>
              </div>
            </div>

            @if ($xenditEnabled)
              <div class="mt-6 rounded-[1.6rem] border border-emerald-200 bg-[linear-gradient(135deg,rgba(16,185,129,0.10),rgba(255,255,255,0.92))] p-6">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-emerald-700">Pembayaran Otomatis</p>
                <h3 class="mt-2 text-2xl font-extrabold text-emerald-950">Bayar lewat halaman aman Xendit</h3>
                <p class="mt-3 text-sm leading-7 text-emerald-900/80">
                  Customer akan diarahkan ke halaman pembayaran aman untuk memilih transfer bank, virtual account, QRIS, atau e-wallet. Setelah pembayaran selesai, sistem Maharani Mobil langsung mencatat transaksi ini.
                </p>

                @if ($gatewayPending)
                  <div class="mt-4 rounded-[1.2rem] border border-emerald-200 bg-white/90 px-4 py-3 text-sm text-emerald-800">
                    Link pembayaran Anda masih aktif. Anda bisa melanjutkan dari link yang sama tanpa membuat ulang invoice.
                  </div>
                @endif

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                  <div class="rounded-[1.3rem] border border-emerald-200 bg-white/80 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $settlementAccount['bank'] ?? 'Bank Mandiri' }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">a.n. {{ $settlementAccount['account_name'] ?? 'Diki Susanto' }}</p>
                    <p class="mt-1 text-lg font-extrabold text-primary">{{ $settlementAccount['account_number'] ?? '1080093012152' }}</p>
                  </div>
                  <div class="rounded-[1.3rem] border border-emerald-200 bg-white/80 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Status Pencatatan</p>
                    <p class="mt-2 text-sm leading-7 text-slate-700">Pesanan belum masuk daftar supervisor sekarang. Sistem baru mencatat setelah pembayaran aman berhasil.</p>
                  </div>
                </div>
              </div>

              <form method="POST" action="{{ route('customer.payments.store') }}" class="mt-6 space-y-4">
                @csrf
                @if ($order)
                  <input type="hidden" name="order_id" value="{{ $order->id }}"/>
                @elseif ($draftToken)
                  <input type="hidden" name="draft_token" value="{{ $draftToken }}"/>
                @endif
                <input type="hidden" name="method" value="{{ $paymentMethodInput }}"/>
                <input type="hidden" name="payment_plan" value="{{ $selectedPaymentPlan }}"/>

                @if ($showPaymentSuccess)
                  <div class="rounded-[1.3rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ $paidAmount >= $orderTotalAmount
                      ? 'Pembayaran lunas sudah diterima. Dokumen transaksi dapat dilihat dari halaman tracking.'
                      : 'Pembayaran booking fee sudah diterima. Anda bisa lanjut memantau progres dari halaman tracking.' }}
                  </div>
                @else
                  <button class="inline-flex w-full items-center justify-center rounded-[1.2rem] bg-[#08132e] px-6 py-4 text-sm font-bold text-white transition hover:bg-[#10214a]" type="submit">
                    Buka Halaman Pembayaran Aman
                  </button>
                @endif
              </form>
            @else
              <form id="manual-payment-form" method="POST" action="{{ route('customer.payments.store') }}" class="mt-6 space-y-5">
                @csrf
                @if ($order)
                  <input type="hidden" name="order_id" value="{{ $order->id }}"/>
                @elseif ($draftToken)
                  <input type="hidden" name="draft_token" value="{{ $draftToken }}"/>
                @endif
                <input type="hidden" name="method" value="{{ $paymentMethodInput }}"/>
                <input type="hidden" name="payment_plan" value="{{ $selectedPaymentPlan }}"/>

                <div class="grid gap-4">
                  @foreach ($bankAccounts as $bank)
                    <label class="group flex cursor-pointer gap-4 rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-5 transition hover:border-[#08132e]/30 hover:bg-white">
                      <input class="mt-1 h-5 w-5 border-slate-300 text-[#08132e] focus:ring-[#08132e]" type="radio" name="bank_account" value="{{ $bank['code'] }}" @checked($selectedBankCode === $bank['code']) />
                      <div class="flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                          <h3 class="text-lg font-extrabold text-primary">{{ $bank['bank'] }}</h3>
                          <span class="inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Rekening resmi</span>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-700">a.n. {{ $bank['account_name'] }}</p>
                        <p class="mt-1 text-xl font-extrabold tracking-tight text-primary">{{ $bank['account_number'] }}</p>
                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                          {{ $selectedPaymentPlan === 'full'
                            ? 'Pesanan lunas akan tercatat setelah pembayaran berhasil dikonfirmasi sistem.'
                            : 'Pesanan booking akan tercatat setelah pembayaran berhasil dikonfirmasi sistem.' }}
                        </p>
                      </div>
                    </label>
                  @endforeach
                </div>
              </form>

              <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button class="inline-flex flex-1 items-center justify-center rounded-[1.2rem] bg-[#08132e] px-6 py-4 text-sm font-bold text-white transition hover:bg-[#10214a]" id="pay-button" type="submit" form="manual-payment-form">
                  Lanjut Bayar Sekarang
                </button>
                <button
    id="check-status-button"
    type="button"
    class="inline-flex items-center justify-center rounded-[1.2rem] border border-slate-200 bg-white px-6 py-4 text-sm font-bold text-primary transition hover:bg-slate-50">
    Check Status
</button>
                @if ($localSimulationEnabled && $draftToken)
                  <button class="inline-flex items-center justify-center rounded-[1.2rem] border border-slate-200 bg-white px-6 py-4 text-sm font-bold text-primary transition hover:bg-slate-50" id="simulate-pay-button" type="button">
                    Simulasi Berhasil
                  </button>
                @endif
              </div>
            @endif
          </div>
        </section>

        <aside class="space-y-6 xl:h-full">
          <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm xl:flex xl:h-full xl:flex-col">
            <div class="relative aspect-[16/10] overflow-hidden xl:aspect-[11/10]">
              <img alt="{{ $carName }}" class="h-full w-full object-cover" src="{{ $carImage }}"/>
              <div class="absolute inset-0 bg-gradient-to-t from-[#08132e]/88 via-[#08132e]/28 to-transparent"></div>
              <div class="absolute inset-x-0 top-0 flex items-start justify-between p-5">
                <span class="inline-flex rounded-full border px-3 py-1.5 text-xs font-bold uppercase tracking-[0.18em] backdrop-blur-xl {{ $statusBadgeClass }}">
                  {{ $statusLabel }}
                </span>
              </div>
              <div class="absolute bottom-0 left-0 p-4 text-white">
                <div class="inline-flex max-w-[330px] items-center gap-2.5 rounded-[1.05rem] border border-white/16 bg-[rgba(255,255,255,0.14)] px-3 py-2.5 shadow-[0_10px_24px_rgba(8,19,46,0.16)] backdrop-blur-[16px]">
                  <div class="shrink-0">
                    <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-[#f7c35f]">Ringkasan Transaksi</p>
                  </div>
                  <div class="min-w-0 flex-1 border-l border-white/18 pl-2.5">
                    <h2 class="truncate text-[13px] font-bold leading-tight text-white md:text-[14px]">{{ $carName }}</h2>
                    <p class="mt-1 truncate text-[10px] text-slate-100">{{ $displayReference }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-5 p-6 xl:flex xl:flex-1 xl:flex-col">
              <div class="rounded-[1.4rem] bg-[#08132e] p-5 text-white">
                <div class="space-y-3 text-sm text-slate-200">
                  <div class="flex items-start justify-between gap-4">
                    <span>{{ $isCreditPurchase ? 'Nominal DP' : $cashPaymentTypeLabel }}</span>
                    <span class="text-right font-bold text-white">{{ \App\Support\CurrencyFormatter::rupiah($activePaymentAmount) }}</span>
                  </div>
                  <div class="flex items-start justify-between gap-4">
                    <span>{{ $isCreditPurchase ? 'Pelunasan Leasing' : 'Sisa Pembayaran' }}</span>
                    <span class="text-right font-bold text-white">{{ \App\Support\CurrencyFormatter::rupiah($activeRemainingBalance) }}</span>
                  </div>
                  <div class="flex items-start justify-between gap-4">
                    <span>Metode</span>
                    <span class="text-right font-bold text-white">{{ $isCreditPurchase ? 'DP KREDIT SHOWROOM' : ($xenditEnabled ? 'XENDIT HOSTED PAYMENT' : 'TRANSFER BANK') }}</span>
                  </div>
                  <div class="flex items-start justify-between gap-4 border-t border-white/12 pt-3">
                    <span>Status</span>
                    <span class="text-right font-bold text-white">{{ $statusLabel }}</span>
                  </div>
                  @if ($payment?->gateway_channel)
                    <div class="flex items-start justify-between gap-4">
                      <span>Channel</span>
                      <span class="text-right font-bold text-white">{{ strtoupper($payment->gateway_channel_label) }}</span>
                    </div>
                  @endif
                </div>
              </div>

              <div class="rounded-[1.4rem] border border-slate-200 bg-white p-5">
                <h3 class="text-lg font-extrabold text-primary">Info Unit</h3>
                <div class="mt-4 grid gap-3 text-sm text-on-surface-variant">
                  <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                    <span>Kode Unit</span>
                    <span class="font-bold text-primary">{{ $car?->kode_unit ?: '-' }}</span>
                  </div>
                  <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                    <span>Harga Unit</span>
                    <span class="font-bold text-primary">{{ \App\Support\CurrencyFormatter::rupiah($fullPaymentAmount) }}</span>
                  </div>
                  <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                    <span>Tahun</span>
                    <span class="font-bold text-primary">{{ $car?->tahun ?: '-' }}</span>
                  </div>
                </div>
              </div>

              <div class="rounded-[1.4rem] border border-slate-200 bg-slate-50/70 p-5 xl:mt-auto">
                <h3 class="text-lg font-extrabold text-primary">Yang customer perlu tahu</h3>
                <ul class="mt-4 space-y-3 text-sm leading-7 text-on-surface-variant">
                  <li class="flex gap-3">
                    <span class="mt-2 h-2 w-2 rounded-full bg-[#f5a623]"></span>
                    <span>{{ $selectedPaymentPlan === 'full' ? 'Pembayaran ini adalah pembayaran lunas untuk unit pilihan Anda.' : 'Pembayaran ini adalah booking fee untuk melanjutkan proses pembelian unit.' }}</span>
                  </li>
                  <li class="flex gap-3">
                    <span class="mt-2 h-2 w-2 rounded-full bg-[#f5a623]"></span>
                    <span>Setelah dana berhasil diterima, status transaksi dan halaman tracking akan otomatis diperbarui.</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan pembayaran untuk unit ' . $carName . '.'])
  @include('components.ui-system-footer')

  <script
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
  </script>

  <script>
    const payButton = document.getElementById('pay-button');
const simulatePayButton = document.getElementById('simulate-pay-button');
const checkStatusButton = document.getElementById('check-status-button');

const draftToken = @json($draftToken);

let currentOrderId = null;

const completeDraftPayment = () => {
    return fetch('{{ route("payments.complete") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            draft_token: draftToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    });
};

const checkPaymentStatus = () => {

    if (!currentOrderId) {
        alert('Order ID tidak ditemukan.');
        return;
    }

    fetch('{{ route("payments.check-status") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            order_id: currentOrderId
        })
    })
    .then(response => response.json())
    .then(data => {

        console.log('Midtrans Status:', data);

        if (!data.success) {
            alert(data.message || 'Gagal cek status pembayaran');
            return;
        }

        const status = data.status;

        if (
            status === 'settlement' ||
            status === 'capture'
        ) {

            completeDraftPayment();
            return;
        }

        if (status === 'pending') {
            alert('Pembayaran masih pending.');
            return;
        }

        if (
            status === 'expire' ||
            status === 'cancel' ||
            status === 'deny'
        ) {
            alert('Pembayaran gagal atau expired.');
            return;
        }

        alert('Status pembayaran: ' + status);

    })
    .catch(error => {
        console.error(error);
        alert('Gagal menghubungi server.');
    });
};

if (payButton && draftToken) {

    payButton.addEventListener('click', function (event) {

        event.preventDefault();

        fetch('{{ route("payments.snap-token") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                draft_token: draftToken
            })
        })
        .then(response => response.json())
        .then(data => {

            currentOrderId = data.order_id;

            snap.pay(data.token, {

                onSuccess: function(result) {

                    console.log('SUCCESS', result);

                    currentOrderId =
                        result.order_id ||
                        currentOrderId;

                    checkPaymentStatus();
                },

                onPending: function(result) {

                    console.log('PENDING', result);

                    currentOrderId =
                        result.order_id ||
                        currentOrderId;

                    alert('Pembayaran pending. Klik Check Status setelah selesai membayar.');
                },

                onError: function(result) {

                    console.log('ERROR', result);

                    alert('Pembayaran gagal.');
                },

                onClose: function() {

                    console.log('Popup ditutup');

                    alert('Popup pembayaran ditutup.');
                }
            });

        })
        .catch(error => {
            console.error(error);
            alert('Gagal membuat transaksi.');
        });
    });
}

if (checkStatusButton) {

    checkStatusButton.addEventListener('click', function () {
        checkPaymentStatus();
    });
}

if (simulatePayButton && draftToken) {

    simulatePayButton.addEventListener('click', function () {

        fetch('{{ route("customer.payments.simulate-success") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                draft_token: draftToken
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.redirect) {
                window.location.href = data.redirect;
            }

        })
        .catch(error => {
            console.error(error);
        });
    });
}
  </script>
</body>
</html>
