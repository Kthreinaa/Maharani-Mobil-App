@extends('layouts.marketing')

@php
  $title = 'Pantau Transaksi';
  $pageTitle = 'Pantau Transaksi';
@endphp

@section('content')
  <div class="mb-4 rounded-[1.4rem] border border-slate-200 bg-white/75 px-5 py-4 text-sm text-slate-500 shadow-sm">
    Marketing hanya dapat memantau transaksi dan pembayaran dari halaman ini. Input transaksi showroom, validasi pembayaran, dan perubahan data transaksi dikelola oleh supervisor.
  </div>

  <form class="mb-4 flex flex-wrap gap-2" method="GET" action="{{ route('marketing.transactions.index') }}">
    <input class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm" name="q" placeholder="Cari customer, mobil, atau kode order" value="{{ request('q') }}"/>
    <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700" name="status">
      <option value="all">Semua Status</option>
      @foreach(['pending','verified','rejected'] as $item)
        <option value="{{ $item }}" @selected(request('status') === $item)>{{ strtoupper($item) }}</option>
      @endforeach
    </select>
    <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700" name="method">
      <option value="all">Semua Metode</option>
      <option value="cash" @selected(request('method') === 'cash')>CASH</option>
      <option value="credit" @selected(request('method') === 'credit')>KREDIT</option>
    </select>
    <button class="rounded-full bg-[#08132e] px-5 py-2 text-sm font-semibold text-white">Filter</button>
  </form>

  <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
    <table class="mm-data-table w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-5 py-4 text-left">Tanggal</th>
          <th class="px-5 py-4 text-left">Customer</th>
          <th class="px-5 py-4 text-left">Mobil</th>
          <th class="px-5 py-4 text-left">Nominal</th>
          <th class="px-5 py-4 text-left">Metode</th>
          <th class="px-5 py-4 text-left">Validasi Internal</th>
          <th class="px-5 py-4 text-left">Status</th>
          <th class="px-5 py-4 text-left">ROLE</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200/60">
        @forelse($payments as $payment)
          @php
            $purchaseMethodLabel = $payment->order?->is_credit_purchase ? 'Kredit' : 'Cash';
            $paidWithLabel = match ($payment->method) {
              'cash' => 'Tunai',
              'transfer', 'va', 'credit' => 'Transfer',
              default => ucfirst((string) $payment->method),
            };
          @endphp
          <tr class="hover:bg-white/70">
            <td class="px-5 py-4">{{ $payment->created_at?->format('d M Y') }}</td>
            <td class="px-5 py-4">{{ $payment->order?->user?->name ?? '-' }}</td>
            <td class="px-5 py-4">{{ $payment->order?->car?->merk }} {{ $payment->order?->car?->tipe }}</td>
            <td class="px-5 py-4">{{ \App\Support\CurrencyFormatter::rupiah($payment->amount) }}</td>
            <td class="px-5 py-4">
              <p class="font-semibold text-slate-900">{{ $purchaseMethodLabel }}</p>
              <p class="mt-1 text-xs text-slate-500">Dibayar dengan {{ $paidWithLabel }}</p>
            </td>
            <td class="px-5 py-4">
              @if ($payment->has_proof)
                <a class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-xs font-semibold text-slate-700" href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank" rel="noreferrer">
                  Arsip Lampiran
                  <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                </a>
              @else
                <span class="inline-flex rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700">Dicatat internal</span>
              @endif
            </td>
            <td class="px-5 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $payment->status_badge_classes }}">{{ $payment->status }}</span></td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] {{ $payment->role_badge_classes }}">{{ $payment->role_label }}</span>
              <p class="mt-2 text-xs text-slate-500">{{ $payment->handledBy?->name ?? 'Belum ada PIC internal' }}</p>
            </td>
          </tr>
        @empty
          <tr><td class="px-5 py-8 text-slate-500" colspan="8">Belum ada pembayaran.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $payments->links() }}</div>
@endsection
