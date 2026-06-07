<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('Pengaturan Akun') }} | Maharani Mobil</title>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'home', 'utilityActive' => 'settings', 'overlap' => false])

  <main class="flex-grow landing-surface-main py-10 md:py-12">
    <div class="mx-auto w-full max-w-[920px] px-4 md:px-6">
      <div class="mb-8">
        <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">{{ __('Customer Area') }}</p>
        <h1 class="mt-2 font-headline text-[36px] font-extrabold text-primary">{{ __('Pengaturan Akun') }}</h1>
        <p class="mt-3 max-w-[620px] text-sm leading-7 text-on-surface-variant">{{ __('Perbarui identitas akun Anda agar proses pembelian, test drive, dan komunikasi dengan showroom berjalan lebih lancar.') }}</p>
      </div>

      @if (session('success'))
        <div class="mb-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('customer.settings.update') }}" class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="name">{{ __('Nama Lengkap') }}</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
            @error('name')
              <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="phone">{{ __('Nomor Telepon') }}</label>
            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
            @error('phone')
              <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
            @error('email')
              <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password">{{ __('Password Baru') }}</label>
            <input id="password" name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
            @error('password')
              <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password_confirmation">{{ __('Konfirmasi Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-6 py-3 text-[13px] font-bold text-white transition hover:brightness-110">
            <span class="material-symbols-outlined text-[18px]">save</span>
            {{ __('Simpan Perubahan') }}
          </button>
          <a href="{{ route('customer.home') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-6 py-3 text-[13px] font-semibold text-primary transition hover:bg-slate-50">
            {{ __('Kembali ke Home') }}
          </a>
        </div>
      </form>
    </div>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin bantuan terkait pengaturan akun saya.'])
  @include('components.ui-system-footer')
</body>
</html>
