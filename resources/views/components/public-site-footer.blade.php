@php
  $instagramUrl = 'https://www.instagram.com/dikimobilbekaspekanbaruriau?igsh=MWd4MjhvdXBrbTZjaA==';
  $tiktokUrl = 'https://tiktok.com/@mobilbekaspekanbaruriau_';
  $facebookUrl = 'https://www.facebook.com/share/1EC1fw93BB/';
  $youtubeUrl = 'https://www.youtube.com/channel/UC9Dcw3CkFDkjJ163UQyXInw';
  $whatsAppUrl = 'https://wa.me/628117584617?text=Halo%20Maharani%20Mobil%2C%20saya%20ingin%20bertanya%20tentang%20unit%20mobil.';
  $showroomPhone = '0813 7213 6927';
  $whatsAppIcon = 'https://commons.wikimedia.org/wiki/Special:Redirect/file/WhatsApp.svg';
  $instagramIcon = 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Instagram%20Glyph%20Gradient%20RGB%20logo.svg';
  $tiktokIcon = asset('assets/social/tiktok-logo.svg');
  $facebookIcon = 'https://commons.wikimedia.org/wiki/Special:Redirect/file/2023%20Facebook%20icon.svg';
  $youtubeIcon = 'https://commons.wikimedia.org/wiki/Special:Redirect/file/YouTube%20full-color%20icon%20%282024%29.svg';
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
      <div class="mt-4 space-y-4 text-[13px] leading-relaxed text-slate-300">
        <div class="flex items-start gap-3">
          <span class="material-symbols-outlined mt-0.5 text-[18px] text-[#f5a623]">location_on</span>
          <p>
            Jl. Arifin Ahmad No.113<br />
            Sidomulyo Timur, Marpoyan Damai<br />
            Pekanbaru, Riau
          </p>
        </div>
        <a class="inline-flex items-center gap-3 transition hover:text-[#f5a623]" href="tel:081372136927">
          <span class="material-symbols-outlined text-[18px] text-[#f5a623]">call</span>
          <span>{{ $showroomPhone }}</span>
        </a>
      </div>
    </div>

    <div>
      <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">Media Sosial</h4>
      <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-start sm:gap-6">
        <div class="min-w-[180px] flex-1 space-y-3 text-[13px] text-slate-200">
          <a class="flex items-center gap-3 transition hover:text-[#f5a623]" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer">
            <img alt="Logo WhatsApp" class="h-[20px] w-[20px] object-contain" src="{{ $whatsAppIcon }}"/>
            <span>WhatsApp</span>
          </a>
          <a class="flex items-center gap-3 transition hover:text-[#f5a623]" href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">
            <img alt="Logo Instagram" class="h-[20px] w-[20px] object-contain" src="{{ $instagramIcon }}"/>
            <span>Instagram</span>
          </a>
          <a class="flex items-center gap-3 transition hover:text-[#f5a623]" href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer">
            <img alt="Logo TikTok" class="h-[22px] w-[22px] rounded-full object-contain" src="{{ $tiktokIcon }}"/>
            <span>TikTok</span>
          </a>
          <a class="flex items-center gap-3 transition hover:text-[#f5a623]" href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer">
            <img alt="Logo Facebook" class="h-[20px] w-[20px] object-contain" src="{{ $facebookIcon }}"/>
            <span>Facebook</span>
          </a>
          <a class="flex items-center gap-3 transition hover:text-[#f5a623]" href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer">
            <img alt="Logo YouTube" class="h-[20px] w-[20px] object-contain" src="{{ $youtubeIcon }}"/>
            <span>YouTube</span>
          </a>
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
    <p>&copy; 2026 MAHARANI MOBIL PEKANBARU</p>
    <a class="mt-3 inline-flex text-[11px] tracking-[0.18em] text-slate-400 transition hover:text-[#f5a623]" href="{{ url('/privacy') }}">
      {{ __('Kebijakan Privasi') }}
    </a>
  </div>
</footer>
