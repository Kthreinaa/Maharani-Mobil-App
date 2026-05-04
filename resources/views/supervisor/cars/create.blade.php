@extends('layouts.supervisor')

@php
  $title = 'Tambah Mobil';
  $pageTitle = 'Tambah Mobil';
@endphp

@section('content')
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
        <input class="mt-2 w-full rounded-lg border-slate-200" name="kode_unit" value="{{ old('kode_unit') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Merk</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="merk" value="{{ old('merk') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tipe</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="tipe" value="{{ old('tipe') }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Tahun</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="tahun" type="number" value="{{ old('tahun') }}" required/>
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
      <label class="text-sm font-semibold">Foto Mobil (Wajib 3-5 Foto)</label>
      <input
        class="mt-2 block w-full rounded-lg border-slate-200 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-700"
        type="file"
        name="photos[]"
        id="photos"
        multiple
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        required
      />
      <p class="mt-2 text-xs text-slate-500">
        Minimal 3 foto dan maksimal 5 foto. Format: JPG, JPEG, PNG, WEBP. Maksimal 4MB per foto.
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
      const input = document.getElementById('photos');
      if (!input) return;

      input.addEventListener('change', function () {
        const total = this.files ? this.files.length : 0;
        if (total > 5) {
          alert('Maksimal upload 5 foto.');
          this.value = '';
          return;
        }
        if (total > 0 && total < 3) {
          alert('Minimal upload 3 foto.');
        }
      });
    })();
  </script>
@endpush
