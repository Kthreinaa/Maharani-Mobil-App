@extends('layouts.supervisor')

@php
  $title = 'Detail Test Drive';
  $pageTitle = 'Detail Test Drive';
@endphp

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-bold mb-4">Booking #{{ $testDrive->id }}</h2>
    <p class="text-sm">Customer: {{ $testDrive->user?->name }}</p>
    <p class="text-sm">Mobil: {{ $testDrive->car?->merk }} {{ $testDrive->car?->tipe }}</p>
    <p class="text-sm">Tanggal: {{ $testDrive->booking_date }} • {{ $testDrive->booking_time }}</p>
    <p class="text-sm">Status: {{ $testDrive->status }}</p>

    <div class="mt-6 flex gap-3">
      <form method="POST" action="{{ route('supervisor.testdrives.approve', $testDrive) }}">
        @csrf
        @method('PATCH')
        <button class="rounded-lg bg-emerald-600 text-white px-4 py-2">Approve</button>
      </form>
      <form method="POST" action="{{ route('supervisor.testdrives.reject', $testDrive) }}">
        @csrf
        @method('PATCH')
        <button class="rounded-lg bg-red-600 text-white px-4 py-2">Reject</button>
      </form>
    </div>
  </div>
@endsection
