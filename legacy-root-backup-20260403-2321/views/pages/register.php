<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Maharani Mobil | Register</title>
  <meta name="description" content="Daftar akun Maharani Mobil untuk booking test drive, ajukan penawaran, dan melacak pesanan."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface selection:bg-secondary-container selection:text-on-secondary-container min-h-screen flex flex-col">
  <main class="flex-grow flex flex-col md:flex-row">
    <div class="hidden md:flex md:w-1/2 relative overflow-hidden bg-primary items-center justify-center p-12">
      <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover opacity-60 mix-blend-luminosity" data-alt="Luxury automotive showroom with soft lights and deep navy tones" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAf-6IMrqJwbndDsHuNLlgGUIzUn-XJQTMFbEc0VRUyhlQTDXKj-cTorqbl7td_kNRTOqY2MWANqv5_pS0YH92RT_kb_rmYxGjWVbPS1SMtQw3Tj4jbJM8vC9BwQuYFftYBXkJyf_X34-tQdxqCV2Oud4QWHhR_32ZrpxkwGc46ioZE7NH-nvO_SUcrvqaHUhmO5ttt8FTMQXV-f5zitt6YWDIpqGpVKVXoQCsoDK-XI76_nyMpxnheGHlFHlaqroGhV6KvApKKA7c"/>
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/40 to-transparent"></div>
      </div>
      <div class="relative z-10 max-w-lg text-white">
        <div class="mb-8">
          <span class="text-secondary-container font-headline font-bold tracking-widest text-xs uppercase block mb-4">Join The Concierge</span>
          <h2 class="text-5xl font-black font-headline tracking-tighter leading-none mb-6">Your Next Drive Starts Here.</h2>
          <p class="text-on-primary-container text-lg leading-relaxed font-body">Create your account to unlock transparent listings, faster approvals, and concierge-level support.</p>
        </div>
        <div class="grid grid-cols-2 gap-6 pt-8 border-t border-white/10">
          <div>
            <div class="text-secondary-container font-headline font-extrabold text-2xl tracking-tight">175-Point</div>
            <div class="text-on-primary-container text-xs font-label tracking-widest uppercase">Inspection</div>
          </div>
          <div>
            <div class="text-secondary-container font-headline font-extrabold text-2xl tracking-tight">100%</div>
            <div class="text-on-primary-container text-xs font-label tracking-widest uppercase">Transparency</div>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full md:w-1/2 bg-surface flex items-center justify-center p-6 md:p-16 lg:p-24 relative overflow-y-auto">
      <div class="absolute top-12 left-12">
        <a class="text-2xl font-black text-[#1A2B4C] tracking-tighter font-headline" href="index.html">Maharani Mobil</a>
      </div>
      <div class="w-full max-w-md">
        <div class="mb-10">
          <h1 class="text-4xl font-extrabold text-primary tracking-tight font-headline mb-3">Create Account</h1>
          <p class="text-on-surface-variant font-body">Sign up to schedule test drives, make offers, and track payments.</p>
        </div>
        <!-- API: POST /api/auth/register -->
        <form class="space-y-6">
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="full_name">Full Name</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="full_name" name="full_name" placeholder="Nama lengkap" type="text"/>
          </div>
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="email">Email Address</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="email" name="email" placeholder="name@example.com" type="email"/>
          </div>
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="phone">Phone / WhatsApp</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="phone" name="phone" placeholder="08xx-xxxx-xxxx" type="tel"/>
          </div>
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="password">Password</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="password" name="password" placeholder="••••••••" type="password"/>
          </div>
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="password_confirm">Confirm Password</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="password_confirm" name="password_confirm" placeholder="••••••••" type="password"/>
          </div>
          <div class="flex items-start space-x-3 pt-2">
            <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary" id="terms" type="checkbox"/>
            <label class="text-sm font-body text-on-surface-variant" for="terms">I agree to the <a class="text-secondary font-bold hover:underline" href="terms.html">Terms</a> and <a class="text-secondary font-bold hover:underline" href="privacy.html">Privacy Policy</a>.</label>
          </div>
          <button class="w-full bg-primary text-white py-4 px-8 font-headline font-bold rounded-xl shadow-xl shadow-primary/10 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-primary/20 transition-all flex justify-center items-center group" type="submit">
            Create Account
            <span class="material-symbols-outlined ml-2 text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
          </button>
        </form>
        <div class="mt-10 text-center">
          <p class="text-on-surface-variant font-body">
            Already have an account?
            <a class="text-secondary font-bold hover:underline transition-all" href="login.html">Sign in</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <footer class="bg-[#031636] w-full py-12 mt-auto">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-12 w-full max-w-screen-2xl mx-auto">
      <div class="flex flex-col gap-4">
        <span class="text-white font-black italic text-xl">Maharani Mobil</span>
        <p class="text-slate-400 font-body text-xs uppercase tracking-widest leading-relaxed">
          The Digital Concierge for premium automotive experiences in Pekanbaru.
        </p>
      </div>
      <div class="flex flex-col gap-3">
        <span class="text-slate-500 font-label text-[10px] uppercase tracking-widest mb-2">Platform</span>
        <a class="text-slate-400 font-label text-xs uppercase tracking-widest hover:text-white underline transition-all" href="catalog.html">Catalog</a>
        <a class="text-slate-400 font-label text-xs uppercase tracking-widest hover:text-white underline transition-all" href="financing.html">Financing</a>
      </div>
      <div class="flex flex-col gap-3">
        <span class="text-slate-500 font-label text-[10px] uppercase tracking-widest mb-2">Legal</span>
        <a class="text-slate-400 font-label text-xs uppercase tracking-widest hover:text-white underline transition-all" href="privacy.html">Privacy Policy</a>
        <a class="text-slate-400 font-label text-xs uppercase tracking-widest hover:text-white underline transition-all" href="terms.html">Terms of Service</a>
      </div>
      <div class="flex flex-col gap-4">
        <span class="text-slate-500 font-label text-[10px] uppercase tracking-widest mb-2">Copyright</span>
        <p class="text-slate-400 font-label text-xs uppercase tracking-widest leading-relaxed">
          © 2026 Maharani Mobil Pekanbaru. The Digital Concierge.
        </p>
      </div>
    </div>
  </footer>
</body>
</html>

