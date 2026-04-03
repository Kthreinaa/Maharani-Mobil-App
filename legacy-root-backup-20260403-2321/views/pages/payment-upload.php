<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Bukti Pembayaran | Maharani Mobil</title>
  <meta name="description" content="Unggah bukti pembayaran untuk verifikasi transaksi."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  <header class="bg-slate-50/70 backdrop-blur-xl sticky top-0 z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] tracking-tighter font-headline" href="index.html">Maharani Mobil</a>
      <nav class="hidden md:flex items-center gap-8 font-headline tracking-tight">
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="catalog.html">Catalog</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="about.html">About Us</a>
        <a class="text-slate-500 hover:text-[#F5A623] transition-colors" href="financing.html">Financing</a>
      </nav>
      <div class="flex items-center gap-4">
        <a class="px-4 py-2 text-slate-500 hover:text-primary" href="login.html">Login</a>
        <a class="px-6 py-2 bg-primary text-white rounded-full font-bold" href="register.html">Register</a>
      </div>
    </div>
  </header>

  <main class="max-w-screen-2xl mx-auto w-full px-6 md:px-12 py-10 flex-grow">
    <h1 class="text-3xl font-extrabold text-primary mb-6">Upload Bukti Pembayaran</h1>
    <!-- API: POST /api/payments/upload -->
    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8 max-w-2xl">
      <div class="space-y-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Order ID</label>
          <input class="w-full rounded-xl border border-outline-variant p-4" value="#MM-0241" type="text"/>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Upload File</label>
          <div class="border-2 border-dashed border-outline-variant rounded-2xl p-6 text-center bg-surface-container-low">
            <span class="material-symbols-outlined text-3xl text-primary">cloud_upload</span>
            <p class="font-semibold text-primary mt-2">Drag & drop bukti pembayaran</p>
            <p class="text-xs text-on-surface-variant">Format: JPG, PNG, PDF</p>
            <input class="mt-4" type="file"/>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Catatan</label>
          <textarea class="w-full rounded-xl border border-outline-variant p-4 h-24" placeholder="Optional"></textarea>
        </div>
        <button class="w-full bg-primary text-white py-4 rounded-xl font-bold">Kirim untuk Verifikasi</button>
      </div>
    </section>
  </main>

  <footer class="bg-[#031636] w-full py-10 mt-auto text-white text-xs uppercase tracking-widest">
    <div class="max-w-screen-2xl mx-auto px-8 flex flex-col md:flex-row justify-between gap-4">
      <span>© 2026 Maharani Mobil Pekanbaru</span>
      <div class="flex gap-6">
        <a class="hover:text-secondary-container" href="privacy.html">Privacy</a>
        <a class="hover:text-secondary-container" href="terms.html">Terms</a>
      </div>
    </div>
  </footer>
</body>
</html>

