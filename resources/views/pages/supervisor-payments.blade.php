<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verifikasi Pembayaran | Supervisor</title>
  <meta name="description" content="Verifikasi bukti pembayaran pelanggan."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4"><a class="text-xl font-bold text-[#1A2B4C]" href="/dashboard-supervisor">Maharani Mobil</a></div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/dashboard-supervisor"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="/supervisor-payments"><span class="material-symbols-outlined">verified</span>Verifikasi Pembayaran</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/supervisor-transactions"><span class="material-symbols-outlined">receipt_long</span>Manajemen Transaksi</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/supervisor-users"><span class="material-symbols-outlined">group</span>Manajemen User</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/supervisor-activity"><span class="material-symbols-outlined">timeline</span>Monitoring Aktivitas</a>
    </nav>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <h1 class="text-3xl font-extrabold text-primary mb-6">Verifikasi Pembayaran</h1>
    <!-- API: GET /api/payments -->
    <!-- API: PATCH /api/payments/{id}/verify -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="mm-data-table w-full text-left">
          <thead>
            <tr class="text-on-surface-variant text-xs font-black uppercase tracking-widest">
              <th class="px-8 py-4">Payment ID</th>
              <th class="px-8 py-4">Order</th>
              <th class="px-8 py-4">Jumlah</th>
              <th class="px-8 py-4">Status</th>
              <th class="px-8 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-8 py-5 font-semibold text-primary">PAY-8891</td>
              <td class="px-8 py-5">#MM-0241</td>
              <td class="px-8 py-5">Rp 546.500.000</td>
              <td class="px-8 py-5"><span class="bg-secondary-fixed text-on-secondary-fixed px-3 py-1 rounded-full text-xs font-bold">Pending</span></td>
              <td class="px-8 py-5 text-right">
                <button class="px-3 py-1 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed text-xs font-bold">Verify</button>
              </td>
            </tr>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-8 py-5 font-semibold text-primary">PAY-8890</td>
              <td class="px-8 py-5">#MM-0240</td>
              <td class="px-8 py-5">Rp 295.000.000</td>
              <td class="px-8 py-5"><span class="bg-tertiary-fixed text-on-tertiary-fixed px-3 py-1 rounded-full text-xs font-bold">Verified</span></td>
              <td class="px-8 py-5 text-right">
                <button class="px-3 py-1 rounded-lg border border-outline-variant text-xs font-bold">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>


