@extends('layouts.supervisor')

@php
  $title = 'Manajemen Pesanan';
  $pageTitle = 'Manajemen Pesanan';
@endphp

@section('content')
  <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form class="flex flex-wrap gap-2" method="GET">
      <input class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm" name="q" placeholder="Cari customer, mobil, atau kode order" value="{{ request('q') }}"/>
      <select class="rounded-full border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700" name="status">
        <option value="all">Semua Status</option>
        @foreach(['pending','confirmed','paid','completed','cancelled'] as $item)
          <option value="{{ $item }}" @selected(($status ?? request('status')) === $item)>{{ strtoupper($item) }}</option>
        @endforeach
      </select>
      <button class="rounded-full bg-[#08132e] px-5 py-2 text-sm font-semibold text-white">Filter</button>
    </form>
  </div>

  <div class="overflow-x-auto rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
    <table class="mm-data-table w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-5 py-4 text-left">Order</th>
          <th class="px-5 py-4 text-left">Tanggal</th>
          <th class="px-5 py-4 text-left">Customer</th>
          <th class="px-5 py-4 text-left">Sumber</th>
          <th class="px-5 py-4 text-left">Mobil</th>
          <th class="px-5 py-4 text-left">Metode Beli</th>
          <th class="px-5 py-4 text-left">Status</th>
          <th class="px-5 py-4 text-left">Dikelola Oleh</th>
          <th class="px-5 py-4 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200/60">
        @forelse($orders as $order)
          @php
            $purchaseMethodLabel = $order->is_credit_purchase ? 'Kredit' : 'Cash';
            $paymentTone = $purchaseMethodLabel === 'Kredit'
              ? 'border-amber-200 bg-amber-50 text-amber-700'
              : 'border-emerald-200 bg-emerald-50 text-emerald-700';
            $roleTone = $order->handled_role === 'supervisor'
              ? 'border-sky-200 bg-sky-50 text-sky-700'
              : 'border-slate-200 bg-slate-50 text-slate-500';
            $roleLabel = $order->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Diproses';
          @endphp
          <tr class="hover:bg-white/70">
            <td class="px-5 py-4 font-bold text-slate-900">{{ $order->order_reference }}</td>
            <td class="px-5 py-4">{{ $order->created_at?->format('d M Y') }}</td>
            <td class="px-5 py-4">{{ $order->user?->name ?? '-' }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{ $order->transaction_channel_label }}</span>
              <p class="mt-2 text-xs text-slate-500">{{ $order->sales_flow_label }}</p>
            </td>
            <td class="px-5 py-4">{{ $order->car?->merk }} {{ $order->car?->tipe }} {{ $order->car?->tahun }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] {{ $paymentTone }}">{{ $purchaseMethodLabel }}</span>
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase text-slate-700">{{ $order->status }}</span>
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] {{ $roleTone }}">{{ $roleLabel }}</span>
              <p class="mt-2 text-xs text-slate-500">{{ $order->handled_role === 'supervisor' && $order->handledBy?->name ? $order->handledBy->name : 'Pesanan ini belum dikelola supervisor' }}</p>
            </td>
            <td class="px-5 py-4 text-right">
              <a class="rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700" href="{{ route('supervisor.orders.show', $order) }}">Kelola</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-5 py-8 text-slate-500" colspan="9">Belum ada pesanan customer.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->links() }}</div>
@endsection
