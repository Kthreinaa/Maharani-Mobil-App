@extends('layouts.supervisor')

@php
  $title = 'Detail Customer';
  $pageTitle = 'Detail Customer';
@endphp

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-bold mb-4">{{ $user->name }}</h2>
    <p class="text-sm text-slate-600">{{ $user->email }} • {{ $user->phone ?? '-' }}</p>

    <h3 class="text-md font-bold mt-6 mb-2">Histori Pesanan</h3>
    <ul class="text-sm space-y-1">
      @forelse($user->orders as $order)
        <li>{{ $order->order_reference }} • {{ $order->car?->merk }} {{ $order->car?->tipe }} • {{ $order->status }}</li>
      @empty
        <li class="text-slate-500">Belum ada pesanan.</li>
      @endforelse
    </ul>

    <h3 class="text-md font-bold mt-6 mb-2">Histori Test Drive</h3>
    <ul class="text-sm space-y-1">
      @forelse($user->testDrives as $td)
        <li>#{{ $td->id }} • {{ $td->car?->merk }} {{ $td->car?->tipe }} • {{ $td->status }}</li>
      @empty
        <li class="text-slate-500">Belum ada test drive.</li>
      @endforelse
    </ul>
  </div>
@endsection
