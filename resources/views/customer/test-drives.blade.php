<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Test Drive Saya') }} | Maharani Mobil</title>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'test-drives', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[1280px] px-4 md:px-6">
      <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Customer Area') }}</p>
          <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">{{ __('Jadwal Test Drive') }}</h1>
          <p class="mt-3 max-w-[620px] text-sm leading-7 text-on-surface-variant">{{ __('Kelola semua permintaan test drive Anda dan lihat status persetujuan dari tim Maharani Mobil.') }}</p>
        </div>
        <a class="inline-flex items-center gap-2 rounded-full bg-[#f5a623] px-5 py-3 text-[13px] font-bold text-[#111827] transition hover:brightness-105" href="{{ route('test-drive.form') }}">
          <span class="material-symbols-outlined text-[18px]">add_circle</span>
          {{ __('Buat Janji Baru') }}
        </a>
      </div>

      <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        @forelse ($testDrives as $testDrive)
          <article class="rounded-[1.8rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">{{ __('Unit Test Drive') }}</p>
                <h2 class="mt-2 font-headline text-[26px] font-extrabold text-primary">{{ $testDrive->car?->merk }} {{ $testDrive->car?->tipe }} {{ $testDrive->car?->tahun }}</h2>
              </div>
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $testDrive->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($testDrive->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                {{ $testDrive->status }}
              </span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
              <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-widest text-slate-400">{{ __('Tanggal') }}</p>
                <p class="mt-2 font-bold text-primary">{{ optional($testDrive->booking_date)->format('d M Y') }}</p>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-widest text-slate-400">{{ __('Jam') }}</p>
                <p class="mt-2 font-bold text-primary">{{ substr((string) $testDrive->booking_time, 0, 5) }}</p>
              </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
              <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">Booking Test Drive</span>
              <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">{{ $testDrive->customer_channel_label }}</span>
              <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">{{ $testDrive->follow_up_status_label }}</span>
            </div>

            @if ($testDrive->notes)
              <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                <p class="text-[11px] uppercase tracking-widest text-slate-400">{{ __('Catatan') }}</p>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-on-surface-variant">{{ $testDrive->notes }}</p>
              </div>
            @endif

            <div class="mt-5 flex flex-wrap gap-3">
              <a class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-4 py-2.5 text-[13px] font-bold text-white transition hover:brightness-110" href="{{ route('cars.show', $testDrive->car_id) }}">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
                {{ __('Lihat Detail Mobil') }}
              </a>
              <a class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2.5 text-[13px] font-semibold text-primary transition hover:bg-slate-50" href="{{ route('catalog') }}">
                {{ __('Lihat Katalog') }}
              </a>
            </div>
          </article>
        @empty
          <div class="rounded-[1.5rem] border border-slate-200 bg-white p-8 text-sm text-on-surface-variant shadow-sm lg:col-span-2">
            {{ __('Belum ada jadwal test drive. Pilih unit dari katalog atau halaman detail mobil lalu buat janji test drive pertama Anda.') }}
          </div>
        @endforelse
      </div>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin menanyakan jadwal test drive saya.'])
  @include('components.ui-system-footer')
</body>
</html>
