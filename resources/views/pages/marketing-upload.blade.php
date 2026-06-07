<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Produk | Marketing</title>
  <meta name="description" content="Upload unit mobil baru ke katalog Maharani Mobil."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-surface text-on-surface">
  <aside class="h-screen w-64 fixed left-0 top-0 border-r border-slate-100 bg-white font-body text-sm flex flex-col p-4 z-50">
    <div class="mb-10 px-4"><a class="text-xl font-bold text-[#1A2B4C]" href="{{ route('marketing.dashboard') }}">Maharani Mobil</a></div>
    <nav class="flex-1 space-y-2">
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.dashboard') }}"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.products.index') }}"><span class="material-symbols-outlined">directions_car</span>Manajemen Produk</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.orders.index') }}"><span class="material-symbols-outlined">leaderboard</span>Manajemen Pesanan</a>
      <a class="text-slate-500 hover:bg-slate-50 px-4 py-3 flex items-center gap-3 rounded-lg" href="{{ route('marketing.offers.index') }}"><span class="material-symbols-outlined">sell</span>Manajemen Penawaran</a>
      <a class="bg-slate-100 text-[#1A2B4C] font-semibold rounded-lg px-4 py-3 flex items-center gap-3" href="{{ route('marketing.products.upload') }}"><span class="material-symbols-outlined">upload</span>Upload Produk</a>
    </nav>
    <form class="mt-auto pt-6 border-t border-slate-100" method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="w-full text-left text-slate-500 hover:bg-slate-50 px-4 py-2 flex items-center gap-3 rounded-lg" type="submit"><span class="material-symbols-outlined">logout</span>Logout</button>
    </form>
  </aside>

  <main class="ml-64 min-h-screen p-8">
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-primary">Upload Produk Mobil</h1>
        <p class="text-on-surface-variant">Form ini langsung terhubung ke tabel mobil dan akan tampil di katalog setelah disimpan.</p>
      </div>
      <a class="px-6 py-2 rounded-full border border-outline-variant font-semibold hover:bg-surface-container-low transition-colors" href="{{ route('marketing.products.index') }}">View Inventory</a>
    </header>

    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        Data produk belum lengkap. Mohon periksa form upload terlebih dahulu.
      </div>
    @endif

    <section class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-8">
      <form method="POST" action="{{ route('marketing.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Kode Unit</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="kode_unit" placeholder="MM-AVZ-017-01" type="text" value="{{ old('kode_unit') }}"/>
                <p class="mt-2 text-xs text-slate-500"></p>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Status</label>
                <select class="w-full rounded-xl border border-outline-variant p-4" name="status">
                  @foreach (['available', 'reserved', 'sold'] as $status)
                    <option value="{{ $status }}" @selected(old('status', 'available') === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Merk</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="merk" placeholder="Toyota" type="text" value="{{ old('merk') }}"/>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tipe</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="tipe" placeholder="Camry 2.5 V Hybrid" type="text" value="{{ old('tipe') }}"/>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tahun</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="tahun" placeholder="2022" type="number" value="{{ old('tahun') }}"/>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Harga (IDR)</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="harga" placeholder="545000000" type="number" value="{{ old('harga') }}"/>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Kilometer</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="kilometer" placeholder="12450" type="number" value="{{ old('kilometer') }}"/>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Transmisi</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="transmisi" placeholder="Automatic" type="text" value="{{ old('transmisi') }}"/>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Warna</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="warna" placeholder="White Pearl" type="text" value="{{ old('warna') }}"/>
              </div>
              <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Bahan Bakar</label>
                <input class="w-full rounded-xl border border-outline-variant p-4" name="bahan_bakar" placeholder="Hybrid" type="text" value="{{ old('bahan_bakar') }}"/>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Deskripsi Jujur</label>
              <textarea class="w-full rounded-xl border border-outline-variant p-4 h-32" name="deskripsi" placeholder="Kondisi unit, kelebihan, dan catatan penting lainnya...">{{ old('deskripsi') }}</textarea>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Upload Foto (1-5 file)</label>
              <div class="border-2 border-dashed border-outline-variant rounded-2xl p-6 text-center bg-surface-container-low">
                <span class="material-symbols-outlined text-3xl text-primary">cloud_upload</span>
                <p class="font-semibold text-primary mt-2">Pilih foto mobil</p>
                <p class="text-xs text-on-surface-variant">Format JPG, PNG, WEBP. Maksimal 4 MB per file.</p>
                <input class="mt-4 w-full rounded-xl border border-outline-variant bg-white p-3" multiple name="photos[]" type="file" accept=".jpg,.jpeg,.png,.webp"/>
              </div>
            </div>
            <div class="rounded-2xl bg-surface-container-low p-5">
              <h3 class="font-bold text-primary">Checklist sebelum publish</h3>
              <ul class="mt-4 space-y-3 text-sm text-on-surface-variant">
                <li>Pastikan kode unit unik.</li>
                <li>Isi deskripsi apa adanya untuk menjaga trust customer.</li>
                <li>Upload minimal satu foto utama yang jelas.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="mt-8 flex justify-end">
          <button class="px-6 py-3 rounded-xl bg-primary text-white font-semibold" type="submit">Publish Listing</button>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
