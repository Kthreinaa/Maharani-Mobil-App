<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Monitoring Aktivitas | Supervisor</title>
  <meta name="description" content="Monitoring aktivitas user dan perubahan data."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4"><a class="text-xl font-bold text-[#1A2B4C]" href="dashboard-supervisor.html">Maharani Mobil</a></div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="dashboard-supervisor.html"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-payments.html"><span class="material-symbols-outlined">verified</span>Verifikasi Pembayaran</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-transactions.html"><span class="material-symbols-outlined">receipt_long</span>Manajemen Transaksi</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="supervisor-users.html"><span class="material-symbols-outlined">group</span>Manajemen User</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="supervisor-activity.html"><span class="material-symbols-outlined">timeline</span>Monitoring Aktivitas</a>
    </nav>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <h1 class="text-3xl font-extrabold text-primary mb-6">Monitoring Aktivitas</h1>
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6 space-y-4">
      <div class="flex items-start gap-4">
        <span class="material-symbols-outlined text-secondary">update</span>
        <div>
          <p class="font-bold text-primary">Marketing menambah unit baru</p>
          <p class="text-sm text-on-surface-variant">Toyota Fortuner 2022 • 10 menit yang lalu</p>
        </div>
      </div>
      <div class="flex items-start gap-4">
        <span class="material-symbols-outlined text-secondary">person_add</span>
        <div>
          <p class="font-bold text-primary">User baru terdaftar</p>
          <p class="text-sm text-on-surface-variant">Nadia Putri • 1 jam yang lalu</p>
        </div>
      </div>
      <div class="flex items-start gap-4">
        <span class="material-symbols-outlined text-secondary">payments</span>
        <div>
          <p class="font-bold text-primary">Pembayaran masuk</p>
          <p class="text-sm text-on-surface-variant">Order #MM-0241 • Menunggu verifikasi</p>
        </div>
      </div>
    </section>
  </main>
</body>
</html>

