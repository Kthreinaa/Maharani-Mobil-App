<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login') }} - Maharani Mobil</title>
    @include('components.ui-system-head')
    <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="min-h-screen overflow-x-hidden bg-[#edf2fb] font-body text-slate-900">
    <div class="relative isolate min-h-screen overflow-hidden">
        <main class="relative z-10 flex min-h-screen items-center justify-center px-0 py-0">
            <div class="relative grid min-h-screen w-full overflow-hidden rounded-none bg-white shadow-[0_32px_120px_rgba(15,23,42,0.12)] lg:grid-cols-[1.05fr_0.95fr]">
                <section class="relative hidden overflow-hidden bg-[#08132e] text-white lg:flex">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.16),_transparent_28%),linear-gradient(145deg,_rgba(255,255,255,0.05),_rgba(255,255,255,0.01))]"></div>
                    <div class="absolute right-12 top-12 h-32 w-32 rounded-full border border-white/35 bg-white/5 backdrop-blur-xl"></div>
                    <div class="absolute bottom-16 left-14 h-44 w-44 rounded-full border border-white/10 bg-[#f5a623]/10 blur-sm"></div>

                    <div class="relative flex w-full flex-col justify-center px-12 py-14 xl:px-16 xl:py-16">
                        <div>
                            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-white transition hover:text-white/90">
                                <span class="font-headline text-[2.15rem] font-extrabold tracking-tight">MaharaniMobil</span>
                            </a>
                            <div class="mt-16 max-w-[560px]">
                                <p class="text-[11px] font-bold uppercase tracking-[0.34em] text-[#f5a623]">Customer Access</p>
                                <h1 class="mt-6 font-headline text-[3.7rem] font-extrabold leading-[1.03] tracking-tight">
                                    Masuk dengan pengalaman yang lebih mudah dan cepat.
                                </h1>
                                <p class="mt-7 max-w-[500px] text-[1.02rem] leading-8 text-slate-300">
                                    Akses katalog, favorit, pesanan, dan jadwal test drive Anda dalam satu sistem yang aman.
                                    
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="relative flex items-center justify-center overflow-hidden px-5 py-10 sm:px-8 lg:px-10 xl:px-16">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(96,165,250,0.22),_transparent_34%),radial-gradient(circle_at_bottom_left,_rgba(245,166,35,0.18),_transparent_30%),linear-gradient(180deg,_#edf2fb_0%,_#f7f9ff_46%,_#eef3fb_100%)]"></div>
                    <div class="pointer-events-none absolute right-0 top-0 h-80 w-80 rounded-full bg-[#7aa8ff]/20 blur-3xl"></div>
                    <div class="pointer-events-none absolute bottom-0 left-0 h-72 w-72 rounded-full bg-[#f5a623]/16 blur-3xl"></div>

                    <div class="relative z-10 w-full max-w-[540px]">
                        <div class="lg:hidden">
                            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-primary transition hover:text-[#f5a623]">
                                <span class="font-headline text-[2rem] font-extrabold tracking-tight">MaharaniMobil</span>
                            </a>
                        </div>

                        <div class="mt-6 rounded-[2.2rem] border border-white/75 bg-white/70 p-6 shadow-[0_22px_80px_rgba(15,23,42,0.12)] backdrop-blur-2xl sm:p-8">
                            <div class="mb-7">
                                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#f5a623]">Login Akun</p>
                                <h2 class="mt-3 font-headline text-[2.55rem] font-extrabold leading-tight text-primary">Selamat datang kembali</h2>
                                <p class="mt-3 text-sm leading-7 text-slate-500"></p>
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

                            <a
                                href="{{ route('auth.google') }}"
                                class="inline-flex w-full items-center justify-center gap-3 rounded-[1.3rem] border border-slate-200/90 bg-white/90 px-4 py-3.5 text-sm font-semibold text-slate-700 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition hover:-translate-y-[1px] hover:bg-white"
                            >
                                <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 48 48">
                                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.659 29.215 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                    <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 13 24 13c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                                    <path fill="#4CAF50" d="M24 44c5.156 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.143 35.091 26.715 36 24 36c-5.196 0-9.623-3.328-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.793 2.237-2.231 4.166-4.084 5.571.001-.001 6.19 5.238 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                                </svg>
                                Lanjutkan dengan Google
                            </a>
                            <p class="mt-2 text-center text-xs text-slate-500"></p>

                            <div class="my-6 flex items-center gap-3">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <span class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">atau</span>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>

                            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        required
                                        class="w-full rounded-[1.2rem] border border-slate-200 bg-[#f1f5fd]/90 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0"
                                        placeholder="nama@email.com"
                                    >
                                    @error('email')
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
                                        class="w-full rounded-[1.2rem] border border-slate-200 bg-[#f1f5fd]/90 px-4 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0b1a40] focus:ring-0"
                                        placeholder="Masukkan password"
                                    >
                                    @error('password')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <label class="flex items-center gap-3 rounded-[1.2rem] bg-white/75 px-4 py-3 text-sm text-slate-600">
                                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-[#0b1a40] focus:ring-[#0b1a40]">
                                    Ingat saya di perangkat ini
                                </label>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-[1.25rem] bg-[#08132e] px-5 py-3.5 text-sm font-bold text-white shadow-[0_18px_40px_rgba(8,19,46,0.18)] transition hover:-translate-y-[1px] hover:brightness-110"
                                >
                                    Masuk ke Akun
                                </button>
                            </form>

                            <div class="mt-6 rounded-[1.25rem] bg-white/70 px-4 py-4 text-sm text-slate-600">
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="font-bold text-primary transition hover:text-[#f5a623]">Daftar sekarang</a>
                            </div>

                            <a href="{{ route('landing') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-primary">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Kembali ke halaman utama
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    @include('components.ui-system-footer')
</body>
</html>
