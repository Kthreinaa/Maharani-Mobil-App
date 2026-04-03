<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Grafik Pendapatan | Owner</title>
  <meta name="description" content="Grafik pendapatan Maharani Mobil."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4"><a class="text-xl font-bold text-[#1A2B4C]" href="/dashboard-owner">Maharani Mobil</a></div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/dashboard-owner"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/owner-sales"><span class="material-symbols-outlined">bar_chart</span>Laporan Penjualan</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="/owner-revenue"><span class="material-symbols-outlined">payments</span>Grafik Pendapatan</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/owner-performance"><span class="material-symbols-outlined">insights</span>Analisis Performa</a>
    </nav>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <h1 class="text-3xl font-extrabold text-primary mb-6">Grafik Pendapatan</h1>
    <!-- API: GET /api/reports/revenue -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
      <div class="h-64 bg-surface-container-low rounded-xl flex items-center justify-center text-on-surface-variant">Revenue Chart Placeholder</div>
      <div class="mt-6 text-sm text-on-surface-variant">Peak revenue terjadi pada Oktober 2026.</div>
    </section>
  </main>
</body>
</html>


