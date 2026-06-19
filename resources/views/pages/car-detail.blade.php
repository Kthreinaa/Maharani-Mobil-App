@php
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

    $priceLabel = \App\Support\CurrencyFormatter::rupiah($car->harga ?? 0);
    $kmLabel = number_format((int) ($car->kilometer ?? 0), 0, ',', '.') . ' KM';
    $iosGlassActionClass = 'inline-flex w-full items-center justify-center gap-2 rounded-[1.15rem] border border-white/70 bg-[linear-gradient(135deg,rgba(255,255,255,0.82)_0%,rgba(255,245,235,0.78)_42%,rgba(232,236,242,0.82)_100%)] px-4 py-3.5 font-semibold text-[#8a4d12] shadow-[0_16px_36px_rgba(148,163,184,0.18),inset_0_1px_0_rgba(255,255,255,0.76)] backdrop-blur-[18px] transition hover:-translate-y-[1px] hover:bg-[linear-gradient(135deg,rgba(255,255,255,0.92)_0%,rgba(255,243,229,0.88)_42%,rgba(237,240,245,0.9)_100%)]';
    $shareUrl = request()->fullUrl();
    $shareDescription = \Illuminate\Support\Str::limit(
        strip_tags((string) ($car->deskripsi ?: 'Lihat detail unit, harga, spesifikasi, dan ajukan test drive di Maharani Mobil.')),
        150
    );
    $shareTitle = $carName !== '' ? $carName : 'Detail Mobil';
    $shareText = 'Lihat unit ' . $shareTitle . ' di Maharani Mobil. ' . $shareDescription;
    $shareMessage = $shareText . ' ' . $shareUrl;
    $whatsAppShareUrl = 'https://wa.me/?text=' . rawurlencode($shareMessage);
    $facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
    $viewerRole = auth()->check() ? (string) auth()->user()->role : 'guest';
    $isMarketingViewer = $viewerRole === 'marketing';
    $marketingBackUrl = route('marketing.products.index');
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
    <meta property="og:type" content="product"/>
    <meta property="og:title" content="{{ $shareTitle }}"/>
    <meta property="og:description" content="{{ $shareDescription }}"/>
    <meta property="og:url" content="{{ $shareUrl }}"/>
    <meta property="og:image" content="{{ $mainPhoto }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="{{ $shareTitle }}"/>
    <meta name="twitter:description" content="{{ $shareDescription }}"/>
    <meta name="twitter:image" content="{{ $mainPhoto }}"/>
    @include('components.ui-system-head')
    <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
    @if ($isMarketingViewer)
        @include('components.public-site-header', [
            'active' => 'detail-mobil',
            'overlap' => false,
            'showCatalog' => true,
            'catalogInline' => true,
            'inlineBadgeLabel' => 'Kembali',
            'inlineBadgeHref' => $marketingBackUrl,
            'inlineLinks' => [],
            'showLogout' => false,
            'showRightActions' => false,
        ])
    @else
        @include('components.public-site-header', [
            'active' => 'detail-mobil',
            'overlap' => false,
            'showCatalog' => true,
            'catalogInline' => true,
            'inlineBadgeLabel' => 'Detail Mobil',
            'inlineBadgeHref' => $shareUrl,
            'inlineLinks' => [],
        ])
    @endif

    <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-8 py-8 flex-grow">
        <nav aria-label="Breadcrumb" class="flex mb-6 text-sm font-medium text-on-surface-variant">
            <ol class="flex items-center gap-2">
                <li><a class="hover:text-primary transition-colors" href="{{ $isMarketingViewer ? $marketingBackUrl : url('/') }}">{{ $isMarketingViewer ? 'Pantau Unit Mobil' : 'Home' }}</a></li>
                <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
                @unless ($isMarketingViewer)
                    <li><a class="hover:text-primary transition-colors" href="/catalog">Katalog</a></li>
                    <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
                @endunless
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

                    @if (! $isMarketingViewer)
                        <div class="mt-5 space-y-3">
                            @auth
                                @if (auth()->user()->role === 'customer')
                                    <a class="{{ $iosGlassActionClass }}" href="{{ route('checkout.cash', ['car_id' => $car->id]) }}">
                                        <span class="material-symbols-outlined text-[19px]">shopping_cart_checkout</span>
                                        Pesan Mobil
                                    </a>
                                @endif
                            @else
                                <a class="js-login-required {{ $iosGlassActionClass }}" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                    <span class="material-symbols-outlined text-[19px]">shopping_cart_checkout</span>
                                    Pesan Mobil
                                </a>
                            @endauth

                            @auth
                                <a class="{{ $iosGlassActionClass }}" href="{{ route('test-drive.form', ['car_id' => $car->id]) }}">
                                    <span class="material-symbols-outlined text-[19px]">event</span>
                                    Test Drive
                                </a>
                            @else
                                <a class="js-login-required {{ $iosGlassActionClass }}" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                    <span class="material-symbols-outlined text-[19px]">event</span>
                                    Test Drive
                                </a>
                            @endauth

                            @auth
                                <a class="{{ $iosGlassActionClass }}" href="{{ route('offers.page', ['car_id' => $car->id]) }}">
                                    <span class="material-symbols-outlined text-[19px]">payments</span>
                                    Ajukan Penawaran
                                </a>
                            @else
                                <a class="js-login-required {{ $iosGlassActionClass }}" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                                    <span class="material-symbols-outlined text-[19px]">payments</span>
                                    Ajukan Penawaran
                                </a>
                            @endauth

                            <button
                                type="button"
                                class="share-native-btn {{ $iosGlassActionClass }}"
                                data-share-url="{{ $shareUrl }}"
                                data-share-title="{{ $shareTitle }}"
                                data-share-text="{{ $shareText }}"
                                data-share-image="{{ $mainPhoto }}"
                            >
                                <span class="material-symbols-outlined text-[19px]">ios_share</span>
                                Bagikan Unit
                            </button>
                        </div>
                    @else
                        <div class="mt-5 rounded-[1.15rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium leading-6 text-slate-600">

                        </div>
                    @endif
                </article>

                <article class="rounded-2xl border border-outline-variant bg-surface p-5 md:p-6">
                    <h3 class="text-lg font-bold text-primary">Review Customer & Bagikan</h3>
                    <p class="mt-1 text-sm text-on-surface-variant">
                        @if (($approvedReviewsCount ?? 0) > 0)
                            Rating {{ number_format((float) $approvedReviewsAverage, 1) }}/5 • {{ $approvedReviewsCount }} ulasan
                        @else
                            Belum ada review customer untuk unit ini.
                        @endif
                    </p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ $isMarketingViewer ? route('marketing.products.reviews.index', $car->id) : route('reviews.page', ['car' => $car->id]) }}" class="rounded-[1rem] bg-surface-container-low px-3 py-2.5 text-center text-xs font-semibold text-primary transition hover:bg-slate-100">Lihat Ulasan</a>
                        <button type="button" class="share-copy-btn rounded-[1rem] bg-surface-container-low px-3 py-2.5 text-center text-xs font-semibold text-primary transition hover:bg-slate-100" data-copy-url="{{ $shareUrl }}" data-copy-message="Link produk berhasil disalin.">
                            Salin Link
                        </button>
                    </div>
                    <p id="share-feedback" class="mt-3 hidden rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700"></p>
                </article>
            </aside>
        </div>

        @if (! $isMarketingViewer && ($relatedCars ?? collect())->isNotEmpty())
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

    @unless ($isMarketingViewer)
        @include('components.public-site-footer')
    @endunless

    @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya tertarik dengan unit ' . ($car->merk ?? 'mobil') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? '') . '.'])
    @include('components.ui-system-footer')

    <script>
        (() => {
            const mainPhoto = document.getElementById('detail-main-photo');
            const thumbButtons = document.querySelectorAll('.detail-thumb-btn');
            const shareFeedback = document.getElementById('share-feedback');
            const shareNativeButtons = document.querySelectorAll('.share-native-btn');
            const shareCopyButtons = document.querySelectorAll('.share-copy-btn');

            const showShareFeedback = (message, isError = false) => {
                if (!shareFeedback) {
                    return;
                }

                shareFeedback.textContent = message;
                shareFeedback.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-rose-50', 'text-rose-700');
                shareFeedback.classList.add(isError ? 'bg-rose-50' : 'bg-emerald-50');
                shareFeedback.classList.add(isError ? 'text-rose-700' : 'text-emerald-700');
            };

            const copyText = async (value) => {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(value);
                    return;
                }

                const textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            };

            const buildShareFiles = async (imageUrl, title) => {
                if (!imageUrl || !navigator.canShare) {
                    return [];
                }

                try {
                    const response = await fetch(imageUrl, { mode: 'cors' });
                    if (!response.ok) {
                        return [];
                    }

                    const blob = await response.blob();
                    const extension = blob.type.includes('png') ? 'png' : blob.type.includes('webp') ? 'webp' : 'jpg';
                    const safeTitle = (title || 'produk-maharani-mobil')
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '') || 'produk-maharani-mobil';
                    const file = new File([blob], `${safeTitle}.${extension}`, { type: blob.type || 'image/jpeg' });

                    return navigator.canShare({ files: [file] }) ? [file] : [];
                } catch (error) {
                    return [];
                }
            };

            if (mainPhoto && thumbButtons.length) {
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
            }

            shareNativeButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.getAttribute('data-share-url') || window.location.href;
                    const title = button.getAttribute('data-share-title') || document.title;
                    const text = button.getAttribute('data-share-text') || '';
                    const imageUrl = button.getAttribute('data-share-image') || '';

                    if (navigator.share) {
                        try {
                            const files = await buildShareFiles(imageUrl, title);
                            const payload = files.length ? { title, text, url, files } : { title, text, url };
                            await navigator.share(payload);
                            showShareFeedback('Link produk siap dibagikan.');
                            return;
                        } catch (error) {
                            if (error?.name === 'AbortError') {
                                return;
                            }
                        }
                    }

                    try {
                        await copyText(url);
                        showShareFeedback('Link produk berhasil disalin. Sekarang bisa ditempel ke story, chat, atau media sosial.');
                    } catch (error) {
                        showShareFeedback('Gagal menyalin link produk.', true);
                    }
                });
            });

            shareCopyButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.getAttribute('data-copy-url') || window.location.href;
                    const message = button.getAttribute('data-copy-message') || 'Link produk berhasil disalin.';

                    try {
                        await copyText(url);
                        showShareFeedback(message);
                    } catch (error) {
                        showShareFeedback('Gagal menyalin link produk.', true);
                    }
                });
            });
        })();
    </script>
</body>
</html>
