@php
    /**
     * Halaman detail mobil dinamis.
     * Menampilkan galeri foto, ringkasan unit, spesifikasi, serta CTA utama.
     */
    $carName = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
    $pageTitle = ($carName !== '' ? $carName . ' | ' : '') . 'Detail Mobil | Maharani Mobil';

    $photoPaths = collect($car->photos ?? [])
        ->filter(fn ($path) => filled($path))
        ->map(fn ($path) => asset('storage/' . ltrim((string) $path, '/')))
        ->values();

    if ($photoPaths->isEmpty()) {
        $photoPaths = collect([
            'https://images.unsplash.com/photo-1542282088-fe8426682b8f?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?q=80&w=1400&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=1400&auto=format&fit=crop',
        ]);
    }

    $mainPhoto = $photoPaths->first();

    $statusKey = strtolower((string) ($car->status ?? 'available'));
    $statusLabelMap = [
        'available' => 'Available',
        'reserved' => 'Reserved',
        'sold' => 'Sold',
    ];
    $statusClassMap = [
        'available' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'reserved' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'sold' => 'bg-rose-100 text-rose-700 border border-rose-200',
    ];
    $statusLabel = $statusLabelMap[$statusKey] ?? 'Available';
    $statusClass = $statusClassMap[$statusKey] ?? $statusClassMap['available'];

    $priceLabel = 'Rp ' . number_format((float) ($car->harga ?? 0), 0, ',', '.');
    $kmLabel = number_format((int) ($car->kilometer ?? 0), 0, ',', '.') . ' KM';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="Detail lengkap {{ $carName !== '' ? $carName : 'unit mobil' }} di Maharani Mobil: foto, harga, spesifikasi, dan aksi booking test drive."/>
    <meta name="robots" content="index,follow"/>
    <link rel="canonical" href="{{ request()->url() }}"/>
    @include('components.ui-system-head')
    <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
    <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl docked full-width top-0 sticky z-50">
        <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
            <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="/">Maharani Mobil</a>
            <nav class="hidden md:flex items-center space-x-8 font-headline tracking-tight">
                <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="/home">{{ __('Home') }}</a>
                <a class="text-[#1A2B4C] font-bold border-b-2 border-[#F5A623] pb-1" href="/catalog">{{ __('Catalog') }}</a>
                <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="/about">{{ __('About Us') }}</a>
                <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="/financing">{{ __('Financing') }}</a>
            </nav>
            <div class="flex items-center space-x-4">
                @include('components.nav-tools')
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-500 font-medium px-4 py-2 hover:text-[#1A2B4C] transition-colors">Logout</button>
                    </form>
                @else
                    <a class="text-slate-500 font-medium px-4 py-2 hover:text-[#1A2B4C] transition-colors" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="bg-primary text-white font-bold px-6 py-2 rounded-lg hover:scale-95 transition-transform duration-200" href="{{ route('register') }}">{{ __('Register') }}</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-8 py-8 flex-grow">
        <nav aria-label="Breadcrumb" class="flex mb-6 text-sm font-medium text-on-surface-variant">
            <ol class="flex items-center gap-2">
                <li><a class="hover:text-primary transition-colors" href="/home">Beranda</a></li>
                <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
                <li><a class="hover:text-primary transition-colors" href="/catalog">Katalog</a></li>
                <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
                <li class="text-primary font-semibold truncate max-w-[260px] md:max-w-none">{{ $carName !== '' ? $carName : 'Detail Mobil' }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="lg:col-span-8 space-y-6">
                <article class="rounded-2xl border border-outline-variant bg-surface p-4 md:p-5">
                    <div class="aspect-[16/9] rounded-xl overflow-hidden bg-surface-container">
                        <img
                            id="detail-main-photo"
                            src="{{ $mainPhoto }}"
                            alt="{{ $carName !== '' ? $carName : 'Foto mobil' }}"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <div class="mt-4 grid grid-cols-4 sm:grid-cols-5 gap-2">
                        @foreach ($photoPaths as $index => $photo)
                            <button
                                type="button"
                                class="detail-thumb-btn group relative overflow-hidden rounded-lg border {{ $index === 0 ? 'border-[#f5a623]' : 'border-outline-variant' }} aspect-[4/3]"
                                data-photo-src="{{ $photo }}"
                                aria-label="Foto {{ $index + 1 }}"
                            >
                                <img src="{{ $photo }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform"/>
                            </button>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-2xl border border-outline-variant bg-surface p-5 md:p-6">
                    <h2 class="text-xl font-bold text-primary mb-3">Spesifikasi & Kondisi</h2>
                    <p class="text-sm text-on-surface-variant mb-4">
                        {{ ($car->transmisi ?? '-') }} • {{ $kmLabel }} • {{ ($car->bahan_bakar ?? '-') }} • {{ ($car->warna ?? '-') }} • {{ ($car->tahun ?? '-') }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Mesin OK</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Body OK</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Interior OK</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Dokumen OK</span>
                    </div>

                    <div class="mt-5 pt-5 border-t border-outline-variant/70">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-on-surface-variant mb-2">Deskripsi Unit</h3>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            {{ $car->deskripsi ?: 'Belum ada deskripsi detail untuk unit ini. Silakan hubungi tim Maharani Mobil untuk mendapatkan informasi tambahan dan jadwalkan test drive.' }}
                        </p>
                    </div>
                </article>
            </section>

            <aside class="lg:col-span-4 space-y-4">
                <article class="rounded-2xl border border-outline-variant bg-surface p-5 md:p-6">
                    <h2 class="text-2xl font-extrabold text-primary leading-tight">{{ $carName !== '' ? $carName : 'Detail Unit' }}</h2>
                    <p class="mt-2 text-sm text-on-surface-variant">Kode Unit: {{ $car->kode_unit ?: '-' }}</p>
                    <p class="mt-1 text-sm">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                            Status: {{ strtoupper($statusLabel) }}
                        </span>
                    </p>

                    <div class="mt-5 space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">Harga</p>
                        <p class="text-3xl font-extrabold text-primary">{{ $priceLabel }}</p>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border border-outline-variant p-3">
                            <p class="text-xs text-on-surface-variant">Kilometer</p>
                            <p class="font-semibold text-primary">{{ $kmLabel }}</p>
                        </div>
                        <div class="rounded-lg border border-outline-variant p-3">
                            <p class="text-xs text-on-surface-variant">Transmisi</p>
                            <p class="font-semibold text-primary">{{ $car->transmisi ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @auth
                            <a class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#0b1a40] px-4 py-3 font-semibold text-white hover:brightness-110 transition" href="{{ route('test-drive.form', ['car_id' => $car->id]) }}">
                                <span class="material-symbols-outlined text-[19px]">event</span>
                                Buat Janji Test Drive
                            </a>
                        @else
                            <a class="js-login-required inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#0b1a40] px-4 py-3 font-semibold text-white hover:brightness-110 transition" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                <span class="material-symbols-outlined text-[19px]">event</span>
                                Buat Janji Test Drive
                            </a>
                        @endauth

                        @auth
                            <a class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#f5a623] px-4 py-3 font-semibold text-[#0b1a40] hover:brightness-105 transition" href="{{ route('offers.page') }}">
                                <span class="material-symbols-outlined text-[19px]">payments</span>
                                Ajukan Penawaran
                            </a>
                        @else
                            <a class="js-login-required inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#f5a623] px-4 py-3 font-semibold text-[#0b1a40] hover:brightness-105 transition" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                <span class="material-symbols-outlined text-[19px]">payments</span>
                                Ajukan Penawaran
                            </a>
                        @endauth

                        @if (auth()->check() && auth()->user()->role === 'customer')
                            <form method="POST" action="{{ route('customer.favorites.store', $car->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-outline-variant px-4 py-3 font-semibold text-primary hover:bg-surface-container-low transition">
                                    <span class="material-symbols-outlined text-[19px]">favorite</span>
                                    Simpan Favorit
                                </button>
                            </form>
                        @elseif (!auth()->check())
                            <a class="js-login-required inline-flex w-full items-center justify-center gap-2 rounded-lg border border-outline-variant px-4 py-3 font-semibold text-primary hover:bg-surface-container-low transition" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                <span class="material-symbols-outlined text-[19px]">favorite</span>
                                Simpan Favorit
                            </a>
                        @endif
                    </div>
                </article>

                <article class="rounded-2xl border border-outline-variant bg-surface p-5 md:p-6">
                    <h3 class="text-lg font-bold text-primary">Review Singkat & Bagikan</h3>
                    <p class="mt-1 text-sm text-on-surface-variant">Rating 4.8/5 • 52 ulasan</p>
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <button type="button" class="rounded-lg bg-surface-container-low px-3 py-2 text-xs font-semibold text-primary">Lihat Ulasan</button>
                        <button type="button" class="rounded-lg bg-surface-container-low px-3 py-2 text-xs font-semibold text-primary">Facebook</button>
                        <button type="button" class="rounded-lg bg-surface-container-low px-3 py-2 text-xs font-semibold text-primary">TikTok</button>
                    </div>
                </article>
            </aside>
        </div>

        @if (($relatedCars ?? collect())->isNotEmpty())
            <section class="mt-12">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-2xl font-extrabold text-primary">Unit Serupa</h2>
                    <a class="text-sm font-semibold text-primary hover:text-[#f5a623] transition-colors" href="{{ route('catalog') }}">Lihat semua katalog</a>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($relatedCars as $index => $relatedCar)
                        @php
                            $relatedPhoto = (is_array($relatedCar->photos) && !empty($relatedCar->photos[0]))
                                ? asset('storage/' . $relatedCar->photos[0])
                                : $photoPaths[$index % $photoPaths->count()];
                        @endphp
                        <div class="h-full">
                            @include('partials.car-card', [
                                'car' => $relatedCar,
                                'imageUrl' => $relatedPhoto,
                                'showFavorite' => true,
                            ])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <footer class="bg-[#031636] dark:bg-[#03163f] w-full py-12 mt-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-12 w-full max-w-screen-2xl mx-auto">
            <div class="space-y-4">
                <div class="text-white font-black italic text-2xl tracking-tighter">Maharani Mobil</div>
                <p class="text-slate-400 text-xs uppercase tracking-widest leading-loose font-label">© 2026 Maharani Mobil Pekanbaru.<br/>The Digital Concierge.</p>
            </div>
            <div class="space-y-4">
                <h5 class="text-white font-bold text-sm tracking-widest uppercase">Navigation</h5>
                <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/catalog">Catalog</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/financing">Financing</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/about">About Us</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/test-drive">Contact</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h5 class="text-white font-bold text-sm tracking-widest uppercase">Support</h5>
                <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/privacy">Privacy Policy</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/terms">Terms of Service</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/faq">Cookie Settings</a></li>
                    <li><a class="text-slate-400 hover:text-white transition-all underline" href="/test-drive">Contact Support</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h5 class="text-white font-bold text-sm tracking-widest uppercase">Location</h5>
                <p class="text-slate-400 text-sm normal-case tracking-normal">Jl. Soekarno - Hatta No. 88<br/>Marpoyan Damai, Pekanbaru<br/>Riau 28282</p>
            </div>
        </div>
    </footer>

    @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan unit ' . ($car->merk ?? 'mobil') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? '') . '.'])
    @include('components.ui-system-footer')

    <script>
        (() => {
            const mainPhoto = document.getElementById('detail-main-photo');
            const thumbButtons = document.querySelectorAll('.detail-thumb-btn');

            if (!mainPhoto || !thumbButtons.length) {
                return;
            }

            thumbButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const nextSrc = button.getAttribute('data-photo-src');
                    if (!nextSrc) {
                        return;
                    }

                    mainPhoto.setAttribute('src', nextSrc);

                    thumbButtons.forEach((item) => {
                        item.classList.remove('border-[#f5a623]');
                        item.classList.add('border-outline-variant');
                    });

                    button.classList.add('border-[#f5a623]');
                    button.classList.remove('border-outline-variant');
                });
            });
        })();
    </script>
</body>
</html>
