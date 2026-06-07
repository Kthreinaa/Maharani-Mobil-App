@extends('layouts.marketing')

@php
  $title = 'Analisis Pemasaran';
  $pageTitle = 'Analisis Pemasaran';
@endphp

@section('content')
  <section class="space-y-6">
    <div class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">Marketing Insight</p>
          <h2 class="mt-2 font-headline text-2xl font-extrabold text-slate-900">Arah Strategi Pemasaran</h2>
          <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Halaman ini membantu tim marketing melihat customer yang tertarik, mengecek apakah follow-up sudah berjalan dengan baik, dan mengetahui unit mana yang perlu lebih diprioritaskan untuk dipasarkan</p>
        </div>

        <form id="marketingAnalysisFilterForm" class="flex flex-wrap items-center gap-3" method="GET" action="{{ route('marketing.analysis.index') }}">
          <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500" for="year">Tahun</label>
          <select id="year" name="year" data-auto-submit class="rounded-full border border-slate-200 bg-white/90 px-4 py-2.5 text-sm font-semibold text-slate-700">
            @foreach ($availableYears as $year)
              <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
            @endforeach
          </select>
          <label class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500" for="month">Bulan</label>
          <select id="month" name="month" data-auto-submit class="rounded-full border border-slate-200 bg-white/90 px-4 py-2.5 text-sm font-semibold text-slate-700">
            <option value="0" @selected($selectedMonth === 0)>Semua Bulan</option>
            @foreach (range(1, 12) as $monthNumber)
              <option value="{{ $monthNumber }}" @selected($selectedMonth === $monthNumber)>{{ \Carbon\Carbon::create(null, $monthNumber, 1)->translatedFormat('F') }}</option>
            @endforeach
          </select>
          <button class="rounded-full bg-[#08132e] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)]" type="submit">Tampilkan</button>
        </form>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      @foreach ([
        ['label' => 'Sinyal Minat', 'value' => (int) $summary['sinyal_minat'], 'note' => 'Favorite, penawaran, dan test drive'],
        ['label' => 'Calon Pembeli Belum Ditindaklanjuti', 'value' => (int) $summary['lead_perlu_aksi'], 'note' => 'Customer yang masih menunggu follow-up'],
        ['label' => 'Rasio Tindak Lanjut', 'value' => (int) $summary['rasio_follow_up'] . '%', 'note' => 'Persentase customer tahun ini yang sudah ditangani'],
        ['label' => 'Respon Rata-rata', 'value' => $summary['rata_respon_jam'] !== null ? $summary['rata_respon_jam'] . ' jam' : '-', 'note' => 'Kecepatan tim menindaklanjuti customer yang masuk'],
      ] as $item)
        <article class="rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_18px_50px_rgba(15,23,42,0.06)] backdrop-blur-[18px]">
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $item['label'] }}</p>
          <p class="mt-3 text-[28px] font-extrabold tracking-tight text-slate-900">{{ is_numeric($item['value']) ? number_format((int) $item['value']) : $item['value'] }}</p>
          <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['note'] }}</p>
        </article>
      @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Signal Trend</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Minat Awal vs Tindak Lanjut</h3>
        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ $periodLabel }}</p>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
          `Disimpan ke Favorit` menunjukkan minat awal. `Calon Pembeli Baru` adalah customer yang lanjut ke penawaran atau test drive. `Sudah Ditindaklanjuti` berarti customer itu sudah diproses oleh tim.
        </p>
        <div class="mt-5 h-[330px]">
          <canvas id="marketingAnalysisChart"></canvas>
        </div>
      </article>

      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Action List</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Prioritas yang Bisa Dikerjakan</h3>
        <div class="mt-5 space-y-4">
          @forelse ($actionRecommendations as $item)
            <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
              <div class="flex items-center justify-between gap-4">
                <p class="font-extrabold text-slate-900">{{ $item['title'] }}</p>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $item['metric'] }}</span>
              </div>
              <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['action'] }}</p>
            </div>
          @empty
            <div class="rounded-[1.3rem] border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">Belum ada rekomendasi aksi.</div>
          @endforelse
        </div>
      </article>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Minat per Merk</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Merk yang Paling Kuat Menarik Minat</h3>
        <div class="mt-5 space-y-4">
          @forelse ($brandInterest as $item)
            <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
              <div class="flex items-center justify-between gap-3">
                <p class="font-extrabold text-slate-900">{{ $item['brand'] }}</p>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Skor {{ $item['interest_score'] }}</span>
              </div>
              <p class="mt-2 text-sm text-slate-600">{{ $item['favorites'] }} favorite / {{ $item['offers'] }} penawaran / {{ $item['test_drives'] }} test drive / {{ $item['sales'] }} transaksi</p>
            </div>
          @empty
            <div class="rounded-[1.3rem] border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">Belum ada data minat per merk.</div>
          @endforelse
        </div>
      </article>

      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Follow-up Health</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Kesehatan Tindak Lanjut Customer</h3>
        <div class="mt-5 space-y-4">
          @forelse ($responseHealth['cards'] as $item)
            <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
              <div class="flex items-center justify-between gap-3">
                <p class="font-extrabold text-slate-900">{{ $item['label'] }}</p>
                <span class="rounded-full {{ $item['pending'] > 0 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }} px-3 py-1 text-xs font-bold">{{ $item['response_rate'] }}%</span>
              </div>
              <p class="mt-2 text-sm text-slate-600">{{ $item['handled'] }} ditangani / {{ $item['pending'] }} pending / respon rata-rata {{ $item['avg_response_hours'] !== null ? $item['avg_response_hours'] . ' jam' : '-' }}</p>
            </div>
          @empty
            <div class="rounded-[1.3rem] border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">Belum ada data follow-up.</div>
          @endforelse
        </div>
      </article>
    </div>

    <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="flex items-center justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Opportunity Map</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Unit yang Layak Diprioritaskan</h3>
        </div>
        <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-500">Top 5</span>
      </div>

      <div class="mt-5 grid gap-4 xl:grid-cols-2">
        @forelse ($opportunityCars as $item)
          @php($car = $item['car'])
          <div class="rounded-[1.4rem] border border-slate-200/80 bg-white/80 px-4 py-4">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900">{{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</p>
                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item['action'] }}</p>
              </div>
              <span class="rounded-full bg-[#08132e] px-3 py-1 text-xs font-bold uppercase text-white">{{ $item['segment'] }}</span>
            </div>
            <p class="mt-4 text-xs font-semibold text-slate-500">{{ $item['favorites'] }} favorite / {{ $item['offers'] }} offer / {{ $item['test_drives'] }} test drive / {{ $item['orders'] }} order</p>
          </div>
        @empty
          <div class="rounded-[1.3rem] border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">Belum ada data unit prioritas.</div>
        @endforelse
      </div>
    </article>
  </section>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function () {
      const filterForm = document.getElementById('marketingAnalysisFilterForm');
      const chartCanvas = document.getElementById('marketingAnalysisChart');

      if (filterForm) {
        const buildFilterUrl = () => {
          const params = new URLSearchParams(new FormData(filterForm));
          return `${filterForm.action}?${params.toString()}`;
        };

        filterForm.addEventListener('submit', (event) => {
          event.preventDefault();
          window.location.replace(buildFilterUrl());
        });

        filterForm.querySelectorAll('[data-auto-submit]').forEach((field) => {
          field.addEventListener('change', () => {
            window.location.replace(buildFilterUrl());
          });
        });
      }

      if (!chartCanvas) {
        return;
      }

      const items = @json($signalsTrend->values());

      new Chart(chartCanvas, {
        type: 'bar',
        data: {
          labels: items.map(item => item.label),
          datasets: [
            {
              label: 'Disimpan ke Favorit',
              data: items.map(item => item.favorites),
              backgroundColor: 'rgba(245, 158, 11, 0.78)',
              borderRadius: 12,
            },
            {
              label: 'Calon Pembeli Baru',
              data: items.map(item => item.leads),
              backgroundColor: 'rgba(14, 165, 233, 0.78)',
              borderRadius: 12,
            },
            {
              label: 'Sudah Ditindaklanjuti',
              data: items.map(item => item.follow_ups),
              backgroundColor: 'rgba(8, 19, 46, 0.82)',
              borderRadius: 12,
            }
          ]
        },
        options: {
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
    })();
  </script>
@endpush
