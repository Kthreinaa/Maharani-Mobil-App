@php
    /**
     * Partial kartu mobil reusable untuk halaman home + katalog.
     * Wajib: $car
     * Opsional: $imageUrl, $showFavorite
     */
    $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1200&auto=format&fit=crop';
    $showFavorite = $showFavorite ?? true;

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
    $subtitle = count($subtitleParts) ? implode(' • ', $subtitleParts) : 'Lihat detail spesifikasi unit';
@endphp

<article class="car-card group overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
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
    </a>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h3 class="car-card-title font-headline text-[20px] font-extrabold leading-tight text-primary sm:text-[22px]">
                    {{ strtoupper(trim(($car->merk ?? '') . ' ' . ($car->tipe ?? '') . ' ' . ($car->tahun ?? ''))) }}
                </h3>
                <p class="car-card-subtitle mt-1 text-[13px] text-on-surface-variant">{{ $subtitle }}</p>
            </div>

            @if ($showFavorite)
                @php
                    $isCustomer = auth()->check() && auth()->user()->role === 'customer';
                @endphp

                @if ($isCustomer)
                    <form method="POST" action="{{ route('customer.favorites.store', $car->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                            aria-label="Tambah favorit"
                        >
                            <span class="material-symbols-outlined text-[20px]">favorite</span>
                        </button>
                    </form>
                @else
                    <a
                        class="js-login-required inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                        href="{{ route('login') }}"
                        data-popup-message="{{ __('Please login first') }}"
                        aria-label="Login untuk favorit"
                    >
                        <span class="material-symbols-outlined text-[20px]">favorite</span>
                    </a>
                @endif
            @endif
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
            <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-on-surface-variant">Harga OTR</p>
                <p class="car-card-price font-headline text-[20px] font-extrabold leading-none text-green-600 dark:text-green-400 sm:text-[22px]">
                    Rp {{ number_format((float) $car->harga, 0, ',', '.') }}
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
