@extends('layouts.supervisor')

@php
  use App\Support\CurrencyFormatter;

  $title = 'Detail Pesanan';
  $pageTitle = 'Detail Pesanan';
  $handledRole = $order->handled_role === 'marketing'
    ? 'Marketing'
    : ($order->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Dikelola');
  $paymentRole = $order->payment?->handled_role === 'marketing'
    ? 'Marketing'
    : ($order->payment?->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Dikelola');
  $photoPaths = collect($order->car?->photos ?? [])
    ->filter(fn ($path) => filled($path))
    ->map(fn ($path) => asset('storage/' . ltrim((string) $path, '/')))
    ->values();
  $primaryPhoto = $photoPaths->first();
  $editUnitRoute = $order->car ? route('supervisor.cars.edit', $order->car) : null;
  $paidAmount = (float) ($order->payment?->amount ?? 0);
  $remainingAmount = max((float) $order->total - $paidAmount, 0);
  $documentsReady = $order->areTransactionDocumentsReady();
  $transactionPurchaseMethod = old('purchase_method', $order->is_credit_purchase ? 'credit' : 'cash');
  $transactionPaymentMethod = old(
    'payment_method',
    in_array(($order->payment?->method ?? null), ['cash', 'transfer'], true)
      ? $order->payment?->method
      : ((string) $order->payment_method === 'transfer' ? 'transfer' : 'cash')
  );
  $transactionAmount = old('amount', $order->payment?->amount ?? ($order->is_credit_purchase ? ($order->credit_dp_amount ?? 0) : $order->total));
  $transactionLeasingPartner = old('leasing_partner', $order->leasing_partner);
  $transactionCreditDp = old('credit_dp_amount', $order->credit_dp_amount ?? null);
  $transactionLeasingSettlement = old('leasing_settlement_amount', $order->is_credit_purchase ? max((float) $order->total - (float) ($order->credit_dp_amount ?? 0), 0) : null);
  $paymentMethodLabel = $order->payment?->internal_method_label ?? 'Belum dipilih';
  $roleChipClasses = $order->handled_role === 'marketing'
    ? 'border-amber-200 bg-amber-50/90 text-amber-700'
    : ($order->handled_role === 'supervisor'
      ? 'border-sky-200 bg-sky-50/90 text-sky-700'
      : 'border-slate-200 bg-slate-50/90 text-slate-600');
  $isCreditDpRecorded = $order->is_credit_purchase && $paidAmount > 0 && $paidAmount < (float) $order->total;
@endphp

@section('content')
  <div class="grid items-start gap-6 xl:grid-cols-[1fr_0.82fr]">
    <section class="space-y-6">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">{{ $order->order_reference }}</p>
        <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
          <div class="min-w-0">
            <h2 class="font-headline text-2xl font-extrabold text-slate-900">{{ $order->car?->merk }} {{ $order->car?->tipe }} {{ $order->car?->tahun }}</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500"></p>
          </div>
          <span class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-600 shadow-sm">
            {{ strtoupper($order->status) }}
          </span>
        </div>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.02fr_0.98fr]">
          @if ($primaryPhoto)
            <div class="rounded-[1.6rem] border border-slate-200 bg-white/80 p-4 shadow-sm">
              <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($photoPaths as $photoIndex => $photoPath)
                  <article class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-slate-100 {{ $loop->last && $photoPaths->count() % 2 === 1 ? 'sm:col-span-2' : '' }}">
                    <div class="aspect-[4/3] p-2">
                      <img class="h-full w-full rounded-[0.9rem] object-contain object-center" src="{{ $photoPath }}" alt="Foto unit {{ $photoIndex + 1 }}">
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-white/90 px-3 py-3">
                      <div>
                        <p class="text-sm font-semibold text-slate-800">Foto {{ $photoIndex + 1 }}</p>
                        <p class="text-xs text-slate-500">Tampilan unit tersimpan</p>
                      </div>
                      <div class="flex flex-col items-end gap-1.5">
                        <form method="POST" action="{{ route('supervisor.cars.photos.replace', [$order->car, $photoIndex]) }}" enctype="multipart/form-data" class="photo-replace-form">
                          @csrf
                          @method('PATCH')
                          <input class="hidden photo-replace-input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                          <button class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-sky-200 bg-sky-50 text-sky-700 shadow-sm" type="button" data-photo-replace-trigger title="Ganti foto">
                            <span class="material-symbols-outlined text-[16px]">sync</span>
                          </button>
                        </form>
                        <form method="POST" action="{{ route('supervisor.cars.photos.destroy', [$order->car, $photoIndex]) }}" onsubmit="return confirm('Hapus foto ini dari data mobil?');">
                          @csrf
                          @method('DELETE')
                          <button class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 shadow-sm" type="submit" title="Hapus foto">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                          </button>
                        </form>
                      </div>
                    </div>
                  </article>
                @endforeach
              </div>
              <div class="mt-4 flex items-center justify-between gap-3 rounded-[1.1rem] border border-slate-200 bg-slate-50/80 px-4 py-3">
                <div>
                  <p class="text-sm font-semibold text-slate-800">Galeri unit tersedia</p>
                  <p class="text-xs text-slate-500">{{ $photoPaths->count() }} foto tersimpan pada data mobil ini.</p>
                </div>
                @if ($editUnitRoute)
                  <a class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm" href="{{ $editUnitRoute }}">Kelola Foto Unit</a>
                @endif
              </div>
            </div>
          @else
            <div class="rounded-[1.6rem] border border-rose-200 bg-rose-50/90 p-5 shadow-sm">
              <div class="flex h-full flex-col justify-between gap-5">
                <div class="flex items-start gap-3">
                  <span class="material-symbols-outlined rounded-full bg-rose-100 p-2 text-rose-600">warning</span>
                  <div>
                    <p class="text-base font-bold text-rose-800">Foto unit belum tersedia</p>
                    <p class="mt-2 text-sm leading-6 text-rose-700">Mohon tambahkan foto mobil pada unit ini agar gambar produk dapat ditampilkan dengan jelas.</p>
                  </div>
                </div>
                @if ($editUnitRoute)
                  <a class="inline-flex items-center justify-center self-start rounded-full border border-rose-200 bg-white px-5 py-3 text-sm font-semibold text-rose-700 shadow-sm" href="{{ $editUnitRoute }}">Tambah Foto Unit Sekarang</a>
                @endif
              </div>
            </div>
          @endif

          <div class="grid gap-4 content-start sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-[1.35rem] border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(125,211,252,0.14)]">
              <p class="text-slate-500">Customer</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->user?->name ?? '-' }}</p>
            </div>

            <div class="rounded-[1.35rem] border border-indigo-100 bg-[linear-gradient(135deg,rgba(238,242,255,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(129,140,248,0.12)]">
              <p class="text-slate-500">Alur Customer</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->customer_journey_title }}</p>
              <p class="mt-1 text-slate-500">{{ $order->customer_journey_detail }}</p>
              <p class="mt-2 text-xs font-semibold text-slate-600">Status pembelian: {{ $order->customer_purchase_status_label }}</p>
            </div>

            <div class="rounded-[1.35rem] border border-amber-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(251,191,36,0.12)]">
              <p class="text-slate-500">Dikelola Oleh</p>
              <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $roleChipClasses }}">{{ $handledRole }}</span>
                @if ($order->handledBy?->name)
                  <span class="text-sm font-semibold text-slate-700">{{ $order->handledBy->name }}</span>
                @endif
              </div>
              @unless ($order->handledBy?->name)
                <p class="mt-2 text-slate-500">Pesanan ini belum dikelola oleh supervisor.</p>
              @endunless
            </div>

            <div class="rounded-[1.35rem] border border-emerald-100 bg-[linear-gradient(135deg,rgba(236,253,245,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(52,211,153,0.12)]">
              <p class="text-slate-500">Total</p>
              <p class="mt-1 font-bold text-slate-900">{{ CurrencyFormatter::rupiah($order->total) }}</p>
              <p class="mt-1 text-xs font-semibold {{ $remainingAmount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                {{ $remainingAmount > 0 ? 'Sisa pelunasan: ' . CurrencyFormatter::rupiah($remainingAmount) : 'Pembayaran sudah lunas' }}
              </p>
            </div>

            @if ($order->is_credit_purchase)
              <div class="rounded-[1.35rem] border border-emerald-100 bg-[linear-gradient(135deg,rgba(236,253,245,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(52,211,153,0.12)]">
                <p class="text-slate-500">DP Kredit</p>
                <p class="mt-1 font-bold text-slate-900">{{ CurrencyFormatter::rupiah($order->credit_dp_amount) }}</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">Leasing: {{ $order->leasing_partner_label ?? '-' }}</p>
              </div>
            @endif

            <div class="rounded-[1.35rem] border border-cyan-100 bg-[linear-gradient(135deg,rgba(236,254,255,0.94),rgba(255,255,255,0.82))] p-4 shadow-[0_14px_30px_rgba(103,232,249,0.12)]">
              <p class="text-slate-500">Transaksi & Pembayaran</p>
              <p class="mt-1 font-bold text-slate-900">{{ $order->is_credit_purchase ? 'Kredit' : 'Cash' }}</p>
              <p class="mt-1 text-slate-500">Metode bayar: {{ $paymentMethodLabel }}</p>
              <p class="mt-1 text-slate-500">Transaksi diproses oleh Supervisor</p>
              @if ($isCreditDpRecorded)
                <p class="mt-1 text-xs font-semibold text-emerald-700">Status pembayaran: DP Kredit tercatat</p>
              @endif
              @if ($order->payment?->proof_file)
                <a class="mt-3 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-xs font-semibold text-slate-700" href="{{ asset('storage/' . $order->payment->proof_file) }}" target="_blank" rel="noreferrer">
                  Lihat Arsip Lampiran
                  <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                </a>
              @elseif ($order->payment)
                <p class="mt-2 text-xs font-semibold text-sky-700">Pembayaran divalidasi oleh supervisor.</p>
              @endif
            </div>
          </div>
        </div>
      </article>
    </section>

    <section class="space-y-6">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <h3 class="font-headline text-xl font-extrabold text-slate-900">Kelola Status Pesanan</h3>
        <p class="mt-2 text-sm text-slate-500">Pastikan status sesuai progres transaksi agar dashboard dan laporan tetap konsisten.</p>
        <form class="mt-5 space-y-4" method="POST" action="{{ route('supervisor.orders.updateStatus', $order) }}" data-save-lock-form>
          @csrf
          @method('PATCH')
          @php($selectedStatus = old('status', $order->status))
          <select class="w-full rounded-xl border-slate-200" name="status" required>
            @foreach (['pending', 'confirmed', 'paid', 'completed', 'cancelled'] as $status)
              <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ strtoupper($status) }}</option>
            @endforeach
          </select>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Alasan batal / catatan follow-up</label>
            <input class="w-full rounded-xl border-slate-200" name="cancel_reason" value="{{ old('cancel_reason', $order->cancel_reason) }}" placeholder="Contoh: dana belum cukup, masih membandingkan showroom lain" />
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Jadwal follow-up berikutnya</label>
            <input class="w-full rounded-xl border-slate-200" name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at', $order->next_follow_up_at?->format('Y-m-d\\TH:i')) }}" />
          </div>
          <button class="w-full rounded-full bg-[#7d94c5] px-5 py-3 text-sm font-semibold text-white transition disabled:cursor-not-allowed" type="submit" data-lock-submit disabled>Simpan Status Supervisor</button>
        </form>
      </article>

      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <h3 class="font-headline text-xl font-extrabold text-slate-900">Kelola Transaksi</h3>
        <p class="mt-2 text-sm text-slate-500">Input metode pembelian, pembayaran, dan rincian kredit agar data transaksi tetap akurat.</p>
        <form class="mt-5 space-y-4" method="POST" action="{{ route('supervisor.orders.syncPayment', $order) }}" data-save-lock-form data-credit-transaction-form>
          @csrf
          @method('PATCH')
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Metode Pembelian</label>
            <select class="w-full rounded-xl border-slate-200" name="purchase_method" required data-purchase-method>
              <option value="cash" @selected($transactionPurchaseMethod === 'cash')>Cash</option>
              <option value="credit" @selected($transactionPurchaseMethod === 'credit')>Kredit</option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Metode Pembayaran</label>
            <select class="w-full rounded-xl border-slate-200" name="payment_method" required>
              <option value="cash" @selected($transactionPaymentMethod === 'cash')>Tunai</option>
              <option value="transfer" @selected($transactionPaymentMethod === 'transfer')>Transfer</option>
            </select>
          </div>
          <div class="{{ $transactionPurchaseMethod === 'credit' ? '' : 'hidden' }}" data-credit-fields>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Leasing</label>
            <select class="w-full rounded-xl border-slate-200" name="leasing_partner" data-credit-required>
              <option value="">Pilih leasing</option>
              @foreach ($leasingPartners as $leasingPartner)
                <option value="{{ $leasingPartner }}" @selected($transactionLeasingPartner === $leasingPartner)>{{ $leasingPartner }}</option>
              @endforeach
            </select>
          </div>
          <div class="{{ $transactionPurchaseMethod === 'credit' ? '' : 'hidden' }} grid gap-4 md:grid-cols-2" data-credit-fields>
            <div>
              <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">DP Customer</label>
              <input class="w-full rounded-xl border-slate-200" name="credit_dp_amount" type="number" min="0" step="0.01" value="{{ $transactionCreditDp }}" data-credit-required />
            </div>
            <div>
              <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Sisa Pelunasan oleh Leasing</label>
              <input class="w-full rounded-xl border-slate-200" name="leasing_settlement_amount" type="number" min="0" step="0.01" value="{{ $transactionLeasingSettlement }}" data-credit-required />
            </div>
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nominal Tercatat</label>
            <input class="w-full rounded-xl border-slate-200" name="amount" type="number" min="0" step="0.01" value="{{ $transactionAmount }}" required />
            <p class="mt-2 text-xs text-slate-500">Untuk kredit, isi nominal yang saat ini tercatat di sistem. Saat baru bayar DP, nominal bisa sebesar DP. Setelah pelunasan leasing selesai, ubah menjadi total harga unit.</p>
          </div>
          <button class="w-full rounded-full bg-[#7d94c5] px-5 py-3 text-sm font-semibold text-white transition disabled:cursor-not-allowed" type="submit" data-lock-submit disabled>Simpan Transaksi Supervisor</button>
        </form>
      </article>

      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <h3 class="font-headline text-xl font-extrabold text-slate-900">Dokumen Serah Terima</h3>
        <p class="mt-2 text-sm text-slate-500">Supervisor dapat memakai checklist ini untuk approval akhir sebelum kendaraan benar-benar diserahkan ke customer. Faktur, kwitansi, dan berita acara serah terima hanya aktif jika pembayaran sudah lunas.</p>
        <div class="mt-4 rounded-[1.2rem] border {{ $documentsReady ? 'border-emerald-200 bg-emerald-50/90' : 'border-amber-200 bg-amber-50/90' }} px-4 py-3 text-sm {{ $documentsReady ? 'text-emerald-700' : 'text-amber-700' }}">
          @if ($documentsReady)
            Dokumen transaksi sudah siap dicetak dari sistem karena nominal pembayaran telah memenuhi total harga unit.
          @else
            Dokumen transaksi belum siap. Saat ini pembayaran yang masuk baru {{ CurrencyFormatter::rupiah($paidAmount) }} dari total {{ CurrencyFormatter::rupiah($order->total) }}.
          @endif
        </div>
        <div class="mt-5 space-y-3">
          @foreach ($order->handover_document_checklist as $document)
            <div class="flex items-start justify-between gap-3 rounded-[1.1rem] border border-slate-200 bg-white/80 px-4 py-3">
              <p class="text-sm text-slate-700">{{ $document['label'] }}</p>
              <span class="rounded-full px-2.5 py-1 text-[11px] font-bold uppercase {{ in_array($document['status'], ['siap', 'tersedia'], true) ? 'bg-emerald-100 text-emerald-700' : ($document['status'] === 'belum' || $document['status'] === 'tidak tersedia' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                {{ $document['status'] }}
              </span>
            </div>
          @endforeach
        </div>
        <div class="mt-5 grid gap-2">
          @foreach (['invoice' => 'Download Faktur', 'receipt' => 'Download Kwitansi Digital', 'handover_note' => 'Download BAST'] as $docType => $docLabel)
            @php($docReady = in_array($order->document_status[$docType] ?? 'pending', ['ready', 'submitted', 'done'], true))
            <a
              class="inline-flex items-center justify-center rounded-full px-4 py-2.5 text-sm font-bold {{ $docReady ? 'bg-[#08132e] text-white' : 'pointer-events-none bg-slate-100 text-slate-400' }}"
              href="{{ $docReady ? route('documents.orders.download', [$order, $docType]) : '#' }}"
            >
              {{ $docLabel }}
            </a>
          @endforeach
        </div>
      </article>
    </section>
  </div>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('.photo-replace-form').forEach(function (form) {
      const input = form.querySelector('.photo-replace-input');
      const trigger = form.querySelector('[data-photo-replace-trigger]');

      if (!input || !trigger) return;

      trigger.addEventListener('click', function () {
        input.click();
      });

      input.addEventListener('change', function () {
        if (input.files && input.files.length > 0) {
          form.submit();
        }
      });
    });

    document.querySelectorAll('[data-save-lock-form]').forEach(function (form) {
      const submitButton = form.querySelector('[data-lock-submit]');
      const fields = Array.from(form.querySelectorAll('input, select, textarea')).filter(function (field) {
        return field.name && !['submit', 'button', 'file', 'hidden'].includes(field.type);
      });

      if (!submitButton || fields.length === 0) {
        return;
      }

      function snapshot() {
        return JSON.stringify(fields.map(function (field) {
          if (field.type === 'checkbox' || field.type === 'radio') {
            return field.checked ? field.value : '';
          }

          return field.value;
        }));
      }

      const initialSnapshot = snapshot();

      function syncButtonState() {
        const isDirty = snapshot() !== initialSnapshot;

        submitButton.disabled = !isDirty;
        submitButton.classList.toggle('bg-[#08132e]', isDirty);
        submitButton.classList.toggle('bg-[#7d94c5]', !isDirty);
        submitButton.classList.toggle('cursor-not-allowed', !isDirty);
      }

      fields.forEach(function (field) {
        field.addEventListener('input', syncButtonState);
        field.addEventListener('change', syncButtonState);
      });

      syncButtonState();
    });

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
