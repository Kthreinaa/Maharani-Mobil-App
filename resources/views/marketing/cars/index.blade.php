@extends('layouts.marketing')

@php
  $title = 'Pantau Unit Mobil';
  $pageTitle = 'Pantau Unit Mobil';
  $sourceOptions = [
    'all' => 'Semua Input',
    'marketing' => 'Marketing',
    'supervisor' => 'Supervisor',
    'unknown' => 'Belum Tercatat',
  ];

  $statusOptions = [
    'all' => 'Semua Status',
    'available' => 'Available',
    'reserved' => 'Reserved',
    'sold' => 'Sold',
  ];

  $datasetOptions = [
    'all' => 'Semua Data',
    'archive' => 'Arsip Excel 2021-2025',
    'operational' => 'Data Operasional 2026',
  ];

  $hasActiveFilters = $search !== '' || $status !== 'all' || $source !== 'all' || $dataset !== 'all';
@endphp

@section('content')
  <div class="mb-6 grid gap-3 md:grid-cols-3">
    <div class="rounded-[1.6rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-4 shadow-[0_24px_70px_rgba(15,23,42,0.08)] backdrop-blur-[18px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Total Unit</p>
      <p class="mt-2 text-[28px] font-extrabold tracking-tight text-slate-900">{{ number_format((int) ($sourceSummary['all'] ?? 0)) }}</p>
      <p class="mt-1 text-xs text-slate-500">Seluruh data mobil yang dapat dipantau marketing.</p>
    </div>
    <div class="rounded-[1.6rem] border border-[#f5a623]/20 bg-[#fff8ec] px-5 py-4 shadow-[0_18px_45px_rgba(245,166,35,0.10)]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-500">Unit Tersedia</p>
      <p class="mt-2 text-[28px] font-extrabold tracking-tight text-slate-900">{{ number_format((int) ($sourceSummary['available'] ?? 0)) }}</p>
      <p class="mt-1 text-xs text-slate-500">Unit yang masih bisa dipasarkan ke customer.</p>
    </div>
    <div class="rounded-[1.6rem] border border-sky-200 bg-sky-50 px-5 py-4 shadow-[0_18px_45px_rgba(14,165,233,0.10)]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-sky-600">Unit Terjual</p>
      <p class="mt-2 text-[28px] font-extrabold tracking-tight text-slate-900">{{ number_format((int) ($sourceSummary['sold'] ?? 0)) }}</p>
      <p class="mt-1 text-xs text-slate-500">Unit yang sudah selesai diproses showroom.</p>
    </div>
  </div>

  <div class="mb-6 flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
    <form id="marketingCarFilterForm" class="flex flex-col gap-3 md:flex-row" method="GET" action="{{ route('marketing.products.index') }}">
      <input id="marketingCarSearch" class="rounded-full border-slate-200 bg-white/80 px-4 py-2.5 text-sm" name="q" placeholder="Cari mobil..." value="{{ $search }}" />
      <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-700" name="status">
        @foreach ($statusOptions as $value => $label)
          <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
        @endforeach
      </select>
      <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-semibold text-slate-700" name="dataset">
        @foreach ($datasetOptions as $value => $label)
          <option value="{{ $value }}" @selected($dataset === $value)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="rounded-full bg-[#08132e] px-5 py-2.5 text-sm font-semibold text-white" type="submit">Filter</button>
      @if ($hasActiveFilters)
        <a class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white/80 px-5 py-2.5 text-sm font-semibold text-slate-600" href="{{ route('marketing.products.index') }}">Reset</a>
      @endif
    </form>

    
  </div>

  <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
    <table class="mm-data-table w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-5 py-4 text-left">Kode</th>
          <th class="w-[22%] px-5 py-4 text-left">Unit</th>
          <th class="w-[16%] px-5 py-4 text-left">Harga</th>
          <th class="px-5 py-4 text-left">KM / Foto</th>
          <th class="px-5 py-4 text-left">Status</th>
          <th class="px-5 py-4 text-left">Sumber Input</th>
          <th class="px-5 py-4 text-left">Update Terakhir</th>
          <th class="px-5 py-4 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200/70">
        @php
          $currentGroup = null;
        @endphp
        @forelse($cars as $car)
          @php
            $sourceRole = $car->createdBy?->role;
            $sourceLabel = match ($sourceRole) {
              'marketing' => 'Marketing',
              'supervisor' => 'Supervisor',
              default => 'Belum Tercatat',
            };
            $sourceClass = match ($sourceRole) {
              'marketing' => 'border-amber-200 bg-amber-50 text-amber-700',
              'supervisor' => 'border-sky-200 bg-sky-50 text-sky-700',
              default => 'border-slate-200 bg-slate-100 text-slate-600',
            };
            $isImportArchive = (int) ($car->is_import_archive ?? 0) === 1;
            $latestTransactionAt = $car->latest_transaction_at ? \Illuminate\Support\Carbon::parse($car->latest_transaction_at) : null;
            $rowGroup = $isImportArchive ? 'archive' : 'operational';
          @endphp
          @if ($dataset === 'all' && $currentGroup !== $rowGroup)
            <tr class="bg-slate-50/90">
              <td class="px-5 py-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-500" colspan="8">
                {{ $rowGroup === 'archive' ? 'Arsip Excel 2021-2025' : 'Data Operasional 2026' }}
              </td>
            </tr>
            @php
              $currentGroup = $rowGroup;
            @endphp
          @endif
          <tr class="hover:bg-white/60">
            <td class="px-5 py-4">
              <p class="font-semibold text-slate-900">{{ $car->kode_unit }}</p>
              <p class="mt-1 text-xs text-slate-500">ID {{ $car->id }}</p>
            </td>
            <td class="px-5 py-4">
              <p class="font-bold text-slate-900">{{ $car->merk }} {{ $car->tipe }}</p>
              <p class="mt-1 text-xs text-slate-500">Tahun {{ $car->tahun }}</p>
            </td>
            <td class="px-5 py-4 font-medium text-slate-700 whitespace-nowrap">{{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}</td>
            <td class="px-5 py-4">
              <p class="font-medium text-slate-700">{{ number_format((int) $car->kilometer, 0, ',', '.') }} KM</p>
              <p class="mt-1 text-xs text-slate-500">{{ is_array($car->photos) ? count($car->photos) : 0 }} foto</p>
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $car->status_badge_class }}">{{ $car->status_display_label }}</span>
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $sourceClass }}">{{ $sourceLabel }}</span>
              <p class="mt-2 text-xs font-semibold {{ $isImportArchive ? 'text-sky-600' : 'text-amber-600' }}">
                {{ $isImportArchive ? 'Arsip Excel 2021-2025' : 'Data Operasional 2026' }}
              </p>
              <p class="mt-2 text-xs text-slate-500">{{ $car->createdBy?->name ?? 'Data lama tanpa user input' }}</p>
            </td>
            <td class="px-5 py-4">
              @if ($isImportArchive && $latestTransactionAt)
                <p class="font-medium text-slate-700">{{ $latestTransactionAt->format('d M Y H:i') }}</p>
                <p class="mt-1 text-xs text-slate-500">Tanggal transaksi arsip</p>
              @else
                <p class="font-medium text-slate-700">{{ optional($car->updated_at)->format('d M Y H:i') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $car->updated_at?->diffForHumans() }}</p>
              @endif
            </td>
            <td class="px-5 py-4 text-center">
              <div class="inline-flex flex-col items-center gap-2">
                <a class="inline-flex min-w-[92px] items-center justify-center gap-1 rounded-full border border-slate-200 bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700" href="{{ route('cars.show', $car->id) }}">
                  <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                  Preview
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr><td class="px-5 py-8 text-slate-500" colspan="8">Belum ada mobil.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $cars->links() }}</div>
@endsection

@push('scripts')
  <script>
    (function () {
      const form = document.getElementById('marketingCarFilterForm');
      const searchInput = document.getElementById('marketingCarSearch');

      if (!form || !searchInput) {
        return;
      }

      const selects = form.querySelectorAll('select');
      let debounceId = null;

      const submitForm = function () {
        form.submit();
      };

      selects.forEach(function (select) {
        select.addEventListener('change', submitForm);
      });

      searchInput.addEventListener('input', function () {
        window.clearTimeout(debounceId);

        if (searchInput.value.trim() === '') {
          submitForm();
          return;
        }

        debounceId = window.setTimeout(submitForm, 400);
      });
    })();
  </script>
@endpush
