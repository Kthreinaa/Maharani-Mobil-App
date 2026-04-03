<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Katalog Mobil | Maharani Mobil</title>
  <meta name="description" content="Katalog mobil bekas Maharani Mobil Pekanbaru. Filter merek, tahun, harga, dan status unit."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body min-h-screen flex flex-col">
  <!-- TopNavBar -->
  <header class="bg-slate-50/70 dark:bg-slate-950/70 backdrop-blur-xl docked full-width top-0 sticky z-50">
    <div class="flex justify-between items-center w-full px-8 py-4 max-w-screen-2xl mx-auto">
      <a class="text-2xl font-black text-[#1A2B4C] dark:text-white tracking-tighter font-headline" href="index.html">Maharani Mobil</a>
      <nav class="hidden md:flex items-center space-x-8 font-headline tracking-tight">
        <a class="text-[#1A2B4C] font-bold border-b-2 border-[#F5A623] pb-1" href="catalog.html">Catalog</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="about.html">About Us</a>
        <a class="text-slate-500 dark:text-slate-400 font-medium hover:text-[#F5A623] transition-colors duration-300" href="financing.html">Financing</a>
      </nav>
      <div class="flex items-center space-x-4">
        <a class="text-slate-500 font-medium px-4 py-2 hover:text-[#1A2B4C] transition-colors" href="login.html">Login</a>
        <a class="bg-primary text-white font-bold px-6 py-2 rounded-lg hover:scale-95 transition-transform duration-200" href="register.html">Register</a>
      </div>
    </div>
  </header>

  <main class="flex-grow max-w-screen-2xl mx-auto w-full px-8 py-8">
    <nav aria-label="Breadcrumb" class="flex mb-8 text-sm font-medium text-on-surface-variant/60">
      <ol class="flex items-center space-x-2">
        <li><a class="hover:text-primary transition-colors" href="index.html">Home</a></li>
        <li><span class="material-symbols-outlined text-sm">chevron_right</span></li>
        <li><a class="text-primary font-bold" href="catalog.html">Catalog</a></li>
      </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
      <aside class="w-full lg:w-72 flex-shrink-0">
        <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm sticky top-24">
          <h2 class="font-headline font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">filter_list</span>
            Filters
          </h2>
          <div class="space-y-6">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Brand</label>
              <select class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-primary/20">
                <option>All Brands</option>
                <option>Toyota</option>
                <option>Honda</option>
                <option>Mitsubishi</option>
                <option>Suzuki</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Price Range</label>
              <div class="space-y-2">
                <input class="w-full accent-secondary" max="1000000000" min="0" step="10000000" type="range"/>
                <div class="flex justify-between text-xs font-semibold">
                  <span>Rp 50jt</span>
                  <span>Rp 1M+</span>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Year</label>
              <div class="grid grid-cols-2 gap-2">
                <input class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="Min" type="text"/>
                <input class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm" placeholder="Max" type="text"/>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Transmission</label>
              <div class="flex flex-col gap-2">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input class="rounded text-secondary focus:ring-secondary border-outline-variant" type="checkbox"/>
                  <span class="text-sm">Automatic (AT)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input class="rounded text-secondary focus:ring-secondary border-outline-variant" type="checkbox"/>
                  <span class="text-sm">Manual (MT)</span>
                </label>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Fuel Type</label>
              <select class="w-full bg-surface-container-low border-none rounded-lg p-3 text-sm">
                <option>All Types</option>
                <option>Petrol</option>
                <option>Diesel</option>
                <option>Hybrid</option>
              </select>
            </div>
            <button class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary-container transition-colors mt-4">Apply Filters</button>
          </div>
        </div>
      </aside>

      <div class="flex-grow">
        <!-- API: GET /api/cars?brand=&year=&price_min=&price_max=&status= -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 class="font-headline font-extrabold text-3xl text-primary tracking-tight">Available Inventory</h1>
            <p class="text-on-surface-variant text-sm mt-1">Showing 142 luxury and family vehicles</p>
          </div>
          <div class="flex items-center gap-4 bg-surface-container-lowest p-2 rounded-xl shadow-sm">
            <span class="text-xs font-bold uppercase tracking-widest pl-2">Sort By</span>
            <select class="bg-transparent border-none text-sm font-semibold focus:ring-0">
              <option>Newest Listed</option>
              <option>Price: High to Low</option>
              <option>Price: Low to High</option>
              <option>KM: Low to High</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div class="bg-surface-container-lowest rounded-xl overflow-hidden group shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Toyota Avanza" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Modern white Toyota Avanza parked on a clean studio background with soft cinematic lighting and sharp details" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAm_kOv_2zpTXV40_mX9vsauOG9S29LdkyOrObzAHCAnfd-I-Ti83HJ93UwDLMoUPnbc2JVG8_apX-UHDJ7eCQ8jwH8-vcMZmoEPc8vUb4NzfKHVcMfeGLHLR44FGU5moEOl3PP4VZdOXEPZQeU0Cm0UUXgIw5GJvykemBUDeqW6hODi4sJA47--ch7UJXeyLSmMWo2b0OZC4f4giImRxiubBxf72b1lj8jSAQScm3diuaZvRoCwszBUhBzBo7R2-zJsKzleZQXmGk"/>
              <div class="absolute top-4 left-4">
                <span class="status-available px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Available</span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="font-headline font-bold text-xl mb-1 group-hover:text-secondary transition-colors">Toyota Avanza G 2019</h3>
              <div class="text-2xl font-black text-primary mb-4">Rp 185.000.000</div>
              <div class="flex items-center gap-4 py-4 border-t border-outline-variant/15">
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">speed</span>
                  <span class="text-sm font-medium">42,000 KM</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">settings_input_component</span>
                  <span class="text-sm font-medium">Automatic</span>
                </div>
              </div>
              <a class="w-full bg-surface-container-high text-primary font-bold py-3 rounded-lg group-hover:bg-secondary group-hover:text-white transition-all duration-300 block text-center" href="car-detail.html">View Details</a>
            </div>
          </div>

          <div class="bg-surface-container-lowest rounded-xl overflow-hidden group shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Honda HR-V" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Metallic grey Honda HR-V luxury SUV parked on an urban street at twilight with glowing headlights and high contrast" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB17A4TfIieJ8Xb-A5HJEOSgt20mUJR4tIf2LC7r3sWAOCmk9ByGCMfottc2E0k8uhMCMIZCB-R7SJrST1HN63m58XbPMyw4VMfeLhH6sYnfi3znIynT738YSCvRAJCtSdhs-oz4TlXcGOHC3fP_feLjWGkc7Bv1G7Bc3OUI-VxeLO9Yfbwop0I2UqT_O-mHL__9_hCdsiNxuGzUHGGoXwxeEPXwcvNVoOLqJuvm81OqU-PUKiZJUDzbi0h0X1OwRVcJ_fSdEXB-I4"/>
              <div class="absolute top-4 left-4">
                <span class="status-reserved px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Reserved</span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="font-headline font-bold text-xl mb-1 group-hover:text-secondary transition-colors">Honda HR-V 1.5 E 2021</h3>
              <div class="text-2xl font-black text-primary mb-4">Rp 295.000.000</div>
              <div class="flex items-center gap-4 py-4 border-t border-outline-variant/15">
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">speed</span>
                  <span class="text-sm font-medium">18,500 KM</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">settings_input_component</span>
                  <span class="text-sm font-medium">CVT AT</span>
                </div>
              </div>
              <a class="w-full bg-surface-container-high text-primary font-bold py-3 rounded-lg group-hover:bg-secondary group-hover:text-white transition-all duration-300 block text-center" href="car-detail.html">View Details</a>
            </div>
          </div>

          <div class="bg-surface-container-lowest rounded-xl overflow-hidden group shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Mitsubishi Pajero Sport" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Rugged dark bronze Mitsubishi Pajero Sport off-road SUV in a minimalist driveway with elegant shadows" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBjjamtnQUvZKurcGVFHyIxqkuzleHgJlLBObX71YO__sxxXbS6oESz92yAK2-FOPJfteqbhOkDs50t9Bb0eJxr4DES59YBpN3oo21iiwKmGVhoCBfU4oAXJprLSsOjymMHYsZqeYUE7YlWlBLUo7Chxt4FELeVDmJqDZgX2UK9Z9q6XIhyu7Tic-194OoIag7E_-xYExNNoiy17bhQ_pRJ_mRXdtycYJ4zOceCzsAWKE1d4jtxdzYsGPy522iDvNRG51bHlPEcEQ8"/>
              <div class="absolute top-4 left-4">
                <span class="status-sold px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Sold</span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="font-headline font-bold text-xl mb-1 group-hover:text-secondary transition-colors">Mitsubishi Pajero Dakar 2018</h3>
              <div class="text-2xl font-black text-primary mb-4">Rp 415.000.000</div>
              <div class="flex items-center gap-4 py-4 border-t border-outline-variant/15">
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">speed</span>
                  <span class="text-sm font-medium">65,000 KM</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">settings_input_component</span>
                  <span class="text-sm font-medium">Automatic</span>
                </div>
              </div>
              <button class="w-full bg-surface-container-high text-primary font-bold py-3 rounded-lg group-hover:bg-secondary group-hover:text-white transition-all duration-300">Sold Out</button>
            </div>
          </div>

          <div class="bg-primary text-white rounded-xl p-8 flex flex-col justify-between overflow-hidden relative col-span-1 md:col-span-2 shadow-sm">
            <div class="relative z-10">
              <h4 class="font-headline font-extrabold text-3xl mb-4 leading-tight">Can't find your<br/>dream car?</h4>
              <p class="text-primary-fixed-dim max-w-xs mb-8">Our expert concierges can help you find specific models through our premium network.</p>
              <a class="bg-secondary text-on-secondary-fixed font-bold px-8 py-3 rounded-lg hover:scale-105 transition-transform inline-block" href="test-drive.html">Consult Our Concierge</a>
            </div>
            <div class="absolute -right-12 -bottom-12 opacity-20">
              <span class="material-symbols-outlined text-[160px]" style="font-variation-settings: 'FILL' 1;">directions_car</span>
            </div>
          </div>

          <div class="bg-surface-container-lowest rounded-xl overflow-hidden group shadow-sm hover:shadow-xl transition-all duration-300">
            <div class="relative aspect-[16/9] overflow-hidden">
              <img alt="Porsche Taycan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Sleek black electric sports car parked in a modern architectural building with dramatic lighting and reflections" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3VLBiwqSXrHk0fuIH6WaClWvG85BDh7oI0DnPDzLX0jC7jrgNqUzZrCun4ihizPfzpZyNxlxi-q3wOJ_sjD12BRbnkOtJrbxuxJq2B1aEIwYigiu_Q_4p82iUdCsZlI_QCTRp_cM8qw9aykZF_pEiDyPfavgFNEpkmam1KvHYKLbNiHx1cdYtMDeUDcyEpzlHAG-g1TigerlxkZNH2pNM4Jb8rsrX3QD0WzgwXlz20W9x_8CXmFPOf0M4Y8-OcOCS_jOwTqTl4IM"/>
              <div class="absolute top-4 left-4">
                <span class="status-available px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Available</span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="font-headline font-bold text-xl mb-1 group-hover:text-secondary transition-colors">Toyota Raize 1.0 Turbo 2022</h3>
              <div class="text-2xl font-black text-primary mb-4">Rp 238.000.000</div>
              <div class="flex items-center gap-4 py-4 border-t border-outline-variant/15">
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">speed</span>
                  <span class="text-sm font-medium">8,200 KM</span>
                </div>
                <div class="flex items-center gap-1.5 text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg">settings_input_component</span>
                  <span class="text-sm font-medium">Automatic</span>
                </div>
              </div>
              <a class="w-full bg-surface-container-high text-primary font-bold py-3 rounded-lg group-hover:bg-secondary group-hover:text-white transition-all duration-300 block text-center" href="car-detail.html">View Details</a>
            </div>
          </div>
        </div>

        <div class="mt-12 flex justify-center items-center gap-2">
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant hover:bg-primary hover:text-white transition-colors">
            <span class="material-symbols-outlined">chevron_left</span>
          </button>
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold">1</button>
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant hover:bg-primary hover:text-white transition-colors font-medium">2</button>
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant hover:bg-primary hover:text-white transition-colors font-medium">3</button>
          <span class="px-2">...</span>
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant hover:bg-primary hover:text-white transition-colors font-medium">12</button>
          <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant hover:bg-primary hover:text-white transition-colors">
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>

        <section class="mt-16">
          <h2 class="font-headline font-extrabold text-2xl text-primary mb-6">UI States</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Loading</p>
              <div class="space-y-3">
                <div class="h-4 bg-surface-container-high rounded-full w-3/4 animate-pulse"></div>
                <div class="h-4 bg-surface-container-high rounded-full w-1/2 animate-pulse"></div>
                <div class="h-32 bg-surface-container-high rounded-xl animate-pulse"></div>
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Error</p>
              <div class="flex items-center gap-3 text-error">
                <span class="material-symbols-outlined">error</span>
                <span class="font-semibold">Gagal memuat data. Coba lagi.</span>
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Empty</p>
              <div class="text-on-surface-variant text-sm">
                Belum ada unit sesuai filter Anda. Reset filter untuk melihat semua unit.
              </div>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/30 relative">
              <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Toast</p>
              <div class="absolute right-4 bottom-4 bg-primary text-white text-xs font-bold px-3 py-2 rounded-full shadow-lg">
                Unit ditambahkan ke keranjang
              </div>
              <div class="text-on-surface-variant text-sm">Contoh notifikasi toast pada aksi cepat.</div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <footer class="bg-[#031636] dark:bg-black w-full py-12 mt-auto">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-12 w-full max-w-screen-2xl mx-auto">
      <div class="space-y-4">
        <div class="text-white font-black italic text-2xl tracking-tighter">Maharani Mobil</div>
        <p class="text-slate-400 text-xs uppercase tracking-widest leading-loose font-label">© 2026 Maharani Mobil Pekanbaru.<br/>The Digital Concierge.</p>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Navigation</h5>
        <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="catalog.html">Catalog</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="financing.html">Financing</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="about.html">About Us</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="test-drive.html">Contact</a></li>
        </ul>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Support</h5>
        <ul class="space-y-2 text-xs font-label uppercase tracking-widest">
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="privacy.html">Privacy Policy</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="terms.html">Terms of Service</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="faq.html">Cookie Settings</a></li>
          <li><a class="text-slate-400 hover:text-white transition-all underline" href="test-drive.html">Contact Support</a></li>
        </ul>
      </div>
      <div class="space-y-4">
        <h5 class="text-white font-bold text-sm tracking-widest uppercase">Connect</h5>
        <div class="flex gap-4">
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">share</span>
          </a>
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">alternate_email</span>
          </a>
          <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center text-white hover:bg-secondary hover:border-secondary transition-colors" href="#">
            <span class="material-symbols-outlined text-lg">call</span>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <div class="fixed bottom-8 right-8 z-50 md:hidden">
    <button class="bg-secondary text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center glass-nav ring-4 ring-secondary/20" aria-label="Search">
      <span class="material-symbols-outlined text-3xl">search</span>
    </button>
  </div>
</body>
</html>

