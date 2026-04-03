<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Supervisor Dashboard | Maharani Mobil</title>
  <meta name="description" content="Dashboard supervisor: verifikasi pembayaran, validasi data, dan monitoring aktivitas."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4">
      <a class="text-xl font-bold text-[#1A2B4C]" href="dashboard-supervisor.html">Maharani Mobil</a>
    </div>
    <nav class="flex-1 space-y-2">
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="dashboard-supervisor.html"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-payments.html"><span class="material-symbols-outlined">verified</span>Verifikasi Pembayaran</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-transactions.html"><span class="material-symbols-outlined">receipt_long</span>Manajemen Transaksi</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-users.html"><span class="material-symbols-outlined">group</span>Manajemen User</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-activity.html"><span class="material-symbols-outlined">timeline</span>Monitoring Aktivitas</a>
    </nav>
    <div class="mt-auto pt-6 border-t border-slate-100">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" href="login.html"><span class="material-symbols-outlined">logout</span>Logout</a>
    </div>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-primary">Supervisor Dashboard</h1>
        <p class="text-on-surface-variant">Kontrol kualitas data, verifikasi transaksi, dan aktivitas harian.</p>
      </div>
      <div class="flex items-center gap-3">
        <button class="px-4 py-2 rounded-full border border-outline-variant text-sm font-semibold">Export</button>
        <button class="px-4 py-2 rounded-full bg-primary text-white text-sm font-semibold">New Alert</button>
      </div>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-primary p-6 rounded-2xl text-white">
        <p class="text-xs uppercase tracking-widest text-blue-200">Pending Verification</p>
        <p class="text-4xl font-black mt-4">18</p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow-xl shadow-blue-900/5">
        <p class="text-xs uppercase tracking-widest text-on-surface-variant">Active Transactions</p>
        <p class="text-4xl font-black text-primary mt-4">42</p>
      </div>
      <div class="bg-secondary-container p-6 rounded-2xl text-on-secondary-fixed">
        <p class="text-xs uppercase tracking-widest">Data Issues</p>
        <p class="text-4xl font-black text-primary mt-4">6</p>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
        <h2 class="text-xl font-bold text-primary mb-4">Verifikasi Pembayaran Terbaru</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between border border-outline-variant/40 rounded-xl p-4">
            <div>
              <p class="font-bold text-primary">#MM-0241</p>
              <p class="text-sm text-on-surface-variant">Toyota Camry 2022 • Rp 546.5jt</p>
            </div>
            <a class="px-4 py-2 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-semibold" href="supervisor-payments.html">Review</a>
          </div>
          <div class="flex items-center justify-between border border-outline-variant/40 rounded-xl p-4">
            <div>
              <p class="font-bold text-primary">#MM-0240</p>
              <p class="text-sm text-on-surface-variant">Honda HR-V 2021 • Rp 295jt</p>
            </div>
            <a class="px-4 py-2 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-semibold" href="supervisor-payments.html">Review</a>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
        <h2 class="text-xl font-bold text-primary mb-4">Monitoring Aktivitas</h2>
        <ul class="space-y-3 text-sm text-on-surface-variant">
          <li>Marketing menambahkan unit baru: Toyota Fortuner 2022.</li>
          <li>User baru mendaftar: Angga H.</li>
          <li>Offer masuk untuk Honda Civic RS.</li>
        </ul>
        <a class="inline-block mt-4 text-secondary font-bold" href="supervisor-activity.html">Lihat semua aktivitas</a>
      </div>
    </section>
  </main>
</body>
</html>

