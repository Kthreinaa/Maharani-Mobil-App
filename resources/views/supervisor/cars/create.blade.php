@extends('layouts.supervisor')

@php
  $title = 'Tambah Mobil';
  $pageTitle = 'Tambah Mobil';
@endphp

@section('content')
  @php
    $hasPhotoError = $errors->has('photos') || $errors->has('photos.*');
  @endphp

  <form class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6" method="POST" action="{{ route('supervisor.cars.store') }}" enctype="multipart/form-data">
    @csrf

    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-semibold">Periksa kembali data input:</p>
        <ul class="mt-1 list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-semibold">Kode Unit</label>
        <input id="kode_unit" class="mt-2 w-full rounded-lg border-slate-200" name="kode_unit" placeholder="MM-AVZ-024-524" value="{{ old('kode_unit') }}" required/>
        <p class="mt-2 text-xs text-slate-500">Nomor urut berikutnya: {{ str_pad((string) ($nextUnitSequence ?? 1), 3, '0', STR_PAD_LEFT) }}. Kode akan disarankan otomatis setelah tipe dan tahun diisi.</p>
      </div>
      <div>
        <label class="text-sm font-semibold">BM Unit</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="bm" placeholder="BM 1234 XYZ" value="{{ old('bm') }}" required/>
        <p class="mt-2 text-xs text-slate-500">BM dipakai sebagai identitas unik mobil untuk mencegah data unit ganda.</p>
      </div>
      <div>
        <label class="text-sm font-semibold">Merk</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="merk" value="{{ old('merk') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tipe</label>
        <input id="tipe" class="mt-2 w-full rounded-lg border-slate-200" name="tipe" value="{{ old('tipe') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tahun</label>
        <input id="tahun" class="mt-2 w-full rounded-lg border-slate-200" name="tahun" type="number" value="{{ old('tahun') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Harga</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="harga" type="number" value="{{ old('harga') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Kilometer</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="kilometer" type="number" value="{{ old('kilometer') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Transmisi</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="transmisi" value="{{ old('transmisi') }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Warna</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="warna" value="{{ old('warna') }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Bahan Bakar</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="bahan_bakar" value="{{ old('bahan_bakar') }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Status</label>
        <select class="mt-2 w-full rounded-lg border-slate-200" name="status" required>
          <option value="available" @selected(old('status', 'available') === 'available')>Available</option>
          <option value="reserved" @selected(old('status') === 'reserved')>Reserved</option>
          <option value="sold" @selected(old('status') === 'sold')>Sold</option>
        </select>
      </div>
    </div>
    <div class="mt-4">
      <label class="text-sm font-semibold">Deskripsi</label>
      <textarea class="mt-2 w-full rounded-lg border-slate-200" name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
    </div>

    <div class="mt-4">
      <label class="text-sm font-semibold text-slate-900" for="photos">
        Foto Mobil
        <span class="text-rose-600">Wajib minimal 5 foto</span>
      </label>
      <input
        class="mt-2 block w-full rounded-lg border {{ $hasPhotoError ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200' }} file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-700"
        type="file"
        name="photos[]"
        id="photos"
        multiple
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        required
      />
      <p id="photos-helper" class="mt-2 text-xs font-semibold {{ $hasPhotoError ? 'text-rose-600' : 'text-slate-500' }}">
        Wajib upload minimal 5 foto mobil. Sistem tidak akan melanjutkan jika foto kurang dari 5. Format: JPG, JPEG, PNG, WEBP. Maksimal 4MB per foto.
      </p>
    </div>

    <div class="mt-6 flex gap-3">
      <a href="{{ route('supervisor.cars.index') }}" class="rounded-lg border border-slate-300 px-4 py-2">Batal</a>
      <button class="rounded-lg bg-slate-900 text-white px-4 py-2">Simpan</button>
    </div>
  </form>
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
        if (!codeInput || !typeInput || !yearInput || codeTouched) return;
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

      function setPhotoState() {
        const total = input.files ? input.files.length : 0;

        if (total < 5) {
          input.setCustomValidity('Minimal upload 5 foto mobil.');
          if (helper) {
            helper.textContent = total === 0
              ? 'Wajib upload minimal 5 foto mobil. Form tidak bisa dilanjutkan sebelum 5 foto dipilih.'
              : 'Wajib upload minimal 5 foto mobil. Saat ini file yang dipilih masih kurang dari 5.';
            helper.classList.remove('text-slate-500');
            helper.classList.add('text-rose-600');
          }
          return;
        }

        input.setCustomValidity('');
        if (helper) {
          helper.textContent = 'Wajib upload minimal 5 foto mobil. Sistem siap dilanjutkan karena jumlah foto sudah memenuhi syarat.';
          helper.classList.remove('text-rose-600');
          helper.classList.add('text-slate-500');
        }
      }

      input.addEventListener('change', function () {
        setPhotoState();
        if (input.validationMessage) {
          input.reportValidity();
        }
      });

      input.addEventListener('invalid', function () {
        setPhotoState();
      });
    })();
  </script>
@endpush
