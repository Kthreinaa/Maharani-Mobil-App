@php
  $car = $order->car;
  $carName = trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '') . ' ' . ($car?->tahun ?? ''));
  $isCreditPurchase = $order->is_credit_purchase;

  $paymentStatus = $payment?->status ?? 'pending';
  $handoverReady = $order->isHandoverNoteReady();
  if ($isCreditPurchase) {
    $hasVerifiedCreditDp = $paymentStatus === 'verified' && (float) ($payment?->amount ?? 0) > 0 && (float) ($payment?->amount ?? 0) < (float) $order->total;
    $currentStep = 1;
    if (in_array($order->status, ['confirmed', 'paid', 'completed'], true) || $hasVerifiedCreditDp) {
      $currentStep = 2;
    }
    if ($order->status === 'completed' || $handoverReady) {
      $currentStep = 3;
    }
    $flowSteps = [
      'Website -> pilih unit -> pilih kredit leasing -> kirim pengajuan',
      'Supervisor review pengajuan -> customer bayar DP ke showroom -> showroom lanjutkan ke leasing',
      'Pelunasan leasing dan dokumen selesai -> unit siap diserahterimakan',
    ];
  } else {
    $currentStep = 1;
    if ($paymentStatus === 'verified' || in_array($order->status, ['paid', 'completed'], true)) {
      $currentStep = 2;
    }
    if ($handoverReady || $order->status === 'completed') {
      $currentStep = 3;
    }

    $flowSteps = match ($order->sales_flow) {
      'after_test_drive' => [
        'Website -> pilih mobil -> booking test drive',
        'Test drive -> negosiasi -> lanjut pesan online',
        'Pembayaran -> verifikasi supervisor -> serah terima unit',
      ],
      'offline_showroom' => [
        'Datang ke showroom -> lihat unit langsung',
        'Test drive dan negosiasi di showroom',
        'Pembayaran -> verifikasi supervisor -> serah terima unit',
      ],
      default => [
        'Website -> pilih unit -> lanjut pesan online',
        'Tanpa test drive -> lanjut pembayaran booking',
        'Pembayaran -> verifikasi supervisor -> serah terima unit',
      ],
    };
  }

  $statusBadgeClass = match ($order->status) {
    'completed' => 'bg-emerald-100 text-emerald-700',
    'paid' => 'bg-blue-100 text-blue-700',
    'cancelled' => 'bg-rose-100 text-rose-700',
    default => 'bg-amber-100 text-amber-700',
  };
  $documentsReady = $order->areTransactionDocumentsReady();
  $paidAmount = (float) ($payment?->amount ?? 0);
  $remainingAmount = max((float) $order->total - $paidAmount, 0);
  $gatewayCheckoutUrl = $payment?->gateway_checkout_url;
  $gatewayPending = filled($gatewayCheckoutUrl) && $paymentStatus !== 'verified';
  $creditDpAmount = (float) ($order->credit_dp_amount ?? 0);
  $creditInstallment = (float) ($order->credit_monthly_installment ?? 0);
  $hasVerifiedCreditDp = $isCreditPurchase && $paymentStatus === 'verified' && $paidAmount > 0 && $paidAmount < (float) $order->total;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tracking Pesanan | Maharani Mobil</title>
  <meta name="description" content="Lacak status pesanan, pembayaran, dan verifikasi unit Maharani Mobil."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'orders', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <div class="mb-8 flex items-center justify-between gap-4">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">Customer Tracking</p>
          <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">Tracking Pesanan</h1>
        </div>
        <a class="text-sm font-semibold text-primary hover:text-[#F5A623]" href="{{ route('customer.orders.index') }}">Lihat semua pesanan</a>
      </div>

      @if (session('success'))
        <div class="mb-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="mb-6 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
          {{ session('error') }}
        </div>
      @endif

      <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-8 flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
          <div>
            <p class="text-sm text-on-surface-variant">Kode Order</p>
            <h2 class="mt-1 text-3xl font-extrabold text-primary">{{ $order->order_reference }}</h2>
            <p class="mt-2 text-sm text-on-surface-variant">{{ $carName }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
              <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">{{ $order->purchase_method_label }}</span>
              <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">{{ $order->transaction_channel_label }}</span>
            </div>
          </div>
          <span class="inline-flex w-fit rounded-full px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] {{ $statusBadgeClass }}">
            {{ $order->customer_purchase_status_label }}
          </span>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <div class="lg:col-span-2">
            <div class="mb-6 rounded-[1.4rem] border border-slate-200 bg-slate-50/70 p-5">
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">pembelian customer</p>
              <p class="mt-2 text-sm font-semibold text-primary">{{ $order->customer_journey_label }}</p>
              <ol class="mt-3 space-y-2 text-sm text-on-surface-variant">
                @foreach ($flowSteps as $stepText)
                  <li>{{ $loop->iteration }}. {{ $stepText }}</li>
                @endforeach
              </ol>
              @if ($order->status === 'cancelled')
                <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">Customer batal membeli. Data tetap disimpan agar marketing bisa follow-up kembali.</p>
              @endif
            </div>

            <ol class="space-y-5">
              <li class="flex gap-4 {{ $currentStep >= 1 ? '' : 'opacity-60' }}">
                <span class="material-symbols-outlined {{ $currentStep >= 1 ? 'text-secondary' : 'text-slate-300' }}">radio_button_checked</span>
                <div>
                  <p class="font-bold text-primary">{{ $isCreditPurchase ? 'Pengajuan Kredit Dibuat' : 'Order Dibuat' }}</p>
                  <p class="text-sm text-on-surface-variant">
                    @if ($isCreditPurchase)
                      Pengajuan kredit sudah tercatat untuk unit pilihan Anda lengkap dengan leasing, DP, tenor, dan estimasi cicilan.
                    @else
                      Pesan online telah berhasil dibuat dan sistem menyiapkan order aktif untuk unit pilihan Anda.
                    @endif
                  </p>
                </div>
              </li>
              <li class="flex gap-4 {{ $currentStep >= 2 ? '' : 'opacity-60' }}">
                <span class="material-symbols-outlined {{ $currentStep >= 2 ? 'text-secondary' : 'text-slate-300' }}">{{ $currentStep >= 2 ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>
                <div>
                  <p class="font-bold text-primary">{{ $isCreditPurchase ? 'Tindak Lanjut Leasing' : 'Verifikasi Pembayaran' }}</p>
                  <p class="text-sm text-on-surface-variant">
                    @if ($isCreditPurchase)
                      @if ($order->status === 'pending')
                        Pengajuan kredit Anda sedang menunggu persetujuan supervisor. Setelah disetujui, customer dapat melanjutkan pembayaran DP ke pihak showroom.
                      @elseif ($hasVerifiedCreditDp)
                        DP kredit sudah diterima showroom. Proses selanjutnya menunggu pelunasan leasing dan finalisasi transaksi oleh pihak showroom.
                      @else
                        Pengajuan kredit sudah disetujui. Lanjutkan pembayaran DP ke showroom agar proses ke leasing bisa diteruskan.
                      @endif
                    @else
                      @if ($paymentStatus === 'verified')
                        Pembayaran telah diterima dan diverifikasi otomatis oleh sistem pembayaran.
                      @elseif ($paymentStatus === 'rejected')
                        Pembayaran belum valid. Supervisor akan menghubungi Anda untuk konfirmasi ulang.
                      @elseif ($gatewayPending)
                        Menunggu Anda menyelesaikan pembayaran di halaman aman. Setelah sukses, status order akan terupdate otomatis.
                      @else
                        Menunggu validasi supervisor. Estimasi maksimal 1x24 jam kerja.
                      @endif
                    @endif
                  </p>
                </div>
              </li>
              <li class="flex gap-4 {{ $currentStep >= 3 ? '' : 'opacity-60' }}">
                <span class="material-symbols-outlined {{ $currentStep >= 3 ? 'text-secondary' : 'text-slate-300' }}">{{ $currentStep >= 3 ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>
                <div>
                  <p class="font-bold text-primary">Serah Terima Unit</p>
                  <p class="text-sm text-on-surface-variant">
                    @if ($currentStep >= 3)
                      BAST sudah diterbitkan dan proses serah terima unit telah dicatat selesai oleh sistem.
                    @else
                      {{ $isCreditPurchase ? 'Setelah leasing menyetujui pengajuan dan transaksi dilanjutkan oleh showroom, unit akan dijadwalkan untuk serah terima.' : 'Unit akan dijadwalkan untuk serah terima setelah pembayaran dikonfirmasi selesai.' }}
                    @endif
                  </p>
                </div>
              </li>
            </ol>
          </div>

          <aside class="space-y-6">
            <div class="rounded-[1.5rem] bg-primary p-6 text-white">
              <h3 class="mb-4 text-lg font-bold">Ringkasan Status</h3>
              <div class="space-y-3 text-sm text-blue-100">
                <div class="flex justify-between gap-4">
                  <span>Status Pembelian</span>
                  <span>{{ $order->customer_purchase_status_label }}</span>
                </div>
                <div class="flex justify-between gap-4">
                  <span>Metode Pembelian</span>
                  <span>{{ $isCreditPurchase ? 'Kredit Leasing' : $order->purchase_method_label }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Total Order</span>
                  <span>{{ \App\Support\CurrencyFormatter::rupiah($order->total) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>{{ $isCreditPurchase ? 'Status Leasing' : 'Pembayaran' }}</span>
                  <span>{{ $isCreditPurchase ? ($hasVerifiedCreditDp ? 'DP KREDIT DITERIMA' : ($order->status === 'pending' ? 'MENUNGGU APPROVAL' : 'DISETUJUI SHOWROOM')) : strtoupper($paymentStatus) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>{{ $isCreditPurchase ? 'Leasing' : 'Metode' }}</span>
                  <span>{{ $isCreditPurchase ? ($order->leasing_partner_label ?? '-') : strtoupper($payment?->method ?? $order->payment_method ?? '-') }}</span>
                </div>
                @if (!$isCreditPurchase && $payment?->gateway_channel)
                  <div class="flex justify-between gap-4">
                    <span>Channel</span>
                    <span>{{ $payment->gateway_channel_label }}</span>
                  </div>
                @endif
                <div class="flex justify-between">
                  <span>Validasi</span>
                  <span>{{ $isCreditPurchase ? 'Supervisor & Leasing' : ($payment?->handled_role === 'gateway' ? 'Gateway otomatis' : 'Supervisor') }}</span>
                </div>
                @if ($isCreditPurchase)
                  <div class="flex justify-between gap-4">
                    <span>DP Kredit</span>
                    <span>{{ \App\Support\CurrencyFormatter::rupiah($paidAmount > 0 ? $paidAmount : $creditDpAmount) }}</span>
                  </div>
                  <div class="flex justify-between gap-4">
                    <span>Pelunasan Leasing</span>
                    <span>{{ \App\Support\CurrencyFormatter::rupiah($remainingAmount) }}</span>
                  </div>
                  <div class="flex justify-between gap-4">
                    <span>Tenor</span>
                    <span>{{ $order->credit_tenor_months }} bulan</span>
                  </div>
                  <div class="flex justify-between gap-4">
                    <span>Cicilan / Bulan</span>
                    <span>{{ \App\Support\CurrencyFormatter::rupiah($creditInstallment) }}</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="rounded-[1.5rem] border border-slate-200 p-6">
              <h3 class="text-lg font-bold text-primary">Dokumen Serah Terima</h3>
              <div class="mt-4 rounded-xl border {{ $documentsReady ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }} px-4 py-3 text-sm">
                @if ($documentsReady)
                  Pembayaran sudah lunas, jadi faktur, kwitansi digital, dan BAST sudah siap diunduh.
                @else
                  {{ $isCreditPurchase
                    ? 'Dokumen transaksi akan diterbitkan setelah pengajuan kredit disetujui, transaksi dilanjutkan oleh showroom, dan status pembayaran dinyatakan lunas.'
                    : 'Dokumen transaksi belum tersedia. Sistem akan menampilkannya setelah pembayaran lunas sesuai total order. Sisa pelunasan Anda saat ini ' . \App\Support\CurrencyFormatter::rupiah($remainingAmount) . '.' }}
                @endif
              </div>
              <div class="mt-4 space-y-3">
                @foreach ($order->handover_document_checklist as $document)
                  <div class="flex items-start justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-sm text-slate-700">{{ $document['label'] }}</p>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase {{ in_array($document['status'], ['siap', 'tersedia'], true) ? 'bg-emerald-100 text-emerald-700' : ($document['status'] === 'belum' || $document['status'] === 'tidak tersedia' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                      {{ $document['status'] }}
                    </span>
                  </div>
                @endforeach
              </div>
              <div class="mt-5 grid gap-2">
                @foreach (['invoice' => 'Unduh Faktur', 'receipt' => 'Unduh Kwitansi Digital', 'handover_note' => 'Unduh BAST'] as $docType => $docLabel)
                  @php($docReady = in_array($order->document_status[$docType] ?? 'pending', ['ready', 'submitted', 'done'], true))
                  <a
                    class="inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-bold {{ $docReady ? 'bg-[#08132e] text-white' : 'pointer-events-none bg-slate-100 text-slate-400' }}"
                    href="{{ $docReady ? route('documents.orders.download', [$order, $docType]) : '#' }}"
                  >
                    {{ $docLabel }}
                  </a>
                @endforeach
              </div>
            </div>

            <div class="rounded-[1.5rem] border border-slate-200 p-6">
              <h3 class="text-lg font-bold text-primary">Aksi Cepat</h3>
              <div class="mt-4 space-y-3">
                @if (!$isCreditPurchase)
                  <a class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-primary" href="{{ route('payment.page', ['order' => $order->id]) }}">Lihat Pembayaran</a>
                @endif
                @if (!$isCreditPurchase && $gatewayPending)
                  <a class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white" href="{{ $gatewayCheckoutUrl }}" target="_blank" rel="noopener noreferrer">Lanjutkan Pembayaran Aman</a>
                @endif
                @if ($isCreditPurchase && $order->status !== 'pending' && !$hasVerifiedCreditDp)
                  <a class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white" href="{{ route('payment.page', ['order' => $order->id]) }}">Lanjutkan Pembayaran DP Kredit</a>
                @endif
              </div>
            </div>
          </aside>
        </div>
      </section>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan status order ' . $order->order_reference . '.'])
  @include('components.ui-system-footer')
</body>
</html>
