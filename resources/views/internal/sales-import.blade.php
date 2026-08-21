@extends($layout)

@php
  $stats = session('import_stats');
  $importedFileName = session('import_file_name');
  $purgedStats = session('purge_stats');
  $salesTemplateDownload = file_exists(public_path('templates/template-penjualan-maharani.xlsx'))
    ? asset('templates/template-penjualan-maharani.xlsx')
    : null;
@endphp

@section('content')
  <section class="mb-6 overflow-hidden rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] md:p-7">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
      <div class="max-w-[780px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">{{ $workspace }}</p>
        <h2 class="mt-3 font-headline text-[28px] font-extrabold tracking-tight text-slate-900">Import Workbook Penjualan Excel</h2>
        <p class="mt-3 text-sm leading-7 text-slate-600">
          Upload file `.xlsx` penjualan dari tahun mana pun. Sistem akan membaca setiap sheet, membentuk data mobil, customer, order, dan pembayaran secara otomatis dengan referensi import dan BM unit agar data duplikat tidak masuk dua kali. Jika file lama belum punya kolom BM, sistem juga akan mencoba membaca nomor plat dari teks seperti catatan, keterangan, atau nopol yang tertulis di baris Excel.
        </p>
      </div>

      <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 px-5 py-4 text-sm text-slate-600 shadow-sm">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Akses Aktif</p>
        <p class="mt-2 font-semibold text-slate-900">{{ $accent }}</p>
        <p class="mt-1 text-xs leading-6 text-slate-500">Import ini akan mencatat user login sebagai penginput arsip penjualan.</p>
      </div>
    </div>
  </section>

  @if ($stats || $purgedStats)
    <section class="mb-6 rounded-[2rem] border border-emerald-200 bg-emerald-50/90 p-6 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-emerald-600">{{ $stats ? 'Import Summary' : 'Purge Summary' }}</p>
          <h3 class="mt-2 font-headline text-[24px] font-extrabold text-emerald-900">{{ $stats ? 'Import berhasil diproses' : 'Data import berhasil dibersihkan' }}</h3>
          @if ($stats)
            <p class="mt-2 text-sm text-emerald-800">
              File: <span class="font-semibold">{{ $importedFileName }}</span>
            </p>
          @endif
        </div>
      </div>

      <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @php
          $summaryCards = $stats
            ? [
                ['label' => 'Mobil Dibuat', 'value' => $stats['cars_created'] ?? 0],
                ['label' => 'Customer Import Dibuat', 'value' => $stats['customers_created'] ?? 0],
                ['label' => 'Order Dibuat', 'value' => $stats['orders_created'] ?? 0],
                ['label' => 'Pembayaran Dibuat', 'value' => $stats['payments_created'] ?? 0],
                ['label' => 'Duplikat Kombinasi', 'value' => $stats['skipped_fingerprint_duplicates'] ?? 0],
                ['label' => 'Baris Dilewati', 'value' => $stats['skipped'] ?? 0],
              ]
            : [
                ['label' => 'Mobil Dihapus', 'value' => $purgedStats['cars'] ?? 0],
                ['label' => 'Customer Import Dihapus', 'value' => $purgedStats['customers'] ?? 0],
                ['label' => 'Order Dihapus', 'value' => $purgedStats['orders'] ?? 0],
                ['label' => 'Pembayaran Dihapus', 'value' => $purgedStats['payments'] ?? 0],
                ['label' => 'Penawaran Arsip Dihapus', 'value' => $purgedStats['offers'] ?? 0],
                ['label' => 'Test Drive Arsip Dihapus', 'value' => $purgedStats['test_drives'] ?? 0],
              ];
        @endphp

        @foreach ($summaryCards as $card)
          <div class="rounded-[1.3rem] border border-emerald-200/70 bg-white/80 px-4 py-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600">{{ $card['label'] }}</p>
            <p class="mt-2 text-[26px] font-extrabold tracking-tight text-emerald-950">{{ number_format((int) $card['value']) }}</p>
          </div>
        @endforeach
      </div>
    </section>
  @endif

  <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <article class="flex h-full flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div>
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Upload Workbook</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Mulai Import Penjualan</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">
          Gunakan file `.xlsx` dengan struktur kolom penjualan showroom. Jika tersedia, sertakan kolom `BM`, `Plat`, atau `Nopol` agar sistem bisa menolak unit duplikat dengan lebih akurat. Untuk file lama tanpa kolom khusus, tetap usahakan nomor plat tertulis jelas di kolom catatan atau keterangan.
        </p>
      </div>

      <form class="mt-6 space-y-5" method="POST" action="{{ route($route_prefix . '.imports.sales.store') }}" enctype="multipart/form-data">
        @csrf

        <div>
          <label class="text-sm font-semibold text-slate-900" for="sales_workbook">File Excel Penjualan <span class="text-rose-600">Wajib .xlsx</span></label>
          <input
            id="sales_workbook"
            name="sales_workbook"
            type="file"
            accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            required
            class="mt-2 block w-full rounded-[1rem] border {{ $errors->has('sales_workbook') ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200 bg-white/80' }} file:mr-4 file:rounded-full file:border-0 file:bg-[#08132e] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
          />
          <p class="mt-2 text-xs font-semibold {{ $errors->has('sales_workbook') ? 'text-rose-600' : 'text-slate-500' }}">
            Sistem hanya menerima file `.xlsx`, maksimal 10MB, dan akan menolak file yang struktur sheet-nya tidak bisa dibaca.
          </p>
          @error('sales_workbook')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex flex-wrap gap-3">
          <button class="inline-flex items-center justify-center gap-2 rounded-full bg-[#08132e] px-5 py-2.5 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)]" type="submit">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            Import Excel Sekarang
          </button>
          <a class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white/80 px-5 py-2.5 text-sm font-semibold text-slate-700" href="{{ route($route_prefix . '.dashboard') }}">
            Kembali ke Dashboard
          </a>
        </div>
      </form>
    </article>

    <div class="h-full">
      <article class="flex h-full flex-col rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Riwayat Import</p>
        <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Pilih File yang Akan Dihapus</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">
          Klik ikon hapus di ujung kanan baris file. Pastikan file yang dipilih memang benar.
        </p>

        @if (!empty($import_history))
          <div class="mt-5 max-h-[15.5rem] overflow-y-auto rounded-[1.5rem] border border-slate-200/80 bg-white/85 pr-1">
            @foreach ($import_history as $history)
              <div class="{{ !$loop->last ? 'border-b border-slate-200/80' : '' }}">
                <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-base font-extrabold text-slate-900">{{ $history['source_name'] }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold">
                      <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">Tahun {{ $history['year'] }}</span>
                      @if ($history['imported_at'])
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700">Import {{ $history['imported_at']->format('d M Y H:i') }}</span>
                      @endif
                      <span class="rounded-full bg-slate-50 px-3 py-1 text-slate-600">
                        {{ number_format((int) $history['orders']) }} order • {{ number_format((int) $history['cars']) }} mobil • {{ number_format((int) $history['payments']) }} pembayaran
                      </span>
                    </div>
                  </div>

                  <form method="POST" action="{{ route($route_prefix . '.imports.sales.destroy') }}" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="import_year" value="{{ $history['year'] }}">
                    <input type="hidden" name="import_source" value="{{ $history['source_name'] }}">
                    <button
                      type="submit"
                      data-import-file="{{ $history['source_name'] }}"
                      data-import-year="{{ $history['year'] }}"
                      class="js-open-delete-modal inline-flex h-11 w-11 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100 hover:text-rose-700"
                      aria-label="Hapus file {{ $history['source_name'] }}"
                      title="Hapus file"
                    >
                      <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="mt-5 rounded-[1.25rem] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-5 text-sm text-slate-500">
            Belum ada riwayat import Excel yang tersimpan, jadi belum ada target hapus per file atau per tahun.
          </div>
        @endif
      </article>
    </div>
  </section>

  <section class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <article class="rounded-[2rem] border border-rose-200 bg-rose-50/80 p-6 shadow-sm">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rose-600">Reset Import</p>
      <h3 class="mt-2 font-headline text-[24px] font-extrabold text-rose-900">Hapus Semua Data Import Excel</h3>
      <p class="mt-2 text-sm leading-6 text-rose-800">
        Gunakan tombol ini hanya jika memang ingin membersihkan seluruh arsip Excel yang pernah diimport. Kalau hanya ingin menghapus tahun atau file tertentu, gunakan daftar target di atas agar tidak salah hapus. Penawaran atau test drive yang dulu pernah tergenerasi otomatis dari import lama juga akan ikut dibersihkan.
      </p>

      @error('purge_import')
        <p class="mt-4 rounded-[1rem] border border-rose-200 bg-white/80 px-4 py-3 text-sm font-semibold text-rose-700">{{ $message }}</p>
      @enderror

      <form class="mt-5" method="POST" action="{{ route($route_prefix . '.imports.sales.destroy') }}">
        @csrf
        @method('DELETE')
        <button
          type="submit"
          onclick="return confirm('Hapus seluruh data hasil import Excel dari sistem? Semua target import akan dibersihkan dan tindakan ini tidak bisa dibatalkan.')"
          class="inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_16px_28px_rgba(225,29,72,0.18)] transition hover:bg-rose-700"
        >
          <span class="material-symbols-outlined text-[18px]">delete_forever</span>
          Hapus Seluruh Data Excel
        </button>
      </form>
    </article>

    <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Panduan Format</p>
      <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900"></h3>

      <div class="mt-5 space-y-4 text-sm leading-6 text-slate-600">
        <div class="rounded-[1.25rem] border border-[#d8e3f5] bg-[linear-gradient(135deg,rgba(8,19,46,0.04)_0%,rgba(245,166,35,0.08)_100%)] px-4 py-4">
          <p class="font-semibold text-slate-900">Template Excel Maharani Mobil</p>
          <p class="mt-2">
            Gunakan template ini agar workbook yang diupload selalu sesuai dengan format kolom, susunan sheet, dan struktur data yang dibutuhkan sistem import.
          </p>
          @if ($salesTemplateDownload)
            <div class="mt-4 flex flex-wrap items-center gap-3">
              <a
                class="inline-flex items-center justify-center gap-2 rounded-full bg-[#08132e] px-4 py-2.5 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)]"
                href="{{ $salesTemplateDownload }}"
                download="Template Penjualan Maharani.xlsx"
              >
                <span class="material-symbols-outlined text-[18px]">download</span>
                Download Template Excel
              </a>
              <span class="text-xs font-medium text-slate-500">Disarankan gunakan file ini sebelum mengisi data penjualan.</span>
            </div>
          @endif
        </div>
       
    </article>
  </section>

  <div id="deleteImportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 px-4">
    <div class="w-full max-w-md rounded-[1.8rem] border border-white/70 bg-white p-6 shadow-[0_24px_70px_rgba(15,23,42,0.20)]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rose-500">Konfirmasi Hapus</p>
      <h3 class="mt-2 font-headline text-[24px] font-extrabold text-slate-900">Apakah Anda ingin menghapus file ini?</h3>
      <p id="deleteImportModalText" class="mt-3 text-sm leading-6 text-slate-600"></p>

      <div class="mt-6 flex flex-wrap justify-end gap-3">
        <button
          type="button"
          id="deleteImportCancel"
          class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
        >
          Tidak
        </button>
        <button
          type="button"
          id="deleteImportConfirm"
          class="inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_16px_28px_rgba(225,29,72,0.18)] transition hover:bg-rose-700"
        >
          <span class="material-symbols-outlined text-[18px]">delete</span>
          Iya, Hapus
        </button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      const modal = document.getElementById('deleteImportModal');
      const modalText = document.getElementById('deleteImportModalText');
      const confirmButton = document.getElementById('deleteImportConfirm');
      const cancelButton = document.getElementById('deleteImportCancel');
      let activeForm = null;

      if (!modal || !modalText || !confirmButton || !cancelButton) {
        return;
      }

      const closeModal = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        activeForm = null;
      };

      document.querySelectorAll('.js-open-delete-modal').forEach(function (button) {
        button.addEventListener('click', function (event) {
          event.preventDefault();
          activeForm = button.closest('form');

          const fileName = button.dataset.importFile || 'file Excel ini';
          const year = button.dataset.importYear || '';
          modalText.textContent = 'File ' + fileName + (year ? ' untuk tahun ' + year : '') + ' akan dihapus dari sistem.';

          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });
      });

      cancelButton.addEventListener('click', closeModal);

      confirmButton.addEventListener('click', function () {
        if (activeForm) {
          activeForm.submit();
        }
      });

      modal.addEventListener('click', function (event) {
        if (event.target === modal) {
          closeModal();
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
          closeModal();
        }
      });
    })();
  </script>
@endpush
