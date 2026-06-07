@extends('layouts.supervisor')

@php
  $title = 'Detail Test Drive';
  $pageTitle = 'Detail Test Drive';
@endphp

@section('content')
  <div class="rounded-xl border border-slate-200 bg-white p-6">
    <h2 class="mb-4 text-lg font-bold">Booking #{{ $testDrive->id }}</h2>
    <p class="text-sm">Customer: {{ $testDrive->user?->name }}</p>
    <p class="text-sm">Mobil: {{ $testDrive->car?->merk }} {{ $testDrive->car?->tipe }}</p>
    <p class="text-sm">Tanggal: {{ $testDrive->booking_date }} - {{ $testDrive->booking_time }}</p>
    <p class="text-sm">Status: {{ $testDrive->status }}</p>

    <div class="mt-6 flex gap-3">
      <form method="POST" action="{{ route('supervisor.testdrives.updateStatus', $testDrive) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="approved">
        <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white">Approve</button>
      </form>
      <form method="POST" action="{{ route('supervisor.testdrives.updateStatus', $testDrive) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="rejected">
        <button class="rounded-lg bg-red-600 px-4 py-2 text-white">Reject</button>
      </form>
    </div>
  </div>
@endsection
