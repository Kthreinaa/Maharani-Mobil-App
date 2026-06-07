@extends('layouts.supervisor')

@php
  $title = 'Edit Mobil';
  $pageTitle = 'Edit Mobil';
@endphp

@section('content')
  @php
    $hasPhotoError = $errors->has('photos') || $errors->has('photos.*');
    $currentPhotos = collect($car->photos ?? [])
      ->filter(fn ($path) => filled($path))
      ->values();
  @endphp

  <form class="max-w-3xl bg-white border border-slate-200 rounded-xl p-6" method="POST" action="{{ route('supervisor.cars.update', $car) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
        <input class="mt-2 w-full rounded-lg border-slate-200" name="kode_unit" placeholder="MM-AVZ-017-01" value="{{ old('kode_unit', $car->kode_unit) }}" required/>
        <p class="mt-2 text-xs text-slate-500"></p>
      </div>
      <div>
        <label class="text-sm font-semibold">Merk</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="merk" value="{{ old('merk', $car->merk) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tipe</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="tipe" value="{{ old('tipe', $car->tipe) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tahun</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="tahun" type="number" value="{{ old('tahun', $car->tahun) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Harga</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="harga" type="number" value="{{ old('harga', $car->harga) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Kilometer</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="kilometer" type="number" value="{{ old('kilometer', $car->kilometer) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Transmisi</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="transmisi" value="{{ old('transmisi', $car->transmisi) }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Warna</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="warna" value="{{ old('warna', $car->warna) }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Bahan Bakar</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="bahan_bakar" value="{{ old('bahan_bakar', $car->bahan_bakar) }}"/>
      </div>
      <div>
        <label class="text-sm font-semibold">Status</label>
        <select class="mt-2 w-full rounded-lg border-slate-200" name="status" required>
          <option value="available" @selected(old('status', $car->status)==='available')>Available</option>
          <option value="reserved" @selected(old('status', $car->status)==='reserved')>Reserved</option>
          <option value="sold" @selected(old('status', $car->status)==='sold')>Sold</option>
        </select>
      </div>
    </div>
    <div class="mt-4">
      <label class="text-sm font-semibold">Deskripsi</label>
      <textarea class="mt-2 w-full rounded-lg border-slate-200" name="deskripsi" rows="4">{{ old('deskripsi', $car->deskripsi) }}</textarea>
    </div>

    @if ($currentPhotos->isNotEmpty())
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
        Tambah Foto Mobil
        <span class="text-slate-500">Opsional</span>
      </label>
      <input
        class="mt-2 block w-full rounded-lg border {{ $hasPhotoError ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200' }} file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-700"
        type="file"
        name="photos[]"
        id="photos"
        multiple
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
      />
      <p id="photos-helper" class="mt-2 text-xs font-semibold {{ $hasPhotoError ? 'text-rose-600' : 'text-slate-500' }}">
        Tambahkan 1 foto atau lebih jika ingin melengkapi galeri unit. Minimal total foto tetap 5, dan jumlah foto boleh lebih dari 5.
      </p>
      <p class="mt-1 text-xs text-slate-500">
        Jumlah foto saat ini: {{ $currentPhotos->count() }} foto.
      </p>
    </div>

    <div id="selected-photos-preview-shell" class="mt-4 hidden">
      <div class="flex items-center justify-between gap-3">
        <label class="text-sm font-semibold text-slate-900">Foto yang akan ditambahkan</label>
        <p class="text-xs font-semibold text-slate-500">Foto yang dipilih akan ditambahkan menjadi foto produk.</p>
      </div>
      <div id="selected-photos-preview" class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
    </div>

    <div class="mt-6 flex gap-3">
      <a href="{{ route('supervisor.cars.index') }}" class="rounded-lg border border-slate-300 px-4 py-2">Batal</a>
      <button class="rounded-lg bg-slate-900 text-white px-4 py-2">Update</button>
    </div>
  </form>

  @foreach ($currentPhotos as $photoIndex => $photoPath)
    <form id="replace-photo-form-{{ $photoIndex }}" method="POST" action="{{ route('supervisor.cars.photos.replace', [$car, $photoIndex]) }}" enctype="multipart/form-data" class="hidden">
      @csrf
      @method('PATCH')
      <input id="replace-photo-input-{{ $photoIndex }}" class="hidden photo-replace-input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
    </form>

    <form id="delete-photo-form-{{ $photoIndex }}" method="POST" action="{{ route('supervisor.cars.photos.destroy', [$car, $photoIndex]) }}" class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach
@endsection

@push('scripts')
  <script>
    (function () {
      const input = document.getElementById('photos');
      if (!input) return;
      const helper = document.getElementById('photos-helper');
      const previewShell = document.getElementById('selected-photos-preview-shell');
      const previewGrid = document.getElementById('selected-photos-preview');

      function setPhotoState() {
        const total = input.files ? input.files.length : 0;

        if (total === 0) {
          input.setCustomValidity('');
          if (helper) {
            helper.textContent = 'Tambahkan 1 foto atau lebih jika ingin melengkapi galeri unit. Minimal total foto tetap 5, dan jumlah foto boleh lebih dari 5.';
            helper.classList.remove('text-rose-600');
            helper.classList.add('text-slate-500');
          }
          return;
        }

        if (total < 1) {
          input.setCustomValidity('Pilih minimal 1 foto mobil.');
          if (helper) {
            helper.textContent = 'Pilih minimal 1 foto mobil yang ingin ditambahkan.';
            helper.classList.remove('text-slate-500');
            helper.classList.add('text-rose-600');
          }
          return;
        }

        input.setCustomValidity('');
        if (helper) {
          helper.textContent = 'Foto baru siap ditambahkan. Total foto mobil ini boleh lebih dari 5.';
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
    })();

    document.querySelectorAll('[data-photo-replace-trigger]').forEach(function (trigger) {
      const inputId = trigger.getAttribute('data-input-id');
      const input = inputId ? document.getElementById(inputId) : null;
      const form = input ? input.closest('form') : null;

      if (!input || !form) return;

      trigger.addEventListener('click', function () {
        input.click();
      });

      input.addEventListener('change', function () {
        if (input.files && input.files.length > 0) {
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
  </script>
@endpush
