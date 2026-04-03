<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manajemen Produk | Marketing</title>
  <meta name="description" content="Kelola daftar produk mobil bekas Maharani Mobil."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4">
      <a class="text-xl font-bold text-[#1A2B4C]" href="/dashboard-marketing">Maharani Mobil</a>
    </div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/dashboard-marketing"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="/marketing-products"><span class="material-symbols-outlined">directions_car</span>Manajemen Produk</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-orders"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-offers"><span class="material-symbols-outlined">sell</span>Manajemen Penawaran</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-upload"><span class="material-symbols-outlined">upload</span>Upload Produk</a>
    </nav>
    <div class="mt-auto pt-6 border-t border-slate-100">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" href="/login"><span class="material-symbols-outlined">logout</span>Logout</a>
    </div>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-primary">Manajemen Produk</h1>
        <p class="text-on-surface-variant">Edit informasi unit, status, dan stok yang tampil di katalog.</p>
      </div>
      <a class="px-6 py-2 rounded-full bg-primary text-white font-semibold" href="/marketing-upload">Tambah Unit</a>
    </header>

    <!-- API: GET /api/cars -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
      <div class="px-8 py-6 border-b border-slate-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined text-primary">search</span>
          <input class="border-none focus:ring-0 text-sm" placeholder="Cari nama mobil atau nomor unit" type="text"/>
        </div>
        <div class="flex items-center gap-3 text-sm text-on-surface-variant">
          <span>Status</span>
          <select class="rounded-full border border-outline-variant px-4 py-2">
            <option>All</option>
            <option>Available</option>
            <option>Reserved</option>
            <option>Sold</option>
          </select>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-on-surface-variant text-xs font-black uppercase tracking-widest">
              <th class="px-8 py-4">Unit</th>
              <th class="px-8 py-4">Harga</th>
              <th class="px-8 py-4">KM</th>
              <th class="px-8 py-4">Status</th>
              <th class="px-8 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-8 py-5 font-bold text-primary">Toyota Fortuner VRZ 2022</td>
              <td class="px-8 py-5">Rp 545.000.000</td>
              <td class="px-8 py-5">14.200 KM</td>
              <td class="px-8 py-5"><span class="status-available px-3 py-1 rounded-full text-xs font-bold">Available</span></td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2">
                  <button class="px-3 py-1 rounded-lg border border-outline-variant text-xs font-bold">Edit</button>
                  <button class="px-3 py-1 rounded-lg bg-error-container text-on-error-container text-xs font-bold">Delete</button>
                </div>
              </td>
            </tr>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-8 py-5 font-bold text-primary">Honda HR-V SE 2021</td>
              <td class="px-8 py-5">Rp 295.000.000</td>
              <td class="px-8 py-5">18.500 KM</td>
              <td class="px-8 py-5"><span class="status-reserved px-3 py-1 rounded-full text-xs font-bold">Reserved</span></td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2">
                  <button class="px-3 py-1 rounded-lg border border-outline-variant text-xs font-bold">Edit</button>
                  <button class="px-3 py-1 rounded-lg bg-error-container text-on-error-container text-xs font-bold">Delete</button>
                </div>
              </td>
            </tr>
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-8 py-5 font-bold text-primary">Mitsubishi Pajero Dakar 2019</td>
              <td class="px-8 py-5">Rp 415.000.000</td>
              <td class="px-8 py-5">65.000 KM</td>
              <td class="px-8 py-5"><span class="status-sold px-3 py-1 rounded-full text-xs font-bold">Sold</span></td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2">
                  <button class="px-3 py-1 rounded-lg border border-outline-variant text-xs font-bold">Edit</button>
                  <button class="px-3 py-1 rounded-lg bg-error-container text-on-error-container text-xs font-bold">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-6 border-t border-slate-50 text-sm text-on-surface-variant">API Update: PUT /api/cars/{id} • DELETE /api/cars/{id}</div>
    </section>
  </main>
</body>
</html>


