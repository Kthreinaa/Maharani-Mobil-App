@props([
    'active' => 'home',
    'authActive' => null,
    'overlap' => true,
    'showCatalog' => false,
    'catalogInline' => false,
    'utilityActive' => null,
    'inlineBadgeLabel' => null,
    'inlineBadgeHref' => null,
    'inlineLinks' => null,
])

@php
    $isCustomer = auth()->check() && auth()->user()->role === 'customer';
    $homeUrl = auth()->check()
        ? match (auth()->user()->role) {
            'customer' => route('customer.home'),
            'supervisor' => '/supervisor/dashboard',
            'marketing' => '/marketing/dashboard',
            'owner' => '/owner/dashboard',
            default => route('home.public'),
        }
        : url('/');

    $primaryLink = $showCatalog
        ? ['key' => 'catalog', 'label' => __('Catalog'), 'href' => route('catalog')]
        : ['key' => 'home', 'label' => __('Home'), 'href' => $homeUrl];

    $inlineBadge = [
        'label' => $inlineBadgeLabel ?: __('Catalog'),
        'href' => $inlineBadgeHref ?: route('catalog'),
    ];

    $links = $catalogInline
        ? (is_array($inlineLinks) && !empty($inlineLinks) ? $inlineLinks : [$primaryLink])
        : ($isCustomer
            ? [
                ['key' => 'home', 'label' => __('Home'), 'href' => $homeUrl],
                ['key' => 'catalog', 'label' => __('Katalog'), 'href' => route('catalog')],
                ['key' => 'test-drives', 'label' => __('Test Drive'), 'href' => route('customer.test-drives.index')],
                ['key' => 'orders', 'label' => __('Pesanan Saya'), 'href' => route('customer.orders.index')],
                ['key' => 'favorites', 'label' => __('Favorit'), 'href' => route('customer.favorites.index')],
                ['key' => 'customer-reviews', 'label' => __('Review'), 'href' => route('customer.reviews.create')],
            ]
            : [
                $primaryLink,
                ['key' => 'about', 'label' => __('About Us'), 'href' => url('/about')],
                ['key' => 'reviews', 'label' => __('Ulasan'), 'href' => route('reviews.page')],
                ['key' => 'faq', 'label' => __('FAQ'), 'href' => route('faq')],
            ]);

    $dashboardUrl = null;
    if (auth()->check()) {
        $dashboardUrl = match (auth()->user()->role) {
            'supervisor' => '/supervisor/dashboard',
            'marketing' => '/marketing/dashboard',
            'owner' => '/owner/dashboard',
            'customer' => route('customer.home'),
            default => route('home.public'),
        };
    }

    $cartUrl = session('checkout_car_id')
        ? route('cart', ['car_id' => session('checkout_car_id')])
        : route('cart');

    $customerNotifications = $dashboardNotifications ?? null;

    $desktopShellClass = $catalogInline
        ? 'md:grid md:grid-cols-[minmax(320px,1fr)_auto_minmax(320px,1fr)]'
        : 'md:grid md:grid-cols-[minmax(260px,1fr)_auto_minmax(260px,1fr)]';
@endphp

<header class="sticky top-0 z-50 {{ $overlap ? '-mb-[116px] md:-mb-[92px]' : '' }}">
  <nav class="w-full rounded-b-[1.75rem] border-b border-white/10 bg-[rgba(8,19,46,0.78)] px-4 py-4 shadow-[0_18px_55px_rgba(2,8,23,0.28)] backdrop-blur-[28px] dark:border-white/12 dark:bg-[rgba(8,19,46,0.82)] dark:shadow-[0_18px_40px_rgba(2,8,23,0.32)] md:px-6">
    <div class="flex items-center justify-between gap-4 {{ $desktopShellClass }} md:items-center">
      <div class="min-w-0 flex items-center gap-4">
        <a class="font-headline text-[22px] font-extrabold tracking-tight text-white" href="{{ $homeUrl }}">MaharaniMobil</a>
        @if ($catalogInline)
          <a class="hidden md:inline-flex items-center rounded-full border border-white/12 bg-[rgba(255,255,255,0.08)] px-4 py-2 text-[13px] font-semibold tracking-tight text-white shadow-sm backdrop-blur-xl" href="{{ $inlineBadge['href'] }}">
            {{ $inlineBadge['label'] }}
          </a>
        @endif
      </div>

      <div class="hidden justify-self-center md:flex">
        <div class="inline-flex items-center gap-1 rounded-full border border-white/12 bg-[rgba(255,255,255,0.08)] px-2 py-1.5 shadow-sm backdrop-blur-xl dark:border-white/15 dark:bg-[rgba(255,255,255,0.06)]">
          @foreach ($links as $link)
            @php
                $isActive = $active === $link['key'];
            @endphp
            <a
              class="group relative inline-flex h-11 {{ $isCustomer ? 'w-[108px]' : 'w-[110px]' }} items-center justify-center text-[13px] font-semibold tracking-tight transition {{ $isActive ? 'text-white' : 'text-slate-300 hover:text-white dark:text-slate-200 dark:hover:text-white' }}"
              href="{{ $link['href'] }}"
            >
              <span>{{ $link['label'] }}</span>
              <span class="absolute bottom-[4px] left-1/2 h-[2px] w-9 -translate-x-1/2 rounded-full bg-[#f5a623] transition-transform duration-300 {{ $isActive ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
            </a>
          @endforeach
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 md:min-w-0 md:justify-self-end">
        @include('components.nav-tools')

        @auth
          @if ($isCustomer)
            @if ($customerNotifications)
              <x-dashboard-notification-bell
                :data="$customerNotifications"
                :title="__('Notifikasi Customer')"
                variant="dark"
              />
            @endif
            <a
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border text-slate-100 transition {{ $utilityActive === 'cart' ? 'border-[rgba(245,166,35,0.28)] bg-[rgba(245,166,35,0.18)] shadow-[inset_0_1px_0_rgba(255,255,255,0.12)] backdrop-blur-xl' : 'border-white/15 bg-[rgba(255,255,255,0.08)] hover:bg-[rgba(255,255,255,0.14)]' }}"
              href="{{ $cartUrl }}"
              aria-label="{{ __('Keranjang') }}"
            >
              <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
            </a>
            <a
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border text-slate-100 transition {{ $utilityActive === 'settings' ? 'border-[rgba(245,166,35,0.28)] bg-[rgba(245,166,35,0.18)] shadow-[inset_0_1px_0_rgba(255,255,255,0.12)] backdrop-blur-xl' : 'border-white/15 bg-[rgba(255,255,255,0.08)] hover:bg-[rgba(255,255,255,0.14)]' }}"
              href="{{ route('customer.settings.edit') }}"
              aria-label="{{ __('Pengaturan') }}"
            >
              <span class="material-symbols-outlined text-[18px]">settings</span>
            </a>
          @else
            <a class="hidden text-[13px] font-semibold text-slate-200 transition hover:text-white dark:text-slate-200 dark:hover:text-white sm:inline" href="{{ $dashboardUrl }}">Dashboard</a>
          @endif
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-full border border-white/15 bg-[rgba(255,255,255,0.08)] px-4 py-1.5 text-[12px] font-semibold text-slate-100 transition hover:bg-[rgba(255,255,255,0.14)] dark:border-white/15 dark:bg-[rgba(255,255,255,0.08)] dark:text-slate-100 dark:hover:bg-[rgba(255,255,255,0.12)]">Logout</button>
          </form>
        @else
          <a class="rounded-full px-4 py-1.5 text-[12px] font-semibold transition {{ $authActive === 'login' ? 'border border-[rgba(245,166,35,0.22)] bg-[rgba(245,166,35,0.22)] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.12)] backdrop-blur-xl' : 'text-slate-200 hover:bg-[rgba(255,255,255,0.08)] hover:text-white' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
          <a class="rounded-full px-4 py-1.5 text-[12px] font-bold transition {{ $authActive === 'register' ? 'border border-[rgba(245,166,35,0.22)] bg-[rgba(245,166,35,0.22)] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.12)] backdrop-blur-xl' : 'bg-[#f5a623] text-[#111827] hover:brightness-105' }}" href="{{ route('register') }}">{{ __('Register') }}</a>
        @endauth
      </div>
    </div>

    <div class="mt-3 grid {{ $catalogInline ? 'grid-cols-1' : ($isCustomer ? 'grid-cols-3' : 'grid-cols-4') }} rounded-2xl border border-white/12 bg-[rgba(255,255,255,0.08)] p-1 shadow-sm backdrop-blur-xl md:hidden dark:border-white/15 dark:bg-[rgba(255,255,255,0.06)]">
      @foreach ($links as $link)
        @php
            $isActive = $active === $link['key'];
        @endphp
        <a
          class="relative inline-flex items-center justify-center rounded-xl px-2 py-2.5 text-center text-[12px] font-semibold transition {{ $isActive ? 'bg-[rgba(255,255,255,0.10)] text-white dark:bg-[rgba(255,255,255,0.10)] dark:text-white' : 'text-slate-300 hover:bg-[rgba(255,255,255,0.05)] hover:text-white dark:text-slate-300 dark:hover:bg-[rgba(255,255,255,0.05)] dark:hover:text-white' }}"
          href="{{ $link['href'] }}"
        >
          <span>{{ $link['label'] }}</span>
          @if ($isActive)
            <span class="absolute bottom-[5px] left-1/2 h-[2px] w-8 -translate-x-1/2 rounded-full bg-[#f5a623]"></span>
          @endif
        </a>
      @endforeach
    </div>
  </nav>
</header>
