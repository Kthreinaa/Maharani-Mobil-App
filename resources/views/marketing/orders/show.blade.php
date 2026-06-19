@extends('layouts.marketing')

@php
  use App\Support\CurrencyFormatter;

  $title = 'Detail Pesanan';
  $pageTitle = 'Detail Pesanan';
  $handledRole = $order->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Dikelola';
  $photoPaths = collect($order->car?->photos ?? [])
    ->filter(fn ($path) => filled($path))
    ->map(fn ($path) => asset('storage/' . ltrim((string) $path, '/')))
    ->values();
  $primaryPhoto = $photoPaths->first();
  $paidAmount = (float) ($order->payment?->amount ?? 0);
  $remainingAmount = max((float) $order->total - $paidAmount, 0);
  $roleChipClasses = $order->handled_role === 'supervisor'
    ? 'border-sky-200 bg-sky-50/90 text-sky-700'
    : 'border-slate-200 bg-slate-50/90 text-slate-600';
@endphp

@section('content')
  <div class="grid items-start gap-6 xl:grid-cols-[1fr_0.82fr]">
    <section class="space-y-6">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">{{ $order->order_reference }}</p>
        <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
          <div class="min-w-0">
            <h2 class="font-headline text-2xl font-extrabold text-slate-900">{{ $order->car?->merk }} {{ $order->car?->tipe }} {{ $order->car?->tahun }}</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Detail ini membantu marketing membaca progres pesanan customer tanpa melakukan perubahan data unit atau transaksi.</p>
          </div>
          <span class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-600 shadow-sm">
            {{ strtoupper($order->status) }}
          </span>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.02fr_0.98fr]">
          @if ($primaryPhoto)
            <div class="rounded-[1.6rem] border border-slate-200 bg-white/80 p-4 shadow-sm">
              <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($photoPaths as $photoIndex => $photoPath)
                  <article class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100 {{ $loop->last && $photoPaths->count() % 2 === 1 ? 'sm:col-span-2' : '' }}">
                    <div class="aspect-[4/3] p-2">
                      <img class="h-full w-full rounded-[0.9rem] object-contain object-center" src="{{ $photoPath }}" alt="Foto unit {{ $photoIndex + 1 }}">
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-white/90 px-3 py-3">
                      <div>
                        <p class="text-sm font-semibold text-slate-800">Foto {{ $photoIndex + 1 }}</p>
                        <p class="text-xs text-slate-500">Tampilan unit tersimpan</p>
                      </div>
                      <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-500">Read Only</span>
                    </div>
                  </article>
                @endforeach
              </div>
              <div class="mt-4 flex items-center justify-between gap-3 rounded-[1.1rem] border border-slate-200 bg-slate-50/80 px-4 py-3">
                <div>
                  <p class="text-sm font-semibold text-slate-800">Galeri unit tersedia</p>
                  <p class="text-xs text-slate-500">{{ $photoPaths->count() }} foto tersimpan pada data mobil ini.</p>
                </div>
                <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">Pantau Saja</span>
              </div>
            </div>
          @else
            <div class="rounded-[1.6rem] border border-rose-200 bg-rose-50/90 p-5 shadow-sm">
              <div class="flex h-full flex-col justify-between gap-5">
                <div class="flex items-start gap-3">
                  <span class="material-symbols-outlined rounded-full bg-rose-100 p-2 text-rose-600">warning</span>
                  <div>
                    <p class="text-base font-bold text-rose-800">Foto unit belum tersedia</p>
                    <p class="mt-2 text-sm leading-6 text-rose-700">Foto unit belum tersedia pada data showroom. Marketing dapat meneruskan informasi ini ke supervisor.</p>
                  </div>
                </div>
                <span class="inline-flex items-center justify-center self-start rounded-full border border-rose-200 bg-white px-5 py-3 text-sm font-semibold text-rose-700 shadow-sm">Perlu Tindak Lanjut Supervisor</span>
              </div>
            </div>
          @endif

          <div class="grid gap-4 content-start sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-[1.35rem] border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(125,211,252,0.14)]">
              <p class="text-slate-500">Customer</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->user?->name ?? '-' }}</p>
            </div>

            <div class="rounded-[1.35rem] border border-indigo-100 bg-[linear-gradient(135deg,rgba(238,242,255,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(129,140,248,0.12)]">
              <p class="text-slate-500">Alur Customer</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->customer_journey_title }}</p>
              <p class="mt-1 text-slate-500">{{ $order->customer_journey_detail }}</p>
              <p class="mt-2 text-xs font-semibold text-slate-600">Status pembelian: {{ $order->customer_purchase_status_label }}</p>
            </div>

            <div class="rounded-[1.35rem] border border-amber-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(251,191,36,0.12)]">
              <p class="text-slate-500">Dikelola Oleh</p>
              <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $roleChipClasses }}">{{ $handledRole }}</span>
                @if ($order->handledBy?->name)
                  <span class="text-sm font-semibold text-slate-700">{{ $order->handledBy->name }}</span>
                @endif
              </div>
              @unless ($order->handledBy?->name)
                <p class="mt-2 text-slate-500">Pesanan ini belum dikelola oleh supervisor.</p>
              @endunless
            </div>

            <div class="rounded-[1.35rem] border border-emerald-100 bg-[linear-gradient(135deg,rgba(236,253,245,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(52,211,153,0.12)]">
              <p class="text-slate-500">Total</p>
              <p class="mt-1 font-bold text-slate-900">{{ CurrencyFormatter::rupiah($order->total) }}</p>
              <p class="mt-1 text-xs font-semibold {{ $remainingAmount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                {{ $remainingAmount > 0 ? 'Sisa pelunasan: ' . CurrencyFormatter::rupiah($remainingAmount) : 'Pembayaran sudah lunas' }}
              </p>
            </div>

            <div class="rounded-[1.35rem] border border-cyan-100 bg-[linear-gradient(135deg,rgba(236,254,255,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(103,232,249,0.12)]">
              <p class="text-slate-500">Metode Bayar</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->payment?->internal_method_label ?? $order->internal_payment_method_label }}</p>
              <p class="mt-1 text-slate-500">Transaksi diproses oleh Supervisor</p>
              @if ($order->payment?->proof_file)
                <a class="mt-3 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700" href="{{ asset('storage/' . $order->payment->proof_file) }}" target="_blank" rel="noreferrer">
                  Lihat Arsip Lampiran
                  <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                </a>
              @elseif ($order->payment)
                <p class="mt-2 text-xs font-semibold text-sky-700">Pembayaran divalidasi oleh supervisor.</p>
              @endif
            </div>
          </div>
        </div>
      </article>
    </section>

    <section class="space-y-6">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <h3 class="font-headline text-xl font-extrabold text-slate-900">Ringkasan Pantauan</h3>
        <div class="mt-4 space-y-3">
          <div class="rounded-[1.1rem] border border-slate-200 bg-white/80 px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Status Order</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ strtoupper($order->status) }}</p>
          </div>
          <div class="rounded-[1.1rem] border border-slate-200 bg-white/80 px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Pembayaran Masuk</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ CurrencyFormatter::rupiah($paidAmount) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $remainingAmount > 0 ? 'Sisa pelunasan masih ada.' : 'Pembayaran sudah lunas.' }}</p>
          </div>
          <div class="rounded-[1.1rem] border border-slate-200 bg-white/80 px-4 py-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">PIC Internal</p>
            <p class="mt-2 text-sm font-bold text-slate-900">{{ $order->handledBy?->name ?? 'Belum ada supervisor yang menangani' }}</p>
          </div>
        </div>
      </article>
    </section>
  </div>
@endsection
