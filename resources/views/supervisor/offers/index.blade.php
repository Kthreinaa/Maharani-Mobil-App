@extends('layouts.supervisor')

@php
  use App\Support\CurrencyFormatter;

  $title = 'Penawaran';
  $pageTitle = 'Penawaran Customer';
@endphp

@section('content')
  <section class="space-y-6">
    <div class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#f5a623]">Offer Monitoring</p>
          <h2 class="mt-2 font-headline text-2xl font-extrabold text-slate-900">Pantau Penawaran Customer</h2>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Cari berdasarkan nama customer, merk mobil, tipe, tahun, atau kode unit. Supervisor tetap bisa memantau siapa yang mengelola penawaran tanpa membingungkan tim.</p>
        </div>
        <div class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm">
          {{ $offers->total() }} data penawaran
        </div>
      </div>

      <form class="mt-5 flex flex-col gap-3 xl:flex-row xl:flex-wrap xl:items-center" method="GET" action="{{ route('supervisor.offers.index') }}">
        <input
          class="w-full rounded-full border border-slate-200 bg-white/88 px-5 py-3 text-sm text-slate-700 placeholder:text-slate-400 xl:max-w-sm"
          type="search"
          name="q"
          value="{{ $search }}"
          placeholder="Cari customer, unit, merk, tipe, atau kode..."
        />
        <select class="rounded-full border border-slate-200 bg-white/88 px-5 py-3 text-sm font-semibold text-slate-700" name="status">
          <option value="all">Semua Status</option>
          @foreach (['pending', 'countered', 'accepted', 'rejected'] as $item)
            <option value="{{ $item }}" @selected($status === $item)>{{ strtoupper($item) }}</option>
          @endforeach
        </select>
        <button class="rounded-full bg-[#08132e] px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)]" type="submit">Filter</button>
        <a class="rounded-full border border-slate-200 bg-white/86 px-5 py-3 text-sm font-semibold text-slate-600" href="{{ route('supervisor.offers.index') }}">Reset</a>
      </form>

      <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
        <span class="rounded-full border border-slate-200 bg-slate-50/80 px-3 py-1.5 text-slate-600">Status: {{ $status === 'all' ? 'Semua Status' : strtoupper($status) }}</span>
        <span class="rounded-full border border-slate-200 bg-slate-50/80 px-3 py-1.5 text-slate-600">Pencarian: {{ $search !== '' ? $search : 'Semua Data' }}</span>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-5">
      @forelse ($offers as $offer)
        @php
          $unitName = trim(collect([$offer->car?->merk, $offer->car?->tipe])->filter()->implode(' '));
          $customerNote = (string) optional(
            $offer->histories
              ->filter(fn ($history) => $history->actor_role === 'customer' && filled($history->note))
              ->sortBy('created_at')
              ->last()
          )->note;
          $internalHistory = $offer->histories->first(fn ($history) => in_array($history->actor_role, ['supervisor', 'marketing'], true));
          $internalNote = (string) ($internalHistory?->note ?? '');
        @endphp

        <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-5 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $offer->car?->kode_unit ?? 'Unit belum tersedia' }}</p>
              <h3 class="mt-2 font-headline text-xl font-extrabold text-slate-900">{{ $unitName !== '' ? $unitName : 'Unit tidak ditemukan' }}</h3>
              <p class="mt-2 text-sm text-slate-500">{{ $offer->user?->name ?? 'Customer belum tersedia' }}</p>
            </div>
            <div class="flex flex-col items-end gap-2">
              <span class="rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-[0.14em] {{ $offer->status_badge_classes }}">{{ $offer->status_label }}</span>
              <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold text-slate-600">Ronde {{ $offer->negotiation_round ?? 1 }}</span>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-2 gap-3 xl:grid-cols-4">
            <div class="min-w-0 rounded-[1.25rem] border border-slate-200 bg-white/80 p-4">
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Harga Jual</p>
              <p class="mt-2 break-words text-base font-extrabold leading-8 text-slate-900 sm:text-lg">{{ CurrencyFormatter::rupiah($offer->car?->harga) }}</p>
            </div>
            <div class="min-w-0 rounded-[1.25rem] border border-slate-200 bg-white/80 p-4">
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Nominal Penawaran</p>
              <p class="mt-2 break-words text-base font-extrabold leading-8 text-slate-900 sm:text-lg">{{ CurrencyFormatter::rupiah($offer->offer_price) }}</p>
            </div>
            <div class="min-w-0 rounded-[1.25rem] border border-slate-200 bg-white/80 p-4">
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Tawar Balik</p>
              <p class="mt-2 break-words text-base font-extrabold leading-8 text-slate-900 sm:text-lg">{{ $offer->counter_price ? CurrencyFormatter::rupiah($offer->counter_price) : '-' }}</p>
            </div>
            <div class="min-w-0 rounded-[1.25rem] border border-slate-200 bg-white/80 p-4">
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Harga Deal</p>
              <p class="mt-2 break-words text-base font-extrabold leading-8 text-emerald-700 sm:text-lg">{{ $offer->final_price ? CurrencyFormatter::rupiah($offer->final_price) : '-' }}</p>
            </div>
          </div>

          @if ($customerNote !== '')
            <div class="mt-4 rounded-[1.25rem] border border-slate-200/80 bg-slate-50/75 px-4 py-3 text-sm leading-6 text-slate-600">
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Keterangan Customer</p>
              <p class="mt-2">
                {{ $customerNote }}
              </p>
            </div>
          @endif

          <div class="mt-4 rounded-[1.3rem] border border-slate-200 bg-white/80 p-4">
            <p class="text-sm font-bold text-slate-900">Riwayat Negosiasi</p>
            <div class="mt-3 space-y-3">
              @forelse ($offer->histories->sortBy('created_at') as $history)
                <div class="rounded-[1rem] bg-slate-50 px-4 py-3">
                  <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                      <p class="text-sm font-bold text-slate-900">{{ $history->action_label }}</p>
                      <p class="mt-1 text-sm text-slate-600">{{ $history->offered_price ? CurrencyFormatter::rupiah($history->offered_price) : '-' }}</p>
                      @if ($history->note)
                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ $history->note }}</p>
                      @endif
                    </div>
                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ strtoupper($history->actor_role) }}</span>
                  </div>
                </div>
              @empty
                <div class="rounded-[1rem] bg-slate-50 px-4 py-3 text-sm text-slate-500">Belum ada riwayat negosiasi.</div>
              @endforelse
            </div>
          </div>

          @if ($offer->handled_at)
            <div class="mt-4 rounded-[1.4rem] border border-sky-200/80 bg-[linear-gradient(135deg,rgba(239,246,255,0.95),rgba(255,255,255,0.92))] p-4 shadow-sm">
              <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                  <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-600">Tindak Lanjut Supervisor</p>
                  <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $offer->status_label }}</p>
                  <p class="mt-1 text-sm text-slate-500">
                    Ditangani oleh {{ $offer->handledBy?->name ?? 'Supervisor' }} pada {{ $offer->handled_at->translatedFormat('d M Y H:i') }}.
                  </p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <span class="rounded-full px-3 py-1.5 text-[11px] font-bold uppercase tracking-[0.14em] {{ $offer->status_badge_classes }}">{{ $offer->status_label }}</span>
                  <span class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600">{{ $offer->follow_up_status_label }}</span>
                </div>
              </div>

              <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="rounded-[1rem] border border-white/80 bg-white/90 px-4 py-3">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Tawar Balik Aktif</p>
                  <p class="mt-2 text-sm font-bold text-slate-900">{{ $offer->counter_price ? CurrencyFormatter::rupiah($offer->counter_price) : '-' }}</p>
                </div>
                <div class="rounded-[1rem] border border-white/80 bg-white/90 px-4 py-3">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Harga Deal</p>
                  <p class="mt-2 text-sm font-bold text-emerald-700">{{ $offer->final_price ? CurrencyFormatter::rupiah($offer->final_price) : '-' }}</p>
                </div>
                <div class="rounded-[1rem] border border-white/80 bg-white/90 px-4 py-3">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Follow-up Berikutnya</p>
                  <p class="mt-2 text-sm font-bold text-slate-900">{{ $offer->next_follow_up_at ? $offer->next_follow_up_at->translatedFormat('d M Y H:i') : '-' }}</p>
                </div>
              </div>

              @if ($offer->lost_reason || $internalNote !== '')
                <div class="mt-3 rounded-[1rem] border border-white/80 bg-white/88 px-4 py-3 text-sm leading-6 text-slate-600">
                  <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Catatan Tindak Lanjut</p>
                  <p class="mt-2">{{ $offer->lost_reason ?: $internalNote }}</p>
                </div>
              @endif
            </div>
          @endif

          <div class="mt-5 rounded-[1.4rem] border border-slate-200 bg-white/82 p-4">
            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
              <div>
                <p class="text-sm font-bold text-slate-900">{{ $offer->handled_at ? 'Perbarui Respons Supervisor' : 'Respons Supervisor' }}</p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                  {{ $offer->handled_at ? 'Respons sebelumnya sudah tersimpan. Anda masih bisa memperbarui status, harga, atau catatan follow-up dari sini.' : 'Lengkapi tindak lanjut penawaran customer dari form ini.' }}
                </p>
              </div>
              @if ($offer->handled_at)
                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-sky-700">Sudah Diproses</span>
              @endif
            </div>

            <form class="mt-4 grid gap-3 md:grid-cols-2" method="POST" action="{{ route('supervisor.offers.updateStatus', $offer) }}">
              @csrf
              @method('PATCH')
              <select class="min-w-0 flex-1 rounded-full border-slate-200 bg-white/90 text-sm font-semibold text-slate-700" name="status">
                @foreach (['pending', 'countered', 'accepted', 'rejected'] as $item)
                  <option value="{{ $item }}" @selected(old('status', $offer->status) === $item)>{{ strtoupper($item) }}</option>
                @endforeach
              </select>
              <input class="rounded-full border-slate-200 bg-white/90 text-sm" name="counter_price" type="number" min="0" step="0.01" value="{{ old('counter_price', $offer->counter_price) }}" placeholder="Isi harga tawar balik jika counter" />
              <input class="rounded-full border-slate-200 bg-white/90 text-sm" name="lost_reason" value="{{ old('lost_reason', $offer->lost_reason) }}" placeholder="Alasan batal / belum deal" />
              <input class="rounded-full border-slate-200 bg-white/90 text-sm" name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at', $offer->next_follow_up_at?->format('Y-m-d\\TH:i')) }}" />
              <textarea class="md:col-span-2 rounded-[1.3rem] border-slate-200 bg-white/90 text-sm" name="notes" rows="3" placeholder="Catatan supervisor untuk tindak lanjut customer">{{ old('notes', $internalNote) }}</textarea>
              <button class="rounded-full bg-[#08132e] px-5 py-3 text-sm font-semibold text-white shadow-[0_16px_28px_rgba(8,19,46,0.16)] md:col-span-2" type="submit">{{ $offer->handled_at ? 'Perbarui Respons Supervisor' : 'Simpan Respons Supervisor' }}</button>
            </form>
          </div>
        </article>
      @empty
        <div class="rounded-[2rem] border border-white/70 bg-white/70 p-6 text-sm text-slate-500">
          Tidak ada penawaran yang cocok dengan pencarian atau filter yang dipilih.
        </div>
      @endforelse
    </div>

    <div>{{ $offers->links() }}</div>
  </section>
@endsection
