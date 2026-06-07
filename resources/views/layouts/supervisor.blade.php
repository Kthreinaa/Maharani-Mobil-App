<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $title ?? 'Supervisor Dashboard' }}</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700" rel="stylesheet"/>
  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    .glass-toolbar {
      border: 1px solid rgba(255, 255, 255, 0.75);
      background: linear-gradient(135deg, rgba(255,255,255,0.78), rgba(255,255,255,0.58));
      box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
      backdrop-filter: blur(24px);
    }
    .glass-table-shell {
      border: 1px solid rgba(255, 255, 255, 0.78);
      background: linear-gradient(180deg, rgba(255,255,255,0.78), rgba(255,255,255,0.62));
      box-shadow: 0 30px 75px rgba(15, 23, 42, 0.08);
      backdrop-filter: blur(24px);
    }
    .glass-table {
      border-collapse: separate;
      border-spacing: 0;
    }
    .mm-data-table {
      border-collapse: separate;
      border-spacing: 0;
    }
    .glass-table thead,
    .mm-data-table thead {
      background: linear-gradient(135deg, rgba(8,19,46,0.08) 0%, rgba(236,244,255,0.94) 48%, rgba(255,248,231,0.96) 100%);
    }
    .glass-table thead th,
    .mm-data-table thead th {
      letter-spacing: 0.14em;
      font-size: 12px;
      font-weight: 800;
      color: #5f6f89;
      background: transparent;
    }
    .glass-table thead th:first-child,
    .mm-data-table thead th:first-child {
      border-top-left-radius: 1rem;
    }
    .glass-table thead th:last-child,
    .mm-data-table thead th:last-child {
      border-top-right-radius: 1rem;
    }
    .glass-table tbody tr,
    .mm-data-table tbody tr {
      transition: background-color 180ms ease, transform 180ms ease;
    }
    .glass-table tbody tr:hover,
    .mm-data-table tbody tr:hover {
      background: rgba(255,255,255,0.74);
    }
    .glass-chip {
      border: 1px solid rgba(226, 232, 240, 0.95);
      background: rgba(248, 250, 252, 0.86);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.65);
    }
    .glass-action {
      border: 1px solid rgba(226, 232, 240, 0.92);
      background: rgba(255,255,255,0.82);
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
    }
    .glass-action-edit {
      border-color: rgba(251, 191, 36, 0.36);
      background: linear-gradient(135deg, rgba(255,244,214,0.92), rgba(255,255,255,0.82));
      color: #b45309;
    }
    .glass-action-delete {
      border-color: rgba(251, 113, 133, 0.28);
      background: rgba(255, 241, 242, 0.82);
      color: #e11d48;
    }
    .glass-pagination nav > div:first-child {
      display: none;
    }
    .glass-pagination nav > div:last-child {
      display: flex;
      justify-content: space-between;
      gap: 0.75rem;
      align-items: center;
      flex-wrap: wrap;
    }
  </style>
  @stack('head')
</head>
@php
  $navItems = [
    ['label' => 'Dashboard', 'route' => 'supervisor.dashboard', 'match' => 'supervisor.dashboard', 'icon' => 'dashboard'],
    ['label' => 'Validasi Unit Mobil', 'route' => 'supervisor.cars.index', 'match' => 'supervisor.cars.*', 'icon' => 'fact_check'],
    ['label' => 'Manajemen Pesanan', 'route' => 'supervisor.orders.index', 'match' => 'supervisor.orders.*', 'icon' => 'receipt_long'],
    ['label' => 'Penawaran', 'route' => 'supervisor.offers.index', 'match' => 'supervisor.offers.*', 'icon' => 'sell'],
    ['label' => 'Test Drive', 'route' => 'supervisor.testdrives.index', 'match' => 'supervisor.testdrives.*', 'icon' => 'event_available'],
    ['label' => 'Transaksi & Pembayaran', 'route' => 'supervisor.payments.index', 'match' => 'supervisor.payments.*', 'icon' => 'payments'],
    ['label' => 'Customer', 'route' => 'supervisor.customers.index', 'match' => 'supervisor.customers.*', 'icon' => 'groups'],
    ['label' => 'User Internal', 'route' => 'supervisor.users.index', 'match' => 'supervisor.users.*', 'icon' => 'badge'],
    ['label' => 'Review Customer', 'route' => 'supervisor.reviews.index', 'match' => 'supervisor.reviews.*', 'icon' => 'rate_review'],
    ['label' => 'Laporan', 'route' => 'supervisor.reports.index', 'match' => 'supervisor.reports.*', 'icon' => 'monitoring'],
    ['label' => 'Import Excel', 'route' => 'supervisor.imports.sales.create', 'match' => 'supervisor.imports.sales.*', 'icon' => 'upload_file'],
  ];
@endphp
<body class="min-h-screen bg-[linear-gradient(180deg,#eef4ff_0%,#f8fbff_28%,#ffffff_100%)] text-slate-900">
  <div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(11,26,64,0.18),transparent_26%),radial-gradient(circle_at_top_right,rgba(245,166,35,0.16),transparent_22%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.10),transparent_18%)]"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-[1600px] gap-6 px-4 py-4 md:px-6 md:py-6">
      <aside class="hidden w-[298px] shrink-0 lg:block">
        <div class="flex h-full flex-col overflow-visible rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)] backdrop-blur-[26px]">
          <div class="rounded-[1.6rem] bg-[linear-gradient(135deg,#08132e_0%,#102a63_56%,#f5a623_140%)] px-5 py-5 text-white shadow-[0_18px_40px_rgba(8,19,46,0.24)]">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-[#f7c35f]">Supervisor Workspace</p>
            <div class="mt-3 text-[28px] font-extrabold tracking-tight">Maharani Mobil</div>
          </div>

          <nav class="mt-5 flex-1 space-y-1.5">
            @foreach ($navItems as $item)
              @php
                $active = request()->routeIs($item['match'] ?? $item['route']);
              @endphp
              <a
                class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold transition {{ $active ? 'bg-[#08132e] text-white shadow-[0_18px_34px_rgba(8,19,46,0.18)]' : 'text-slate-600 hover:bg-white/80 hover:text-slate-900' }}"
                href="{{ route($item['route']) }}"
              >
                <span class="material-symbols-outlined text-[20px] {{ $active ? 'text-[#f7c35f]' : 'text-slate-400 group-hover:text-[#08132e]' }}">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
              </a>
            @endforeach
          </nav>

          <div class="mt-auto rounded-[1.4rem] border border-slate-200/80 bg-white/70 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Session</p>
            <p class="mt-2 text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'Supervisor' }}</p>
            <p class="mt-1 text-xs text-slate-500">Akses pengawasan operasional aktif.</p>
            <form class="mt-4" method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-white" type="submit">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Logout
              </button>
            </form>
          </div>
        </div>
      </aside>

      <div class="min-w-0 flex-1">
        <div class="sticky top-4 z-30 mb-6 overflow-visible rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-4 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur-[26px] md:px-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
              <div class="flex items-center gap-3 lg:hidden">
                <span class="material-symbols-outlined rounded-full bg-[#08132e] p-2 text-white">shield_person</span>
                <span class="font-headline text-lg font-extrabold text-slate-900">Maharani Mobil</span>
              </div>
              <h1 class="mt-1 font-headline text-[24px] font-extrabold tracking-tight text-slate-900 md:text-[28px]">{{ $pageTitle ?? 'Dashboard' }}</h1>
              <p class="mt-1 text-sm text-slate-500">Supervisor Control Center</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">calendar_month</span>
                {{ now()->format('d M Y') }}
              </div>
              @isset($dashboardNotifications)
                <x-dashboard-notification-bell :data="$dashboardNotifications" title="Notifikasi Supervisor" />
              @endisset
              <a class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-white" href="{{ route('supervisor.settings.edit') }}">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">settings</span>
                Setting
              </a>
              <div class="inline-flex h-11 min-w-11 items-center justify-center rounded-full bg-[linear-gradient(135deg,#08132e_0%,#102a63_100%)] px-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(8,19,46,0.18)]">
                {{ strtoupper(substr(auth()->user()->name ?? 'SP', 0, 2)) }}
              </div>
            </div>
          </div>
        </div>

        <main class="pb-6">
          @if (session('success'))
            <div class="mb-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-700 shadow-sm backdrop-blur-xl">
              {{ session('success') }}
            </div>
          @endif

          @if ($errors->any())
            <div class="mb-6 rounded-[1.2rem] border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 shadow-sm backdrop-blur-xl">
              Mohon cek kembali input yang Anda kirim.
            </div>
          @endif

          @yield('content')
        </main>
      </div>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
