<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Maharani Mobil - Marketing Dashboard</title>
  <meta name="description" content="Dashboard marketing Maharani Mobil: inventory, lead, dan campaign performance."/>
  <script src="/assets/tailwind.config.js"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 dark:border-slate-800 bg-white dark:bg-[#031636] font-body text-sm flex flex-col h-full p-4 z-50">
    <div class="mb-10 px-4">
      <a class="text-xl font-bold text-[#1A2B4C] dark:text-[#F5A623]" href="dashboard-marketing.html">Maharani Mobil</a>
    </div>
    <div class="flex items-center gap-3 px-4 py-6 mb-8 border-b border-slate-100 dark:border-slate-800">
      <img alt="User profile photo" class="w-10 h-10 rounded-full object-cover" data-alt="Professional headshot of a smiling middle-aged Indonesian man in a clean business shirt with soft office lighting background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMlhEEtJiXHfeGyjwVl6iptcSP25ysnwmr-_y1bnpV_E9VPMYPIs3xue7XRD2Z5CiVxQ9qHY9fL5_W5aFwRRbF_LQKpUuNwlt3l1mNEeWt9UkukkUnTlE8Vwk1gjW-6sLObStQPqndGpDm7BqgRELi6XW6eQyeFkayfH5MmMFjRv5QtteJ0XLtcKkzOCbwTSQ2fX7qPi4U1vYsH5-ypbsSRxRnUzt3De_cf_rJps847BOHQOcsRMem815Dc47zlWTSZUmci3vsWcw"/>
      <div class="flex flex-col">
        <span class="font-bold text-[#1A2B4C] dark:text-white">Budi Santoso</span>
        <span class="text-xs text-slate-500">Senior Supervisor</span>
      </div>
    </div>
    <nav class="flex-1 space-y-2">
      <a class="bg-slate-100 dark:bg-white/10 text-[#1A2B4C] dark:text-white font-semibold rounded-lg px-4 py-3 flex items-center gap-3 transition-transform duration-200 hover:translate-x-1" href="dashboard-marketing.html">
        <span class="material-symbols-outlined">dashboard</span>
        Dashboard
      </a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg transition-transform duration-200 hover:translate-x-1" href="marketing-products.html">
        <span class="material-symbols-outlined">directions_car</span>
        Manajemen Produk
      </a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg transition-transform duration-200 hover:translate-x-1" href="marketing-orders.html">
        <span class="material-symbols-outlined">leaderboard</span>
        Manajemen Pesanan
      </a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg transition-transform duration-200 hover:translate-x-1" href="marketing-offers.html">
        <span class="material-symbols-outlined">sell</span>
        Manajemen Penawaran
      </a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg transition-transform duration-200 hover:translate-x-1" href="marketing-upload.html">
        <span class="material-symbols-outlined">upload</span>
        Upload Produk
      </a>
    </nav>
    <div class="mt-auto pt-6 border-t border-slate-100 dark:border-slate-800 space-y-1">
      <a class="w-full mb-6 bg-[#feae2c] text-[#291800] font-bold py-3 rounded-xl shadow-lg shadow-secondary/20 hover:scale-105 transition-transform text-center block" href="marketing-upload.html">Add New Listing</a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" href="faq.html">
        <span class="material-symbols-outlined">help</span>
        Help Center
      </a>
      <a class="text-slate-500 dark:text-slate-400 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" href="login.html">
        <span class="material-symbols-outlined">logout</span>
        Logout
      </a>
    </div>
  </aside>

  <main class="ml-64 min-h-screen p-8 bg-surface">
    <header class="flex justify-between items-end mb-10">
      <div>
        <h1 class="text-3xl font-extrabold tracking-tighter text-primary">Marketing Dashboard</h1>
        <p class="text-on-surface-variant font-medium">Welcome back, Budi. Here's what's happening today.</p>
      </div>
      <div class="flex gap-4">
        <button class="px-6 py-2.5 rounded-full border border-outline-variant font-semibold hover:bg-surface-container-low transition-colors">Export Report</button>
        <div class="glass-panel border border-white px-4 py-2.5 rounded-full flex items-center gap-2 text-primary shadow-sm">
          <span class="material-symbols-outlined text-xl">calendar_today</span>
          <span class="text-sm font-bold">Oct 2026 - Present</span>
        </div>
      </div>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <div class="bg-primary p-8 rounded-[2rem] text-white flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
          <span class="material-symbols-outlined text-[10rem]">directions_car</span>
        </div>
        <div>
          <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-md">
            <span class="material-symbols-outlined text-secondary-container">inventory_2</span>
          </div>
          <p class="text-blue-200 font-medium uppercase tracking-widest text-xs mb-1">Active Inventory</p>
          <h3 class="text-5xl font-black italic tracking-tighter">142</h3>
        </div>
        <div class="mt-8 flex items-center gap-2">
          <span class="text-tertiary-fixed font-bold flex items-center">
            <span class="material-symbols-outlined text-sm">trending_up</span> 12%
          </span>
          <span class="text-blue-300/60 text-xs">vs last month</span>
        </div>
      </div>
      <div class="bg-white border border-slate-100 p-8 rounded-[2rem] flex flex-col justify-between shadow-xl shadow-blue-900/5 relative overflow-hidden group">
        <div>
          <div class="w-12 h-12 bg-surface-container-low rounded-2xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-primary">group</span>
          </div>
          <p class="text-on-surface-variant font-medium uppercase tracking-widest text-xs mb-1">New Leads</p>
          <h3 class="text-5xl font-black text-primary italic tracking-tighter">856</h3>
        </div>
        <div class="mt-8 flex items-center gap-2">
          <span class="text-green-600 font-bold flex items-center">
            <span class="material-symbols-outlined text-sm">trending_up</span> 24%
          </span>
          <span class="text-on-surface-variant/60 text-xs">Conversion up 3%</span>
        </div>
      </div>
      <div class="bg-secondary-container p-8 rounded-[2rem] flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute right-0 bottom-0 opacity-20 transform translate-x-4 translate-y-4">
          <span class="material-symbols-outlined text-9xl text-on-secondary-fixed">payments</span>
        </div>
        <div>
          <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-primary">sell</span>
          </div>
          <p class="text-on-secondary-fixed-variant font-medium uppercase tracking-widest text-xs mb-1">Sales (Oct)</p>
          <h3 class="text-5xl font-black text-primary italic tracking-tighter">Rp 2.4B</h3>
        </div>
        <div class="mt-8 flex items-center gap-2">
          <span class="text-on-secondary-fixed-variant font-bold">Goal: Rp 3.0B</span>
          <div class="flex-1 h-2 bg-primary/10 rounded-full overflow-hidden">
            <div class="h-full bg-primary rounded-full" style="width: 80%"></div>
          </div>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-10">
      <div class="lg:col-span-3 bg-white p-8 rounded-[2rem] shadow-xl shadow-blue-900/5">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-xl font-bold text-primary">Lead Trends</h2>
          <div class="flex gap-2">
            <span class="flex items-center gap-1 text-xs font-semibold text-primary">
              <span class="w-3 h-3 rounded-full bg-secondary"></span> Facebook Ads
            </span>
            <span class="flex items-center gap-1 text-xs font-semibold text-primary">
              <span class="w-3 h-3 rounded-full bg-primary-container"></span> Marketplace
            </span>
          </div>
        </div>
        <div class="h-64 flex items-end justify-between gap-2 px-2">
          <div class="w-full bg-surface-container-low rounded-t-xl h-[40%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[70%] transition-all group-hover:h-[80%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[30%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[60%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[60%] transition-all group-hover:h-[70%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[40%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[85%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[80%] transition-all group-hover:h-[90%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[25%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[70%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[55%] transition-all group-hover:h-[65%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[50%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[95%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[90%] transition-all group-hover:h-[100%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[35%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[50%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[40%] transition-all group-hover:h-[50%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[20%] translate-y-[-100%]"></div>
          </div>
          <div class="w-full bg-surface-container-low rounded-t-xl h-[75%] relative group">
            <div class="absolute bottom-0 w-full bg-primary rounded-t-xl h-[65%] transition-all group-hover:h-[75%]"></div>
            <div class="absolute bottom-0 w-full bg-secondary/80 rounded-t-xl h-[45%] translate-y-[-100%]"></div>
          </div>
        </div>
        <div class="flex justify-between mt-4 text-xs font-bold text-on-surface-variant uppercase tracking-tighter">
          <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
        </div>
      </div>
      <div class="lg:col-span-2 bg-primary-container p-8 rounded-[2rem] text-white flex flex-col">
        <div class="flex justify-between items-start mb-6">
          <h2 class="text-xl font-bold leading-tight">Featured <br/>Campaign</h2>
          <span class="bg-tertiary-fixed text-on-tertiary-fixed px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Active</span>
        </div>
        <div class="flex-1 rounded-2xl overflow-hidden relative mb-6">
          <img alt="Luxury car showcase" class="w-full h-full object-cover grayscale-[30%] hover:scale-110 transition-transform duration-[2s]" data-alt="Sleek modern black SUV parked in a luxury minimalist concrete driveway with dramatic golden hour lighting and long shadows" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQhGmHXpTNCqRmKezTpGFLjh0YsfCfSHC3hOL44VA7ko1sjyOr-bRNTbXQLhu0ftd8rh2fC9pLnrqlXnXah7XQ6GZ54rucci1_UtdRTh6ufgMXDoyHmU-xB_NjdbYiw_aKFqu4p3oahvbGbWG7UIRG5XCDMnmZEPXzo_X82IYDeICGOsqQ2eKh0MXMA_Fm2Az8iwhDeECmOk-g6p-qUeSnCEuDbV9DNAHVT9-gk-Nmz401vgEQKApyZcugL_lK5zJ17rhS4EBSvgs"/>
          <div class="absolute bottom-4 left-4 right-4 glass-panel p-4 rounded-xl text-primary">
            <p class="text-xs font-bold uppercase mb-1">Toyota Fortuner GR</p>
            <p class="text-[10px] opacity-70">324 clicks today • CTR 4.2%</p>
          </div>
        </div>
        <button class="w-full py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 transition-colors">Manage Campaign</button>
      </div>
    </section>

    <section class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 overflow-hidden">
      <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-primary">Recent Inquiries</h2>
        <div class="flex items-center gap-2 text-sm text-on-surface-variant font-medium">
          <span class="material-symbols-outlined text-lg">filter_list</span>
          Filter by Status
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="text-on-surface-variant text-xs font-black uppercase tracking-widest">
              <th class="px-8 py-4">Customer</th>
              <th class="px-8 py-4">Vehicle Interest</th>
              <th class="px-8 py-4">Source</th>
              <th class="px-8 py-4">Status</th>
              <th class="px-8 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">AR</div>
                  <div>
                    <div class="font-bold text-primary">Adi Rahmadi</div>
                    <div class="text-xs text-on-surface-variant">Pekanbaru, Riau</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5"><span class="font-semibold text-primary">Mitsubishi Pajero Sport 2022</span></td>
              <td class="px-8 py-5"><span class="text-xs px-2 py-1 bg-surface-container-low rounded-md font-bold text-primary">Facebook Marketplace</span></td>
              <td class="px-8 py-5">
                <span class="flex items-center gap-1.5 text-secondary font-bold text-xs uppercase">
                  <span class="w-2 h-2 rounded-full bg-secondary"></span> Waiting Response
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <button class="inline-flex items-center gap-2 bg-[#25D366]/10 text-[#25D366] px-4 py-2 rounded-full font-bold text-xs hover:bg-[#25D366] hover:text-white transition-all">
                  <span class="material-symbols-outlined text-lg">chat</span>
                  WhatsApp
                </button>
              </td>
            </tr>
            <tr class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">SM</div>
                  <div>
                    <div class="font-bold text-primary">Siti Mardiah</div>
                    <div class="text-xs text-on-surface-variant">Kampar</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5"><span class="font-semibold text-primary">Honda HR-V Prestige</span></td>
              <td class="px-8 py-5"><span class="text-xs px-2 py-1 bg-surface-container-low rounded-md font-bold text-primary">Direct Website</span></td>
              <td class="px-8 py-5">
                <span class="flex items-center gap-1.5 text-tertiary-container font-bold text-xs uppercase">
                  <span class="w-2 h-2 rounded-full bg-tertiary-container"></span> Contacted
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <button class="inline-flex items-center gap-2 bg-[#25D366]/10 text-[#25D366] px-4 py-2 rounded-full font-bold text-xs hover:bg-[#25D366] hover:text-white transition-all">
                  <span class="material-symbols-outlined text-lg">chat</span>
                  WhatsApp
                </button>
              </td>
            </tr>
            <tr class="hover:bg-slate-50 transition-colors group">
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">BP</div>
                  <div>
                    <div class="font-bold text-primary">Bambang Pratama</div>
                    <div class="text-xs text-on-surface-variant">Siak</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5"><span class="font-semibold text-primary">Toyota Avanza Veloz 2023</span></td>
              <td class="px-8 py-5"><span class="text-xs px-2 py-1 bg-surface-container-low rounded-md font-bold text-primary">OLX Autos</span></td>
              <td class="px-8 py-5">
                <span class="flex items-center gap-1.5 text-secondary font-bold text-xs uppercase">
                  <span class="w-2 h-2 rounded-full bg-secondary"></span> Hot Lead
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <button class="inline-flex items-center gap-2 bg-[#25D366]/10 text-[#25D366] px-4 py-2 rounded-full font-bold text-xs hover:bg-[#25D366] hover:text-white transition-all">
                  <span class="material-symbols-outlined text-lg">chat</span>
                  WhatsApp
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-6 text-center border-t border-slate-50">
        <button class="text-primary font-bold text-sm hover:underline">View All Leads (856)</button>
      </div>
    </section>
  </main>

  <button class="fixed bottom-8 right-8 w-16 h-16 bg-secondary-container text-on-secondary-fixed rounded-2xl shadow-2xl shadow-secondary/40 flex items-center justify-center group hover:rotate-90 transition-all duration-500 z-[100]" aria-label="Add">
    <span class="material-symbols-outlined text-3xl font-bold">add</span>
  </button>
</body>
</html>

