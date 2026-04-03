<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Test Drive | Maharani Mobil</title>
  <meta name="description" content="Jadwalkan test drive mobil bekas pilihan Anda di Maharani Mobil Pekanbaru."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  <header class="bg-slate-50/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] tracking-tighter font-headline" href="/">Maharani Mobil</a>
      <nav class="hidden md:flex items-center gap-8 font-headline tracking-tight">
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/catalog">Catalog</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/about">About Us</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="/financing">Financing</a>
      </nav>
      <div class="flex items-center gap-4">
        <a class="px-4 py-2 text-slate-500 hover:text-primary" href="/login">Login</a>
        <a class="px-6 py-2 bg-primary text-white rounded-full font-bold" href="/register">Register</a>
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    <nav class="flex items-center gap-2 text-on-surface-variant text-sm mb-6" aria-label="Breadcrumb">
      <a class="hover:text-primary" href="/">Home</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <a class="hover:text-primary" href="/catalog">Inventory</a>
      <span class="material-symbols-outlined text-xs">chevron_right</span>
      <span class="text-primary font-bold">Booking Test Drive</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <section class="lg:col-span-2 bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8">
        <h1 class="text-3xl font-extrabold text-primary mb-2">Buat Janji Test Drive</h1>
        <p class="text-on-surface-variant mb-8">Pilih tanggal dan jam. Kami akan menghubungi Anda untuk konfirmasi.</p>

        <!-- API: POST /api/test-drive -->
        <form class="space-y-6">
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Lengkap</label>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Nama lengkap" type="text"/>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tanggal</label>
              <input class="w-full rounded-xl border border-outline-variant p-4" type="date"/>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Jam</label>
              <select class="w-full rounded-xl border border-outline-variant p-4">
                <option>09:00</option>
                <option>11:00</option>
                <option>13:00</option>
                <option>15:00</option>
                <option>17:00</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Lokasi</label>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Alamat pengantaran atau showroom" type="text"/>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Catatan</label>
            <textarea class="w-full rounded-xl border border-outline-variant p-4 h-28" placeholder="Preferensi lokasi, rute, atau permintaan khusus"></textarea>
          </div>
          <button class="w-full bg-secondary-container text-on-secondary-fixed py-4 rounded-xl font-bold">Konfirmasi Booking</button>
        </form>
      </section>

      <aside class="space-y-6">
        <div class="bg-primary text-white rounded-2xl p-6 shadow-xl">
          <h2 class="text-lg font-bold mb-4">Unit Pilihan</h2>
          <div class="space-y-2 text-sm text-blue-100">
            <p class="font-semibold text-white">Toyota Camry 2.5 V Hybrid 2022</p>
            <p>Pekanbaru • 12.450 KM • Automatic</p>
            <p class="text-secondary-container font-black text-lg">Rp 545.000.000</p>
          </div>
        </div>
        <div class="bg-surface-container-low rounded-2xl p-6">
          <h3 class="font-bold text-primary mb-3">Reminder</h3>
          <ul class="space-y-2 text-sm text-on-surface-variant">
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">notifications</span> H-1 akan ada pengingat via WhatsApp.</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">verified</span> Bawa SIM & KTP untuk verifikasi.</li>
            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">location_on</span> Pilih lokasi yang nyaman untuk Anda.</li>
          </ul>
        </div>
      </aside>
    </div>
  </main>

  <footer class="bg-[#031636] w-full py-10 mt-auto text-white text-xs uppercase tracking-widest">
    <div class="max-w-screen-2xl mx-auto px-8 flex flex-col md:flex-row justify-between gap-4">
      <span>© 2026 Maharani Mobil Pekanbaru</span>
      <div class="flex gap-6">
        <a class="hover:text-secondary-container" href="/privacy">Privacy</a>
        <a class="hover:text-secondary-container" href="/terms">Terms</a>
      </div>
    </div>
  </footer>
</body>
</html>


