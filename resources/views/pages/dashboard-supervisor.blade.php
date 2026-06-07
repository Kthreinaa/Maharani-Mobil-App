@extends('layouts.supervisor')

@php
  $title = 'Supervisor Dashboard';
  $pageTitle = 'Dashboard Overview';
  $reportFilters = [
    'daily' => 'Harian',
    'weekly' => 'Mingguan',
    'monthly' => 'Bulanan',
    'yearly' => 'Tahunan',
  ];

  $kpiCards = [
    [
      'label' => 'Total Mobil Tersedia',
      'value' => $totalAvailableCars,
      'icon' => 'directions_car',
      'tone' => 'from-[#08132e] to-[#12357a]',
      'meta' => 'Stok unit siap jual',
    ],
    [
      'label' => 'Total Mobil Terjual',
      'value' => $totalSold,
      'icon' => 'sell',
      'tone' => 'from-emerald-500 to-emerald-600',
      'meta' => 'Unit yang sudah closing',
    ],
    [
      'label' => 'Total Pesanan',
      'value' => $totalOrders,
      'icon' => 'receipt_long',
      'tone' => 'from-amber-400 to-amber-500',
      'meta' => 'Seluruh order masuk',
    ],
    [
      'label' => 'Total Customer',
      'value' => $totalCustomers,
      'icon' => 'groups',
      'tone' => 'from-fuchsia-500 to-violet-500',
      'meta' => 'Akun customer aktif',
    ],
    [
      'label' => 'Transaksi Pending',
      'value' => $pendingPayments,
      'icon' => 'hourglass_top',
      'tone' => 'from-rose-500 to-orange-400',
      'meta' => 'Menunggu verifikasi',
    ],
    [
      'label' => 'Transaksi Verified',
      'value' => $verifiedPayments,
      'icon' => 'verified',
      'tone' => 'from-sky-500 to-cyan-500',
      'meta' => 'Pembayaran tervalidasi',
    ],
    [
      'label' => 'Booking Test Drive',
      'value' => $totalTestDrives,
      'icon' => 'event_available',
      'tone' => 'from-indigo-500 to-blue-500',
      'meta' => 'Seluruh jadwal booking',
    ],
    [
      'label' => 'Penawaran Masuk',
      'value' => $totalOffers,
      'icon' => 'local_offer',
      'tone' => 'from-slate-700 to-slate-900',
      'meta' => 'Negosiasi aktif customer',
    ],
  ];

@endphp

@section('content')
  <section class="mb-6 overflow-hidden rounded-[2.2rem] border border-white/70 bg-[linear-gradient(135deg,#08132e_0%,#102a63_55%,#f5a623_145%)] p-6 text-white shadow-[0_26px_70px_rgba(8,19,46,0.18)] md:p-8">
    <div class="flex flex-col gap-7">
      <div class="max-w-[980px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f7c35f]">Supervisor Command Center</p>
        <h2 class="mt-3 max-w-[760px] font-headline text-[30px] font-extrabold leading-tight md:text-[40px]">
          Semua indikator showroom, transaksi, dan tim ada dalam satu dashboard.
        </h2>
        <p class="mt-4 max-w-[700px] text-sm leading-7 text-slate-200 md:text-[15px]">
        </p>

      </div>

      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($kpiCards as $card)
          <article class="rounded-[1.6rem] border border-white/12 bg-white/10 p-4 backdrop-blur-xl">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-200/90">{{ $card['label'] }}</p>
                <p class="mt-2 text-[24px] font-extrabold leading-none tracking-tight text-white">{{ number_format((int) $card['value']) }}</p>
                <p class="mt-2 line-clamp-2 text-[12px] text-slate-200/90">{{ $card['meta'] }}</p>
              </div>
              <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[1.1rem] bg-gradient-to-br {{ $card['tone'] }} text-white shadow-lg">
                <span class="material-symbols-outlined text-[20px]">{{ $card['icon'] }}</span>
              </span>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-[0.75fr_1.75fr]">
    <article id="supervisor-sales-trend-section" class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] scroll-mt-28">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Pola Pembelian</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Tren Transaksi Cash dan Kredit</h3>
          <p class="mt-2 text-sm leading-6 text-slate-500">
            Grafik ini membantu supervisor membaca kecenderungan customer membeli mobil bekas dengan metode cash atau kredit.
          </p>
        </div>
        <form id="supervisorSalesTrendForm" method="GET" action="{{ route('supervisor.dashboard') }}">
          <input type="hidden" name="report_range" value="{{ $reportRange }}">
          <select
            id="supervisorSalesTrendYear"
            name="year"
            class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 outline-none transition hover:bg-slate-200"
            aria-label="Pilih tahun"
          >
            @foreach(($availableYears ?? []) as $y)
              <option value="{{ $y }}" @selected((int) $year === (int) $y)>Tahun {{ $y }}</option>
            @endforeach
          </select>
        </form>
      </div>
      <canvas id="salesChart" height="105"></canvas>
      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-[1.15rem] border border-slate-200/80 bg-white/70 px-4 py-3">
          <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Pembelian Cash</p>
          <p id="supervisorCashPurchaseTotal" class="mt-2 text-lg font-extrabold text-slate-900">{{ number_format((int) $yearlyCashPurchases) }}</p>
          <p class="mt-1 text-xs text-slate-500">Total customer yang membeli secara cash pada tahun {{ $year }}.</p>
        </div>
        <div class="rounded-[1.15rem] border border-slate-200/80 bg-white/70 px-4 py-3">
          <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Pembelian Kredit</p>
          <p id="supervisorCreditPurchaseTotal" class="mt-2 text-lg font-extrabold text-slate-900">{{ number_format((int) $yearlyCreditPurchases) }}</p>
          <p class="mt-1 text-xs text-slate-500">Total customer yang membeli secara kredit pada tahun {{ $year }}.</p>
        </div>
      </div>
    </article>

    <article id="supervisor-sales-report-section" class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] scroll-mt-28">
      <div class="mb-5 flex flex-col gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Realtime Sales Report</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Laporan Penjualan</h3>
          <p class="mt-2 text-sm leading-6 text-slate-500">
            Data diperbarui langsung dari transaksi berstatus paid dan completed saat dashboard dibuka.
          </p>
        </div>

        <form id="supervisorSalesReportForm" method="GET" action="{{ route('supervisor.dashboard') }}" class="flex flex-col gap-4">
          <div class="flex flex-wrap gap-2">
            @foreach ($reportFilters as $key => $label)
              <button
                type="submit"
                name="report_range"
                value="{{ $key }}"
                data-report-range="{{ $key }}"
                class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-semibold transition {{ $reportRange === $key ? 'border-[#08132e] bg-[#08132e] text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)]' : 'border-slate-200 bg-white/80 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
              >
                {{ $label }}
              </button>
            @endforeach
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">Tahun</span>
              <select
                id="supervisorSalesReportYear"
                name="year"
                class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 outline-none transition hover:bg-white"
                aria-label="Pilih tahun laporan"
              >
                @foreach(($availableYears ?? []) as $y)
                  <option value="{{ $y }}" @selected((int) $year === (int) $y)>{{ $y }}</option>
                @endforeach
              </select>
            </div>

            <div id="supervisorSalesReportMonthWrap" class="flex flex-wrap items-center gap-2 {{ $reportRange === 'monthly' ? '' : 'hidden' }}">
              <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">Bulan</span>
              <select
                id="supervisorSalesReportMonth"
                name="month"
                class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 outline-none transition hover:bg-white"
                aria-label="Pilih bulan"
              >
                @foreach(($availableMonths ?? []) as $m => $label)
                  <option value="{{ $m }}" @selected((int) $reportMonth === (int) $m)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
          </div>

          @if ($reportRange === 'monthly')
            <div id="supervisorSalesReportNote" class="rounded-[1.1rem] border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-xs text-slate-500">{{ $reportNote }}</div>
          @elseif ($reportRange === 'yearly')
            <div id="supervisorSalesReportNote" class="rounded-[1.1rem] border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-xs text-slate-500">{{ $reportNote }}</div>
          @else
            <div id="supervisorSalesReportNote" class="rounded-[1.1rem] border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-xs text-slate-500">{{ $reportNote }}</div>
          @endif

          <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-[1.25rem] border border-slate-200/80 bg-white/70 px-4 py-3">
              <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Range Aktif</p>
              <p id="supervisorSalesReportRangeActive" class="mt-2 text-lg font-extrabold text-slate-900">{{ $reportFilters[$reportRange] ?? $salesReport['range_label'] }}</p>
            </div>
            <div class="rounded-[1.25rem] border border-slate-200/80 bg-white/70 px-4 py-3">
              <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Omzet</p>
              @php($formattedRevenue = \App\Support\CurrencyFormatter::rupiah($salesReport['total_revenue']))
              <p id="supervisorSalesReportRevenue" class="mt-2 max-w-full text-[13px] font-extrabold leading-tight tracking-tight text-slate-900 [font-variant-numeric:tabular-nums] sm:text-[14px] lg:text-[15px]">
                {!! preg_replace('/,/', ',<wbr>', e($formattedRevenue)) !!}
              </p>
            </div>
            <div class="rounded-[1.25rem] border border-slate-200/80 bg-white/70 px-4 py-3">
              <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Order Masuk</p>
              <p id="supervisorSalesReportOrders" class="mt-2 text-lg font-extrabold text-slate-900">{{ number_format((int) $salesReport['total_orders']) }}</p>
            </div>
          </div>

          <div class="rounded-[1.25rem] border border-slate-200/80 bg-[linear-gradient(135deg,rgba(8,19,46,0.06)_0%,rgba(245,166,35,0.10)_100%)] px-4 py-3 text-xs text-slate-500">
            Rata-rata nilai order pada periode ini: <span id="supervisorSalesReportAverage" class="font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($salesReport['average_order_value']) }}</span>
            <span class="mx-2 text-slate-300">&middot;</span>
            Update terakhir <span id="supervisorSalesReportUpdatedAt">{{ $salesReport['last_updated']->format('d M Y H:i') }}</span>
          </div>
        </form>
      </div>

      <div class="rounded-[1.4rem] border border-slate-200/80 bg-white/60 p-3 shadow-inner shadow-white/70">
        <div class="h-[220px]">
          <canvas id="salesReportChart"></canvas>
        </div>
      </div>
    </article>
  </section>

  <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.45fr]">
    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Revenue Flow</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Pendapatan Bulanan</h3>
        </div>
      </div>
      <canvas id="revenueChart" height="170"></canvas>
    </article>

    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Demand Leaderboard</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Merk Mobil Paling Laku</h3>
          <p class="mt-2 text-sm text-slate-500">Data mengikuti periode aktif dashboard: <span id="supervisorTopBrandsRangeLabel">{{ $activeRangeLabel }}</span></p>
        </div>
      </div>
      <div id="supervisorTopBrandsEmpty" class="rounded-[1.25rem] border border-dashed border-slate-200 px-4 py-12 text-center text-sm text-slate-500 {{ $topBrands->isEmpty() ? '' : 'hidden' }}">
        Belum ada data penjualan pada periode {{ strtolower($activeRangeLabel) }}.
      </div>
      <div id="supervisorTopBrandsChartWrap" class="{{ $topBrands->isEmpty() ? 'hidden' : '' }}">
        <canvas id="topCarsChart" height="170"></canvas>
      </div>
    </article>
  </section>

  <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Live Activity</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Aktivitas Terbaru</h3>
        </div>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Timeline</span>
      </div>

      <ul class="space-y-3">
        @if ($activity->isNotEmpty())
          @foreach ($activity as $item)
            <li class="group flex items-center justify-between gap-4 rounded-[1.25rem] border border-slate-200 bg-white/80 px-4 py-3.5 shadow-[0_10px_30px_rgba(15,23,42,0.05)] transition hover:bg-white">
              <div class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#08132e] text-[#f7c35f] shadow-[0_14px_24px_rgba(8,19,46,0.18)]">
                  <span class="material-symbols-outlined text-[20px]">
                    @switch($item['type'])
                      @case('order')
                        receipt_long
                        @break
                      @case('payment')
                        payments
                        @break
                      @case('user')
                        person_add
                        @break
                      @case('car')
                        directions_car
                        @break
                      @case('testdrive')
                        event_available
                        @break
                      @case('offer')
                        sell
                        @break
                      @default
                        notifications
                    @endswitch
                  </span>
                </span>
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                  <p class="mt-1 truncate text-xs text-slate-500">{{ $item['detail'] }}</p>
                </div>
              </div>
              <div class="flex shrink-0 items-center">
                <span class="whitespace-nowrap rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500">{{ $item['time']->diffForHumans() }}</span>
              </div>
            </li>
          @endforeach
        @else
          <li class="rounded-[1.25rem] border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">Belum ada aktivitas terbaru.</li>
        @endif
      </ul>
    </article>

    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Test Drive Queue</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Booking Terbaru</h3>
        </div>
        <a class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500 transition hover:bg-slate-200 hover:text-slate-700" href="{{ route('supervisor.testdrives.index') }}">Monitoring</a>
      </div>

      <div class="space-y-3">
        @forelse ($recentTestDrives as $testDrive)
          <a href="{{ route('supervisor.testdrives.show', $testDrive) }}" class="group flex items-center justify-between gap-4 rounded-[1.25rem] border border-slate-200 bg-white/80 px-4 py-3.5 shadow-[0_10px_30px_rgba(15,23,42,0.05)] transition hover:bg-white">
            <div class="flex min-w-0 items-center gap-3">
              <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[linear-gradient(135deg,#08132e_0%,#12357a_100%)] text-[#f7c35f] shadow-[0_14px_24px_rgba(8,19,46,0.18)]">
                <span class="material-symbols-outlined text-[20px]">event_available</span>
              </span>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900">{{ $testDrive->user?->name ?? 'Customer' }}</p>
                <p class="mt-1 truncate text-xs text-slate-500">{{ $testDrive->car?->merk }} {{ $testDrive->car?->tipe }}</p>
                <p class="mt-1 text-[11px] text-slate-400">{{ optional($testDrive->booking_date)->format('d M Y') }} {{ substr((string) $testDrive->booking_time, 0, 5) }}</p>
              </div>
            </div>
            <div class="flex shrink-0 items-center">
              <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase text-[#08132e]">{{ $testDrive->status }}</span>
            </div>
          </a>
        @empty
          <div class="rounded-[1.25rem] border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">Belum ada booking test drive terbaru.</div>
        @endforelse
      </div>
    </article>

    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Payment Feed</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Pembayaran Terbaru</h3>
        </div>
        <a class="text-sm font-semibold text-slate-500 transition hover:text-slate-900" href="{{ route('supervisor.payments.index') }}">Lihat Semua</a>
      </div>

      <ul class="space-y-3">
        @forelse($recentPayments as $payment)
          <li class="flex items-center justify-between gap-3 rounded-[1.25rem] border border-slate-200/80 bg-white/70 px-4 py-3">
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ $payment->order?->user?->name }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ $payment->order?->car?->merk }} {{ $payment->order?->car?->tipe }}</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase text-[#08132e]">{{ $payment->status }}</span>
          </li>
        @empty
          <li class="rounded-[1.25rem] border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">Belum ada pembayaran.</li>
        @endforelse
      </ul>
    </article>
  </section>

  <section class="grid grid-cols-1 gap-6">
    <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Order Feed</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Pesanan Terbaru</h3>
        </div>
        <a class="text-sm font-semibold text-slate-500 transition hover:text-slate-900" href="{{ route('supervisor.orders.index') }}">Lihat Semua</a>
      </div>

      <div class="overflow-hidden rounded-[1.7rem] border border-white/80 bg-[linear-gradient(180deg,rgba(255,255,255,0.88)_0%,rgba(238,244,255,0.92)_100%)] p-2 shadow-[inset_0_1px_0_rgba(255,255,255,0.95),0_22px_60px_rgba(15,23,42,0.08)] backdrop-blur-[22px]">
        <div class="overflow-x-auto rounded-[1.35rem] bg-[rgba(255,255,255,0.72)]">
        <table class="mm-data-table w-full min-w-[720px] border-separate border-spacing-0 text-left">
          <thead>
            <tr class="bg-[linear-gradient(135deg,rgba(8,19,46,0.08)_0%,rgba(245,166,35,0.06)_100%)]">
              <th class="rounded-tl-[1.15rem] px-6 py-4 text-sm font-semibold tracking-[0.16em] text-slate-500">Kode</th>
              <th class="px-5 py-4 text-sm font-semibold tracking-[0.16em] text-slate-500">Customer</th>
              <th class="px-5 py-4 text-sm font-semibold tracking-[0.16em] text-slate-500">Mobil</th>
              <th class="rounded-tr-[1.15rem] px-5 py-4 text-sm font-semibold tracking-[0.16em] text-slate-500">Status</th>
            </tr>
          </thead>
          <tbody>
            @if($recentOrders->isNotEmpty())
              @foreach($recentOrders as $order)
                <tr class="align-top transition hover:bg-[rgba(248,250,252,0.88)]">
                  <td class="border-t border-slate-100 px-6 py-5 font-semibold text-slate-900">
                    <div class="inline-flex rounded-full border border-slate-200/80 bg-white/85 px-3.5 py-2 text-base font-bold shadow-[0_10px_24px_rgba(15,23,42,0.06)]">
                      {{ $order->order_reference }}
                    </div>
                  </td>
                  <td class="border-t border-slate-100 px-5 py-5">
                    <p class="text-[1.05rem] font-semibold leading-6 text-slate-800">{{ $order->user?->name }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $order->created_at?->format('d M Y H:i') }}</p>
                  </td>
                  <td class="border-t border-slate-100 px-5 py-5">
                    <p class="text-[1.05rem] font-semibold leading-6 text-slate-800">{{ $order->car?->merk }} {{ $order->car?->tipe }}</p>
                    <p class="mt-1 text-sm text-slate-500">Tahun {{ $order->car?->tahun }}</p>
                  </td>
                  <td class="border-t border-slate-100 px-5 py-5">
                    <span
                      @class([
                        'inline-flex rounded-full border px-4 py-2 text-xs font-bold uppercase tracking-[0.14em]',
                        'border-emerald-200 bg-emerald-50/90 text-emerald-700' => strtolower((string) $order->status) === 'completed',
                        'border-amber-200 bg-amber-50/90 text-amber-700' => strtolower((string) $order->status) === 'pending',
                        'border-rose-200 bg-rose-50/90 text-rose-700' => strtolower((string) $order->status) === 'paid',
                        'border-slate-200 bg-slate-100/90 text-slate-700' => !in_array(strtolower((string) $order->status), ['completed', 'pending', 'paid'], true),
                      ])
                    >{{ $order->status }}</span>
                  </td>
                </tr>
              @endforeach
            @else
              <tr><td class="px-6 py-5 text-sm text-slate-500" colspan="4">Belum ada pesanan.</td></tr>
            @endif
          </tbody>
        </table>
        </div>
      </div>
    </article>
  </section>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function () {
      const chartTextColor = '#475569';
      const chartGridColor = 'rgba(148, 163, 184, 0.18)';

      const salesTrendForm = document.getElementById('supervisorSalesTrendForm');
      const salesTrendYear = document.getElementById('supervisorSalesTrendYear');
      const salesReportForm = document.getElementById('supervisorSalesReportForm');
      const salesReportYear = document.getElementById('supervisorSalesReportYear');
      const salesReportMonth = document.getElementById('supervisorSalesReportMonth');
      const salesReportMonthWrap = document.getElementById('supervisorSalesReportMonthWrap');
      const salesReportNote = document.getElementById('supervisorSalesReportNote');
      const salesReportRangeActive = document.getElementById('supervisorSalesReportRangeActive');
      const salesReportRevenue = document.getElementById('supervisorSalesReportRevenue');
      const salesReportOrders = document.getElementById('supervisorSalesReportOrders');
      const salesReportAverage = document.getElementById('supervisorSalesReportAverage');
      const salesReportUpdatedAt = document.getElementById('supervisorSalesReportUpdatedAt');
      const topBrandsRangeLabel = document.getElementById('supervisorTopBrandsRangeLabel');
      const topBrandsEmpty = document.getElementById('supervisorTopBrandsEmpty');
      const topBrandsChartWrap = document.getElementById('supervisorTopBrandsChartWrap');
      const cashPurchaseTotal = document.getElementById('supervisorCashPurchaseTotal');
      const creditPurchaseTotal = document.getElementById('supervisorCreditPurchaseTotal');

      const salesCanvas = document.getElementById('salesChart');
      const salesReportCanvas = document.getElementById('salesReportChart');
      const revenueCanvas = document.getElementById('revenueChart');
      const topCarsCanvas = document.getElementById('topCarsChart');

      if (!salesCanvas || !salesReportCanvas || !revenueCanvas || !topCarsCanvas || !salesTrendForm || !salesTrendYear || !salesReportForm || !salesReportYear || !salesReportMonth || !salesReportMonthWrap || !salesReportNote || !salesReportRangeActive || !salesReportRevenue || !salesReportOrders || !salesReportAverage || !salesReportUpdatedAt || !topBrandsRangeLabel || !topBrandsEmpty || !topBrandsChartWrap || !cashPurchaseTotal || !creditPurchaseTotal) {
        return;
      }

      const smoothLineMotion = {
        animation: {
          duration: 760,
          easing: 'easeOutCubic',
        },
        transitions: {
          active: {
            animation: {
              duration: 760,
              easing: 'easeOutCubic',
            },
          },
        },
      };

      const smoothBarMotion = {
        animation: {
          duration: 680,
          easing: 'easeOutQuart',
        },
        transitions: {
          active: {
            animation: {
              duration: 680,
              easing: 'easeOutQuart',
            },
          },
        },
      };

      const salesGradient = salesCanvas.getContext('2d').createLinearGradient(0, 0, 0, 260);
      salesGradient.addColorStop(0, 'rgba(8, 19, 46, 0.35)');
      salesGradient.addColorStop(1, 'rgba(8, 19, 46, 0.02)');

      const salesReportContext = salesReportCanvas.getContext('2d');
      const salesReportGradient = salesReportContext.createLinearGradient(0, 0, 0, 240);
      salesReportGradient.addColorStop(0, 'rgba(245, 166, 35, 0.32)');
      salesReportGradient.addColorStop(1, 'rgba(245, 166, 35, 0.02)');

      const salesChart = new Chart(salesCanvas, {
        type: 'line',
        data: {
          labels: @json($paymentBehaviorLabels),
          datasets: [{
            label: 'Cash',
            data: @json($monthlyCashTransactions),
            borderColor: '#08132e',
            backgroundColor: salesGradient,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 5,
            pointBackgroundColor: '#f5a623',
            tension: 0.35
          }, {
            label: 'Kredit',
            data: @json($monthlyCreditTransactions),
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.06)',
            fill: false,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 5,
            pointBackgroundColor: '#dcfce7',
            pointBorderColor: '#16a34a',
            pointBorderWidth: 2,
            tension: 0.35
          }]
        },
        options: {
          ...smoothLineMotion,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: { color: chartTextColor, usePointStyle: true, boxWidth: 10 }
            }
          },
          scales: {
            x: { ticks: { color: chartTextColor }, grid: { display: false } },
            y: { ticks: { color: chartTextColor }, grid: { color: chartGridColor } }
          }
        }
      });

      const revenueChart = new Chart(revenueCanvas, {
        type: 'bar',
        data: {
          labels: @json($monthlyRevenue->pluck('month')),
          datasets: [{
            label: 'Pendapatan',
            data: @json($monthlyRevenue->pluck('total')),
            borderRadius: 14,
            backgroundColor: ['#08132e', '#12357a', '#1f4ba8', '#2a5fd3', '#f5a623', '#f7c35f']
          }]
        },
        options: {
          ...smoothBarMotion,
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: chartTextColor }, grid: { display: false } },
            y: { ticks: { color: chartTextColor }, grid: { color: chartGridColor } }
          }
        }
      });

      const salesReportChart = new Chart(salesReportCanvas, {
        type: 'line',
        data: {
          labels: @json($salesReport['labels']),
          datasets: [{
            label: 'Omzet',
            data: @json($salesReport['revenues']),
            yAxisID: 'y',
            borderColor: '#f5a623',
            backgroundColor: salesReportGradient,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#f5a623',
            pointBorderWidth: 2,
            tension: 0.38
          }, {
            label: 'Order',
            data: @json($salesReport['orders']),
            yAxisID: 'y1',
            borderColor: '#08132e',
            backgroundColor: '#08132e',
            borderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#08132e',
            fill: false,
            tension: 0.32
          }],
        },
        options: {
          ...smoothLineMotion,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: chartTextColor, usePointStyle: true, boxWidth: 10 }
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  if (context.dataset.label === 'Omzet') {
                    return ' Omzet: Rp ' + Number(context.parsed.y || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                  }
                  return ' Order: ' + Number(context.parsed.y || 0).toLocaleString('en-US');
                }
              }
            }
          },
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: { color: chartTextColor, maxRotation: 0, autoSkip: true },
              grid: { display: false }
            },
            y: {
              position: 'left',
              ticks: {
                color: chartTextColor,
                callback: function (value) {
                  return 'Rp ' + Number(value).toLocaleString('en-US');
                }
              },
              grid: { color: chartGridColor }
            },
            y1: {
              position: 'right',
              ticks: { color: '#08132e' },
              grid: { drawOnChartArea: false }
            }
          }
        }
      });

      const topCarsChart = new Chart(topCarsCanvas, {
        type: 'bar',
        data: {
          labels: @json($topBrands->pluck('merk')->map(fn($merk) => $merk ?: 'Tidak diketahui')),
          datasets: [{
            label: 'Total Terjual',
            data: @json($topBrands->pluck('total')),
            borderRadius: 14,
            backgroundColor: '#102a63'
          }]
        },
        options: {
          ...smoothBarMotion,
          indexAxis: 'y',
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: chartTextColor }, grid: { color: chartGridColor } },
            y: { ticks: { color: chartTextColor }, grid: { display: false } }
          }
        }
      });

      let salesTrendRequest = null;
      let salesReportRequest = null;
      let dashboardSyncRequest = null;
      let currentReportRange = @json($reportRange);

      const syncSupervisorYear = (yearValue) => {
        salesTrendYear.value = yearValue;
        salesReportYear.value = yearValue;
      };

      const setSalesTrendLoading = (isLoading) => {
        salesTrendYear.disabled = isLoading;
        salesTrendForm.classList.toggle('opacity-70', isLoading);
      };

      const setSalesReportLoading = (isLoading) => {
        salesReportForm.classList.toggle('opacity-70', isLoading);
        salesReportYear.disabled = isLoading;
        salesReportMonth.disabled = isLoading || currentReportRange !== 'monthly';
        salesReportForm.querySelectorAll('[data-report-range]').forEach((button) => {
          button.disabled = isLoading;
        });
      };

      const applySalesTrendPayload = (payload) => {
        salesChart.data.labels = payload.sales_chart.labels;
        salesChart.data.datasets[0].data = payload.sales_chart.cash_totals;
        salesChart.data.datasets[1].data = payload.sales_chart.credit_totals;
        cashPurchaseTotal.textContent = new Intl.NumberFormat('id-ID').format(payload.sales_chart.cash_purchase_total || 0);
        creditPurchaseTotal.textContent = new Intl.NumberFormat('id-ID').format(payload.sales_chart.credit_purchase_total || 0);

        revenueChart.data.labels = payload.revenue_chart.labels;
        revenueChart.data.datasets[0].data = payload.revenue_chart.totals;
      };

      const applySalesReportPayload = (payload) => {
        salesReportNote.textContent = payload.period_note;
        salesReportRangeActive.textContent = payload.summary.range_active;
        salesReportRevenue.innerHTML = payload.summary.total_revenue.replace(/,/g, ',<wbr>');
        salesReportOrders.textContent = payload.summary.total_orders;
        salesReportAverage.textContent = payload.summary.average_order_value;
        salesReportUpdatedAt.textContent = payload.summary.last_updated;
        topBrandsRangeLabel.textContent = payload.active_range_label;
        salesReportMonthWrap.classList.toggle('hidden', currentReportRange !== 'monthly');
        salesReportMonth.disabled = currentReportRange !== 'monthly';
        salesTrendForm.elements.report_range.value = currentReportRange;

        salesReportChart.data.labels = payload.chart.labels;
        salesReportChart.data.datasets[0].data = payload.chart.revenues;
        salesReportChart.data.datasets[1].data = payload.chart.orders;

        topCarsChart.data.labels = payload.top_brands.labels;
        topCarsChart.data.datasets[0].data = payload.top_brands.totals;

        topBrandsEmpty.textContent = payload.top_brands.empty_message;
        topBrandsEmpty.classList.toggle('hidden', !payload.top_brands.empty);
        topBrandsChartWrap.classList.toggle('hidden', payload.top_brands.empty);

        salesReportForm.querySelectorAll('[data-report-range]').forEach((button) => {
          const isActive = button.value === currentReportRange;
          button.className = `inline-flex items-center rounded-full border px-4 py-2 text-xs font-semibold transition ${isActive ? 'border-[#08132e] bg-[#08132e] text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)]' : 'border-slate-200 bg-white/80 text-slate-600 hover:bg-slate-50 hover:text-slate-900'}`;
        });
      };

      const syncSupervisorHistory = (params) => {
        const nextUrl = new URL(window.location.href);
        params.forEach((value, key) => {
          if (key !== 'panel') {
            nextUrl.searchParams.set(key, value);
          }
        });
        window.history.replaceState({}, '', nextUrl);
      };

      const updateSalesTrend = async () => {
        if (salesTrendRequest) {
          salesTrendRequest.abort();
        }

        const requestController = new AbortController();
        salesTrendRequest = requestController;
        const params = new URLSearchParams({
          panel: 'sales-trend',
          year: salesTrendYear.value,
          report_range: salesTrendForm.elements.report_range.value,
        });

        setSalesTrendLoading(true);

        try {
          const response = await fetch(`${salesTrendForm.action}?${params.toString()}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            signal: requestController.signal,
          });

          if (!response.ok) {
            throw new Error('Gagal memuat tren penjualan.');
          }

          const payload = await response.json();
          applySalesTrendPayload(payload);
          salesChart.update('active');
          revenueChart.update('active');

          syncSupervisorHistory(params);
        } catch (error) {
          if (error.name !== 'AbortError') {
            salesTrendForm.submit();
          }
        } finally {
          if (salesTrendRequest === requestController) {
            setSalesTrendLoading(false);
          }
        }
      };

      const updateSalesReport = async (submitter = null) => {
        if (salesReportRequest) {
          salesReportRequest.abort();
        }

        const requestController = new AbortController();
        salesReportRequest = requestController;
        const params = new URLSearchParams();
        const formData = new FormData(salesReportForm);
        formData.forEach((value, key) => {
          if (value !== null && value !== '') {
            params.set(key, value.toString());
          }
        });

        if (submitter && submitter.name && submitter.value) {
          params.set(submitter.name, submitter.value);
        }

          if (submitter && submitter.name === 'report_range') {
            currentReportRange = submitter.value;
          }

          params.set('report_range', currentReportRange);
          params.set('panel', 'sales-report');
          setSalesReportLoading(true);

        try {
          const response = await fetch(`${salesReportForm.action}?${params.toString()}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            signal: requestController.signal,
          });

          if (!response.ok) {
            throw new Error('Gagal memuat laporan penjualan.');
          }

          const payload = await response.json();
          applySalesReportPayload(payload);
          salesReportChart.update('active');
          topCarsChart.update('active');
          syncSupervisorHistory(params);
        } catch (error) {
          if (error.name !== 'AbortError') {
            if (submitter && submitter.name === 'report_range') {
              const hiddenSubmit = document.createElement('input');
              hiddenSubmit.type = 'hidden';
              hiddenSubmit.name = submitter.name;
              hiddenSubmit.value = submitter.value;
              salesReportForm.appendChild(hiddenSubmit);
            }
            salesReportForm.submit();
          }
        } finally {
          if (salesReportRequest === requestController) {
            setSalesReportLoading(false);
          }
        }
      };

      const updateSupervisorChartsTogether = async () => {
        if (dashboardSyncRequest) {
          dashboardSyncRequest.abort();
        }

        if (salesTrendRequest) {
          salesTrendRequest.abort();
        }

        if (salesReportRequest) {
          salesReportRequest.abort();
        }

        const requestController = new AbortController();
        dashboardSyncRequest = requestController;
        salesTrendForm.elements.report_range.value = currentReportRange;

        const trendParams = new URLSearchParams({
          panel: 'sales-trend',
          year: salesTrendYear.value,
          report_range: currentReportRange,
        });

        const reportParams = new URLSearchParams();
        const reportFormData = new FormData(salesReportForm);
        reportFormData.forEach((value, key) => {
          if (value !== null && value !== '') {
            reportParams.set(key, value.toString());
          }
        });
        reportParams.set('report_range', currentReportRange);
        reportParams.set('panel', 'sales-report');

        setSalesTrendLoading(true);
        setSalesReportLoading(true);

        try {
          const [trendResponse, reportResponse] = await Promise.all([
            fetch(`${salesTrendForm.action}?${trendParams.toString()}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
              signal: requestController.signal,
            }),
            fetch(`${salesReportForm.action}?${reportParams.toString()}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
              signal: requestController.signal,
            }),
          ]);

          if (!trendResponse.ok || !reportResponse.ok) {
            throw new Error('Gagal memuat dashboard supervisor.');
          }

          const [trendPayload, reportPayload] = await Promise.all([
            trendResponse.json(),
            reportResponse.json(),
          ]);

          applySalesTrendPayload(trendPayload);
          applySalesReportPayload(reportPayload);

          requestAnimationFrame(() => {
            salesChart.update('active');
            revenueChart.update('active');
            salesReportChart.update('active');
            topCarsChart.update('active');
          });

          syncSupervisorHistory(reportParams);
        } catch (error) {
          if (error.name !== 'AbortError') {
            salesReportForm.submit();
          }
        } finally {
          if (dashboardSyncRequest === requestController) {
            setSalesTrendLoading(false);
            setSalesReportLoading(false);
          }
        }
      };

      salesTrendYear.addEventListener('change', async () => {
        syncSupervisorYear(salesTrendYear.value);
        await updateSupervisorChartsTogether();
      });
      salesTrendForm.addEventListener('submit', (event) => event.preventDefault());

      salesReportYear.addEventListener('change', async () => {
        syncSupervisorYear(salesReportYear.value);
        await updateSupervisorChartsTogether();
      });
      salesReportMonth.addEventListener('change', () => updateSalesReport());
      salesReportForm.querySelectorAll('[data-report-range]').forEach((button) => {
        button.addEventListener('click', (event) => {
          event.preventDefault();
          updateSalesReport(button);
        });
      });
      salesReportForm.addEventListener('submit', (event) => event.preventDefault());
    })();
  </script>
@endpush
