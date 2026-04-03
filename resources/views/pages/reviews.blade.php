<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Review & Testimoni | Maharani Mobil</title>
  <meta name="description" content="Review pelanggan Maharani Mobil Pekanbaru."/>
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
    <div class="text-center mb-12">
      <h1 class="text-4xl font-extrabold text-primary mb-3">Review & Testimoni</h1>
      <p class="text-on-surface-variant">Ribuan pelanggan mempercayai Maharani Mobil untuk transaksi aman dan nyaman.</p>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
        <div class="flex gap-1 mb-4">
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
        </div>
        <p class="text-on-surface-variant">Unit sesuai deskripsi, proses cepat, dan team sangat responsif.</p>
        <p class="mt-4 font-bold text-primary">Dian P.</p>
      </div>
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
        <div class="flex gap-1 mb-4">
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
        </div>
        <p class="text-on-surface-variant">Penjelasan kondisi mobil sangat detail dan jujur. Recommended.</p>
        <p class="mt-4 font-bold text-primary">Rizky A.</p>
      </div>
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-6">
        <div class="flex gap-1 mb-4">
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
        </div>
        <p class="text-on-surface-variant">Test drive langsung ke rumah, sangat membantu.</p>
        <p class="mt-4 font-bold text-primary">Salsa M.</p>
      </div>
    </section>

    <section class="mt-12 bg-surface-container-low rounded-2xl p-8">
      <h2 class="text-2xl font-bold text-primary mb-4">Tulis Review Anda</h2>
      <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input class="rounded-xl border border-outline-variant p-4" placeholder="Nama" type="text"/>
        <input class="rounded-xl border border-outline-variant p-4" placeholder="Email" type="email"/>
        <textarea class="rounded-xl border border-outline-variant p-4 md:col-span-2 h-28" placeholder="Ceritakan pengalaman Anda"></textarea>
        <button class="md:col-span-2 bg-primary text-white py-3 rounded-xl font-bold">Kirim Review</button>
      </form>
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


