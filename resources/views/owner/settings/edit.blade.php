<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Setting Owner | Maharani Mobil</title>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-[linear-gradient(180deg,#eef4ff_0%,#f8fbff_28%,#ffffff_100%)] text-slate-900">
  <div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(11,26,64,0.18),transparent_26%),radial-gradient(circle_at_top_right,rgba(245,166,35,0.15),transparent_22%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.10),transparent_18%)]"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-[1600px] gap-6 px-4 py-4 md:px-6 md:py-6">
      <aside class="hidden w-[298px] shrink-0 lg:block">
        <div class="flex h-full flex-col overflow-visible rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.12)] backdrop-blur-[26px]">
          <div class="rounded-[1.6rem] bg-[linear-gradient(135deg,#08132e_0%,#102a63_56%,#f5a623_140%)] px-5 py-5 text-white shadow-[0_18px_40px_rgba(8,19,46,0.24)]">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-[#f7c35f]">Owner Workspace</p>
            <div class="mt-3 text-[28px] font-extrabold tracking-tight">Maharani Mobil</div>
          </div>

          <nav class="mt-5 flex-1 space-y-1.5">
            <a class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-white/80 hover:text-slate-900" href="{{ route('owner.dashboard') }}">
              <span class="material-symbols-outlined text-[20px] text-slate-400 group-hover:text-[#08132e]">dashboard</span>
              <span>Dashboard</span>
            </a>
            <a class="group flex items-center gap-3 rounded-[1.2rem] px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-white/80 hover:text-slate-900" href="{{ route('owner.reports.index') }}">
              <span class="material-symbols-outlined text-[20px] text-slate-400 group-hover:text-[#08132e]">monitoring</span>
              <span>Laporan Penjualan</span>
            </a>
            <a class="flex items-center gap-3 rounded-[1.2rem] bg-[#08132e] px-4 py-3 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(8,19,46,0.18)]" href="{{ route('owner.settings.edit') }}">
              <span class="material-symbols-outlined text-[20px] text-[#f7c35f]">settings</span>
              <span>Setting</span>
            </a>
          </nav>

          <div class="mt-auto rounded-[1.4rem] border border-slate-200/80 bg-white/70 p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Owner Mode</p>
            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $user->name }}</p>
            <p class="mt-1 text-xs text-slate-500">Perbarui identitas akun owner dengan aman dan rapi.</p>
            <form class="mt-4" method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-white" type="submit">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Logout
              </button>
            </form>
          </div>
        </div>
      </aside>

      <div class="min-w-0 flex-1">
        <div class="mb-6 overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] px-5 py-4 shadow-[0_20px_55px_rgba(15,23,42,0.10)] backdrop-blur-[26px] md:px-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="min-w-0">
              <h1 class="mt-1 font-headline text-[24px] font-extrabold tracking-tight text-slate-900 md:text-[28px]">Setting Owner</h1>
              <p class="mt-1 text-sm text-slate-500">Owner Control Center</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-[#08132e]">calendar_month</span>
                {{ now()->format('d M Y') }}
              </div>
              <div class="inline-flex h-11 min-w-11 items-center justify-center rounded-full bg-[linear-gradient(135deg,#08132e_0%,#102a63_100%)] px-3 text-sm font-bold text-white shadow-[0_16px_30px_rgba(8,19,46,0.18)]">
                {{ strtoupper(substr($user->name ?? 'OW', 0, 2)) }}
              </div>
            </div>
          </div>
        </div>

        @if (session('success'))
          <div class="mb-6 rounded-[1.2rem] border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-700 shadow-sm backdrop-blur-xl">
            {{ session('success') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mb-6 rounded-[1.2rem] border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 shadow-sm backdrop-blur-xl">
            Mohon cek kembali input yang Anda kirim.
          </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.7fr]">
          <section class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] md:p-7">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Setting</p>
            <h2 class="mt-2 font-headline text-[28px] font-extrabold tracking-tight text-slate-900">Kelola Akun Owner</h2>
            <p class="mt-3 max-w-[720px] text-sm leading-7 text-slate-600">
              Perbarui identitas akun owner agar akses pemantauan bisnis, laporan penjualan, dan analisis strategi tetap aman dan jelas.
            </p>

            <form class="mt-6 grid gap-5 md:grid-cols-2" method="POST" action="{{ route('owner.settings.update') }}">
              @csrf
              @method('PUT')

              <div>
                <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="name">Nama Lengkap</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
                @error('name')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="phone">Nomor Telepon</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
                @error('phone')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="md:col-span-2">
                <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
                @error('email')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password">Password Baru</label>
                <input id="password" name="password" type="password" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
                @error('password')
                  <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
              </div>

              <div class="md:col-span-2 flex flex-wrap gap-3 pt-1">
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-6 py-3 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)] transition hover:brightness-110">
                  <span class="material-symbols-outlined text-[18px]">save</span>
                  Simpan Perubahan
                </button>
                <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
                  Kembali ke Dashboard
                </a>
              </div>
            </form>
          </section>

          <aside class="space-y-6">
            <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Akun Aktif</p>
              <div class="mt-4 rounded-[1.4rem] border border-slate-200/80 bg-white/75 p-4">
                <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                <span class="mt-4 inline-flex rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-violet-700">
                  {{ $user->role }}
                </span>
              </div>
            </article>

            <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Catatan</p>
              <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                <li>Owner melihat gambaran bisnis secara menyeluruh, termasuk pendapatan, pertumbuhan, dan strategi stok.</li>
                <li>Gunakan email aktif agar laporan dan koordinasi internal tetap mudah dilacak.</li>
                <li>Ganti password hanya jika memang diperlukan untuk menjaga keamanan akun owner.</li>
              </ul>
            </article>
          </aside>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
