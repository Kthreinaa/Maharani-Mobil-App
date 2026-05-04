@extends('layouts.supervisor')

@php
  $title = 'Activity Log';
  $pageTitle = 'Activity Log';
@endphp

@section('content')
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <ul class="space-y-3 text-sm">
      @forelse($activity as $item)
        <li class="flex items-center justify-between">
          <span>{{ $item['label'] }} • {{ $item['detail'] }}</span>
          <span class="text-xs text-slate-500">{{ $item['time']->diffForHumans() }}</span>
        </li>
      @empty
        <li class="text-slate-500">Belum ada aktivitas.</li>
      @endforelse
    </ul>
  </div>
@endsection
