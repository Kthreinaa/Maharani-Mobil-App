@extends($layout)

@section('content')
  @php
    $selectedPurchaseMethod = old('purchase_method', 'cash');
  @endphp

  <form class="grid gap-6 xl:grid-cols-[1fr_0.72fr]" method="POST" action="{{ $submitRoute }}" data-credit-transaction-form>
    @csrf

    <section class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Input Internal {{ $workspaceLabel }}</p>
      <h2 class="mt-2 font-headline text-2xl font-extrabold text-slate-900">Catat transaksi showroom ke sistem</h2>
      <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600">
        Form ini dipakai untuk customer offline atau customer online yang sudah datang test drive di showroom. Jadi setelah negosiasi di lokasi, proses transaksi tetap tercatat digital dan tidak kembali ke pencatatan manual.
      </p>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nama Customer</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Contoh: Rina" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Email Customer</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_email" type="email" value="{{ old('customer_email') }}" placeholder="Opsional untuk customer offline" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nomor WhatsApp</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Domisili</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_city" value="{{ old('customer_city') }}" placeholder="Pekanbaru / luar kota" />
        </div>
      </div>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Unit Mobil</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="car_id" required>
            <option value="">Pilih unit</option>
            @foreach ($cars as $car)
              <option value="{{ $car->id }}" @selected((int) old('car_id') === (int) $car->id)>
                {{ $car->kode_unit ?: 'Tanpa kode' }} - {{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }} / {{ \App\Support\CurrencyFormatter::rupiah($car->harga) }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Sumber Transaksi</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="transaction_channel" required>
            <option value="offline" @selected(old('transaction_channel', 'offline') === 'offline')>Datang ke Showroom</option>
            <option value="online" @selected(old('transaction_channel') === 'online')>Online setelah Test Drive</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Alur Pembelian</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="sales_flow">
            <option value="after_test_drive" @selected(old('sales_flow', 'after_test_drive') === 'after_test_drive')>Dengan Test Drive</option>
            <option value="direct_purchase" @selected(old('sales_flow') === 'direct_purchase')>Tanpa Test Drive</option>
          </select>
          <p class="mt-2 text-xs text-slate-500">Jika sumbernya datang ke showroom, sistem otomatis menandai jalur sebagai test drive di showroom.</p>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Status Transaksi</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="status" required>
            @foreach (['pending' => 'Prospek / negosiasi', 'confirmed' => 'Menunggu pembayaran', 'paid' => 'Pembayaran terverifikasi', 'completed' => 'Transaksi selesai', 'cancelled' => 'Batal membeli'] as $value => $label)
              <option value="{{ $value }}" @selected(old('status', 'confirmed') === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Metode Pembelian</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="purchase_method" data-purchase-method required>
            <option value="cash" @selected($selectedPurchaseMethod === 'cash')>Cash</option>
            <option value="credit" @selected($selectedPurchaseMethod === 'credit')>Kredit</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Metode Pembayaran</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="payment_method" required>
            <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
            <option value="transfer" @selected(old('payment_method', 'transfer') === 'transfer')>Transfer</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nominal Transaksi</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="amount" type="number" min="0" step="0.01" value="{{ old('amount') }}" placeholder="Contoh: 165000000" required />
        </div>
      </div>

      <div class="{{ $selectedPurchaseMethod === 'credit' ? '' : 'hidden' }} mt-6 grid gap-5 md:grid-cols-3" data-credit-fields>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Leasing</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="leasing_partner" data-credit-required>
            <option value="">Pilih leasing</option>
            @foreach (($leasingPartners ?? collect()) as $leasingPartner)
              <option value="{{ $leasingPartner }}" @selected(old('leasing_partner') === $leasingPartner)>{{ $leasingPartner }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">DP Customer</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="credit_dp_amount" type="number" min="0" step="0.01" value="{{ old('credit_dp_amount') }}" placeholder="Contoh: 30000000" data-credit-required />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Sisa Pelunasan oleh Leasing</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="leasing_settlement_amount" type="number" min="0" step="0.01" value="{{ old('leasing_settlement_amount') }}" placeholder="Contoh: 120000000" data-credit-required />
        </div>
      </div>
      <p class="mt-3 text-xs text-slate-500">Jika pembelian kredit dipilih, nominal transaksi harus sama dengan DP customer ditambah sisa pelunasan dari leasing.</p>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Alasan batal / catatan CRM</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="cancel_reason" value="{{ old('cancel_reason') }}" placeholder="Diisi jika customer batal membeli" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Jadwal Follow-up</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at') }}" />
        </div>
      </div>

      <div class="mt-6">
        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Catatan Tambahan</label>
        <textarea class="h-28 w-full rounded-xl border-slate-200 bg-white/80" name="notes" placeholder="Contoh: customer sudah test drive, deal setelah negosiasi, pelunasan dijadwalkan akhir pekan">{{ old('notes') }}</textarea>
      </div>

      <div class="mt-6 flex flex-wrap justify-end gap-3">
        <a class="rounded-full border border-slate-200 bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700" href="{{ $backRoute }}">Batal</a>
        <button class="rounded-full bg-[#08132e] px-6 py-3 text-sm font-bold text-white" type="submit">Simpan ke Sistem</button>
      </div>
    </section>

    <aside class="space-y-5">
      <article class="rounded-[2rem] border border-white/70 bg-[linear-gradient(135deg,#08132e_0%,#102a63_65%,#f5a623_150%)] p-6 text-white shadow-[0_24px_70px_rgba(15,23,42,0.14)]">
        <h3 class="font-headline text-xl font-extrabold">Kenapa transaksi offline tetap diinput?</h3>
        <p class="mt-3 text-sm leading-7 text-slate-100">
          Karena laporan owner harus membaca semua penjualan showroom, bukan hanya customer yang checkout sendiri lewat website.
        </p>
      </article>
      <article class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-[0_18px_50px_rgba(15,23,42,0.07)] backdrop-blur-[18px]">
        <h3 class="font-headline text-xl font-extrabold text-slate-900">Dokumen yang disiapkan</h3>
        <div class="mt-4 space-y-3 text-sm text-slate-700">
          <p class="rounded-xl bg-slate-50 px-4 py-3">Faktur / invoice pembelian</p>
          <p class="rounded-xl bg-slate-50 px-4 py-3">Kwitansi digital setelah pembayaran terverifikasi</p>
          <p class="rounded-xl bg-slate-50 px-4 py-3">Berita acara serah terima kendaraan saat transaksi selesai</p>
          <p class="rounded-xl bg-slate-50 px-4 py-3">Status ketersediaan STNK dan BPKB</p>
        </div>
      </article>
    </aside>
  </form>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('[data-credit-transaction-form]').forEach(function (form) {
      const purchaseMethodField = form.querySelector('[data-purchase-method]');
      const creditSections = form.querySelectorAll('[data-credit-fields]');
      const creditRequiredFields = form.querySelectorAll('[data-credit-required]');

      if (!purchaseMethodField) {
        return;
      }

      function syncCreditSections() {
        const isCredit = purchaseMethodField.value === 'credit';

        creditSections.forEach(function (section) {
          section.classList.toggle('hidden', !isCredit);
        });

        creditRequiredFields.forEach(function (field) {
          field.required = isCredit;
        });
      }

      purchaseMethodField.addEventListener('change', syncCreditSections);
      syncCreditSections();
    });
  </script>
@endpush
