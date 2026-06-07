@extends('layouts.marketing')

@php
  $title = 'Detail Customer';
  $pageTitle = 'Detail Customer';
@endphp

@section('content')
  <section class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
    <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Customer Profile</p>
      <h2 class="mt-2 font-headline text-[26px] font-extrabold text-slate-900">{{ $user->name }}</h2>
      <div class="mt-5 space-y-4 text-sm text-slate-600">
        <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
          <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Email</p>
          <p class="mt-2 font-semibold text-slate-900">{{ $user->email }}</p>
        </div>
        <div class="rounded-[1.3rem] border border-slate-200/80 bg-white/80 px-4 py-4">
          <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Nomor Telepon</p>
          <p class="mt-2 font-semibold text-slate-900">{{ $user->phone ?: '-' }}</p>
        </div>
      </div>
    </article>

    <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Aktivitas Customer</p>
      <h2 class="mt-2 font-headline text-[26px] font-extrabold text-slate-900">Riwayat Interaksi</h2>

      <div class="mt-5 space-y-5">
        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Pesanan</h3>
          <ul class="mt-3 space-y-2 text-sm text-slate-600">
            @forelse($user->orders as $order)
              <li class="rounded-[1rem] border border-slate-200/80 bg-white/80 px-4 py-3">
                {{ $order->order_reference }} • {{ $order->car?->merk }} {{ $order->car?->tipe }} • {{ strtoupper($order->status) }}
              </li>
            @empty
              <li class="rounded-[1rem] border border-dashed border-slate-200 px-4 py-3 text-slate-500">Belum ada pesanan.</li>
            @endforelse
          </ul>
        </div>

        <div>
          <h3 class="text-sm font-extrabold text-slate-900">Penawaran</h3>
          <ul class="mt-3 space-y-2 text-sm text-slate-600">
            @forelse($user->offers as $offer)
              <li class="rounded-[1rem] border border-slate-200/80 bg-white/80 px-4 py-3">
                {{ $offer->car?->merk }} {{ $offer->car?->tipe }} • {{ \App\Support\CurrencyFormatter::rupiah($offer->offer_price) }} • {{ strtoupper($offer->status) }}
              </li>
            @empty
              <li class="rounded-[1rem] border border-dashed border-slate-200 px-4 py-3 text-slate-500">Belum ada penawaran.</li>
            @endforelse
          </ul>
        </div>

      </div>
    </article>
  </section>
@endsection
