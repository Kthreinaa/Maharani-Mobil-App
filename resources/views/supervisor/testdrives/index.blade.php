@extends('layouts.supervisor')

@php
  $title = 'Manajemen Test Drive';
  $pageTitle = 'Jadwal Test Drive';
@endphp

@section('content')
  <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form class="flex flex-wrap gap-2" method="GET">
      <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700" name="status">
        <option value="all">Semua Status</option>
        @foreach(['pending','approved','rejected','completed','cancelled'] as $item)
          <option value="{{ $item }}" @selected(($status ?? request('status')) === $item)>{{ strtoupper($item) }}</option>
        @endforeach
      </select>
      <button class="rounded-full bg-[#08132e] px-5 py-2 text-sm font-semibold text-white">Filter</button>
    </form>
    <a class="inline-flex items-center justify-center rounded-full bg-[#08132e] px-5 py-2 text-sm font-semibold text-white" href="{{ route('supervisor.testdrives.create') }}">
      Input Test Drive Manual
    </a>
  </div>

  <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
    <table class="mm-data-table w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-5 py-4 text-left">Tanggal</th>
          <th class="px-5 py-4 text-left">Jam</th>
          <th class="px-5 py-4 text-left">Customer</th>
          <th class="px-5 py-4 text-left">Mobil</th>
          <th class="px-5 py-4 text-left">Status & Jalur</th>
          <th class="px-5 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200/60">
        @forelse($testDrives as $item)
          @php
            $roleLabel = $item->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Diproses';
          @endphp
          <tr class="hover:bg-white/70">
            <td class="px-5 py-4">{{ $item->booking_date?->format('d M Y') }}</td>
            <td class="px-5 py-4">{{ $item->booking_time }}</td>
            <td class="px-5 py-4">{{ $item->user?->name ?? '-' }}</td>
            <td class="px-5 py-4">{{ $item->car?->merk }} {{ $item->car?->tipe }} {{ $item->car?->tahun }}</td>
            <td class="px-5 py-4">
              <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-slate-700">{{ $item->status }}</span>
              <p class="mt-2 text-xs text-slate-500">{{ $item->handled_role === 'supervisor' && $item->handledBy?->name ? $roleLabel . ' / ' . $item->handledBy->name : 'Test drive ini belum dikelola supervisor' }}</p>
              <p class="mt-1 text-xs font-semibold text-slate-500">{{ $item->customer_channel_label }} / {{ $item->follow_up_status_label }}</p>
              <p class="mt-1 text-xs text-slate-500">{{ $item->process_outcome_label }}</p>
              @if ($item->lost_reason || $item->next_follow_up_at)
                <p class="mt-1 text-xs text-slate-500">{{ $item->lost_reason ?: 'Follow-up lanjutan' }}{{ $item->next_follow_up_at ? ' / ' . $item->next_follow_up_at->translatedFormat('d M Y H:i') : '' }}</p>
              @endif
            </td>
            <td class="px-5 py-4 text-right">
              <form class="inline-grid min-w-[260px] gap-2 text-left" method="POST" action="{{ route('supervisor.testdrives.updateStatus', $item) }}">
                @csrf
                @method('PATCH')
                <select class="rounded-full border-slate-200 text-xs" name="status">
                  @foreach(['pending','approved','rejected','completed','cancelled'] as $statusItem)
                    <option value="{{ $statusItem }}" @selected($item->status === $statusItem)>{{ strtoupper($statusItem) }}</option>
                  @endforeach
                </select>
                <input class="rounded-full border-slate-200 text-xs" name="lost_reason" value="{{ $item->lost_reason }}" placeholder="Alasan batal / catatan" />
                <input class="rounded-full border-slate-200 text-xs" name="next_follow_up_at" type="datetime-local" value="{{ $item->next_follow_up_at?->format('Y-m-d\\TH:i') }}" />
                <button class="rounded-full bg-[#08132e] px-3 py-1.5 text-xs font-semibold text-white">Update</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="px-5 py-8 text-slate-500" colspan="6">Belum ada jadwal test drive.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $testDrives->links() }}</div>
@endsection
