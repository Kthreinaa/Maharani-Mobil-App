<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Maharani Mobil | Authentication</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#031636",
            "on-primary-fixed": "#071b3b",
            "tertiary-fixed-dim": "#88d982",
            "on-secondary-container": "#6b4500",
            "secondary-fixed": "#ffddb4",
            "on-surface": "#191c1d",
            "surface-container-low": "#f3f4f5",
            "on-primary-container": "#8293ba",
            "inverse-on-surface": "#f0f1f2",
            "on-background": "#191c1d",
            "surface-container-highest": "#e1e3e4",
            "surface-tint": "#4e5e82",
            "surface-bright": "#f8f9fa",
            "on-primary": "#ffffff",
            "tertiary-fixed": "#a3f69c",
            "error": "#ba1a1a",
            "tertiary": "#001d03",
            "primary-container": "#1a2b4c",
            "surface-container-high": "#e7e8e9",
            "error-container": "#ffdad6",
            "on-error-container": "#93000a",
            "inverse-surface": "#2e3132",
            "surface-dim": "#d9dadb",
            "secondary": "#835500",
            "on-secondary-fixed": "#291800",
            "primary-fixed-dim": "#b6c6f0",
            "inverse-primary": "#b6c6f0",
            "secondary-fixed-dim": "#ffb955",
            "on-tertiary-fixed": "#002204",
            "on-error": "#ffffff",
            "surface-container-lowest": "#ffffff",
            "surface": "#f8f9fa",
            "on-primary-fixed-variant": "#364669",
            "background": "#f8f9fa",
            "outline": "#75777f",
            "tertiary-container": "#003408",
            "on-secondary": "#ffffff",
            "on-tertiary-fixed-variant": "#005312",
            "primary-fixed": "#d8e2ff",
            "outline-variant": "#c5c6cf",
            "surface-container": "#edeeef",
            "on-tertiary": "#ffffff",
            "surface-variant": "#e1e3e4",
            "on-surface-variant": "#44474e",
            "secondary-container": "#feae2c",
            "on-tertiary-container": "#55a454",
            "on-secondary-fixed-variant": "#633f00"
          },
          borderRadius: {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          fontFamily: {
            "headline": ["Plus Jakarta Sans"],
            "body": ["Inter"],
            "label": ["Inter"]
          }
        }
      }
    };
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-surface selection:bg-secondary-container selection:text-on-secondary-container min-h-screen flex flex-col">
  <main class="flex-grow flex flex-col md:flex-row">
    <!-- Visual Column -->
    <div class="hidden md:flex md:w-1/2 relative overflow-hidden bg-primary items-center justify-center p-12">
      <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover opacity-60 mix-blend-luminosity" data-alt="Luxurious dark blue sports car parked in a minimalist modern showroom with dramatic architectural lighting and soft reflections" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAf-6IMrqJwbndDsHuNLlgGUIzUn-XJQTMFbEc0VRUyhlQTDXKj-cTorqbl7td_kNRTOqY2MWANqv5_pS0YH92RT_kb_rmYxGjWVbPS1SMtQw3Tj4jbJM8vC9BwQuYFftYBXkJyf_X34-tQdxqCV2Oud4QWHhR_32ZrpxkwGc46ioZE7NH-nvO_SUcrvqaHUhmO5ttt8FTMQXV-f5zitt6YWDIpqGpVKVXoQCsoDK-XI76_nyMpxnheGHlFHlaqroGhV6KvApKKA7c"/>
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/40 to-transparent"></div>
      </div>
      <div class="relative z-10 max-w-lg text-white">
        <div class="mb-8">
          <span class="text-secondary-container font-headline font-bold tracking-widest text-xs uppercase block mb-4">The Digital Concierge</span>
          <h2 class="text-5xl font-black font-headline tracking-tighter leading-none mb-6">Experience Automotive Elegance.</h2>
          <p class="text-on-primary-container text-lg leading-relaxed font-body">Access your personalized dashboard, manage your dream inventory, and connect with our elite concierge service.</p>
        </div>
        <div class="grid grid-cols-2 gap-6 pt-8 border-t border-white/10">
          <div>
            <div class="text-secondary-container font-headline font-extrabold text-2xl tracking-tight">500+</div>
            <div class="text-on-primary-container text-xs font-label tracking-widest uppercase">Premium Cars</div>
          </div>
          <div>
            <div class="text-secondary-container font-headline font-extrabold text-2xl tracking-tight">24/7</div>
            <div class="text-on-primary-container text-xs font-label tracking-widest uppercase">Expert Support</div>
          </div>
        </div>
      </div>
    </div>
    <!-- Form Column -->
    <div class="w-full md:w-1/2 bg-surface flex items-center justify-center p-6 md:p-16 lg:p-24 relative overflow-y-auto">
      <div class="absolute top-12 left-12">
        <a class="text-2xl font-black text-[#1A2B4C] tracking-tighter font-headline" href="/">Maharani Mobil</a>
      </div>
      <div class="w-full max-w-md">
        <div class="mb-12">
          <h1 class="text-4xl font-extrabold text-primary tracking-tight font-headline mb-3">Welcome Back</h1>
          <p class="text-on-surface-variant font-body">Sign in to your account to continue your journey.</p>
        </div>
        <form class="space-y-6">
          <div class="space-y-2">
            <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="email">Email Address</label>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="email" name="email" placeholder="name@example.com" type="email"/>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label class="block text-xs font-label font-semibold text-on-surface-variant uppercase tracking-widest ml-1" for="password">Password</label>
              <a class="text-xs font-label font-bold text-secondary hover:text-on-secondary-container transition-colors uppercase tracking-widest" href="#">Forgot?</a>
            </div>
            <input class="w-full px-0 py-3 bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 transition-all text-on-surface placeholder:text-outline font-body text-base outline-none" id="password" name="password" placeholder="••••••••" type="password"/>
          </div>
          <div class="flex items-center space-x-3 pt-2">
            <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary" id="remember" type="checkbox"/>
            <label class="text-sm font-body text-on-surface-variant select-none" for="remember">Remember this device</label>
          </div>
          <button class="w-full bg-primary text-white py-4 px-8 font-headline font-bold rounded-xl shadow-xl shadow-primary/10 hover:translate-y-[-2px] hover:shadow-2xl hover:shadow-primary/20 transition-all flex justify-center items-center group" type="submit">
            Sign In
            <span class="material-symbols-outlined ml-2 text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
          </button>
        </form>
        <div class="relative my-12">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-outline-variant/30"></div>
          </div>
          <div class="relative flex justify-center text-xs font-label uppercase tracking-widest">
            <span class="px-4 bg-surface text-on-surface-variant">Or continue with</span>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <button class="flex items-center justify-center px-6 py-3 border border-outline-variant rounded-xl hover:bg-surface-container-low transition-colors font-body font-semibold text-on-surface group" type="button">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.8-5.5 3.8A6.2 6.2 0 0 1 5.8 12 6.2 6.2 0 0 1 12 5.8c1.8 0 3 0.8 3.7 1.5l2.5-2.4C16.7 3.5 14.6 2.6 12 2.6A9.4 9.4 0 0 0 2.6 12 9.4 9.4 0 0 0 12 21.4c5.4 0 9-3.8 9-9.2 0-.6-.1-1.1-.2-1.6H12z"/>
              <path fill="#4285F4" d="M21.8 12.2c0-.5-.1-1-.2-1.5H12v3.9h5.5c-.3 1.4-1.6 3.8-5.5 3.8A6.2 6.2 0 0 1 5.8 12c0-3.4 2.7-6.2 6.2-6.2 1.8 0 3 .8 3.7 1.5l2.5-2.4C16.7 3.5 14.6 2.6 12 2.6A9.4 9.4 0 0 0 2.6 12 9.4 9.4 0 0 0 12 21.4c5.4 0 9-3.8 9-9.2z"/>
              <path fill="#FBBC05" d="M5.1 14.4l-3.2 2.5A9.4 9.4 0 0 0 12 21.4c2.6 0 4.7-.9 6.3-2.4l-3.1-2.4c-.9.6-2 1-3.2 1a6.2 6.2 0 0 1-5.9-4.2z"/>
              <path fill="#34A853" d="M5.1 9.6A6.1 6.1 0 0 1 12 5.8c1.8 0 3 .8 3.7 1.5l2.5-2.4C16.7 3.5 14.6 2.6 12 2.6A9.4 9.4 0 0 0 2.6 12c0 1.6.4 3.1 1.1 4.4l3.4-2.8z"/>
            </svg>
            Google
          </button>
          <button class="flex items-center justify-center px-6 py-3 border border-outline-variant rounded-xl hover:bg-surface-container-low transition-colors font-body font-semibold text-on-surface group" type="button">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="#111827" d="M16.2 13.4c0-1.8 1-3.2 2.4-4-1.3-1.8-3.3-2-4-2.1-1.7-.2-3.3 1-4.1 1-0.9 0-2.2-1-3.7-1-1.9.1-3.6 1.1-4.6 2.8-2 3.4-.5 8.4 1.4 11.1.9 1.3 2 2.8 3.5 2.7 1.4-.1 1.9-.9 3.6-.9s2.1.9 3.7.9c1.5 0 2.5-1.4 3.4-2.7 1-1.5 1.4-3 1.5-3.1-.1 0-3.1-1.2-3.1-4.7zm-2.4-7.6c.7-.9 1.2-2.1 1.1-3.3-1 .1-2.2.7-2.9 1.6-.6.7-1.2 2-1.1 3.1 1.1.1 2.2-.5 2.9-1.4z"/>
            </svg>
            Apple
          </button>
        </div>
        <div class="mt-12 text-center">
          <p class="text-on-surface-variant font-body">
            Don't have an account?
            <a class="text-secondary font-bold hover:underline transition-all" href="/register">Create an account</a>
          </p>
        </div>
      </div>
    </div>
  </main>
  <footer class="bg-[#031636] w-full py-12 mt-auto">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-12 w-full max-w-screen-2xl mx-auto">
      <div class="flex flex-col gap-4">
        <span class="text-white font-black italic text-xl">Maharani Mobil</span>
        <p class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest leading-relaxed">
          The Digital Concierge for premium automotive experiences in Pekanbaru.
        </p>
      </div>
      <div class="flex flex-col gap-3">
        <span class="text-slate-500 font-['Inter'] text-[10px] uppercase tracking-widest mb-2">Platform</span>
        <a class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest hover:text-white underline transition-all" href="/catalog">Catalog</a>
        <a class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest hover:text-white underline transition-all" href="/financing">Financing</a>
      </div>
      <div class="flex flex-col gap-3">
        <span class="text-slate-500 font-['Inter'] text-[10px] uppercase tracking-widest mb-2">Legal</span>
        <a class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest hover:text-white underline transition-all" href="/privacy">Privacy Policy</a>
        <a class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest hover:text-white underline transition-all" href="/terms">Terms of Service</a>
      </div>
      <div class="flex flex-col gap-4">
        <span class="text-slate-500 font-['Inter'] text-[10px] uppercase tracking-widest mb-2">Copyright</span>
        <p class="text-slate-400 font-['Inter'] text-xs uppercase tracking-widest leading-relaxed">
          © 2026 Maharani Mobil Pekanbaru. The Digital Concierge.
        </p>
      </div>
    </div>
  </footer>
</body>
</html>
