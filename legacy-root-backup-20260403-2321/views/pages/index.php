<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Maharani Mobil Pekanbaru | The Digital Concierge</title>
  <meta name="description" content="Marketplace mobil bekas terpercaya di Pekanbaru. Unit terverifikasi, transparan, dan proses test drive hingga serah terima yang jelas."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface font-body text-on-surface">
  <!-- TopNavBar -->
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl docked full-width top-0 sticky z-50">
    <nav class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto font-headline tracking-tight">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter" href="index.html">Maharani Mobil</a>
      <div class="hidden md:flex items-center space-x-8">
        <a class="text-[#1A2B4C] font-bold border-b-2 border-[#F5A623] pb-1" href="catalog.html">Catalog</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="about.html">About Us</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="financing.html">Financing</a>
      </div>
      <div class="flex items-center gap-4">
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300 px-4 py-2" href="login.html">Login</a>
        <a class="bg-primary text-white px-6 py-2 rounded-full font-bold hover:scale-95 transition-transform duration-300" href="register.html">Register</a>
      </div>
    </nav>
  </header>

  <main>
    <!-- Hero Section -->
    <section class="relative h-[870px] min-h-[700px] flex items-center overflow-hidden">
      <div class="absolute inset-0 z-0">
        <img alt="Hero Luxury Car" class="w-full h-full object-cover" data-alt="Modern luxury silver sedan parked in a minimalist architectural setting with clean lines and soft cinematic morning lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuConvadsWmAfnzAS-tDjrVGZjidZ5WAJe781pgk4_JhUv6Al3fcdH8X61ruFfznCZ1yjaaNyqfDxWwTt4I2-4QrqW2T_ueXdY-JAt4XFHt-m8n7rZa5K23IskvQInwW-dHTSs4SK_7578w-KFFZnozG6kQAh86ZC6kXT_M8iJqehQyT4BJnwYfiWHMLuJxx0y4qK_BE8nJps46Ony3UTSEpLgcyWzn_9Y2zxlAYVjE9hoglSjq01GFjEWH-1Kl0Eh4OZnKMryjly5A"/>
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent"></div>
      </div>
      <div class="container mx-auto px-8 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7">
          <h1 class="font-headline text-5xl md:text-7xl font-extrabold text-white leading-tight tracking-tighter mb-6">
            The Digital <br/><span class="text-secondary-container">Concierge</span> For Your Next Drive.
          </h1>
          <p class="text-white/80 text-xl max-w-xl mb-10 font-light leading-relaxed">
            Elevate your journey with hand-picked premium automobiles in Pekanbaru. Curated for performance, verified for peace of mind.
          </p>
          <div class="flex flex-wrap gap-4">
            <a class="bg-secondary-container text-on-secondary-fixed px-10 py-5 rounded-xl font-bold text-lg shadow-xl shadow-secondary/20 hover:scale-105 transition-transform" href="catalog.html">
              Lihat Katalog
            </a>
            <a class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-10 py-5 rounded-xl font-bold text-lg hover:bg-white/20 transition-all" href="test-drive.html">
              Hubungi Konsultan
            </a>
          </div>
          <div class="mt-16 flex flex-wrap gap-8">
            <div class="flex items-center gap-3 text-white">
              <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">verified</span>
              <span class="font-medium">Unit Terverifikasi</span>
            </div>
            <div class="flex items-center gap-3 text-white">
              <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">history</span>
              <span class="font-medium">Berpengalaman 10+ Tahun</span>
            </div>
            <div class="flex items-center gap-3 text-white">
              <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">support_agent</span>
              <span class="font-medium">Layanan Purna Jual</span>
            </div>
          </div>
        </div>
        <div class="lg:col-span-5">
          <div class="glass-panel p-8 rounded-[2rem] editorial-shadow border border-white/40">
            <h3 class="font-headline text-2xl font-bold text-primary mb-6">Cari Kendaraan Anda</h3>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Brand</label>
                <select class="w-full bg-white border-none rounded-xl p-4 text-primary font-medium focus:ring-2 focus:ring-secondary-container">
                  <option>Semua Merek</option>
                  <option>Toyota</option>
                  <option>Honda</option>
                  <option>Mitsubishi</option>
                  <option>BMW</option>
                </select>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Model</label>
                  <input class="w-full bg-white border-none rounded-xl p-4 text-primary placeholder-slate-400 focus:ring-2 focus:ring-secondary-container" placeholder="e.g. Fortuner" type="text"/>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Tahun</label>
                  <select class="w-full bg-white border-none rounded-xl p-4 text-primary font-medium focus:ring-2 focus:ring-secondary-container">
                    <option>2020+</option>
                    <option>2022+</option>
                    <option>2024+</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Range Harga (Juta)</label>
                <div class="flex items-center gap-4">
                  <input class="w-full bg-white border-none rounded-xl p-4 text-primary placeholder-slate-400 focus:ring-2 focus:ring-secondary-container" placeholder="Min" type="number"/>
                  <span class="text-slate-400">—</span>
                  <input class="w-full bg-white border-none rounded-xl p-4 text-primary placeholder-slate-400 focus:ring-2 focus:ring-secondary-container" placeholder="Max" type="number"/>
                </div>
              </div>
              <a class="w-full bg-primary text-white py-5 rounded-xl font-bold text-lg mt-4 shadow-lg hover:brightness-110 transition-all flex items-center justify-center gap-2" href="catalog.html">
                <span class="material-symbols-outlined">search</span>
                Temukan Unit
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- API: GET /api/cars?limit=6 -->
    <!-- API: GET /api/cars/popular -->
    <!-- Unit Terbaru Section -->
    <section class="py-24 bg-surface">
      <div class="container mx-auto px-8">
        <div class="flex justify-between items-end mb-16">
          <div>
            <span class="text-secondary font-bold tracking-[0.2em] uppercase text-sm mb-4 block">Our Inventory</span>
            <h2 class="font-headline text-4xl font-extrabold text-primary">Unit Terbaru Pekanbaru</h2>
          </div>
          <a class="text-primary font-bold flex items-center gap-2 hover:gap-4 transition-all group" href="catalog.html">
            Lihat Semua Koleksi
            <span class="material-symbols-outlined group-hover:text-secondary-container transition-colors">arrow_right_alt</span>
          </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
          <div class="group bg-surface-container-lowest rounded-[1.5rem] overflow-hidden editorial-shadow hover:-translate-y-2 transition-transform duration-500">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Toyota Alphard" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Front profile of a white luxury MPV with sleek chrome accents parked in a bright studio environment" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB36HJqCR3wyD7bTSSQoSLk9jp_Xayq9466uxS8Ulwp5bicKy9CIRhnt3cDu5yQFtY2faDMQLEVBKvnP-20zlfBb2Gf6MHy6mHBjQfr4853HXkFu6tLmOoGvNbYy8QQx6gXxGdn6iVJUPEpjn-sN-r-SBXj-nWQ6kjUc8gY4As78a7q-TSHFd3bB3IEzsTWuZWsfU6LV36LQJ7-QowQXXQQF0vjJeT72f1LJk-4i-Ng94WLB3VT-u8U8QHVnH3rHVy4Fcj3BiNXt4c"/>
              <div class="absolute top-4 left-4">
                <span class="status-available text-xs font-bold px-3 py-1 rounded-full backdrop-blur-md bg-opacity-90">Available</span>
              </div>
            </div>
            <div class="p-8">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="font-headline text-2xl font-extrabold text-primary">Toyota Alphard 2.5 G</h3>
                  <p class="text-slate-400 font-medium">Automatic • White Pearl</p>
                </div>
                <span class="bg-surface-container-low p-2 rounded-lg">
                  <span class="material-symbols-outlined text-primary">favorite</span>
                </span>
              </div>
              <div class="grid grid-cols-2 gap-4 py-4 border-y border-outline-variant/15 mb-6">
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">calendar_today</span>
                  <span class="text-sm font-semibold">2022</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">speed</span>
                  <span class="text-sm font-semibold">12,400 KM</span>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Harga OTR</p>
                  <p class="text-2xl font-black text-primary">Rp 1.150M</p>
                </div>
                <a class="bg-secondary-container text-on-secondary-fixed w-12 h-12 rounded-full flex items-center justify-center hover:scale-110 transition-transform" href="car-detail.html">
                  <span class="material-symbols-outlined">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
          <div class="group bg-surface-container-lowest rounded-[1.5rem] overflow-hidden editorial-shadow hover:-translate-y-2 transition-transform duration-500">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Mitsubishi Pajero" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Side profile of a black robust SUV standing on a high-end showroom floor with polished reflections" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-XQJDRAiht3jmmKlm03BD8d8fkgdRLKKVuVoo9XK2ncsmiR5ElEMj_lTdRbdC9Hf5ABOnHCVnDBnx7vlta4kcEOIeznNW0Ez0L-FuaQabOpkXFLl0yDbHQP-gIJdP0F4mhoV9a_6Vjd1XYqj7Fl3Y-3I9meK4X-DIkTETybpZJb63w0kt-LAkuuV6dYIto14uGMO7d1Mbd9yAkQGBfw9WgGTxwD86KauPENfiFyjQyuk7ZV938YjqEsG_8Az-m4wgyxVl3k6wE6g"/>
              <div class="absolute top-4 left-4">
                <span class="status-reserved text-xs font-bold px-3 py-1 rounded-full">Reserved</span>
              </div>
            </div>
            <div class="p-8">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="font-headline text-2xl font-extrabold text-primary">Mitsubishi Pajero Dakar</h3>
                  <p class="text-slate-400 font-medium">4x2 • Jet Black</p>
                </div>
                <span class="bg-surface-container-low p-2 rounded-lg">
                  <span class="material-symbols-outlined text-primary">favorite</span>
                </span>
              </div>
              <div class="grid grid-cols-2 gap-4 py-4 border-y border-outline-variant/15 mb-6">
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">calendar_today</span>
                  <span class="text-sm font-semibold">2021</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">speed</span>
                  <span class="text-sm font-semibold">35,000 KM</span>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Harga OTR</p>
                  <p class="text-2xl font-black text-primary">Rp 545jt</p>
                </div>
                <a class="bg-secondary-container text-on-secondary-fixed w-12 h-12 rounded-full flex items-center justify-center hover:scale-110 transition-transform" href="car-detail.html">
                  <span class="material-symbols-outlined">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
          <div class="group bg-surface-container-lowest rounded-[1.5rem] overflow-hidden editorial-shadow hover:-translate-y-2 transition-transform duration-500">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Honda Civic RS" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Sleek red sports sedan captured in a high-contrast urban night setting with glowing city lights in background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJWY7D72T0YnbrV7D2n-6KfDmwMocsfTyDthwraZt3spp2IM1JjG_pWptdLbGc-vaH42NWoZslEq8CLQ0N39GaWA_-3EAF762tRGUxcNeFwbHmqXsiln7hEIpbjWkKxzrdXcw5SInqe1JxKze0moIYLz2f-NhDaYM5YUuC1Vzc1qS7792dLv3P1gqnwX3-AAezA7Y_MVrTZ8VlRGgV_uLk5KMaoN7WbFg1ON97mmRbRlW536dLH2ACDLQRAPvQ_EPXRXfWkygBgpk"/>
              <div class="absolute top-4 left-4">
                <span class="status-available text-xs font-bold px-3 py-1 rounded-full">Available</span>
              </div>
            </div>
            <div class="p-8">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="font-headline text-2xl font-extrabold text-primary">Honda Civic RS</h3>
                  <p class="text-slate-400 font-medium">Turbo • Ignite Red</p>
                </div>
                <span class="bg-surface-container-low p-2 rounded-lg">
                  <span class="material-symbols-outlined text-primary">favorite</span>
                </span>
              </div>
              <div class="grid grid-cols-2 gap-4 py-4 border-y border-outline-variant/15 mb-6">
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">calendar_today</span>
                  <span class="text-sm font-semibold">2023</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500">
                  <span class="material-symbols-outlined text-sm">speed</span>
                  <span class="text-sm font-semibold">8,200 KM</span>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Harga OTR</p>
                  <p class="text-2xl font-black text-primary">Rp 480jt</p>
                </div>
                <a class="bg-secondary-container text-on-secondary-fixed w-12 h-12 rounded-full flex items-center justify-center hover:scale-110 transition-transform" href="car-detail.html">
                  <span class="material-symbols-outlined">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Bento Section -->
    <section class="py-24 bg-surface-container-low overflow-hidden">
      <div class="container mx-auto px-8">
        <div class="text-center max-w-2xl mx-auto mb-20">
          <span class="text-secondary font-bold tracking-[0.2em] uppercase text-sm mb-4 block">The Maharani Difference</span>
          <h2 class="font-headline text-4xl md:text-5xl font-extrabold text-primary">Mengapa Pilih Maharani Mobil?</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-auto md:h-[600px]">
          <div class="md:col-span-8 bg-primary rounded-[2rem] p-12 text-white relative overflow-hidden flex flex-col justify-end">
            <div class="absolute top-0 right-0 w-1/2 h-full opacity-20 pointer-events-none">
              <img alt="Engine Detail" class="w-full h-full object-cover" data-alt="Extreme close-up of a high-performance car engine with intricate metallic details and industrial aesthetic" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBs-Zr7O1QV49IUiefHLjvjdPtQ7QD8UG7wpTS-cyVOs2UqWZdk4pf_RmNWpzlPvOdI6VNtjouyR21H9FRLcgORXyN9Sv7nxmvCkJtgCIfTsecVkO4ewev7-LdXHDKXiiim3d38uOYmG1XupxrnpEkU5vzOI_b_2PtrFZ9LLCMCUg9ThzALgxO0WpwnDJ1f-LxYuMHXRmovN3Ng_zRIJpqS-mlM_MJspBrFoSfbHrxKo6x5c-zL0C7xG13kr4yD83lOHAHozNLduCQ"/>
            </div>
            <span class="material-symbols-outlined text-5xl text-secondary-container mb-6" style="font-variation-settings: 'FILL' 1;">verified_user</span>
            <h3 class="text-3xl font-extrabold mb-4">Garansi & Keamanan Unit</h3>
            <p class="text-white/70 text-lg max-w-md leading-relaxed">
              Setiap unit melalui 175 titik inspeksi ketat. Kami memberikan jaminan bebas banjir dan bebas tabrak untuk setiap kilometer yang Anda tempuh.
            </p>
          </div>
          <div class="md:col-span-4 bg-white rounded-[2rem] p-12 flex flex-col justify-center editorial-shadow">
            <span class="material-symbols-outlined text-5xl text-primary mb-6">visibility</span>
            <h3 class="text-2xl font-extrabold text-primary mb-4">Transparansi Harga</h3>
            <p class="text-slate-500 leading-relaxed">Tidak ada biaya tersembunyi. Semua riwayat servis dan dokumen kendaraan tersedia untuk Anda tinjau kapan saja.</p>
          </div>
          <div class="md:col-span-5 bg-secondary-container rounded-[2rem] p-12 flex flex-col justify-center">
            <span class="material-symbols-outlined text-5xl text-on-secondary-fixed mb-6" style="font-variation-settings: 'FILL' 1;">electric_bolt</span>
            <h3 class="text-2xl font-extrabold text-on-secondary-fixed mb-4">Proses Cepat & Mudah</h3>
            <p class="text-on-secondary-fixed/80 leading-relaxed">Persetujuan kredit dalam hitungan jam. Kami mengurus semua dokumen dari awal hingga unit terparkir di garasi Anda.</p>
          </div>
          <div class="md:col-span-7 bg-surface-container-high rounded-[2rem] p-12 flex items-center justify-between group overflow-hidden">
            <div class="max-w-[60%]">
              <h3 class="text-2xl font-extrabold text-primary mb-4">Layanan Home Test Drive</h3>
              <p class="text-slate-500 leading-relaxed">Sibuk? Biarkan kami membawa unit impian langsung ke depan pintu rumah Anda di area Pekanbaru.</p>
            </div>
            <span class="material-symbols-outlined text-7xl text-primary/10 group-hover:text-primary/20 transition-colors group-hover:scale-125 duration-500 transform -rotate-12">directions_car</span>
          </div>
        </div>
      </div>
    </section>

    <!-- API: GET /api/reviews -->
    <!-- Testimonial Section -->
    <section class="py-24 bg-surface">
      <div class="container mx-auto px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
          <div>
            <span class="text-secondary font-bold tracking-[0.2em] uppercase text-sm mb-4 block">Testimonials</span>
            <h2 class="font-headline text-5xl font-extrabold text-primary mb-8 leading-tight">Apa Kata Pemilik Kendaraan Maharani?</h2>
            <div class="flex gap-4 items-center">
              <div class="flex -space-x-4">
                <img alt="User 1" class="w-12 h-12 rounded-full border-4 border-white" data-alt="Portrait of a smiling professional man in business casual attire" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDq-M9x09txwet4TJM0yapiyeh_F_6BB9V3FAcuF6btTG6LcMQWq2TTdBbeWTkAJ0mILv5Qjx0tCKp9BJSSU9FEouIpHUZLied_68dZZqdorlG8JnKiYIXxeD43PmgAzUJyV8tULiE1rSk0x9uhwwC1ro_9GN585FZGz_c3OYRlF2Ro-PvBSpWag0s_dR_rkbrkBP1T1ZYVV-30Ru4FKOYpVtPLJFrDMHP3rndj4jfKZQFCVXggi8Loeq--dc0ytb4-ShLOZulytTk"/>
                <img alt="User 2" class="w-12 h-12 rounded-full border-4 border-white" data-alt="Portrait of a friendly young woman with a warm smile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC8ImN22jvXuhr98Bi5aXuequHSMZ9cMnvrCaZtdLEq64ViNbqpJmtHy4_m1lGF9wil4qUO1gGGx4iHkssxWvJcHmQcYyUcIKaqMg35DyF1Aa-cruh6XK-GPfNvNplcDaYqpkuUUpX3I-B5yUWgEvYEdbhHrkIfpgvJpaMK0vyZcKdZHhu4xQECBNN4D4C1DYl_wpygtNDaD_U1ooy9CAEo6CTHou_jIaLEy8czuXg03hzdZeM5jJ3tI8ZJhelqoMSvi76o8pnS9JU"/>
                <img alt="User 3" class="w-12 h-12 rounded-full border-4 border-white" data-alt="Portrait of a confident middle-aged man with short hair" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC85KKPqkko2hjS8iblfAevjhA_Z_Qyg94Ovx2GosUZ1oi4QgtQP_PObcYtbWQ2uDd6yzRzg9C1a2-tUSbvFiFlBBLdjUEnMT3VQL4FhAnqNfnBoxMBWTcMLifRviL8UmRcMJzx4B9b6BQPS2un9f1MvzANnLo7e3YpU2IpAlvgGD0GQqapaP5LgbVfHUZqhbivUmL2pg2VYlhJOB-gvAN93UYQ7QmVRchjTehQH6DOrSrqKj_Mbn4e5HIzaparNAttD5Tleb9JPSE"/>
              </div>
              <p class="text-slate-500 font-medium">Bergabunglah dengan 5,000+ pelanggan puas kami.</p>
            </div>
          </div>
          <div class="relative">
            <div class="bg-white p-12 rounded-[2.5rem] editorial-shadow relative z-10">
              <span class="material-symbols-outlined text-6xl text-secondary-container/30 absolute top-8 right-12">format_quote</span>
              <div class="flex gap-1 mb-6">
                <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">star</span>
              </div>
              <p class="text-xl text-primary leading-relaxed font-medium mb-8">
                "Pengalaman membeli mobil bekas yang paling berkelas di Pekanbaru. Sales person sangat informatif dan tidak memaksa. Unit diantar dalam kondisi sangat bersih seperti baru."
              </p>
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-slate-200 overflow-hidden">
                  <img alt="Client" class="w-full h-full object-cover" data-alt="Close up portrait of a satisfied male client with a professional look" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9uhyOaZM5HsnZV1UO9EyYZzSePuCpZqm4Er_-mIEMJI3fmwKgMZ9KRNKlwOSfzsauZkv1kyyDagYMJ5GW4XlVchLuRBMkbAW0Unv8ewi0fkicG5w0lCTZLe_DMFlx05oeA6Ce_42YS5iuvq5KIQU8hh7gzkmCooQoCgbtn3QuCOMNKAprcgb-XTOs5aDnQ1QH4yWx6HyL8VtK5rcTU7tByGaqjGc9V8Zg9KlJ7YizNthZJHugyiFO4iyZ8c8A5ate563Zy83pztg"/>
                </div>
                <div>
                  <h4 class="font-bold text-primary">Dr. Andi Wijaya</h4>
                  <p class="text-sm text-slate-500">Pemilik Toyota Land Cruiser</p>
                </div>
              </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-full h-full bg-secondary-container rounded-[2.5rem] -z-10 transform rotate-3"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- SEO Content Section -->
    <section class="py-24 bg-surface-container-low">
      <div class="container mx-auto px-8 max-w-4xl">
        <h2 class="font-headline text-3xl font-extrabold text-primary mb-8">Pusat Jual Beli Mobil Bekas Berkualitas di Pekanbaru</h2>
        <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-loose">
          <p class="mb-6">
            Maharani Mobil Pekanbaru telah berdiri selama lebih dari satu dekade melayani kebutuhan otomotif masyarakat Riau. Sebagai penyedia mobil bekas Pekanbaru yang terpercaya, kami memahami bahwa membeli kendaraan bukan sekadar transaksi, melainkan sebuah investasi jangka panjang.
          </p>
          <p class="mb-6">
            Kami menyediakan berbagai pilihan kendaraan mulai dari MPV keluarga seperti Toyota Avanza dan Mitsubishi Xpander, hingga unit premium seperti BMW dan Mercedes-Benz. Seluruh inventaris kami telah melewati proses multi-point inspection yang ketat untuk memastikan standar kualitas "The Digital Concierge" tetap terjaga.
          </p>
          <p>
            Terletak strategis di jantung kota Pekanbaru, showroom kami menawarkan pengalaman belanja yang nyaman dengan fasilitas purna jual yang lengkap. Baik Anda mencari mobil pertama atau ingin melakukan trade-in, tim ahli kami siap membantu Anda menemukan solusi finansial terbaik yang sesuai dengan anggaran Anda.
          </p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-[#031636] dark:bg-black w-full py-20 mt-auto">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 px-12 w-full max-w-screen-2xl mx-auto">
      <div class="col-span-1 md:col-span-1">
        <div class="text-white font-black italic text-3xl mb-6">Maharani Mobil.</div>
        <p class="text-slate-400 font-body text-sm leading-relaxed mb-8">
          Solusi otomotif premium dan terpercaya di Pekanbaru sejak 2014. Melayani dengan hati, mengantar dengan bangga.
        </p>
        <div class="flex gap-4">
          <span class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-white hover:text-primary transition-all cursor-pointer">
            <span class="material-symbols-outlined text-sm">public</span>
          </span>
          <span class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-white hover:text-primary transition-all cursor-pointer">
            <span class="material-symbols-outlined text-sm">alternate_email</span>
          </span>
        </div>
      </div>
      <div>
        <h4 class="text-white font-bold mb-6 font-label text-xs uppercase tracking-widest">Quick Links</h4>
        <ul class="space-y-4">
          <li><a class="text-slate-400 hover:text-white underline transition-all font-label text-xs uppercase tracking-widest" href="catalog.html">Catalog</a></li>
          <li><a class="text-slate-400 hover:text-white underline transition-all font-label text-xs uppercase tracking-widest" href="financing.html">Financing</a></li>
          <li><a class="text-slate-400 hover:text-white underline transition-all font-label text-xs uppercase tracking-widest" href="reviews.html">Reviews</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-white font-bold mb-6 font-label text-xs uppercase tracking-widest">Office</h4>
        <p class="text-slate-400 font-body text-sm leading-relaxed">
          Jl. Soekarno - Hatta No. 88<br/>
          Marpoyan Damai, Pekanbaru<br/>
          Riau 28282
        </p>
      </div>
      <div>
        <h4 class="text-white font-bold mb-6 font-label text-xs uppercase tracking-widest">Newsletter</h4>
        <p class="text-slate-400 font-label text-xs uppercase tracking-widest mb-4">Dapatkan info unit terbaru</p>
        <div class="relative">
          <input class="w-full bg-white/5 border border-slate-700 rounded-lg p-3 text-white focus:outline-none focus:border-secondary-container" placeholder="Your Email" type="email"/>
          <button class="absolute right-2 top-2 text-secondary-container" aria-label="Kirim">
            <span class="material-symbols-outlined">send</span>
          </button>
        </div>
      </div>
    </div>
    <div class="mt-20 pt-8 border-t border-slate-800 px-12 text-center">
      <p class="text-slate-500 font-label text-xs uppercase tracking-[0.3em]">© 2026 Maharani Mobil Pekanbaru. The Digital Concierge.</p>
    </div>
  </footer>

  <a class="fixed bottom-8 right-8 z-50 bg-secondary-container text-on-secondary-fixed w-16 h-16 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all" href="catalog.html" aria-label="Search">
    <span class="material-symbols-outlined text-3xl">search</span>
  </a>
</body>
</html>

