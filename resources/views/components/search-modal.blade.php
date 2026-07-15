@php
  /**
   * Search modal (glass) untuk landing/home public.
   * Input:
   * - $brandOptions: Collection<string>
   */
  $brandOptions = $brandOptions ?? collect();
@endphp

<div id="mm-search-modal" class="fixed inset-0 z-[95] hidden items-center justify-center p-4">
  <div data-mm-search-close class="absolute inset-0 bg-slate-950/35 backdrop-blur-sm"></div>

  <div class="mm-glass-panel relative w-full max-w-[680px] rounded-[2.2rem] p-7 shadow-2xl md:p-9">
    <div class="flex items-start justify-between gap-4">
      <h3 class="font-headline text-[34px] font-extrabold leading-tight text-white md:text-[42px]">
        {{ __('Cari Kendaraan Anda') }}
      </h3>
      <button type="button" data-mm-search-close class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <form method="GET" action="{{ route('catalog') }}" class="mt-6 space-y-5 text-[12px]">
      <div>
        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-white/80">{{ __('Brand') }}</label>
        <select name="brand" class="w-full rounded-xl border border-white/10 bg-[#071538]/85 px-4 py-3 text-[13px] font-medium text-white focus:border-[#f5a623] focus:ring-[#f5a623]">
          <option value="all">{{ __('Semua Merek') }}</option>
          @foreach($brandOptions as $merk)
            <option value="{{ $merk }}">{{ $merk }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-white/80">{{ __('Model') }}</label>
          <input name="q" class="w-full rounded-xl border border-white/10 bg-[#071538]/85 px-4 py-3 text-[13px] font-medium text-white placeholder:text-white/50 focus:border-[#f5a623] focus:ring-[#f5a623]" placeholder="e.g. Fortuner" />
        </div>
        <div>
          <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-white/80">{{ __('Tahun (Min 2010)') }}</label>
          <input name="year_min" min="2010" inputmode="numeric" class="w-full rounded-xl border border-white/10 bg-[#071538]/85 px-4 py-3 text-[13px] font-medium text-white placeholder:text-white/50 focus:border-[#f5a623] focus:ring-[#f5a623]" placeholder="2020" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-white/80">{{ __('Kilometer') }}</label>
          <input name="kilometer" class="w-full rounded-xl border border-white/10 bg-[#071538]/85 px-4 py-3 text-[13px] font-medium text-white placeholder:text-white/50 focus:border-[#f5a623] focus:ring-[#f5a623]" placeholder="{{ __('Kilometer') }}" inputmode="numeric" />
        </div>
        <div>
          <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-white/80">{{ __('Harga') }}</label>
          <input name="price_target" class="w-full rounded-xl border border-white/10 bg-[#071538]/85 px-4 py-3 text-[13px] font-medium text-white placeholder:text-white/50 focus:border-[#f5a623] focus:ring-[#f5a623]" placeholder="{{ __('Harga (contoh: 250 juta)') }}" inputmode="numeric" />
        </div>
      </div>

      <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#f5a623] py-3.5 text-[13px] font-bold text-[#111827] transition hover:brightness-105" type="submit">
        <span class="material-symbols-outlined text-[18px]">search</span>
        {{ __('Temukan Unit') }}
      </button>
    </form>
  </div>
</div>
