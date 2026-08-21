@php
    /**
     * Partial kartu mobil reusable untuk halaman home + katalog.
     * Wajib: $car
     * Opsional: $imageUrl, $showFavorite
     */
    $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1200&auto=format&fit=crop';
    $showFavorite = $showFavorite ?? true;
    $showNewBadge = $showNewBadge ?? false;

    $statusKey = strtolower((string) ($car->status ?? 'available'));
    $statusLabelMap = [
        'available' => 'Available',
        'reserved' => 'Reserved',
        'sold' => 'Sold',
    ];
    $statusClassMap = [
        'available' => 'status-available',
        'reserved' => 'status-reserved',
        'sold' => 'status-sold',
    ];
    $statusLabel = $statusLabelMap[$statusKey] ?? 'Available';
    $statusClass = $statusClassMap[$statusKey] ?? 'status-available';

    $subtitleParts = array_values(array_filter([
        $car->transmisi ?? null,
        $car->warna ?? null,
        $car->bahan_bakar ?? null,
    ]));
    $subtitle = count($subtitleParts) ? implode(' / ', $subtitleParts) : 'Lihat detail spesifikasi unit';
    $shareTitle = trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''));
    $shareUrl = route('cars.show', $car->id);
    $shareText = 'Lihat unit ' . ($shareTitle !== '' ? $shareTitle : 'Maharani Mobil') . ' di Maharani Mobil.';
    $isCustomer = auth()->check() && auth()->user()->role === 'customer';
    static $favoriteCarIds = null;

    if ($isCustomer && $favoriteCarIds === null) {
        $favoriteCarIds = auth()->user()->favoriteCars()->pluck('cars.id')->map(fn ($id) => (int) $id)->all();
    }

    $isFavorite = $isCustomer && in_array((int) $car->id, $favoriteCarIds ?? [], true);
@endphp

<article class="car-card group overflow-hidden rounded-[1.6rem] border border-slate-200 bg-white shadow-[0_18px_48px_rgba(15,23,42,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(15,23,42,0.14)]">
    <a class="relative block aspect-[16/9] overflow-hidden" href="{{ route('cars.show', $car->id) }}">
        <img
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            src="{{ $imageUrl }}"
            alt="{{ $car->merk }} {{ $car->tipe }}"
            loading="lazy"
        />
        <span class="{{ $statusClass }} absolute left-4 top-4 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider">
            {{ $statusLabel }}
        </span>
        @if ($showNewBadge)
            <span class="absolute right-4 top-4 rounded-full bg-[#f5a623] px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.22em] text-[#0b1a40] shadow-[0_10px_24px_rgba(245,166,35,0.28)]">
                New
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="car-card-title font-headline text-[20px] font-extrabold leading-tight text-primary sm:text-[22px]">
                    {{ strtoupper(trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''))) }}
                </h3>
                <p class="car-card-subtitle mt-1 text-[13px] text-on-surface-variant">{{ $subtitle }}</p>
            </div>

            <div class="flex items-center gap-2">
                @if ($showFavorite)
                    @if ($isCustomer)
                        <button
                            type="button"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 transition hover:bg-slate-200"
                            data-favorite-toggle
                            data-store-url="{{ route('customer.favorites.store', $car->id) }}"
                            data-destroy-url="{{ route('customer.favorites.destroy', $car->id) }}"
                            data-is-favorite="{{ $isFavorite ? '1' : '0' }}"
                            aria-label="{{ $isFavorite ? 'Favorit tersimpan' : 'Tambah favorit' }}"
                            aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
                        >
                            <span
                                class="material-symbols-outlined text-[20px] {{ $isFavorite ? 'text-rose-500' : 'text-slate-700' }}"
                                data-favorite-icon
                                style="font-variation-settings: 'FILL' {{ $isFavorite ? 1 : 0 }}, 'wght' 500, 'GRAD' 0, 'opsz' 24;"
                            >favorite</span>
                        </button>
                    @else
                        <a
                            class="js-login-required inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                            href="{{ route('login') }}"
                            data-popup-message="{{ __('Please login first') }}"
                            aria-label="Login untuk favorit"
                        >
                            <span class="material-symbols-outlined text-[20px]">favorite</span>
                        </a>
                    @endif
                @endif

                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                    aria-label="Bagikan unit"
                    data-share-car
                    data-share-url="{{ $shareUrl }}"
                    data-share-title="{{ $shareTitle }}"
                    data-share-text="{{ $shareText }}"
                    data-share-image="{{ $imageUrl }}"
                >
                    <span class="material-symbols-outlined text-[20px]">share</span>
                </button>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3 border-y border-outline-variant/30 py-3 text-[13px] text-on-surface-variant">
            <span class="inline-flex items-center gap-1.5 truncate">
                <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                {{ $car->tahun ?? '-' }}
            </span>
            <span class="inline-flex items-center gap-1.5 truncate">
                <span class="material-symbols-outlined text-[15px]">speed</span>
                {{ number_format((int) ($car->kilometer ?? 0), 0, ',', '.') }} KM
            </span>
        </div>

        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-on-surface-variant">Harga OTR</p>
                <p class="car-card-price mt-1 max-w-[190px] font-headline text-[18px] font-extrabold leading-tight text-green-600 sm:max-w-none sm:text-[20px]">
                    {{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}
                </p>
            </div>

            <div class="flex flex-col items-end gap-2">
                <a
                    class="text-xs font-semibold text-primary hover:underline"
                    href="{{ route('cars.show', $car->id) }}"
                >
                    Lihat Detail
                </a>
                <a
                    class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-[#f5a623] text-[#0b1a40] transition hover:scale-105"
                    href="{{ route('cars.show', $car->id) }}"
                    aria-label="Lihat detail"
                >
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</article>

@once
    <script>
        (() => {
            if (window.mmCarShareBound) {
                return;
            }

            window.mmCarShareBound = true;

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

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-share-car]');
                if (!button) {
                    return;
                }

                const url = button.getAttribute('data-share-url') || window.location.href;
                const title = button.getAttribute('data-share-title') || document.title;
                const text = button.getAttribute('data-share-text') || '';
                const imageUrl = button.getAttribute('data-share-image') || '';

                if (navigator.share) {
                    try {
                        const files = await buildShareFiles(imageUrl, title);
                        const payload = files.length ? { title, text, url, files } : { title, text, url };
                        await navigator.share(payload);
                        return;
                    } catch (error) {
                        if (error?.name === 'AbortError') {
                            return;
                        }
                    }
                }

                try {
                    await copyText(url);
                    button.classList.add('bg-emerald-100', 'text-emerald-700');
                    setTimeout(() => {
                        button.classList.remove('bg-emerald-100', 'text-emerald-700');
                    }, 1600);
                } catch (error) {
                    console.error('Gagal menyalin link unit.', error);
                }
            });
        })();
    </script>
@endonce

@include('partials.favorite-toggle-script')
