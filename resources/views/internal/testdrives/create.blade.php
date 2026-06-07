@extends($layout)

@section('content')
  <form method="POST" action="{{ $submitRoute }}">
    @csrf

    <section class="rounded-[2rem] border border-white/70 bg-white/75 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Input Internal {{ $workspaceLabel }}</p>
      <h2 class="mt-2 font-headline text-2xl font-extrabold text-slate-900">Catat test drive customer ke sistem</h2>
      <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600">
        Form ini dipakai saat customer menghubungi showroom lewat WhatsApp, sosial media, atau datang langsung lalu supervisor perlu menjadwalkan test drive secara manual di sistem.
      </p>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nama Customer</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Contoh: Rina" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Email Customer</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_email" type="email" value="{{ old('customer_email') }}" placeholder="Opsional jika belum ada" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Nomor WhatsApp</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Domisili</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_city" value="{{ old('customer_city') }}" placeholder="Luar kota / Pekanbaru" />
        </div>
      </div>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Unit Mobil</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="car_id" required>
            <option value="">Pilih unit</option>
            @foreach ($cars as $car)
              <option value="{{ $car->id }}" @selected((int) old('car_id') === (int) $car->id)>
                {{ $car->kode_unit ?: 'Tanpa kode' }} - {{ $car->merk }} {{ $car->tipe }} {{ $car->tahun }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Sumber Customer</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="customer_channel" required>
            <option value="online" @selected(old('customer_channel', 'online') === 'online')>Online / WhatsApp / Sosial Media</option>
            <option value="offline" @selected(old('customer_channel') === 'offline')>Datang ke Showroom</option>
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Tanggal Test Drive</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="booking_date" type="date" value="{{ old('booking_date') }}" required />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Jam Test Drive</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="booking_time" type="time" value="{{ old('booking_time') }}" required />
        </div>
      </div>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Sumber Lead</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="lead_source" value="{{ old('lead_source') }}" placeholder="Contoh: WhatsApp, Instagram, Facebook" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Lokasi Test Drive</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="location" value="{{ old('location') }}" placeholder="Showroom / diantar ke rumah customer" />
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Status Awal</label>
          <select class="w-full rounded-xl border-slate-200 bg-white/80" name="status" required>
            @foreach (['pending' => 'Menunggu persetujuan', 'approved' => 'Sudah dijadwalkan', 'completed' => 'Sudah selesai', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'] as $value => $label)
              <option value="{{ $value }}" @selected(old('status', 'approved') === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Jadwal Follow-up</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at') }}" />
        </div>
      </div>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Alasan batal</label>
          <input class="w-full rounded-xl border-slate-200 bg-white/80" name="lost_reason" value="{{ old('lost_reason') }}" placeholder="Diisi jika test drive dibatalkan / ditolak" />
        </div>
      </div>

      <div class="mt-6">
        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Catatan Tambahan</label>
        <textarea class="h-28 w-full rounded-xl border-slate-200 bg-white/80" name="notes" placeholder="Contoh: customer luar kota, minta test drive di rumah saudara, ingin lihat unit sore hari">{{ old('notes') }}</textarea>
      </div>

      <div class="mt-6 flex flex-wrap justify-end gap-3">
        <a class="rounded-full border border-slate-200 bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700" href="{{ $backRoute }}">Batal</a>
        <button class="rounded-full bg-[#08132e] px-6 py-3 text-sm font-bold text-white" type="submit">Simpan Jadwal Test Drive</button>
      </div>
    </section>
  </form>
@endsection
