@extends('layouts.supervisor')

@php
  $title = 'Penawaran';
  $pageTitle = 'Penawaran Masuk';
@endphp

@section('content')
  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Customer</th>
          <th class="px-4 py-3 text-left">Mobil</th>
          <th class="px-4 py-3 text-left">Harga Penawaran</th>
          <th class="px-4 py-3 text-left">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($offers as $offer)
          <tr>
            <td class="px-4 py-3">{{ $offer->user?->name }}</td>
            <td class="px-4 py-3">{{ $offer->car?->merk }} {{ $offer->car?->tipe }}</td>
            <td class="px-4 py-3">Rp {{ number_format($offer->offer_price,0,',','.') }}</td>
            <td class="px-4 py-3">{{ $offer->status }}</td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="4">Belum ada penawaran.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $offers->links() }}</div>
@endsection
