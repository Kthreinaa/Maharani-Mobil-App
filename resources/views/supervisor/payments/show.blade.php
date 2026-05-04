@extends('layouts.supervisor')

@php
  $title = 'Detail Pembayaran';
  $pageTitle = 'Detail Pembayaran';
@endphp

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-bold mb-4">Payment #{{ $payment->id }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <p><strong>Customer:</strong> {{ $payment->order?->user?->name }}</p>
        <p><strong>Mobil:</strong> {{ $payment->order?->car?->merk }} {{ $payment->order?->car?->tipe }}</p>
      </div>
      <div>
        <p><strong>Nominal:</strong> Rp {{ number_format($payment->amount,0,',','.') }}</p>
        <p><strong>Metode:</strong> {{ $payment->method }}</p>
        <p><strong>Status:</strong> {{ $payment->status }}</p>
      </div>
    </div>

    <div class="mt-6 flex gap-3">
      <form method="POST" action="{{ route('supervisor.payments.verify', $payment) }}">
        @csrf
        @method('PATCH')
        <button class="rounded-lg bg-emerald-600 text-white px-4 py-2">Verifikasi</button>
      </form>
      <form method="POST" action="{{ route('supervisor.payments.reject', $payment) }}">
        @csrf
        @method('PATCH')
        <button class="rounded-lg bg-red-600 text-white px-4 py-2">Tolak</button>
      </form>
    </div>
  </div>
@endsection
