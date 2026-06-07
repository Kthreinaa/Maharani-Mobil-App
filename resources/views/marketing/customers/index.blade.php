@extends('layouts.marketing')

@php
  $title = 'Aktivitas Customer';
  $pageTitle = 'Aktivitas Customer';
@endphp

@section('content')
  <div class="mb-4 rounded-[1.4rem] border border-slate-200 bg-white/75 px-5 py-4 text-sm text-slate-500 shadow-sm">
    Halaman ini menampilkan aktivitas customer secara real-time dari sistem, seperti pesanan, penawaran, favorit, dan interaksi terbaru yang sudah tercatat.
  </div>

  <form class="mb-4 flex gap-2" method="GET" action="{{ route('marketing.customers.index') }}">
    <input class="rounded-lg border-slate-200" name="q" placeholder="Cari customer..." value="{{ request('q') }}" />
    <button class="rounded-lg bg-slate-900 px-4 text-white">Cari</button>
  </form>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="mm-data-table w-full text-sm">
      <colgroup>
        <col class="w-[18%]">
        <col class="w-[40%]">
        <col class="w-[16%]">
        <col class="w-[8%]">
        <col class="w-[8%]">
        <col class="w-[8%]">
        <col class="w-[14%]">
        <col class="w-[8%]">
      </colgroup>
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-6 py-3 text-left">No HP</th>
          <th class="px-5 py-3 text-left">Orders</th>
          <th class="px-5 py-3 text-left">Penawaran</th>
          <th class="px-5 py-3 text-left">Favorit</th>
          <th class="px-4 py-3 text-left">Aktivitas Terakhir</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($customers as $customer)
          <tr>
            <td class="px-4 py-3">{{ $customer->name }}</td>
            <td class="px-4 py-3">{{ $customer->email }}</td>
            <td class="px-6 py-3">{{ $customer->phone ?? '-' }}</td>
            <td class="px-5 py-3">{{ $customer->orders_count }}</td>
            <td class="px-5 py-3">{{ $customer->offers_count }}</td>
            <td class="px-5 py-3">{{ $customer->favorites_count }}</td>
            <td class="px-4 py-3">
              @if (($customer->has_recorded_activity ?? 0) && $customer->latest_activity_at)
                {{ \Illuminate\Support\Carbon::parse($customer->latest_activity_at)->format('d M Y H:i') }}
              @else
                -
              @endif
            </td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('marketing.customers.show', $customer) }}" class="text-slate-700 hover:underline">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="8">Belum ada customer.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $customers->links() }}</div>
@endsection
