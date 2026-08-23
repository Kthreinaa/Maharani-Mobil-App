<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Pesanan Saya') }} | Maharani Mobil</title>
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
      <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Customer Area') }}</p>
          <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">{{ __('Pesanan Saya') }}</h1>
          <p class="mt-3 max-w-[620px] text-sm leading-7 text-on-surface-variant">{{ __('Pantau seluruh transaksi, status pembayaran, dan akses tracking pesanan Anda dari satu halaman.') }}</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-5 py-3 text-[13px] font-bold text-white transition hover:brightness-110" href="{{ route('catalog') }}">
          <span class="material-symbols-outlined text-[18px]">directions_car</span>
          {{ __('Cari unit lain') }}
        </a>
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

      <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[1720px] table-auto text-left text-sm">
          <colgroup>
            <col style="width: 140px;">
            <col style="width: 420px;">
            <col style="width: 220px;">
            <col style="width: 190px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 300px;">
            <col style="width: 260px;">
          </colgroup>
          <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
            <tr>
              <th class="px-6 py-4">{{ __('Tanggal') }}</th>
              <th class="px-6 py-4">{{ __('Unit Mobil') }}</th>
              <th class="px-6 py-4">{{ __('Metode Pembelian') }}</th>
              <th class="px-6 py-4">{{ __('Total') }}</th>
              <th class="px-6 py-4">{{ __('Pembayaran') }}</th>
              <th class="px-6 py-4">{{ __('Status Pembelian') }}</th>
              <th class="px-6 py-4">{{ __('Status Review') }}</th>
              <th class="px-6 py-4">{{ __('Dokumen') }}</th>
              <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($orders as $order)
              @php
                $car = $order->car;
                $carPhoto = (is_array($car?->photos) && !empty($car->photos[0]))
                  ? asset('storage/' . $car->photos[0])
                  : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1200&auto=format&fit=crop';
                $paymentStatusLabel = $order->is_credit_purchase && ($order->payment?->status ?? 'pending') === 'verified' && (float) ($order->payment?->amount ?? 0) < (float) $order->total
                  ? 'DP KREDIT'
                  : strtoupper($order->payment?->status ?? 'pending');
              @endphp
              <tr>
                <td class="px-6 py-4 align-top">
                  <div class="min-w-[132px]">
                    <p class="font-semibold text-slate-900">{{ $order->created_at->format('d M Y') }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $order->created_at->format('H:i') }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex min-w-[360px] items-center gap-4">
                    <img
                      src="{{ $carPhoto }}"
                      alt="{{ $car?->merk }} {{ $car?->tipe }}"
                      class="h-[4.5rem] w-[7rem] rounded-2xl object-cover ring-1 ring-slate-200"
                      loading="lazy"
                    />
                    <div class="min-w-0 flex-1">
                      <p class="break-words text-[15px] font-semibold leading-6 text-slate-900">{{ trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '') . ' ' . ($car?->tahun ?? '')) }}</p>
                      <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                        <span class="whitespace-nowrap">{{ $car?->kode_unit ?: __('Tanpa kode unit') }}</span>
                        <span class="whitespace-nowrap">{{ $car?->transmisi ?: '-' }}</span>
                        <span class="whitespace-nowrap">{{ number_format((int) ($car?->kilometer ?? 0), 0, ',', '.') }} KM</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="min-w-[190px]">
                    <p class="font-semibold text-slate-900">{{ $order->purchase_method_label }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $order->payment?->internal_method_label ?? $order->internal_payment_method_label }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">{{ \App\Support\CurrencyFormatter::rupiah($order->total) }}</td>
                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">{{ $paymentStatusLabel }}</p>
                  <p class="mt-1 text-xs font-semibold text-slate-500">Pembayaran divalidasi oleh supervisor.</p>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex min-w-[142px] items-center justify-center rounded-full bg-slate-100 px-3 py-1.5 text-center text-[11px] font-semibold uppercase leading-none tracking-[0.04em] text-primary whitespace-nowrap">{{ $order->customer_purchase_status_label }}</span>
                  @if ($order->has_pending_cancellation_request)
                    <p class="mt-1 text-xs text-amber-700">Menunggu persetujuan supervisor.</p>
                    @if ($order->customer_cancellation_reason)
                      <p class="mt-1 text-xs text-amber-700">{{ $order->customer_cancellation_reason }}</p>
                    @endif
                  @elseif ($order->cancel_reason)
                    <p class="mt-1 text-xs text-rose-600">{{ $order->cancel_reason }}</p>
                  @endif
                </td>
                <td class="px-6 py-4">
                  <div class="min-w-[180px]">
                    @if ($order->has_purchase_review)
                      <span class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-[11px] font-bold uppercase tracking-[0.04em] text-emerald-700 whitespace-nowrap">
                        {{ $order->review_status_label }}
                      </span>
                    @elseif ($order->can_submit_purchase_review)
                      <a class="inline-flex items-center justify-center rounded-full border border-amber-200 bg-amber-50 px-3 py-2 text-center text-[11px] font-bold uppercase tracking-[0.04em] text-amber-700 whitespace-nowrap transition hover:bg-amber-100" href="{{ route('customer.reviews.create', ['car' => $order->car_id]) }}">
                        {{ $order->review_status_label }}
                      </a>
                    @elseif ($order->review_window_expired)
                      <span class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-center text-[11px] font-bold uppercase tracking-[0.04em] text-rose-700 whitespace-nowrap">
                        {{ $order->review_status_label }}
                      </span>
                    @else
                      <span class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-center text-[11px] font-bold uppercase tracking-[0.04em] text-slate-500 whitespace-nowrap">
                        Menunggu Selesai
                      </span>
                    @endif
                  </div>
                </td>
                <td class="px-6 py-4">
                  @php($documentsReady = $order->areTransactionDocumentsReady())
                  <div class="flex min-w-[280px] flex-nowrap items-center gap-2">
                    @if ($documentsReady)
                      <a class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-center text-xs font-bold text-sky-700" href="{{ route('documents.orders.download', [$order, 'invoice']) }}">Faktur</a>
                      <a class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-center text-xs font-bold text-emerald-700" href="{{ route('documents.orders.download', [$order, 'receipt']) }}">Kwitansi</a>
                      <a class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-center text-xs font-bold text-amber-700" href="{{ route('documents.orders.download', [$order, 'handover_note']) }}">BAST</a>
                    @else
                      <span class="text-xs text-slate-400">-</span>
                    @endif
                  </div>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex min-w-[220px] flex-col items-end gap-2">
                    <a class="inline-flex items-center justify-center whitespace-nowrap rounded-xl border border-outline-variant px-4 py-2 text-center text-xs font-bold text-primary transition hover:bg-slate-50" href="{{ route('order.tracking', ['order' => $order->id]) }}">{{ __('Tracking') }}</a>
                    @if ($order->can_customer_request_cancellation)
                      <form method="POST" action="{{ route('customer.orders.requestCancellation', $order) }}" class="w-full max-w-[220px]" data-cancel-request-form>
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="customer_cancellation_reason" value="" data-cancel-request-reason />
                        <button type="button" class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-center text-xs font-bold text-rose-700 transition hover:bg-rose-100" data-cancel-request-trigger>
                          Ajukan Pembatalan
                        </button>
                      </form>
                    @elseif ($order->has_pending_cancellation_request)
                      <span class="inline-flex w-full max-w-[220px] items-center justify-center whitespace-nowrap rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-center text-xs font-bold text-amber-700">
                        Menunggu Approval Batal
                      </span>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-10 text-center text-slate-500">{{ __('Belum ada pesanan. Mulai dari katalog untuk checkout unit pertama Anda.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan status pesanan saya.'])
  @include('components.ui-system-footer')
  <script>
    document.querySelectorAll('[data-cancel-request-trigger]').forEach((button) => {
      button.addEventListener('click', () => {
        const form = button.closest('[data-cancel-request-form]');
        const reasonField = form?.querySelector('[data-cancel-request-reason]');

        if (!form || !reasonField) {
          return;
        }

        const reason = window.prompt('Tulis alasan pembatalan pesanan ini. Boleh dikosongkan jika belum ada alasan khusus.', reasonField.value || '');
        if (reason === null) {
          return;
        }

        reasonField.value = reason.trim();

        if (!window.confirm('Kirim permintaan pembatalan pesanan ini ke supervisor?')) {
          return;
        }

        form.submit();
      });
    });
  </script>
</body>
</html>
