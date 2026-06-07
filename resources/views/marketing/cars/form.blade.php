@section('content')
  @php
    $hasPhotoError = $errors->has('photos') || $errors->has('photos.*');
    $currentPhotos = collect($car?->photos ?? [])
      ->filter(fn ($path) => filled($path))
      ->values();
  @endphp

  <form class="max-w-4xl rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]" method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method)
      @method($method)
    @endif

    @if ($errors->any())
      <div class="mb-5 rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        Data produk belum lengkap. Mohon periksa kembali form.
      </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div>
        <label class="text-sm font-semibold">Kode Unit</label>
        <input id="kode_unit" class="mt-2 w-full rounded-xl border-slate-200" name="kode_unit" placeholder="MM-AVZ-024-524" value="{{ old('kode_unit', $car?->kode_unit) }}" required/>
        <p class="mt-2 text-xs text-slate-500">
          @if($car)
            Format saran: `MM-KODETIPE-TAHUN-URUT`, contoh `MM-AVZ-017-01`.
          @else
            Nomor urut berikutnya: {{ str_pad((string) ($nextUnitSequence ?? 1), 3, '0', STR_PAD_LEFT) }}. Kode akan disarankan otomatis setelah tipe dan tahun diisi.
          @endif
        </p>
      </div>
      <div>
        <label class="text-sm font-semibold">Status</label>
        <select class="mt-2 w-full rounded-xl border-slate-200" name="status" required>
          @foreach(['available' => 'Available', 'reserved' => 'Reserved', 'sold' => 'Sold'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $car?->status ?? 'available') === $value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-sm font-semibold">Merk</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="merk" value="{{ old('merk', $car?->merk) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tipe</label>
        <input id="tipe" class="mt-2 w-full rounded-xl border-slate-200" name="tipe" value="{{ old('tipe', $car?->tipe) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tahun</label>
        <input id="tahun" class="mt-2 w-full rounded-xl border-slate-200" name="tahun" type="number" value="{{ old('tahun', $car?->tahun) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Harga</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="harga" type="number" value="{{ old('harga', $car?->harga) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Kilometer</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="kilometer" type="number" value="{{ old('kilometer', $car?->kilometer) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Transmisi</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="transmisi" value="{{ old('transmisi', $car?->transmisi) }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Warna</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="warna" value="{{ old('warna', $car?->warna) }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Bahan Bakar</label>
        <input class="mt-2 w-full rounded-xl border-slate-200" name="bahan_bakar" value="{{ old('bahan_bakar', $car?->bahan_bakar) }}"/>
      </div>
    </div>

    <div class="mt-4">
      <label class="text-sm font-semibold">Deskripsi</label>
      <textarea class="mt-2 w-full rounded-xl border-slate-200" name="deskripsi" rows="4">{{ old('deskripsi', $car?->deskripsi) }}</textarea>
    </div>

    @if($car && $currentPhotos->isNotEmpty())
      <section id="stored-photos" class="mt-5 rounded-[1.4rem] border border-slate-200 bg-white/80 p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
          <label class="text-sm font-semibold text-slate-900">Foto Tersimpan</label>
          <p class="text-xs font-semibold text-slate-500">Foto hanya bisa dihapus jika setelah dihapus tetap tersisa minimal 5 foto.</p>
        </div>
        <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          @foreach ($currentPhotos as $photoIndex => $photoPath)
            <article class="overflow-hidden rounded-[1.2rem] border border-slate-200 bg-white shadow-sm {{ $loop->last && $currentPhotos->count() % 2 === 1 ? 'sm:col-span-2 xl:col-span-1' : '' }}">
              <div class="aspect-[4/3] bg-slate-100 p-2">
                <img class="h-full w-full rounded-[0.9rem] object-contain object-center" src="{{ asset('storage/' . ltrim((string) $photoPath, '/')) }}" alt="Foto mobil {{ $photoIndex + 1 }}">
              </div>
              <div class="flex items-center justify-between gap-3 px-3 py-3">
                <p class="text-xs font-semibold text-slate-500">Foto {{ $photoIndex + 1 }}</p>
                <div class="flex flex-col items-end gap-1.5">
                  <button class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-sky-200 bg-sky-50 text-sky-700 shadow-sm" type="button" data-photo-replace-trigger data-input-id="replace-photo-input-{{ $photoIndex }}" title="Ganti foto">
                    <span class="material-symbols-outlined text-[16px]">sync</span>
                  </button>
                  <button class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-rose-200 bg-rose-50 text-rose-700 shadow-sm" type="button" data-delete-form-id="delete-photo-form-{{ $photoIndex }}" title="Hapus foto">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                  </button>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </section>
    @endif

    <div class="mt-4">
      <label class="text-sm font-semibold text-slate-900" for="photos">
        {{ $car ? 'Tambah Foto Mobil' : 'Foto Mobil' }}
        <span class="{{ $car ? 'text-slate-500' : 'text-rose-600' }}">{{ $car ? 'Opsional' : 'Wajib minimal 5 foto' }}</span>
      </label>
      <input class="mt-2 block w-full rounded-xl border {{ $hasPhotoError ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200' }} file:mr-4 file:rounded-full file:border-0 file:bg-[#08132e] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white" type="file" name="photos[]" id="photos" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @required(!$car)/>
      <p id="photos-helper" class="mt-2 text-xs font-semibold {{ $hasPhotoError ? 'text-rose-600' : 'text-slate-500' }}">
        {{ $car ? 'Tambahkan 1 foto atau lebih jika ingin melengkapi galeri unit. Minimal total foto tetap 5, dan jumlah foto boleh lebih dari 5.' : 'Wajib upload minimal 5 foto mobil. Sistem tidak akan melanjutkan jika foto kurang dari 5.' }}
      </p>
      @if($car)
        <p class="mt-2 text-xs text-slate-500">Jumlah foto saat ini: {{ $currentPhotos->count() }} foto.</p>
      @endif
    </div>

    <div id="selected-photos-preview-shell" class="mt-4 hidden">
      <div class="flex items-center justify-between gap-3">
        <label class="text-sm font-semibold text-slate-900">Preview Foto Baru</label>
        <p class="text-xs font-semibold text-slate-500">Foto yang dipilih akan ditambahkan ke galeri setelah tombol update ditekan.</p>
      </div>
      <div id="selected-photos-preview" class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
      <a href="{{ route('marketing.products.index') }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700">Batal</a>
      <button class="rounded-full bg-[#08132e] px-5 py-2 text-sm font-semibold text-white">{{ $car ? 'Update Produk' : 'Simpan Produk' }}</button>
    </div>
  </form>

  @if($car)
    @foreach ($currentPhotos as $photoIndex => $photoPath)
      <form id="replace-photo-form-{{ $photoIndex }}" method="POST" action="{{ route('marketing.products.photos.replace', [$car, $photoIndex]) }}" enctype="multipart/form-data" class="hidden">
        @csrf
        @method('PATCH')
        <input id="replace-photo-input-{{ $photoIndex }}" class="hidden photo-replace-input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
      </form>

      <form id="delete-photo-form-{{ $photoIndex }}" method="POST" action="{{ route('marketing.products.photos.destroy', [$car, $photoIndex]) }}" class="hidden">
        @csrf
        @method('DELETE')
      </form>
    @endforeach
  @endif
@endsection

@push('scripts')
  <script>
    (function () {
      const codeInput = document.getElementById('kode_unit');
      const typeInput = document.getElementById('tipe');
      const yearInput = document.getElementById('tahun');
      const nextSequence = @json((int) ($nextUnitSequence ?? 1));
      let codeTouched = codeInput && codeInput.value.trim() !== '';

      function typeCodeFromValue(value) {
        const normalized = (value || '').toUpperCase().replace(/[^A-Z0-9 ]/g, ' ').replace(/\s+/g, ' ').trim();
        if (!normalized) return 'UNK';

        const map = {
          'ACCORD': 'ACD', 'AGYA': 'AGY', 'ALVEZ': 'ALV', 'APV': 'APV', 'AVANZA': 'AVZ', 'AYLA': 'AYL',
          'BALENO': 'BLN', 'BRV': 'BRV', 'BRIO': 'BRI', 'CALYA': 'CLY', 'CAMRY': 'CMR', 'CAPTIVA': 'CPT',
          'CARRY': 'CRY', 'CITY': 'CTY', 'CIVIC': 'CVC', 'CONFERO': 'CFR', 'COROLLA': 'CRL', 'CRV': 'CRV',
          'CX3': 'CX3', 'ERTIGA': 'ERT', 'ETIOS': 'ETS', 'FORTUNER': 'FRT', 'FREED': 'FRD', 'GRAN': 'GRN',
          'GRAND': 'GRD', 'HILUX': 'HLX', 'HRV': 'HRV', 'INNOVA': 'INV', 'JAZZ': 'JZZ', 'JUKE': 'JUK',
          'KARIMUN': 'KRM', 'KIJANG': 'KJG', 'L300': 'L30', 'LIVINA': 'LVN', 'LUXIO': 'LXI', 'MOBILIO': 'MBL',
          'OUTLANDER': 'OTL', 'PAJERO': 'PJR', 'RAIZE': 'RAZ', 'ROCKY': 'RCK', 'RUSH': 'RSH', 'SERENA': 'SRN',
          'SIENTA': 'SNT', 'SIGRA': 'SGR', 'SIRION': 'SRI', 'SWIFT': 'SWF', 'SX4': 'SX4', 'TERIOS': 'TRS',
          'TRITON': 'TRT', 'VELOZ': 'VLZ', 'VIOS': 'VIS', 'XTRAIL': 'XTR', 'XENIA': 'XEN', 'XL7': 'XL7',
          'XPANDER': 'XPD', 'YARIS': 'YRS'
        };

        const token = normalized.split(' ')[0].replace(/[^A-Z0-9]/g, '');
        return map[token] || token.slice(0, 3).padEnd(3, 'X');
      }

      function yearCodeFromValue(value) {
        const numericYear = parseInt(value, 10);
        if (!numericYear || numericYear <= 0) return '000';
        return String(numericYear % 1000).padStart(3, '0');
      }

      function updateSuggestedCode() {
        if (!codeInput || !typeInput || !yearInput || codeTouched || @json((bool) $car)) return;
        codeInput.value = `MM-${typeCodeFromValue(typeInput.value)}-${yearCodeFromValue(yearInput.value)}-${String(nextSequence).padStart(3, '0')}`;
      }

      codeInput?.addEventListener('input', function () {
        codeTouched = codeInput.value.trim() !== '';
      });

      typeInput?.addEventListener('input', updateSuggestedCode);
      yearInput?.addEventListener('input', updateSuggestedCode);
      updateSuggestedCode();

      const input = document.getElementById('photos');
      if (!input) return;
      const helper = document.getElementById('photos-helper');
      const previewShell = document.getElementById('selected-photos-preview-shell');
      const previewGrid = document.getElementById('selected-photos-preview');
      const defaultText = @json($car
        ? 'Foto baru siap ditambahkan. Total foto mobil ini boleh lebih dari 5.'
        : 'Wajib upload minimal 5 foto mobil. Sistem siap dilanjutkan karena jumlah foto sudah memenuhi syarat.');
      const emptyText = @json($car
        ? 'Tambahkan 1 foto atau lebih jika ingin melengkapi galeri unit. Minimal total foto tetap 5, dan jumlah foto boleh lebih dari 5.'
        : 'Wajib upload minimal 5 foto mobil. Form tidak bisa dilanjutkan sebelum 5 foto dipilih.');
      const invalidText = @json($car
        ? 'Pilih minimal 1 foto mobil yang ingin ditambahkan.'
        : 'Wajib upload minimal 5 foto mobil. Saat ini file yang dipilih masih kurang dari 5.');

      function setPhotoState() {
        const total = input.files ? input.files.length : 0;

        if (total === 0) {
          input.setCustomValidity('');
          if (helper) {
            helper.textContent = emptyText;
            helper.classList.remove('text-rose-600');
            helper.classList.add('text-slate-500');
          }
          return;
        }

        if (!@json((bool) $car) && total < 5) {
          input.setCustomValidity('Minimal upload 5 foto mobil.');
          if (helper) {
            helper.textContent = invalidText;
            helper.classList.remove('text-slate-500');
            helper.classList.add('text-rose-600');
          }
          return;
        }

        input.setCustomValidity('');
        if (helper) {
          helper.textContent = defaultText;
          helper.classList.remove('text-rose-600');
          helper.classList.add('text-slate-500');
        }
      }

      function renderSelectedPhotoPreview() {
        if (!previewShell || !previewGrid) return;

        previewGrid.innerHTML = '';
        const files = input.files ? Array.from(input.files) : [];

        if (files.length === 0) {
          previewShell.classList.add('hidden');
          return;
        }

        previewShell.classList.remove('hidden');

        files.forEach(function (file, index) {
          const objectUrl = URL.createObjectURL(file);
          const card = document.createElement('article');
          card.className = 'overflow-hidden rounded-[1.2rem] border border-slate-200 bg-white shadow-sm';
          card.innerHTML = `
            <div class="aspect-[4/3] bg-slate-100 p-2">
              <img class="h-full w-full rounded-[0.9rem] object-contain object-center" src="${objectUrl}" alt="Preview foto baru ${index + 1}">
            </div>
            <div class="px-3 py-3">
              <p class="text-xs font-semibold text-slate-700">${file.name}</p>
              <p class="mt-1 text-xs text-slate-500">Foto baru ${index + 1}</p>
            </div>
          `;
          previewGrid.appendChild(card);
        });
      }

      input.addEventListener('change', function () {
        setPhotoState();
        renderSelectedPhotoPreview();
        if (input.validationMessage) {
          input.reportValidity();
        }
      });

      input.addEventListener('invalid', function () {
        setPhotoState();
      });

      document.querySelectorAll('[data-photo-replace-trigger]').forEach(function (trigger) {
        const inputId = trigger.getAttribute('data-input-id');
        const replaceInput = inputId ? document.getElementById(inputId) : null;
        const form = replaceInput ? replaceInput.closest('form') : null;

        if (!replaceInput || !form) return;

        trigger.addEventListener('click', function () {
          replaceInput.click();
        });

        replaceInput.addEventListener('change', function () {
          if (replaceInput.files && replaceInput.files.length > 0) {
            form.submit();
          }
        });
      });

      document.querySelectorAll('[data-delete-form-id]').forEach(function (button) {
        button.addEventListener('click', function () {
          const formId = button.getAttribute('data-delete-form-id');
          const form = formId ? document.getElementById(formId) : null;

          if (form && confirm('Hapus foto ini dari data mobil?')) {
            form.submit();
          }
        });
      });
    })();
  </script>
@endpush
