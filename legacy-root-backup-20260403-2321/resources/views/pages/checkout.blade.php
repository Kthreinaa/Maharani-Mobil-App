<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout | Maharani Mobil</title>
  <meta name="description" content="Checkout pembelian mobil bekas, detail pembayaran, dan verifikasi."/>
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
    <h1 class="text-3xl font-extrabold text-primary mb-6">Checkout</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <section class="lg:col-span-2 bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8">
        <!-- API: POST /api/orders -->
        <h2 class="text-xl font-bold text-primary mb-4">Detail Pembelian</h2>
        <div class="flex items-center gap-4 border border-outline-variant/30 rounded-2xl p-4">
          <img class="w-28 h-20 object-cover rounded-xl" alt="Toyota Camry" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqpa-9vFSQn1mJuQq2vGh12zAllpQNwqaENPHPY1fXH2SaBkpTL82at4NrhLq8KKVbmZETBD8XXmvA2V5YlETKM6d-OGPgVo_tm7twSejZEHKdTJTUXEwKcBsuyH_YbToPCVfx_rGOGvpFG27m1vtKFA9O8u_D9zCahxfno-9i39BnnTZI-ZWHoyRCvklqBvobHAk97nqHb590I9PpMQEjvKfMp6TZ0Yel6_HqloVB-Dqqi1t-mPXf3dIrN01RtOI2HiIEc7Hx9EY"/>
          <div class="flex-1">
            <p class="font-bold text-primary">Toyota Camry 2.5 V Hybrid 2022</p>
            <p class="text-sm text-on-surface-variant">Automatic • 12.450 KM • Pekanbaru</p>
          </div>
          <div class="text-right">
            <p class="text-xs text-on-surface-variant uppercase tracking-widest">Harga</p>
            <p class="text-lg font-black text-secondary-container">Rp 545.000.000</p>
          </div>
        </div>

        <div class="mt-8">
          <h3 class="text-lg font-bold text-primary mb-4">Informasi Pembeli</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Nama lengkap" type="text"/>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Nomor WhatsApp" type="tel"/>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Email" type="email"/>
            <input class="w-full rounded-xl border border-outline-variant p-4" placeholder="Kota" type="text"/>
          </div>
        </div>

        <div class="mt-8">
          <h3 class="text-lg font-bold text-primary mb-4">Metode Pembayaran</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="border border-outline-variant rounded-xl p-4 flex items-center gap-3">
              <input type="radio" name="payment"/> Cash
            </label>
            <label class="border border-outline-variant rounded-xl p-4 flex items-center gap-3">
              <input type="radio" name="payment"/> Transfer
            </label>
            <label class="border border-outline-variant rounded-xl p-4 flex items-center gap-3">
              <input type="radio" name="payment"/> Virtual Account
            </label>
          </div>
        </div>

        <div class="mt-10 flex justify-end">
          <a class="px-6 py-3 rounded-xl bg-primary text-white font-semibold" href="/payment">Lanjut ke Pembayaran</a>
        </div>
      </section>

      <aside class="space-y-6">
        <div class="bg-primary text-white rounded-2xl p-6">
          <h2 class="text-lg font-bold mb-4">Ringkasan</h2>
          <div class="space-y-2 text-sm text-blue-100">
            <div class="flex justify-between">
              <span>Harga Unit</span>
              <span>Rp 545.000.000</span>
            </div>
            <div class="flex justify-between">
              <span>Biaya Admin</span>
              <span>Rp 1.500.000</span>
            </div>
            <div class="border-t border-white/20 pt-2 flex justify-between font-bold text-white">
              <span>Total</span>
              <span>Rp 546.500.000</span>
            </div>
          </div>
        </div>
        <div class="bg-surface-container-low rounded-2xl p-6">
          <h3 class="font-bold text-primary mb-3">Alur Setelah Checkout</h3>
          <ol class="text-sm text-on-surface-variant space-y-2">
            <li>1. Pembayaran diterima</li>
            <li>2. Verifikasi supervisor</li>
            <li>3. Serah terima unit</li>
          </ol>
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


