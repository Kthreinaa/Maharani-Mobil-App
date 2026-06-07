@props([
    'data' => ['count' => 0, 'items' => [], 'empty_text' => 'Belum ada notifikasi.'],
    'title' => 'Notifikasi',
    'variant' => 'light',
])

@php
    $count = (int) ($data['count'] ?? 0);
    $items = collect($data['items'] ?? []);
    $emptyText = $data['empty_text'] ?? 'Belum ada notifikasi.';

    $buttonClasses = $variant === 'dark'
        ? 'border-red-300/45 bg-[linear-gradient(135deg,#991b1b_0%,#b91c1c_100%)] text-white hover:brightness-110 shadow-[0_16px_30px_rgba(153,27,27,0.28)]'
        : 'border-red-200 bg-[linear-gradient(135deg,#fff1f2_0%,#ffe4e6_100%)] text-red-700 hover:brightness-105 shadow-[0_12px_24px_rgba(244,63,94,0.12)]';

    $panelClasses = 'border-red-200 bg-white text-slate-900 shadow-[0_24px_60px_rgba(15,23,42,0.20)]';

    $mutedClasses = 'text-slate-500';
    $titleAccentClasses = 'text-red-600';
    $countChipClasses = 'bg-red-50 text-red-700 border border-red-200';
    $emptyClasses = 'border-red-100 bg-red-50/60 text-slate-600';

    $toneClasses = [
        'amber' => 'bg-amber-50 text-amber-700',
        'sky' => 'bg-sky-50 text-sky-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'rose' => 'bg-rose-50 text-rose-700',
        'navy' => 'bg-red-50 text-red-700',
    ];
@endphp

<details class="group relative">
    <summary class="relative inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full border transition {{ $buttonClasses }}">
        <span class="material-symbols-outlined text-[18px]">notifications</span>
        @if ($count > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-w-[20px] items-center justify-center rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white shadow-sm">
                {{ $count > 9 ? '9+' : $count }}
            </span>
        @endif
    </summary>

    <div class="absolute right-0 z-50 mt-3 w-[360px] max-w-[calc(100vw-2rem)] rounded-[1.5rem] border p-4 {{ $panelClasses }}">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] {{ $titleAccentClasses }}">Notification Center</p>
                <h3 class="mt-1 font-headline text-base font-extrabold">{{ $title }}</h3>
            </div>
            <span class="rounded-full px-3 py-1 text-[11px] font-semibold {{ $countChipClasses }}">
                {{ $count }} aktif
            </span>
        </div>

        <div class="mt-4 max-h-[380px] overflow-y-auto pr-1">
            @if ($items->isEmpty())
                <div class="rounded-[1.2rem] border border-dashed px-4 py-5 text-sm {{ $emptyClasses }}">
                    {{ $emptyText }}
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($items as $item)
                        @php
                            $tone = $toneClasses[$item['tone'] ?? 'navy'] ?? $toneClasses['navy'];
                            $wrapperClasses = 'border-red-100 bg-white hover:bg-red-50/35';
                        @endphp
                        @if (!empty($item['href']))
                            <a href="{{ $item['href'] }}" class="block rounded-[1.2rem] border px-4 py-3 transition {{ $wrapperClasses }}">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $tone }}">
                                        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-bold leading-5">{{ $item['title'] }}</p>
                                            <span class="shrink-0 text-[11px] {{ $mutedClasses }}">{{ $item['time_label'] }}</span>
                                        </div>
                                        <p class="mt-1 text-[13px] leading-6 {{ $mutedClasses }}">{{ $item['detail'] }}</p>
                                    </div>
                                </div>
                            </a>
                        @else
                            <div class="rounded-[1.2rem] border px-4 py-3 {{ $wrapperClasses }}">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $tone }}">
                                        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-bold leading-5">{{ $item['title'] }}</p>
                                            <span class="shrink-0 text-[11px] {{ $mutedClasses }}">{{ $item['time_label'] }}</span>
                                        </div>
                                        <p class="mt-1 text-[13px] leading-6 {{ $mutedClasses }}">{{ $item['detail'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</details>
