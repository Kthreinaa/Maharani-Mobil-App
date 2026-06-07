<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Owner | Maharani Mobil</title>
  <meta name="description" content="Dashboard owner Maharani Mobil dengan analisis penjualan, tren pendapatan, dan wawasan strategis."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#eef4ff_0%,#f8fbff_28%,#ffffff_100%)] text-slate-900">
  @php
    $ownerNavItems = [
      ['label' => 'Dashboard', 'route' => route('owner.dashboard', ['year' => $selectedYear]), 'active' => true, 'icon' => 'dashboard'],
      ['label' => 'Laporan Penjualan', 'route' => $reportLinks['center'], 'active' => false, 'icon' => 'monitoring'],
      ['label' => 'Export PDF', 'route' => $reportLinks['pdf'], 'active' => false, 'icon' => 'picture_as_pdf'],
    ];
    $summaryTones = [
      'sky' => 'bg-[linear-gradient(135deg,rgba(227,239,255,0.94),rgba(245,250,255,0.80))] border-sky-100/90',
      'amber' => 'bg-[linear-gradient(135deg,rgba(255,247,214,0.96),rgba(255,252,241,0.82))] border-amber-100/90',
      'emerald' => 'bg-[linear-gradient(135deg,rgba(225,249,239,0.96),rgba(243,255,249,0.82))] border-emerald-100/90',
      'violet' => 'bg-[linear-gradient(135deg,rgba(243,238,255,0.96),rgba(250,247,255,0.84))] border-violet-100/90',
    ];
    $summaryIcons = [
      'sky' => ['icon' => 'payments', 'bg' => 'bg-[#0f3a86]', 'text' => 'text-white'],
      'amber' => ['icon' => 'directions_car', 'bg' => 'bg-[#f5a623]', 'text' => 'text-white'],
      'emerald' => ['icon' => 'monitoring', 'bg' => 'bg-[#14b87a]', 'text' => 'text-white'],
      'violet' => ['icon' => 'trending_up', 'bg' => 'bg-[#7c5cff]', 'text' => 'text-white'],
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
            @foreach ($ownerNavItems as $index => $item)
              <a
                id="{{ $index === 1 ? 'ownerReportCenterLink' : ($index === 2 ? 'ownerSidebarExportPdf' : '') }}"
                class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold transition {{ $item['active'] ? 'bg-[#08132e] text-white shadow-[0_18px_34px_rgba(8,19,46,0.18)]' : 'text-slate-600 hover:bg-white/80 hover:text-slate-900' }}"
                href="{{ $item['route'] }}"
              >
                <span class="material-symbols-outlined text-[20px] {{ $item['active'] ? 'text-[#f7c35f]' : 'text-slate-400 group-hover:text-[#08132e]' }}">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
              </a>
            @endforeach
          </nav>

          <div class="mt-auto rounded-[1.4rem] border border-slate-200/80 bg-white/70 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Owner Mode</p>
            <p class="mt-2 text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'Owner' }}</p>
            <p class="mt-1 text-xs text-slate-500">Pantau penjualan, pertumbuhan omzet, dan arah strategi showroom.</p>
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
        <div class="sticky top-4 z-30 mb-6 overflow-visible rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-4 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur-[26px] md:px-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
              <div class="flex items-center gap-3 lg:hidden">
                <span class="material-symbols-outlined rounded-full bg-[#08132e] p-2 text-white">workspace_premium</span>
                <span class="text-lg font-extrabold text-slate-900">Maharani Mobil</span>
              </div>
              <h1 class="mt-1 font-headline text-[24px] font-extrabold tracking-tight text-slate-900 md:text-[28px]">Owner</h1>
              <p class="mt-1 text-sm text-slate-500">Owner Control Center</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">calendar_month</span>
                {{ now()->format('d M Y') }}
              </div>
              <x-dashboard-notification-bell :data="$dashboardNotifications" title="Aktivitas Tim Internal" />
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

        <main class="pb-6">
          <section class="mb-6 overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,#08132e_0%,#102a63_56%,#f5a623_145%)] p-5 text-white shadow-[0_24px_70px_rgba(15,23,42,0.16)]">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
              <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f7c35f]">Owner Overview</p>
                <h2 class="mt-2 font-headline text-[26px] font-extrabold tracking-tight text-white">Dashboard Penjualan Owner</h2>
                <p class="mt-2 max-w-[780px] text-sm leading-6 text-slate-200">
                  Ringkasan ini membaca transaksi paid dan completed di sistem untuk membantu owner melihat arah pendapatan, merk terkuat, pertumbuhan performa, dan peluang strategi berikutnya.
                </p>
              </div>

              <div class="grid w-full gap-3 sm:max-w-[430px] sm:grid-cols-2">
                <div class="flex items-center gap-2 rounded-[1.35rem] border border-white/20 bg-white/12 px-4 py-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.14)] backdrop-blur-xl">
                  <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-200">Tahun</span>
                  <select id="ownerDashboardYear" class="rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold text-slate-700 outline-none transition hover:bg-white">
                    @foreach ($availableYears as $year)
                      <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                    @endforeach
                  </select>
                </div>
                <a id="ownerTopReportCenterLink" class="inline-flex items-center justify-center rounded-[1.35rem] border border-white/25 bg-white/90 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ $reportLinks['center'] }}">Laporan Penjualan</a>
                <a id="ownerTopExportPdfLink" class="inline-flex items-center justify-center rounded-[1.35rem] bg-[#08132e] px-4 py-3 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)] transition hover:bg-[#102a63]" href="{{ $reportLinks['pdf'] }}">Export PDF</a>
              </div>
            </div>
          </section>

          <section class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $index => $card)
              @php
                $cardIcon = $summaryIcons[$card['tone']] ?? $summaryIcons['sky'];
                $isCurrencyCard = str_starts_with($card['value'], 'Rp.');
              @endphp
              <article class="rounded-[1.8rem] border p-5 shadow-[0_18px_50px_rgba(15,23,42,0.06)] backdrop-blur-[18px] {{ $summaryTones[$card['tone']] ?? $summaryTones['sky'] }}">
                <div class="flex items-start justify-between gap-3">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $card['label'] }}</p>
                  <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full shadow-[0_16px_30px_rgba(15,23,42,0.12)] {{ $cardIcon['bg'] }} {{ $cardIcon['text'] }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $cardIcon['icon'] }}</span>
                  </span>
                </div>
                <p id="ownerSummaryValue{{ $index }}" class="mt-3 font-extrabold tracking-tight text-slate-900 {{ $isCurrencyCard ? 'text-[22px] leading-[1.22] md:text-[24px]' : 'text-[28px]' }}">{!! $isCurrencyCard ? preg_replace('/^Rp\.\s/u', 'Rp.&nbsp;', e($card['value'])) : e($card['value']) !!}</p>
                <p id="ownerSummaryNote{{ $index }}" class="mt-2 text-sm leading-6 text-slate-600">{{ $card['note'] }}</p>
              </article>
            @endforeach
          </section>

          <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Sales Overview</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Dashboard Penjualan</h2>
                  <p class="mt-2 text-sm text-slate-500">Pergerakan omzet dan jumlah transaksi per bulan pada tahun yang sedang dipantau.</p>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Auto sync per tahun</div>
              </div>
              <div class="rounded-[1.4rem] border border-slate-200/80 bg-white/60 p-3 shadow-inner shadow-white/70">
                <div class="h-[300px]">
                  <canvas id="ownerSalesOverviewChart"></canvas>
                </div>
              </div>
            </article>

            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Report Snapshot</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Laporan Penjualan</h2>
                </div>
                <a class="text-sm font-semibold text-slate-500 transition hover:text-slate-900" href="{{ $reportLinks['center'] }}">Buka Detail</a>
              </div>

              <div class="mt-5 space-y-4">
                <div class="rounded-[1.35rem] border border-slate-200/80 bg-white/78 px-4 py-4">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Periode Aktif</p>
                  <p id="ownerReportTitle" class="mt-2 text-xl font-extrabold text-slate-900">{{ $reportSummary['title'] }}</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                  <div class="rounded-[1.25rem] border border-slate-200/80 bg-white/75 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Omzet</p>
                    <p id="ownerReportRevenue" class="mt-2 text-base font-extrabold text-slate-900">{{ $reportSummary['total_revenue'] }}</p>
                  </div>
                  <div class="rounded-[1.25rem] border border-slate-200/80 bg-white/75 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Transaksi</p>
                    <p id="ownerReportOrders" class="mt-2 text-base font-extrabold text-slate-900">{{ $reportSummary['total_orders'] }}</p>
                  </div>
                </div>
                <div class="rounded-[1.25rem] border border-slate-200/80 bg-[linear-gradient(135deg,rgba(8,19,46,0.06)_0%,rgba(245,166,35,0.10)_100%)] px-4 py-4 text-sm text-slate-600">
                  <p>
                    Rata-rata transaksi saat ini <span id="ownerReportAverage" class="font-bold text-slate-900">{{ $reportSummary['average_order'] }}</span>.
                  </p>
                  <p class="mt-2">
                    Puncak omzet ada di <span id="ownerReportPeakMonth" class="font-bold text-slate-900">{{ $reportSummary['peak_month'] }}</span>.
                  </p>
                </div>
                <div class="flex flex-wrap gap-3">
                  <a id="ownerInlineReportCenterLink" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ $reportLinks['center'] }}">Buka Report Center</a>
                  <a id="ownerInlineExportPdfLink" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ $reportLinks['pdf'] }}">Export PDF</a>
                </div>
              </div>
            </article>
          </section>

          <section class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Revenue Flow</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Grafik Pendapatan</h2>
                  <p class="mt-2 text-sm text-slate-500">Akumulasi omzet per tahun dari transaksi yang sudah selesai atau dibayar.</p>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Lintas tahun</div>
              </div>
              <div class="rounded-[1.4rem] border border-slate-200/80 bg-white/60 p-3 shadow-inner shadow-white/70">
                <div class="h-[290px]">
                  <canvas id="ownerAnnualRevenueChart"></canvas>
                </div>
              </div>
            </article>

            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Demand Leaderboard</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Merk Terlaris</h2>
                  <p class="mt-2 text-sm text-slate-500">
                    Merk terkuat tahun <span id="ownerTopBrandHeadlineYear">{{ $selectedYear }}</span>:
                    <span id="ownerTopBrandHeadlineName" class="font-semibold text-slate-900">{{ $topBrandHeadline['name'] }}</span>
                    <span id="ownerTopBrandHeadlineShare" class="font-semibold text-slate-600">({{ number_format((float) $topBrandHeadline['share'], 1) }}%)</span>
                  </p>
                </div>
              </div>
              <div id="ownerTopBrandsEmpty" class="rounded-[1.25rem] border border-dashed border-slate-200 px-4 py-12 text-center text-sm text-slate-500 {{ $topBrands->isEmpty() ? '' : 'hidden' }}">
                Belum ada merk dominan pada tahun {{ $selectedYear }}.
              </div>
              <div id="ownerTopBrandsList" class="space-y-3 {{ $topBrands->isEmpty() ? 'hidden' : '' }}">
                @foreach ($topBrands as $brand)
                  <article class="rounded-[1.3rem] border border-slate-200/80 bg-white/78 px-4 py-4 shadow-[0_12px_30px_rgba(15,23,42,0.05)]">
                    <div class="flex items-center justify-between gap-3">
                      <div>
                        <p class="text-base font-extrabold text-slate-900">{{ $brand['brand'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ number_format($brand['units']) }} unit terjual</p>
                      </div>
                      <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ number_format((float) $brand['share'], 1) }}%</span>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                      <div class="h-full rounded-full bg-[linear-gradient(90deg,#08132e_0%,#f5a623_100%)]" style="width: {{ min(100, max(8, round((float) $brand['share']))) }}%"></div>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah((float) $brand['revenue']) }}</p>
                  </article>
                @endforeach
              </div>
            </article>
          </section>

          <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="mb-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Perilaku Pembelian</p>
                <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Metode Pembelian Customer</h2>
                <p class="mt-2 text-sm text-slate-500">Ringkasan ini menunjukkan berapa banyak customer membeli unit secara cash dan kredit pada tahun aktif.</p>
              </div>

              <div id="ownerPurchaseBehavior" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($purchaseBehavior as $item)
                  <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 px-4 py-4 shadow-[0_12px_30px_rgba(15,23,42,0.05)]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-3 text-[28px] font-extrabold tracking-tight text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ number_format((float) $item['share'], 1) }}% dari total transaksi tahun ini</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $item['note'] }}</p>
                  </article>
                @endforeach
              </div>
            </article>

            <article class="overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                  <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Strategic Insights</p>
                  <h2 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Wawasan Strategis</h2>
                  <p class="mt-2 text-sm text-slate-500">Semua insight di bawah ini dibentuk dari transaksi riil yang tercatat di sistem.</p>
                </div>
              </div>

              <div id="ownerStrategicInsights" class="space-y-4">
                @foreach ($strategicInsights as $insight)
                  <article class="rounded-[1.4rem] border border-slate-200/80 bg-white/82 px-4 py-4 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
                    <p class="text-sm font-extrabold text-slate-900">{{ $insight['title'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $insight['detail'] }}</p>
                  </article>
                @endforeach
              </div>
            </article>
          </section>
        </main>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function () {
      const yearSelect = document.getElementById('ownerDashboardYear');
      const salesOverviewCanvas = document.getElementById('ownerSalesOverviewChart');
      const annualRevenueCanvas = document.getElementById('ownerAnnualRevenueChart');

      if (!yearSelect || !salesOverviewCanvas || !annualRevenueCanvas) {
        return;
      }

      const chartTextColor = '#475569';
      const chartGridColor = 'rgba(148, 163, 184, 0.18)';
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

      const salesOverviewContext = salesOverviewCanvas.getContext('2d');
      const salesOverviewGradient = salesOverviewContext.createLinearGradient(0, 0, 0, 260);
      salesOverviewGradient.addColorStop(0, 'rgba(8, 19, 46, 0.28)');
      salesOverviewGradient.addColorStop(1, 'rgba(8, 19, 46, 0.02)');

      const salesOverviewChart = new Chart(salesOverviewCanvas, {
        type: 'line',
        data: {
          labels: @json($monthlyPerformance->pluck('label')->values()),
          datasets: [{
            label: 'Pendapatan',
            data: @json($monthlyPerformance->pluck('revenue')->values()),
            borderColor: '#08132e',
            backgroundColor: salesOverviewGradient,
            fill: true,
            borderWidth: 3,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#08132e',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            yAxisID: 'y',
            tension: 0.35
          }, {
            type: 'bar',
            label: 'Transaksi',
            data: @json($monthlyPerformance->pluck('orders')->values()),
            borderRadius: 12,
            backgroundColor: 'rgba(245, 166, 35, 0.78)',
            yAxisID: 'y1',
          }]
        },
        options: {
          ...smoothLineMotion,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: chartTextColor, usePointStyle: true, boxWidth: 10 }
            }
          },
          scales: {
            x: { ticks: { color: chartTextColor }, grid: { display: false } },
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

      const annualRevenueChart = new Chart(annualRevenueCanvas, {
        type: 'bar',
        data: {
          labels: @json($annualRevenue->pluck('year')->map(fn ($year) => (string) $year)->values()),
          datasets: [{
            label: 'Pendapatan',
            data: @json($annualRevenue->pluck('revenue')->values()),
            borderRadius: 14,
            backgroundColor: ['#08132e', '#102a63', '#1f4ba8', '#2a5fd3', '#f5a623', '#f7c35f']
          }]
        },
        options: {
          ...smoothBarMotion,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { ticks: { color: chartTextColor }, grid: { display: false } },
            y: {
              ticks: {
                color: chartTextColor,
                callback: function (value) {
                  return 'Rp ' + Number(value).toLocaleString('en-US');
                }
              },
              grid: { color: chartGridColor }
            }
          }
        }
      });

      let ownerDashboardRequest = null;

      const reportLinkIds = [
        'ownerReportCenterLink',
        'ownerTopReportCenterLink',
        'ownerInlineReportCenterLink',
      ];
      const pdfLinkIds = [
        'ownerSidebarExportPdf',
        'ownerTopExportPdfLink',
        'ownerInlineExportPdfLink',
      ];
      const setLoadingState = (isLoading) => {
        yearSelect.disabled = isLoading;
        document.body.classList.toggle('cursor-progress', isLoading);
      };

      const formatOwnerSummaryValue = (value) => {
        if (typeof value === 'string' && value.startsWith('Rp. ')) {
          return value.replace('Rp. ', 'Rp.&nbsp;');
        }

        return value;
      };

      const updateLinks = (payload) => {
        reportLinkIds.forEach((id) => {
          const element = document.getElementById(id);
          if (element) {
            element.href = payload.report_links.center;
          }
        });

        pdfLinkIds.forEach((id) => {
          const element = document.getElementById(id);
          if (element) {
            element.href = payload.report_links.pdf;
          }
        });

      };

      const updateSummaryCards = (payload) => {
        payload.summary_cards.forEach((card, index) => {
          const valueElement = document.getElementById(`ownerSummaryValue${index}`);
          const noteElement = document.getElementById(`ownerSummaryNote${index}`);

          if (valueElement) {
            if (typeof card.value === 'string' && card.value.startsWith('Rp. ')) {
              valueElement.className = 'mt-3 text-[22px] font-extrabold tracking-tight text-slate-900 md:text-[24px] leading-[1.22]';
              valueElement.innerHTML = formatOwnerSummaryValue(card.value);
            } else {
              valueElement.className = 'mt-3 text-[28px] font-extrabold tracking-tight text-slate-900';
              valueElement.textContent = card.value;
            }
          }

          if (noteElement) {
            noteElement.textContent = card.note;
          }
        });
      };

      const updateReportSummary = (payload) => {
        const title = document.getElementById('ownerReportTitle');
        const revenue = document.getElementById('ownerReportRevenue');
        const orders = document.getElementById('ownerReportOrders');
        const average = document.getElementById('ownerReportAverage');
        const peakMonth = document.getElementById('ownerReportPeakMonth');
        const headlineYear = document.getElementById('ownerTopBrandHeadlineYear');
        const headlineName = document.getElementById('ownerTopBrandHeadlineName');
        const headlineShare = document.getElementById('ownerTopBrandHeadlineShare');

        if (title) title.textContent = payload.report_summary.title;
        if (revenue) revenue.textContent = payload.report_summary.total_revenue;
        if (orders) orders.textContent = payload.report_summary.total_orders;
        if (average) average.textContent = payload.report_summary.average_order;
        if (peakMonth) peakMonth.textContent = payload.report_summary.peak_month;
        if (headlineYear) headlineYear.textContent = payload.selected_year;
        if (headlineName) headlineName.textContent = payload.top_brand_headline.name;
        if (headlineShare) headlineShare.textContent = `(${Number(payload.top_brand_headline.share || 0).toFixed(1)}%)`;
      };

      const updatePurchaseBehavior = (payload) => {
        const container = document.getElementById('ownerPurchaseBehavior');

        if (!container) {
          return;
        }

        container.innerHTML = payload.purchase_behavior.map((item) => `
          <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 px-4 py-4 shadow-[0_12px_30px_rgba(15,23,42,0.05)]">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">${item.label}</p>
            <p class="mt-3 text-[28px] font-extrabold tracking-tight text-slate-900">${item.value}</p>
            <p class="mt-2 text-sm font-semibold text-slate-700">${Number(item.share || 0).toFixed(1)}% dari total transaksi tahun ini</p>
            <p class="mt-2 text-sm leading-6 text-slate-500">${item.note}</p>
          </article>
        `).join('');
      };

      const updateTopBrands = (payload) => {
        const list = document.getElementById('ownerTopBrandsList');
        const empty = document.getElementById('ownerTopBrandsEmpty');

        if (!list || !empty) {
          return;
        }

        if (payload.top_brands.empty) {
          list.classList.add('hidden');
          empty.classList.remove('hidden');
          empty.textContent = payload.top_brands.empty_message;
          list.innerHTML = '';
          return;
        }

        empty.classList.add('hidden');
        list.classList.remove('hidden');
        list.innerHTML = payload.top_brands.items.map((item) => {
          const shareWidth = Math.min(100, Math.max(8, Math.round(Number(item.share || 0))));

          return `
            <article class="rounded-[1.3rem] border border-slate-200/80 bg-white/78 px-4 py-4 shadow-[0_12px_30px_rgba(15,23,42,0.05)]">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <p class="text-base font-extrabold text-slate-900">${item.brand}</p>
                  <p class="mt-1 text-sm text-slate-500">${Number(item.units || 0).toLocaleString('en-US')} unit terjual</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">${Number(item.share || 0).toFixed(1)}%</span>
              </div>
              <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-[linear-gradient(90deg,#08132e_0%,#f5a623_100%)]" style="width:${shareWidth}%"></div>
              </div>
              <p class="mt-3 text-sm font-semibold text-slate-900">${item.revenue_formatted ?? item.revenue}</p>
            </article>
          `;
        }).join('');
      };

      const updateStrategicInsights = (payload) => {
        const container = document.getElementById('ownerStrategicInsights');

        if (!container) {
          return;
        }

        container.innerHTML = payload.strategic_insights.map((item) => `
          <article class="rounded-[1.4rem] border border-slate-200/80 bg-white/82 px-4 py-4 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
            <p class="text-sm font-extrabold text-slate-900">${item.title}</p>
            <p class="mt-2 text-sm leading-6 text-slate-600">${item.detail}</p>
          </article>
        `).join('');
      };

      const updateCharts = (payload) => {
        salesOverviewChart.data.labels = payload.sales_overview_chart.labels;
        salesOverviewChart.data.datasets[0].data = payload.sales_overview_chart.revenues;
        salesOverviewChart.data.datasets[1].data = payload.sales_overview_chart.orders;

        annualRevenueChart.data.labels = payload.annual_revenue_chart.labels;
        annualRevenueChart.data.datasets[0].data = payload.annual_revenue_chart.revenues;

        requestAnimationFrame(() => {
          salesOverviewChart.update('active');
          annualRevenueChart.update('active');
        });
      };

      const syncOwnerDashboard = async () => {
        if (ownerDashboardRequest) {
          ownerDashboardRequest.abort();
        }

        const requestController = new AbortController();
        ownerDashboardRequest = requestController;
        const params = new URLSearchParams({ year: yearSelect.value });

        setLoadingState(true);

        try {
          const response = await fetch(`{{ route('owner.dashboard') }}?${params.toString()}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            signal: requestController.signal,
          });

          if (!response.ok) {
            throw new Error('Gagal memuat dashboard owner.');
          }

          const payload = await response.json();
          updateSummaryCards(payload);
          updateReportSummary(payload);
          updatePurchaseBehavior(payload);
          updateLinks(payload);
          updateTopBrands(payload);
          updateStrategicInsights(payload);
          updateCharts(payload);

          const nextUrl = new URL(window.location.href);
          nextUrl.searchParams.set('year', yearSelect.value);
          window.history.replaceState({}, '', nextUrl);
        } catch (error) {
          if (error.name !== 'AbortError') {
            window.location.href = `{{ route('owner.dashboard') }}?${params.toString()}`;
          }
        } finally {
          if (ownerDashboardRequest === requestController) {
            setLoadingState(false);
          }
        }
      };

      yearSelect.addEventListener('change', syncOwnerDashboard);
    })();
  </script>
</body>
</html>
