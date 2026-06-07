@extends('layouts.supervisor')

@php
  $title = 'Detail Pembayaran';
  $pageTitle = 'Detail Pembayaran';
@endphp

@section('content')
  @php
    $order = $payment->order;
    $paidAmount = (float) ($payment->amount ?? 0);
    $orderTotal = (float) ($order?->total ?? 0);
    $remainingAmount = max($orderTotal - $paidAmount, 0);
    $documentsReady = $order?->areTransactionDocumentsReady() ?? false;
  @endphp
  <div class="grid gap-6 xl:grid-cols-[1fr_0.7fr]">
    <section class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Payment #{{ $payment->id }}</p>
      <h2 class="mt-2 font-headline text-2xl font-extrabold text-slate-900">{{ $payment->order?->car?->merk }} {{ $payment->order?->car?->tipe }}</h2>
      <div class="mt-5 grid gap-4 text-sm md:grid-cols-2">
        <div class="rounded-[1.2rem] border border-slate-200 bg-white/70 p-4">
          <p class="text-slate-500">Customer</p>
          <p class="mt-1 font-bold text-slate-900">{{ $payment->order?->user?->name ?? '-' }}</p>
        </div>
        <div class="rounded-[1.2rem] border border-slate-200 bg-white/70 p-4">
          <p class="text-slate-500">ROLE</p>
          <span class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] {{ $payment->role_badge_classes }}">{{ $payment->role_label }}</span>
          <p class="mt-1 text-slate-500">{{ $payment->handledBy?->name ?? 'Belum ada PIC internal' }}</p>
        </div>
        <div class="rounded-[1.2rem] border border-slate-200 bg-white/70 p-4">
          <p class="text-slate-500">Nominal</p>
          <p class="mt-1 font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($payment->amount) }}</p>
        </div>
        <div class="rounded-[1.2rem] border border-slate-200 bg-white/70 p-4">
          <p class="text-slate-500">Metode & Status</p>
          <p class="mt-1 font-bold text-slate-900">{{ $payment->internal_method_label }}</p>
          <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $payment->status_badge_classes }}">{{ $order?->is_credit_purchase && $payment->status === 'verified' && $paidAmount < $orderTotal ? 'DP Kredit' : $payment->status }}</span>
        </div>
      </div>

      <div class="mt-5 rounded-[1.35rem] border border-amber-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(251,191,36,0.10)]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Progress Pembayaran</p>
        <div class="mt-3 grid gap-3 md:grid-cols-3">
          <div class="rounded-[1rem] border border-white/70 bg-white/80 p-3">
            <p class="text-xs text-slate-500">Total Harga Mobil</p>
            <p class="mt-1 text-sm font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($orderTotal) }}</p>
          </div>
          <div class="rounded-[1rem] border border-white/70 bg-white/80 p-3">
            <p class="text-xs text-slate-500">{{ $order?->is_credit_purchase ? 'DP Kredit Tercatat' : 'Pembayaran Tercatat' }}</p>
            <p class="mt-1 text-sm font-bold text-slate-900">{{ \App\Support\CurrencyFormatter::rupiah($paidAmount) }}</p>
          </div>
          <div class="rounded-[1rem] border border-white/70 bg-white/80 p-3">
            <p class="text-xs text-slate-500">Sisa Pelunasan</p>
            <p class="mt-1 text-sm font-bold {{ $remainingAmount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">{{ \App\Support\CurrencyFormatter::rupiah($remainingAmount) }}</p>
          </div>
        </div>
        <p class="mt-3 text-sm font-semibold {{ $documentsReady ? 'text-emerald-700' : 'text-amber-700' }}">
          @if ($documentsReady)
            Pembayaran sudah lunas. Faktur, kwitansi digital, dan berita acara serah terima siap ditampilkan dan dicetak.
          @else
            Dokumen transaksi belum diterbitkan. Faktur, kwitansi, dan berita acara serah terima baru tampil setelah pembayaran lunas sesuai total harga mobil.
          @endif
        </p>
      </div>

      @if($payment->proof_file)
        <a class="mt-5 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700" href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank" rel="noreferrer">
          Lihat Arsip Lampiran
          <span class="material-symbols-outlined text-[18px]">open_in_new</span>
        </a>
      @else
        <div class="mt-5 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
          Pembayaran ini divalidasi langsung oleh supervisor.
        </div>
      @endif
    </section>

    <section class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <h3 class="font-headline text-xl font-extrabold text-slate-900">Verifikasi Supervisor</h3>
      <p class="mt-2 text-sm leading-6 text-slate-600">Supervisor memvalidasi pembayaran berdasarkan transaksi yang tercatat. Dokumen akan terbit otomatis hanya jika nominal pembayaran sudah lunas sesuai total transaksi.</p>
      <div class="mt-6 flex gap-3">
        <form method="POST" action="{{ route('supervisor.payments.verify', $payment) }}">
          @csrf
          @method('PATCH')
          <button class="rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white">Verifikasi</button>
        </form>
        <form method="POST" action="{{ route('supervisor.payments.reject', $payment) }}">
          @csrf
          @method('PATCH')
          <button class="rounded-full bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white">Tolak</button>
        </form>
      </div>

      <div class="mt-8 rounded-[1.35rem] border border-slate-200 bg-white/80 p-4">
        <h4 class="text-base font-bold text-slate-900">Dokumen Transaksi</h4>
        <p class="mt-2 text-sm leading-6 text-slate-500">faktur, kwitansi, dan berita acara serah terima baru dapat dicetak ketika pembayaran lunas.</p>
        <div class="mt-4 grid gap-2">
          @foreach (['invoice' => 'Cetak Faktur', 'receipt' => 'Cetak Kwitansi', 'handover_note' => 'Cetak Berita Acara Serah Terima'] as $docType => $docLabel)
            @php($docReady = in_array($order->document_status[$docType] ?? 'pending', ['ready', 'submitted', 'done'], true))
            <a
              class="inline-flex items-center justify-center rounded-full px-4 py-2.5 text-sm font-bold {{ $docReady ? 'bg-[#08132e] text-white' : 'pointer-events-none bg-slate-100 text-slate-400' }}"
              href="{{ $docReady ? route('documents.orders.download', [$order, $docType]) : '#' }}"
            >
              {{ $docLabel }}
            </a>
          @endforeach
        </div>
        <div class="mt-4 rounded-xl border {{ $documentsReady ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }} px-4 py-3 text-sm">
          @if ($documentsReady)
            Dokumen transaksi sudah selesai dan siap diunduh.
          @else
            Dokumen transaksi belum tersedia. Pastikan pembayaran sudah lunas sesuai total harga mobil agar faktur, kwitansi, dan berita acara serah terima dapat diterbitkan secara otomatis oleh sistem.
          @endif
        </div>
      </div>
    </section>
  </div>
@endsection
