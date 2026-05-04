@extends('layouts.supervisor')

@php
  $title = 'Detail Pesanan';
  $pageTitle = 'Detail Pesanan';
@endphp

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-bold mb-4">Order #{{ $order->id }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <p><strong>Customer:</strong> {{ $order->user?->name }}</p>
        <p><strong>Email:</strong> {{ $order->user?->email }}</p>
        <p><strong>Mobil:</strong> {{ $order->car?->merk }} {{ $order->car?->tipe }}</p>
      </div>
      <div>
        <p><strong>Total:</strong> Rp {{ number_format($order->total,0,',','.') }}</p>
        <p><strong>Metode:</strong> {{ $order->payment_method ?? '-' }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>
      </div>
    </div>

    <form class="mt-6 flex items-center gap-3" method="POST" action="{{ route('supervisor.orders.updateStatus', $order) }}">
      @csrf
      @method('PATCH')
      <select class="rounded-lg border-slate-200" name="status" required>
        @foreach(['pending','confirmed','paid','completed','cancelled'] as $status)
          <option value="{{ $status }}" @selected($order->status===$status)>{{ $status }}</option>
        @endforeach
      </select>
      <button class="rounded-lg bg-slate-900 text-white px-4 py-2">Update Status</button>
    </form>
  </div>
@endsection
