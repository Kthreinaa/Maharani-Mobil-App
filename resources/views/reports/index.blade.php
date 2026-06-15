<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Penjualan | Owner</title>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    .mm-data-table {
      border-collapse: separate;
      border-spacing: 0;
    }
    .mm-data-table thead {
      background: linear-gradient(135deg, rgba(8,19,46,0.08) 0%, rgba(236,244,255,0.94) 48%, rgba(255,248,231,0.96) 100%);
    }
    .mm-data-table thead th {
      letter-spacing: 0.14em;
      font-size: 12px;
      font-weight: 800;
      color: #5f6f89;
      background: transparent;
    }
    .mm-data-table thead th:first-child { border-top-left-radius: 1rem; }
    .mm-data-table thead th:last-child { border-top-right-radius: 1rem; }
    .mm-data-table tbody tr { transition: background-color 180ms ease; }
    .mm-data-table tbody tr:hover { background: rgba(255,255,255,0.74); }
  </style>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#eef4ff_0%,#f8fbff_28%,#ffffff_100%)] text-slate-900">
  @php
    $summary = $report['summary'];
    $rows = $report['rows'];
    $selectedYear = $report['selected_year'];
    $selectedMonth = $report['selected_month'];
    $availableYears = $report['available_years'];
    $monthOptions = [
      1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
      5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
      9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $cardStyles = [
      'bg-[linear-gradient(135deg,rgba(226,239,255,0.94),rgba(245,250,255,0.78))] border-sky-100/90',
      'bg-[linear-gradient(135deg,rgba(255,247,214,0.96),rgba(255,252,240,0.80))] border-amber-100/90',
      'bg-[linear-gradient(135deg,rgba(225,249,239,0.96),rgba(244,255,250,0.80))] border-emerald-100/90',
      'bg-[linear-gradient(135deg,rgba(243,238,255,0.94),rgba(250,247,255,0.82))] border-violet-100/90',
    ];
  @endphp

  <div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(11,26,64,0.18),transparent_26%),radial-gradient(circle_at_top_right,rgba(245,166,35,0.15),transparent_22%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.10),transparent_18%)]"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-[1600px] gap-6 px-4 py-4 md:px-6 md:py-6">
      <aside class="hidden w-[298px] shrink-0 lg:block">
        <div class="flex h-full flex-col overflow-visible rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)] backdrop-blur-[26px]">
          <div class="rounded-[1.6rem] bg-[linear-gradient(135deg,#08132e_0%,#102a63_56%,#f5a623_140%)] px-5 py-5 text-white shadow-[0_18px_40px_rgba(8,19,46,0.24)]">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-[#f7c35f]">Owner Workspace</p>
            <div class="mt-3 text-[28px] font-extrabold tracking-tight">Maharani Mobil</div>
          </div>

          <nav class="mt-5 flex-1 space-y-1.5">
            <a class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-white/80 hover:text-slate-900" href="{{ route('owner.dashboard', ['year' => $selectedYear]) }}">
              <span class="material-symbols-outlined text-[20px] text-slate-400 group-hover:text-[#08132e]">dashboard</span>
              <span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 rounded-[1.2rem] bg-[#08132e] px-4 py-3 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(8,19,46,0.18)]" href="{{ route('owner.reports.index', request()->query()) }}">
              <span class="material-symbols-outlined text-[20px] text-[#f7c35f]">monitoring</span>
              <span>Laporan Penjualan</span>
            </a>
            <a class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-white/80 hover:text-slate-900" href="{{ route('owner.settings.edit') }}">
              <span class="material-symbols-outlined text-[20px] text-slate-400 group-hover:text-[#08132e]">settings</span>
              <span>Setting</span>
            </a>
          </nav>

          <div class="mt-auto rounded-[1.4rem] border border-slate-200/80 bg-white/70 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Owner Mode</p>
            <p class="mt-2 text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'Owner' }}</p>
            <p class="mt-1 text-xs text-slate-500">Pantau ringkasan penjualan, pola pembelian, dan arah bisnis showroom.</p>
            <form class="mt-4" method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-white" type="submit">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Logout
              </button>
            </form>
          </div>
        </div>
      </aside>

      <div class="min-w-0 flex-1">
        <div class="mb-6 overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-4 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur-[26px] md:px-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
              <h1 class="mt-1 font-headline text-[24px] font-extrabold tracking-tight text-slate-900 md:text-[28px]">Laporan Penjualan</h1>
              <p class="mt-1 text-sm text-slate-500">Owner Control Center</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">calendar_month</span>
                {{ now()->format('d M Y') }}
              </div>
              <a class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ route('owner.settings.edit') }}">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">settings</span>
                Setting
              </a>
              <div class="inline-flex h-11 min-w-11 items-center justify-center rounded-full bg-[linear-gradient(135deg,#08132e_0%,#102a63_100%)] px-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(8,19,46,0.18)]">
                {{ strtoupper(substr(auth()->user()->name ?? 'OW', 0, 2)) }}
              </div>
            </div>
          </div>
        </div>

        <div class="pb-6">
          <div class="mb-6 rounded-[2rem] border border-white/70 bg-[linear-gradient(135deg,#08132e_0%,#102a63_55%,#f5a623_145%)] p-6 text-white shadow-[0_26px_70px_rgba(8,19,46,0.18)]">
            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f7c35f]">Sales Report Center</p>
            <h2 class="mt-3 text-[30px] font-extrabold tracking-tight">Laporan Penjualan & Analisis</h2>
            <p class="mt-3 max-w-[820px] text-sm leading-7 text-slate-200">
              Laporan ini merangkum hasil penjualan, pola pembelian, aktivitas pembelian customer, dan performa merk mobil pada periode yang dipilih.
            </p>
          </div>

          <div class="mb-6 flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2">
              @foreach (['weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $key => $label)
                <a data-history-replace class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request('period', 'monthly') === $key ? 'border-[#08132e] bg-[#08132e] text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)]' : 'border-slate-200 bg-white/80 text-slate-600 hover:bg-white' }}" href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['period' => $key])) }}">{{ $label }}</a>
              @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <form id="reportFilterForm" method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="period" value="{{ request('period', 'monthly') }}">
                <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500" for="year">Tahun</label>
                <select id="year" name="year" data-auto-submit class="rounded-full border border-slate-200 bg-white/90 px-4 py-2.5 text-sm font-semibold text-slate-700">
                  @foreach ($availableYears as $year)
                    <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                  @endforeach
                </select>

                @if (request('period', 'monthly') === 'monthly')
                  <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500" for="month">Bulan</label>
                  <select id="month" name="month" data-auto-submit class="rounded-full border border-slate-200 bg-white/90 px-4 py-2.5 text-sm font-semibold text-slate-700">
                    @foreach ($monthOptions as $monthNumber => $monthLabel)
                      <option value="{{ $monthNumber }}" @selected($selectedMonth === $monthNumber)>{{ $monthLabel }}</option>
                    @endforeach
                  </select>
                @endif
              </form>

              <div class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-600">
                {{ $summary['range_label'] }}
              </div>
              <a class="inline-flex items-center justify-center rounded-full bg-[#08132e] px-4 py-2.5 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)]" href="{{ route('owner.reports.exportPdf', request()->query()) }}">Export PDF</a>
            </div>
          </div>

          <section class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ([
              ['label' => 'Total Transaksi', 'value' => number_format((int) $summary['total_orders']), 'note' => 'Jumlah transaksi pada periode ini', 'value_class' => 'text-[30px]'],
              ['label' => 'Omzet', 'value' => \App\Support\CurrencyFormatter::rupiah($summary['omzet']), 'note' => 'Total nilai penjualan tercatat', 'value_class' => 'text-[18px] leading-tight whitespace-nowrap xl:text-[20px]'],
              ['label' => 'Rata-rata Transaksi', 'value' => \App\Support\CurrencyFormatter::rupiah($summary['average_order']), 'note' => 'Rata-rata nilai per transaksi', 'value_class' => 'text-[18px] leading-tight whitespace-nowrap xl:text-[20px]'],
              ['label' => 'Tingkat Selesai', 'value' => $summary['completion_rate'] . '%', 'note' => 'Transaksi yang sudah selesai penuh', 'value_class' => 'text-[30px]'],
              ['label' => 'Online', 'value' => number_format((int) ($summary['online_orders'] ?? 0)), 'note' => ($summary['online_share'] ?? 0) . '% dari website', 'value_class' => 'text-[30px]'],
              ['label' => 'Offline', 'value' => number_format((int) ($summary['offline_orders'] ?? 0)), 'note' => ($summary['offline_share'] ?? 0) . '% dari showroom', 'value_class' => 'text-[30px]'],
            ] as $index => $item)
              <article class="rounded-[1.8rem] border p-5 shadow-[0_18px_50px_rgba(15,23,42,0.06)] backdrop-blur-[18px] {{ $cardStyles[$index % count($cardStyles)] }}">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $item['label'] }}</p>
                <p class="mt-3 font-extrabold tracking-tight text-slate-900 {{ $item['value_class'] }}">{{ $item['value'] }}</p>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['note'] }}</p>
              </article>
            @endforeach
          </section>

          <section class="mb-6 rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Aktivitas Pembelian</p>
                <h2 class="mt-2 text-[24px] font-extrabold text-slate-900">Order dan Metode Pembelian</h2>
              </div>
                <p class="text-sm text-slate-500">Ringkasan ini membantu owner membaca jumlah transaksi online, offline, cash, dan kredit pada periode aktif.</p>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
              @foreach ($report['crm_overview'] as $item)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4 shadow-[0_12px_34px_rgba(15,23,42,0.05)]">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $item['label'] }}</p>
                  <p class="mt-3 text-[24px] font-extrabold tracking-tight text-slate-900">{{ $item['value'] }}</p>
                  <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['note'] }}</p>
                </article>
              @endforeach
            </div>
          </section>

          <section class="mb-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Grafik Ringkas</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Tren Penjualan</h2>
                </div>
                <p class="text-xs font-semibold text-slate-500">Siap diexport ke PDF</p>
              </div>

              <div class="mt-5 space-y-4">
                @forelse ($report['trend'] as $point)
                  <div class="grid gap-3 rounded-[1.3rem] border border-slate-200/80 bg-white/75 px-4 py-4 md:grid-cols-[140px_1fr_auto_auto] md:items-center">
                    <p class="text-sm font-semibold text-slate-700">{{ $point['label'] }}</p>
                    <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                      <div class="h-full rounded-full bg-[linear-gradient(90deg,#f5a623_0%,#102a63_100%)]" style="width: {{ $summary['omzet'] > 0 ? max(6, min(100, round(($point['revenue'] / $summary['omzet']) * 100))) : 0 }}%"></div>
                    </div>
                    <p class="text-sm font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($point['revenue']) }}</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $point['orders'] }} order</p>
                  </div>
                @empty
                  <div class="rounded-[1.3rem] border border-dashed border-slate-200 bg-slate-50/80 px-4 py-6 text-sm text-slate-500">
                    Belum ada data tren penjualan pada periode ini.
                  </div>
                @endforelse
              </div>
            </article>

            <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Analisis</p>
              <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Ringkasan Periode Ini</h2>
              <div class="mt-5 space-y-4">
                @foreach ($report['analysis'] as $item)
                  <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
                    <p class="text-sm font-bold text-slate-900">{{ $item['title'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['detail'] }}</p>
                  </div>
                @endforeach
              </div>
            </article>
          </section>

          <section class="mb-6">
            <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Kontribusi Merk</p>
              <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Merk Mobil Paling Laris</h2>
              <div class="mt-5 space-y-4">
                @forelse ($report['brand_performance'] as $item)
                  <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
                    <div class="flex items-center justify-between gap-4">
                      <p class="text-base font-extrabold text-slate-900">{{ $item['brand'] }}</p>
                      <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $item['share'] }}%</span>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                      <div class="h-full rounded-full bg-[linear-gradient(90deg,#38bdf8_0%,#2563eb_100%)]" style="width: {{ min(100, max(8, round($item['share']))) }}%"></div>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
                      <span>{{ $item['units'] }} unit</span>
                      <span class="font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($item['revenue']) }}</span>
                    </div>
                  </div>
                @empty
                  <div class="rounded-[1.3rem] border border-dashed border-slate-200 bg-slate-50/80 px-4 py-6 text-sm text-slate-500">
                    Belum ada performa merk yang bisa dianalisis.
                  </div>
                @endforelse
              </div>
            </article>
          </section>

          <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
            <div class="border-b border-slate-200/70 px-5 py-4">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Detail Transaksi</p>
              <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Rincian Penjualan</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="mm-data-table w-full min-w-[1300px] border-collapse text-left text-sm">
                <thead class="text-[11px] uppercase tracking-[0.18em] text-slate-500">
                  <tr>
                    <th class="px-5 py-4">Tanggal</th>
                    <th class="px-5 py-4">Customer</th>
                    <th class="px-5 py-4">Unit</th>
                    <th class="px-5 py-4">Alur</th>
                    <th class="px-5 py-4">Metode Beli</th>
                    <th class="px-5 py-4">Nominal</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Pengelola</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/65">
                  @forelse($rows as $row)
                    <tr class="transition hover:bg-white/75">
                      <td class="px-5 py-4 font-semibold text-slate-800">{{ $row->tanggal }}</td>
                      <td class="px-5 py-4 text-slate-700">{{ $row->customer }}</td>
                      <td class="px-5 py-4">
                        <p class="font-semibold text-slate-800">{{ $row->mobil }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $row->kode_unit }}</p>
                      </td>
                      <td class="px-5 py-4 text-slate-700">
                        <p class="font-semibold text-slate-800">{{ $row->transaction_channel_label }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $row->sales_flow_label }}</p>
                      </td>
                      <td class="px-5 py-4 text-slate-700">
                        <p class="font-semibold text-slate-800">{{ $row->metode_beli_label }}</p>
                        <p class="mt-1 text-xs text-slate-500">Dibayar dengan {{ $row->metode_bayar_label }}</p>
                      </td>
                      <td class="px-5 py-4 whitespace-nowrap font-extrabold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($row->nominal) }}</td>
                      <td class="px-5 py-4">
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-slate-700">{{ $row->status_order }}</span>
                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $row->status_pembayaran }}</p>
                      </td>
                      <td class="px-5 py-4">
                        <p class="font-semibold text-slate-800">{{ $row->handled_role === 'marketing' ? 'Marketing' : ($row->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Ditandai') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $row->handled_by_name ?: '-' }}</p>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td class="px-5 py-10 text-center text-slate-500" colspan="8">Belum ada data penjualan untuk periode ini.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const filterForm = document.getElementById('reportFilterForm');

      if (!filterForm) {
        return;
      }

      const replaceTo = (url) => {
        window.location.replace(url);
      };

      const buildFilterUrl = () => {
        const params = new URLSearchParams(new FormData(filterForm));
        return `${filterForm.action}?${params.toString()}`;
      };

      filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        replaceTo(buildFilterUrl());
      });

      filterForm.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
          replaceTo(buildFilterUrl());
        });
      });

      document.querySelectorAll('[data-history-replace]').forEach((link) => {
        link.addEventListener('click', (event) => {
          event.preventDefault();
          replaceTo(link.href);
        });
      });
    })();
  </script>
</body>
</html>
