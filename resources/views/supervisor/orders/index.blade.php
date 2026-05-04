@extends('layouts.supervisor')

@php
  $title = 'Manajemen Pesanan';
  $pageTitle = 'Manajemen Pesanan';
@endphp

@section('content')
  <form class="flex gap-2 mb-4" method="GET">
    <select class="rounded-lg border-slate-200" name="status">
      <option value="">Semua Status</option>
      @foreach(['pending','confirmed','paid','completed','cancelled'] as $status)
        <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
      @endforeach
    </select>
    <button class="rounded-lg bg-slate-900 text-white px-4">Filter</button>
  </form>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Kode</th>
          <th class="px-4 py-3 text-left">Customer</th>
          <th class="px-4 py-3 text-left">Mobil</th>
          <th class="px-4 py-3 text-left">Total</th>
          <th class="px-4 py-3 text-left">Metode</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($orders as $order)
          <tr>
            <td class="px-4 py-3">#{{ $order->id }}</td>
            <td class="px-4 py-3">{{ $order->user?->name }}</td>
            <td class="px-4 py-3">{{ $order->car?->merk }} {{ $order->car?->tipe }}</td>
            <td class="px-4 py-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ $order->payment_method ?? '-' }}</td>
            <td class="px-4 py-3">{{ $order->status }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('supervisor.orders.show', $order) }}" class="text-slate-700 hover:underline">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="7">Belum ada pesanan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->links() }}</div>
@endsection
