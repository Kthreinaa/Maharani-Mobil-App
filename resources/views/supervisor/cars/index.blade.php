@extends('layouts.supervisor')

@php
  $title = 'Manajemen Mobil';
  $pageTitle = 'Manajemen Mobil';
@endphp

@section('content')
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <form class="flex gap-2" method="GET">
      <input class="rounded-lg border-slate-200" name="q" placeholder="Cari mobil..." value="{{ request('q') }}" />
      <select class="rounded-lg border-slate-200" name="status">
        <option value="">Semua Status</option>
        <option value="available" @selected(request('status')==='available')>Available</option>
        <option value="reserved" @selected(request('status')==='reserved')>Reserved</option>
        <option value="sold" @selected(request('status')==='sold')>Sold</option>
      </select>
      <button class="rounded-lg bg-slate-900 text-white px-4">Filter</button>
    </form>
    <a href="{{ route('supervisor.cars.create') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2">Tambah Mobil</a>
  </div>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Kode</th>
          <th class="px-4 py-3 text-left">Merk/Tipe</th>
          <th class="px-4 py-3 text-left">Tahun</th>
          <th class="px-4 py-3 text-left">Harga</th>
          <th class="px-4 py-3 text-left">KM</th>
          <th class="px-4 py-3 text-left">Foto</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($cars as $car)
          <tr>
            <td class="px-4 py-3">{{ $car->kode_unit }}</td>
            <td class="px-4 py-3">{{ $car->merk }} {{ $car->tipe }}</td>
            <td class="px-4 py-3">{{ $car->tahun }}</td>
            <td class="px-4 py-3">Rp {{ number_format($car->harga, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ number_format($car->kilometer, 0, ',', '.') }}</td>
            <td class="px-4 py-3">{{ is_array($car->photos) ? count($car->photos) : 0 }} foto</td>
            <td class="px-4 py-3">{{ $car->status }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('supervisor.cars.edit', $car) }}" class="text-slate-700 hover:underline">Edit</a>
              <form class="inline" method="POST" action="{{ route('supervisor.cars.destroy', $car) }}">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline ml-2" onclick="return confirm('Hapus mobil ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="8">Belum ada mobil.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $cars->links() }}</div>
@endsection
