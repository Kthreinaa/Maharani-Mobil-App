<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tracking Pesanan | Maharani Mobil</title>
  <meta name="description" content="Lacak status pesanan: pending, verified, completed."/>
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
    <h1 class="text-3xl font-extrabold text-primary mb-6">Tracking Pesanan</h1>
    <!-- API: GET /api/orders/{id} -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8 max-w-3xl">
      <div class="flex items-center justify-between mb-6">
        <div>
          <p class="text-on-surface-variant text-sm">Order ID</p>
          <p class="text-xl font-bold text-primary">#MM-0241</p>
        </div>
        <span class="bg-secondary-fixed text-on-secondary-fixed px-4 py-2 rounded-full text-xs font-bold">Pending</span>
      </div>
      <ol class="space-y-6">
        <li class="flex items-start gap-4">
          <span class="material-symbols-outlined text-secondary">radio_button_checked</span>
          <div>
            <p class="font-bold text-primary">Pembayaran Diterima</p>
            <p class="text-sm text-on-surface-variant">Menunggu verifikasi supervisor.</p>
          </div>
        </li>
        <li class="flex items-start gap-4 opacity-60">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
          <div>
            <p class="font-bold text-primary">Verifikasi Pembayaran</p>
            <p class="text-sm text-on-surface-variant">Validasi bukti transfer oleh tim.</p>
          </div>
        </li>
        <li class="flex items-start gap-4 opacity-60">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
          <div>
            <p class="font-bold text-primary">Serah Terima</p>
            <p class="text-sm text-on-surface-variant">Penjadwalan pengiriman unit.</p>
          </div>
        </li>
      </ol>
      <div class="mt-8 flex justify-end">
        <a class="px-6 py-3 rounded-xl bg-secondary-container text-on-secondary-fixed font-semibold" href="/order-tracking">Refresh Status</a>
      </div>
    </section>
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


