<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Produk | Marketing</title>
  <meta name="description" content="Upload unit mobil bekas dan kelola konten listing."/>
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
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-products"><span class="material-symbols-outlined">directions_car</span>Manajemen Produk</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-orders"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="/marketing-offers"><span class="material-symbols-outlined">sell</span>Manajemen Penawaran</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="/marketing-upload"><span class="material-symbols-outlined">upload</span>Upload Produk</a>
    </nav>
    <div class="mt-auto pt-6 border-t border-slate-100">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" href="/login"><span class="material-symbols-outlined">logout</span>Logout</a>
    </div>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-primary">Upload Produk Mobil</h1>
        <p class="text-on-surface-variant">Tambahkan unit baru ke katalog Maharani Mobil.</p>
      </div>
      <a class="px-6 py-2 rounded-full border border-outline-variant font-semibold hover:bg-surface-container-low transition-colors" href="/marketing-products">View Inventory</a>
    </header>

    <!-- API: POST /api/cars -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Mobil</label>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Toyota Fortuner 2.4 VRZ" type="text"/>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun</label>
              <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="2022" type="number"/>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Harga (IDR)</label>
              <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="450000000" type="number"/>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Kilometer</label>
              <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="12000" type="number"/>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Transmisi</label>
              <select class="w-full rounded-xl border border-outline-variant p-4">
                <option>Automatic</option>
                <option>Manual</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Deskripsi Jujur</label>
            <textarea class="w-full rounded-xl border border-outline-variant p-4 h-32" placeholder="Kondisi unit, kelebihan dan minus..."></textarea>
          </div>
          <div class="bg-surface-container-low rounded-2xl p-4 border border-outline-variant/40">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">SEO & Schema</p>
            <div class="space-y-3">
              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Meta Title</label>
                <input class="w-full rounded-xl border border-outline-variant p-3" placeholder="Toyota Camry 2.5 V Hybrid 2022 | Maharani Mobil" type="text"/>
              </div>
              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Meta Description</label>
                <textarea class="w-full rounded-xl border border-outline-variant p-3 h-20" placeholder="Detail unit lengkap, kondisi, dan harga"></textarea>
              </div>
              <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Slug URL</label>
                <input class="w-full rounded-xl border border-outline-variant p-3" placeholder="/toyota-camry-2022" type="text"/>
              </div>
              <label class="flex items-center gap-2 text-xs text-on-surface-variant">
                <input type="checkbox"/> Aktifkan schema markup Vehicle & Offer
              </label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Upload Foto (Multi Image)</label>
            <div class="border-2 border-dashed border-outline-variant rounded-2xl p-6 text-center bg-surface-container-low">
              <span class="material-symbols-outlined text-3xl text-primary">cloud_upload</span>
              <p class="font-semibold text-primary mt-2">Drag & drop foto mobil di sini</p>
              <p class="text-xs text-on-surface-variant">Atau klik untuk memilih file</p>
              <input class="mt-4" multiple type="file"/>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Status Unit</label>
            <div class="flex gap-3">
              <button class="px-4 py-2 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-bold">Available</button>
              <button class="px-4 py-2 rounded-full bg-secondary-fixed text-on-secondary-fixed font-bold">Reserved</button>
              <button class="px-4 py-2 rounded-full bg-error-container text-on-error-container font-bold">Sold</button>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Video Preview (TikTok/IG)</label>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="https://www.instagram.com/..." type="url"/>
          </div>
        </div>
      </div>
      <div class="mt-8 flex items-center justify-between border-t border-outline-variant/40 pt-6">
        <div class="text-sm text-on-surface-variant">Pastikan data lengkap untuk meningkatkan kepercayaan customer.</div>
        <div class="flex gap-3">
          <button class="px-6 py-3 rounded-xl border border-outline-variant font-semibold">Save Draft</button>
          <button class="px-6 py-3 rounded-xl bg-primary text-white font-semibold">Publish Listing</button>
        </div>
      </div>
    </section>
  </main>
</body>
</html>


