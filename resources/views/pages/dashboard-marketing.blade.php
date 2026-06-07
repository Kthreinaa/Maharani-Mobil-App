@extends('layouts.marketing')

@php
  $title = 'Dashboard Marketing';
  $pageTitle = 'Dashboard Marketing';

  $summaryCards = [
    ['label' => 'Unit Aktif', 'value' => (int) ($activeInventory ?? 0), 'note' => 'Unit tersedia yang masih bisa dipasarkan', 'icon' => 'inventory_2', 'tone' => 'from-[#08132e] to-[#12357a]', 'route' => route('marketing.products.index', ['status' => 'available', 'dataset' => 'operational'])],
    ['label' => 'Aktivitas Customer', 'value' => (int) ($customerCount ?? 0), 'note' => 'Customer dengan aktivitas yang terekam di sistem', 'icon' => 'groups', 'tone' => 'from-rose-500 to-pink-500', 'route' => route('marketing.customers.index')],
    ['label' => 'Penawaran Tahun Ini', 'value' => (int) ($offersThisYear ?? 0), 'note' => 'Customer yang sudah masuk ke tahap penawaran', 'icon' => 'sell', 'tone' => 'from-amber-400 to-amber-500', 'route' => route('marketing.offers.index', ['year' => now()->year])],
    ['label' => 'Menunggu Follow-up', 'value' => (int) ($pendingLeadCount ?? 0), 'note' => 'Penawaran yang masih menunggu tindak lanjut supervisor', 'icon' => 'pending_actions', 'tone' => 'from-sky-500 to-cyan-500', 'route' => route('marketing.offers.index', ['status' => 'pending'])],
  ];
@endphp

@section('content')
  <section class="mb-6 overflow-hidden rounded-[2.2rem] border border-white/70 bg-[linear-gradient(135deg,#08132e_0%,#102a63_55%,#f5a623_145%)] p-6 text-white shadow-[0_26px_70px_rgba(8,19,46,0.18)] md:p-8">
    <div class="flex flex-col gap-7">
      <div class="max-w-[980px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f7c35f]">Marketing Workspace</p>
        <h2 class="mt-3 max-w-[820px] font-headline text-[30px] font-extrabold leading-tight md:text-[40px]">
          Pantau Minat Customer dan Jaga Komunikasi Awal Sampai Siap Diproses Supervisor.
        </h2>
        <p class="mt-4 max-w-[760px] text-sm leading-7 text-slate-200 md:text-[15px]">
          Dashboard marketing difokuskan untuk melihat unit yang dipasarkan, memantau customer yang masuk, membaca sinyal minat, dan menjaga follow-up awal sebelum proses pesanan, transaksi, dan operasional dilanjutkan oleh supervisor.
        </p>
      </div>

      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($summaryCards as $card)
          <a href="{{ $card['route'] }}" class="rounded-[1.6rem] border border-white/12 bg-white/10 p-4 backdrop-blur-xl transition hover:-translate-y-[1px] hover:bg-white/12">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-200/90">{{ $card['label'] }}</p>
                <p class="mt-2 text-[24px] font-extrabold leading-none tracking-tight text-white">{{ is_numeric($card['value']) ? number_format((int) $card['value']) : $card['value'] }}</p>
                <p class="mt-3 text-xs leading-5 text-slate-100/85">{{ $card['note'] }}</p>
              </div>
              <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[1.1rem] bg-gradient-to-br {{ $card['tone'] }} text-white shadow-lg">
                <span class="material-symbols-outlined text-[20px]">{{ $card['icon'] }}</span>
              </span>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  <section id="marketing-signals-section" class="mt-6 grid grid-cols-1 items-stretch gap-6 xl:grid-cols-4 scroll-mt-28">
    <article class="flex h-full min-h-[560px] flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] xl:col-span-2">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Marketing Signals</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Tren Minat Customer</h3>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
            Grafik ini menunjukkan berapa banyak customer yang menyimpan unit ke favorit, berapa yang sudah masuk ke tahap penawaran, dan berapa yang sudah ditindaklanjuti supervisor.
          </p>
          <p class="mt-2 text-xs leading-5 text-slate-500">
            Data dibaca langsung dari aktivitas favorit customer, pengajuan penawaran, dan waktu tindak lanjut supervisor yang tersimpan di sistem.
          </p>
          <p id="marketingSignalPeriodLabel" class="mt-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ $signalPeriodLabel }}</p>
        </div>
        <div class="flex flex-wrap items-center justify-end gap-3">
          <form id="marketingSignalFilterForm" class="flex flex-wrap items-center justify-end gap-2" method="GET" action="{{ route('marketing.dashboard') }}">
            <select
              id="marketingSignalMonth"
              name="signal_month"
              class="min-w-[124px] rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-xs font-semibold text-slate-700"
            >
              <option value="0" @selected($selectedSignalMonth === 0)>Semua Bulan</option>
              @foreach (range(1, 12) as $monthNumber)
                <option value="{{ $monthNumber }}" @selected($selectedSignalMonth === $monthNumber)>{{ \Carbon\Carbon::create(null, $monthNumber, 1)->translatedFormat('F') }}</option>
              @endforeach
            </select>
            <select
              id="marketingSignalYear"
              name="signal_year"
              class="min-w-[100px] rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-xs font-semibold text-slate-700"
            >
              @foreach ($availableSignalYears as $year)
                <option value="{{ $year }}" @selected($selectedSignalYear === (int) $year)>{{ $year }}</option>
              @endforeach
            </select>
          </form>
        </div>
      </div>
      <div class="mt-5 h-[320px] flex-1">
        <canvas id="marketingFunnelChart"></canvas>
      </div>
    </article>

    <article class="flex h-full min-h-[560px] flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] xl:col-span-2">
      <div class="flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Action Priority</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Unit Prioritas Follow-up</h3>
        </div>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Top 5</span>
      </div>

      <div class="mt-5 flex flex-1 flex-col justify-between gap-3">
        @forelse(($topOpportunities ?? collect()) as $index => $item)
          @php($car = $item['car'])
          <div class="flex-1 rounded-[1.4rem] border border-slate-200/80 bg-white/70 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">#{{ $index + 1 }}</p>
                <p class="mt-2 font-extrabold text-slate-900 truncate">{{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['insight'] }}</p>
              </div>
              <div class="shrink-0 text-right">
                <span class="inline-flex items-center gap-1 rounded-full bg-[#08132e] px-3 py-1 text-xs font-bold text-white">
                  {{ (int) $item['engagement'] }} sinyal
                </span>
                <p class="mt-3 text-xs font-semibold text-slate-500">{{ (int) $item['favorites'] }} favorite / {{ (int) $item['offers'] }} penawaran / {{ (int) $item['orders'] }} order</p>
              </div>
            </div>
          </div>
        @empty
          <div class="rounded-[1.4rem] border border-dashed border-slate-200 px-4 py-8 text-sm text-slate-500">Belum ada data prioritas unit.</div>
        @endforelse
      </div>
    </article>
  </section>

  <section class="mt-6 grid grid-cols-1 items-stretch gap-6 xl:grid-cols-4">
    <article class="flex h-full min-h-[660px] flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] xl:col-span-2">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Recent Orders</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Pesanan Customer Terbaru</h3>
        </div>
        <a class="inline-flex min-w-[160px] items-center justify-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ route('marketing.orders.index') }}">
          Lihat Semua
          <span class="material-symbols-outlined text-[16px] text-slate-400">arrow_forward</span>
        </a>
      </div>

      <div class="glass-table-shell flex-1 overflow-x-auto rounded-[1.5rem]">
        <table class="glass-table mm-data-table w-full text-sm">
          <thead>
            <tr>
              <th class="px-4 py-3 text-left">Tanggal</th>
              <th class="px-4 py-3 text-left">Customer</th>
              <th class="px-4 py-3 text-left">Mobil</th>
              <th class="px-4 py-3 text-left">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200/60">
            @forelse(($recentOrders ?? collect()) as $order)
              <tr>
                <td class="px-4 py-3">
                  <p class="font-semibold text-slate-800">{{ $order->created_at?->format('d M Y') }}</p>
                  <p class="mt-1 text-xs text-slate-500">{{ $order->created_at?->format('H:i') }}</p>
                </td>
                <td class="px-4 py-3 font-semibold text-slate-800">{{ $order->user?->name ?? '-' }}</td>
                <td class="px-4 py-3">
                  <p class="font-semibold text-slate-800">{{ $order->car?->merk }} {{ $order->car?->tipe }}</p>
                  <p class="mt-1 text-xs text-slate-500">Tahun {{ $order->car?->tahun }}</p>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-slate-700">{{ $order->status }}</span>
                </td>
              </tr>
            @empty
              <tr><td class="px-4 py-6 text-slate-500" colspan="4">Belum ada pesanan terbaru.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>

    <article class="flex h-full min-h-[660px] flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] xl:col-span-2">
      <div class="mb-5 flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Transaksi Terbaru</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Transaksi Terbaru</h3>
        </div>
        <a class="inline-flex min-w-[160px] items-center justify-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ route('marketing.transactions.index') }}">
          Buka Transaksi
          <span class="material-symbols-outlined text-[16px] text-slate-400">arrow_forward</span>
        </a>
      </div>

      <div class="flex flex-1 flex-col justify-between gap-3">
        @forelse(($recentPayments ?? collect()) as $payment)
          <div class="flex-1 rounded-[1.4rem] border border-slate-200/80 bg-white/70 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900">{{ $payment->order?->car?->merk }} {{ $payment->order?->car?->tipe }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $payment->order?->user?->name ?? '-' }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($payment->amount) }}</p>
              </div>
              <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-slate-700">{{ $payment->status }}</span>
            </div>
          </div>
        @empty
          <div class="rounded-[1.4rem] border border-dashed border-slate-200 px-4 py-8 text-sm text-slate-500">Belum ada transaksi terbaru.</div>
        @endforelse
      </div>
    </article>
  </section>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function () {
      const funnelCanvas = document.getElementById('marketingFunnelChart');
      const filterForm = document.getElementById('marketingSignalFilterForm');
      const monthSelect = document.getElementById('marketingSignalMonth');
      const yearSelect = document.getElementById('marketingSignalYear');
      const periodLabel = document.getElementById('marketingSignalPeriodLabel');

      if (!funnelCanvas || !filterForm || !monthSelect || !yearSelect || !periodLabel) {
        return;
      }

      const interestTrend = @json($interestTrend->values());
      let activeRequestController = null;
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
      const chart = new Chart(funnelCanvas, {
        type: 'line',
        data: {
          labels: interestTrend.map(item => item.label),
          datasets: [
            {
              label: 'Disimpan ke Favorit',
              data: interestTrend.map(item => item.favorites),
              borderColor: '#f59e0b',
              backgroundColor: 'rgba(245, 158, 11, 0.14)',
              tension: 0.35,
              fill: true,
            },
            {
              label: 'Calon Pembeli Baru',
              data: interestTrend.map(item => item.leads),
              borderColor: '#0ea5e9',
              backgroundColor: 'rgba(14, 165, 233, 0.10)',
              tension: 0.35,
              fill: true,
            },
            {
              label: 'Sudah Ditindaklanjuti',
              data: interestTrend.map(item => item.follow_ups),
              borderColor: '#08132e',
              backgroundColor: 'rgba(8, 19, 46, 0.08)',
              tension: 0.35,
              fill: true,
            }
          ]
        },
        options: {
          ...smoothLineMotion,
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: {
                color: '#475569',
                usePointStyle: true,
                boxWidth: 10,
              }
            }
          },
          scales: {
            x: {
              ticks: { color: '#64748b' },
              grid: { display: false }
            },
            y: {
              beginAtZero: true,
              ticks: { color: '#64748b', precision: 0 },
              grid: { color: 'rgba(148, 163, 184, 0.18)' }
            }
          }
        }
      });

      const setLoadingState = (isLoading) => {
        monthSelect.disabled = isLoading;
        yearSelect.disabled = isLoading;
        filterForm.classList.toggle('opacity-70', isLoading);
      };

      const updateSignalChart = async () => {
        if (activeRequestController) {
          activeRequestController.abort();
        }

        const requestController = new AbortController();
        activeRequestController = requestController;
        const params = new URLSearchParams({
          signal_month: monthSelect.value,
          signal_year: yearSelect.value,
          panel: 'signals',
        });

        setLoadingState(true);

        try {
          const response = await fetch(`${filterForm.action}?${params.toString()}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            signal: requestController.signal,
          });

          if (!response.ok) {
            throw new Error('Gagal memuat data grafik marketing.');
          }

          const payload = await response.json();
          chart.data.labels = payload.chart.labels;
          chart.data.datasets[0].data = payload.chart.favorites;
          chart.data.datasets[1].data = payload.chart.leads;
          chart.data.datasets[2].data = payload.chart.follow_ups;
          chart.update('active');

          periodLabel.textContent = payload.period_label;

          const nextUrl = new URL(window.location.href);
          nextUrl.searchParams.set('signal_month', monthSelect.value);
          nextUrl.searchParams.set('signal_year', yearSelect.value);
          nextUrl.hash = 'marketing-signals-section';
          window.history.replaceState({}, '', nextUrl);
        } catch (error) {
          if (error.name !== 'AbortError') {
            filterForm.submit();
          }
        } finally {
          if (activeRequestController === requestController) {
            setLoadingState(false);
          }
        }
      };

      monthSelect.addEventListener('change', updateSignalChart);
      yearSelect.addEventListener('change', updateSignalChart);
      filterForm.addEventListener('submit', (event) => event.preventDefault());
    })();
  </script>
@endpush
