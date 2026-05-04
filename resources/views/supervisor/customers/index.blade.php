@extends('layouts.supervisor')

@php
  $title = 'Manajemen Customer';
  $pageTitle = 'Manajemen Customer';
@endphp

@section('content')
  <form class="flex gap-2 mb-4" method="GET">
    <input class="rounded-lg border-slate-200" name="q" placeholder="Cari customer..." value="{{ request('q') }}" />
    <button class="rounded-lg bg-slate-900 text-white px-4">Cari</button>
  </form>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-4 py-3 text-left">No HP</th>
          <th class="px-4 py-3 text-left">Orders</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($customers as $customer)
          <tr>
            <td class="px-4 py-3">{{ $customer->name }}</td>
            <td class="px-4 py-3">{{ $customer->email }}</td>
            <td class="px-4 py-3">{{ $customer->phone ?? '-' }}</td>
            <td class="px-4 py-3">{{ $customer->orders_count }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('supervisor.customers.show', $customer) }}" class="text-slate-700 hover:underline">Detail</a>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="5">Belum ada customer.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $customers->links() }}</div>
@endsection
