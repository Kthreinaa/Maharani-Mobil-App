@extends('layouts.marketing')

@php
  $carName = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
  $title = 'Ulasan Customer Unit';
  $pageTitle = 'Daftar Ulasan Customer';
@endphp

@section('content')
  <section class="mb-6 rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.74)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div class="min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#f5a623]">Preview Marketing</p>
        <h2 class="mt-2 font-headline text-[28px] font-extrabold tracking-tight text-slate-900">{{ $carName !== '' ? $carName : 'Unit Pilihan' }}</h2>
        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
          <span>Kode Unit: {{ $car->kode_unit ?: '-' }}</span>
          <span class="hidden text-slate-300 md:inline">|</span>
          <span>Menampilkan daftar ulasan customer untuk unit ini saja.</span>
        </div>
      </div>

      <a
        href="{{ route('cars.show', $car->id) }}"
        class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-white"
      >
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke preview unit
      </a>
    </div>
  </section>

  <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
    <article class="relative overflow-hidden rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-sky-200/55 blur-2xl"></div>
      <div class="relative">
        <p class="text-xs uppercase tracking-widest text-slate-500">Total Review</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $reviewCount }}</p>
      </div>
    </article>
    <article class="relative overflow-hidden rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-emerald-200/55 blur-2xl"></div>
      <div class="relative">
        <p class="text-xs uppercase tracking-widest text-slate-500">Review Dengan Foto</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $withPhotoCount }}</p>
      </div>
    </article>
    <article class="relative overflow-hidden rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="absolute right-0 top-0 h-24 w-24 rounded-full bg-amber-200/55 blur-2xl"></div>
      <div class="relative">
        <p class="text-xs uppercase tracking-widest text-slate-500">Rata-rata Rating</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($averageRating, 1) }}/5</p>
      </div>
    </article>
  </div>

  <div class="space-y-4">
    @forelse ($reviews as $review)
      @php
        $photos = collect($review->review_photos ?? []);
        $statusLabel = $review->status === 'approved' ? 'Tayang' : ucfirst((string) $review->status);
        $statusClass = $review->status === 'approved'
          ? 'bg-emerald-50 text-emerald-700'
          : 'bg-amber-50 text-amber-700';
      @endphp
      <article class="rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-3">
              <h3 class="text-lg font-bold text-slate-900">{{ $review->user?->name ?: 'Customer Maharani' }}</h3>
              <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
              <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ $review->source_type === 'purchase' ? 'Pembelian selesai' : 'Test drive selesai' }}
              </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500">
              <span>Rating: {{ number_format((float) $review->rating, 1) }}/5</span>
              <span>Dikirim: {{ optional($review->created_at)->format('d M Y H:i') }}</span>
            </div>

            <p class="mt-4 text-sm leading-7 text-slate-700">{{ $review->review_text }}</p>

            @if ($photos->isNotEmpty())
              <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
                @foreach ($photos as $photo)
                  <a class="overflow-hidden rounded-[1rem] border border-slate-200 bg-slate-50" href="{{ asset('storage/' . $photo) }}" target="_blank" rel="noopener noreferrer">
                    <img alt="Foto review {{ $review->user?->name }}" class="h-28 w-full rounded-[1rem] object-cover" src="{{ asset('storage/' . $photo) }}"/>
                  </a>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </article>
    @empty
      <div class="rounded-[1.8rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-8 text-sm text-slate-500 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        Belum ada review customer untuk unit ini.
      </div>
    @endforelse
  </div>

  <div class="mt-4">{{ $reviews->links() }}</div>
@endsection
