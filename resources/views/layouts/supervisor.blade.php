<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $title ?? 'Supervisor Dashboard' }}</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
  @stack('head')
</head>
<body class="bg-slate-100 text-slate-900">
  <div class="flex min-h-screen">
    <aside class="w-64 bg-white border-r border-slate-200 p-5 hidden lg:flex lg:flex-col">
      <div class="text-xl font-extrabold text-slate-900 mb-8">Maharani Mobil</div>
      <nav class="flex-1 space-y-2 text-sm">
        <a class="block rounded-lg px-3 py-2 font-semibold text-slate-900 bg-slate-100" href="{{ route('supervisor.dashboard') }}">Dashboard</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.cars.index') }}">Manajemen Mobil</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.orders.index') }}">Manajemen Pesanan</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.payments.index') }}">Transaksi & Pembayaran</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.customers.index') }}">Customer</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.users.index') }}">User Internal</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.testdrives.index') }}">Test Drive</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.offers.index') }}">Penawaran</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.reports.index') }}">Laporan</a>
        <a class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="{{ route('supervisor.activity.index') }}">Activity Log</a>
      </nav>
      <form class="mt-6" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Logout</button>
      </form>
    </aside>

    <div class="flex-1">
      <div class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
        <div>
          <h1 class="text-xl font-bold">{{ $pageTitle ?? 'Dashboard' }}</h1>
          <p class="text-xs text-slate-500">Supervisor Control Center</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="hidden md:flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
            {{ now()->format('d M Y') }}
          </div>
          <div class="h-9 w-9 rounded-full bg-slate-200"></div>
        </div>
      </div>

      <main class="p-6">
        @if (session('success'))
          <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
          </div>
        @endif
        @yield('content')
      </main>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
