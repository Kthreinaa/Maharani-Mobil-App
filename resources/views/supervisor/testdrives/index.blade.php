@extends('layouts.supervisor')

@php
  $title = 'Manajemen Test Drive';
  $pageTitle = 'Test Drive Management';
@endphp

@section('content')
  <form class="flex gap-2 mb-4" method="GET">
    <select class="rounded-lg border-slate-200" name="status">
      <option value="">Semua Status</option>
      @foreach(['pending','approved','rejected','completed'] as $status)
        <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
      @endforeach
    </select>
    <button class="rounded-lg bg-slate-900 text-white px-4">Filter</button>
  </form>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Customer</th>
          <th class="px-4 py-3 text-left">Mobil</th>
          <th class="px-4 py-3 text-left">Tanggal</th>
          <th class="px-4 py-3 text-left">Jam</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($testDrives as $td)
          <tr>
            <td class="px-4 py-3">{{ $td->user?->name }}</td>
            <td class="px-4 py-3">{{ $td->car?->merk }} {{ $td->car?->tipe }}</td>
            <td class="px-4 py-3">{{ $td->booking_date }}</td>
            <td class="px-4 py-3">{{ $td->booking_time }}</td>
            <td class="px-4 py-3">{{ $td->status }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('supervisor.testdrives.show', $td) }}" class="text-slate-700 hover:underline">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="6">Belum ada booking.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $testDrives->links() }}</div>
@endsection
