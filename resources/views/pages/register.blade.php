<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Register') }} - Maharani Mobil</title>
    @include('components.ui-system-head')
    <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="min-h-screen overflow-x-hidden bg-[#edf2fb] font-body text-slate-900">
    <div class="relative min-h-screen overflow-hidden bg-[linear-gradient(180deg,_#eef4ff_0%,_#f8fbff_44%,_#eef3fb_100%)]">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.95),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(141,181,255,0.18),_transparent_28%),radial-gradient(circle_at_bottom_left,_rgba(255,190,120,0.14),_transparent_24%),radial-gradient(circle_at_bottom_right,_rgba(196,214,255,0.2),_transparent_26%)]"></div>
        <div class="pointer-events-none absolute left-[-8%] top-[12%] h-72 w-72 rounded-full bg-[rgba(167,199,255,0.22)] blur-3xl"></div>
        <div class="pointer-events-none absolute right-[-6%] top-[18%] h-80 w-80 rounded-full bg-[rgba(255,210,152,0.16)] blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[-8%] left-[18%] h-80 w-80 rounded-full bg-[rgba(186,221,255,0.2)] blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-[8%] right-[12%] h-64 w-64 rounded-full bg-white/55 blur-3xl"></div>
        <a href="{{ route('landing') }}" class="absolute left-6 top-6 z-10 inline-flex items-center text-primary transition hover:text-[#f5a623] sm:left-10 sm:top-8">
            <span class="font-headline text-[1.9rem] font-extrabold tracking-tight">MaharaniMobil</span>
        </a>

        <main class="flex min-h-screen items-center justify-center px-5 py-16 sm:px-8">
            <div class="relative w-full max-w-[560px] overflow-hidden rounded-[2.2rem] border border-white/70 bg-[linear-gradient(135deg,rgba(255,255,255,0.62),rgba(255,255,255,0.38))] p-6 shadow-[0_28px_90px_rgba(148,163,184,0.24)] backdrop-blur-[28px] sm:p-8">
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.32),transparent_42%,rgba(167,199,255,0.1)_100%)]"></div>
                <div class="pointer-events-none absolute inset-x-6 top-0 h-px bg-white/80"></div>
                <div class="mb-7">
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#f5a623]">Registrasi Customer</p>
                    <h1 class="mt-3 font-headline text-[2.35rem] font-extrabold leading-tight text-primary">Buat Akun Baru</h1>
                </div>

                @if (session('success'))
                    <div class="mb-4 rounded-[1.25rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-[1.25rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}" class="relative space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Name') }}</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                class="w-full rounded-[1.2rem] border border-white/70 bg-[rgba(255,255,255,0.52)] px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] focus:border-[#0b1a40] focus:ring-0"
                                placeholder="Nama lengkap"
                            >
                            @error('name')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                class="w-full rounded-[1.2rem] border border-white/70 bg-[rgba(255,255,255,0.52)] px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] focus:border-[#0b1a40] focus:ring-0"
                                placeholder="nama@email.com"
                            >
                            @error('email')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Telepon</label>
                            <input
                                id="phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone') }}"
                                required
                                class="w-full rounded-[1.2rem] border border-white/70 bg-[rgba(255,255,255,0.52)] px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] focus:border-[#0b1a40] focus:ring-0"
                                placeholder="08xxxxxxxxxx"
                            >
                            @error('phone')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full rounded-[1.2rem] border border-white/70 bg-[rgba(255,255,255,0.52)] px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] focus:border-[#0b1a40] focus:ring-0"
                                placeholder="Minimal 8 karakter"
                            >
                            @error('password')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Password Confirmation') }}</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-[1.2rem] border border-white/70 bg-[rgba(255,255,255,0.52)] px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] focus:border-[#0b1a40] focus:ring-0"
                                placeholder="Ulangi password"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-[1.25rem] bg-[#08132e] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_40px_rgba(8,19,46,0.18)] transition hover:-translate-y-[1px] hover:brightness-110"
                    >
                        Buat Akun
                    </button>
                </form>

                <div class="mt-6 rounded-[1.25rem] border border-white/70 bg-[rgba(255,255,255,0.4)] px-4 py-4 text-sm text-slate-600 shadow-[inset_0_1px_0_rgba(255,255,255,0.68)]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-bold text-primary transition hover:text-[#f5a623]">Masuk di sini</a>
                </div>
            </div>
        </main>
    </div>

    @include('components.ui-system-footer')
</body>
</html>
