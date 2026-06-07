@php
  $homeUrl = auth()->check()
    ? match (auth()->user()->role) {
        'customer' => route('customer.home'),
        'supervisor' => '/supervisor/dashboard',
        'marketing' => '/marketing/dashboard',
        'owner' => '/owner/dashboard',
        default => route('home.public'),
      }
    : url('/');
@endphp

<footer class="mt-auto bg-[#08132e] py-14 text-white dark:bg-[#08132e]">
  <div class="mx-auto grid w-full max-w-[1280px] grid-cols-1 gap-10 px-4 md:grid-cols-4 md:px-6">
    <div>
      <h3 class="font-headline text-[34px] font-extrabold tracking-tight">MaharaniMobil</h3>
      <p class="mt-4 max-w-[280px] text-[13px] leading-relaxed text-slate-300">
        {{ __('Showroom mobil bekas terpercaya di Pekanbaru dengan pelayanan transparan, unit terseleksi, dan pengalaman beli yang nyaman.') }}
      </p>
    </div>

    <div>
      <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Navigasi') }}</h4>
      <ul class="mt-4 space-y-2 text-[13px] text-slate-200">
        <li><a class="transition hover:text-[#f5a623]" href="{{ $homeUrl }}">{{ __('Home') }}</a></li>
        <li><a class="transition hover:text-[#f5a623]" href="{{ url('/about') }}">{{ __('About Us') }}</a></li>
        <li><a class="transition hover:text-[#f5a623]" href="{{ route('reviews.page') }}">{{ __('Ulasan Customer') }}</a></li>
        <li><a class="transition hover:text-[#f5a623]" href="{{ route('faq') }}">{{ __('FAQ') }}</a></li>
      </ul>
    </div>

    <div>
      <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Showroom') }}</h4>
      <p class="mt-4 text-[13px] leading-relaxed text-slate-300">
        Jl. Arifin Ahmad No.113<br />
        Sidomulyo Timur, Marpoyan Damai<br />
        Pekanbaru, Riau
      </p>
    </div>

    <div>
      <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Kontak') }}</h4>
      <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-start sm:gap-6">
        <div class="min-w-[160px] flex-1 space-y-3 text-[13px] text-slate-200">
          <a class="block transition hover:text-[#f5a623]" href="https://wa.me/628117584617?text=Halo%20Maharani%20Mobil%2C%20saya%20ingin%20bertanya%20tentang%20unit%20mobil." target="_blank" rel="noopener noreferrer">{{ __('WhatsApp') }}</a>
          <a class="block transition hover:text-[#f5a623]" href="https://instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a>
          <a class="block transition hover:text-[#f5a623]" href="{{ url('/privacy') }}">{{ __('Privacy Policy') }}</a>
        </div>

        <div class="w-full max-w-[198px] -mt-2 sm:w-[198px] sm:shrink-0 sm:-mt-10 md:-mt-10">
          <div class="overflow-hidden rounded-[1.35rem] border border-white/10 bg-white/5 shadow-[0_16px_44px_rgba(0,0,0,0.22)]">
            <iframe
              title="Peta Maharani Mobil"
              class="h-[128px] w-full"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=Maharani%20Mobil%2C%20Jl.%20Arifin%20Ahmad%20No.113%2C%20Sidomulyo%20Timur%2C%20Marpoyan%20Damai%2C%20Kota%20Pekanbaru%2C%20Riau&hl=id&z=15&output=embed"
            ></iframe>
          </div>
          <a
            class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-full bg-white/10 px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-white/15"
            href="https://www.google.com/maps/dir/?api=1&destination=Maharani%20Mobil%2C%20Jl.%20Arifin%20Ahmad%20No.113%2C%20Sidomulyo%20Timur%2C%20Marpoyan%20Damai%2C%20Kota%20Pekanbaru%2C%20Riau&travelmode=driving"
            target="_blank"
            rel="noopener noreferrer"
          >
            <span class="material-symbols-outlined text-[16px]">route</span>
            {{ __('Buka Maps') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="mx-auto mt-10 w-full max-w-[1280px] border-t border-white/10 px-4 pt-6 text-center text-[11px] tracking-[0.2em] text-slate-500 md:px-6">
    &copy; 2026 MAHARANI MOBIL PEKANBARU
  </div>
</footer>
