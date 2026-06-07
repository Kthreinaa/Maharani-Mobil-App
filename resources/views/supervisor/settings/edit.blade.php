@extends('layouts.supervisor')

@php
  $title = 'Setting Supervisor';
  $pageTitle = 'Setting Akun Supervisor';
@endphp

@section('content')
  <div class="grid gap-6 xl:grid-cols-[1.1fr_0.7fr]">
    <section class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px] md:p-7">
      <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Setting</p>
      <h2 class="mt-2 font-headline text-[28px] font-extrabold tracking-tight text-slate-900">Kelola Akun Supervisor</h2>
      <p class="mt-3 max-w-[720px] text-sm leading-7 text-slate-600">
        Perbarui identitas akun supervisor agar akses monitoring dan pengelolaan data showroom tetap aman, jelas, dan mudah dipantau.
      </p>

      <form class="mt-6 grid gap-5 md:grid-cols-2" method="POST" action="{{ route('supervisor.settings.update') }}">
        @csrf
        @method('PUT')

        <div>
          <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="name">Nama Lengkap</label>
          <input id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
          @error('name')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="phone">Nomor Telepon</label>
          <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
          @error('phone')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="md:col-span-2">
          <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="email">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" required />
          @error('email')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password">Password Baru</label>
          <input id="password" name="password" type="password" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
          @error('password')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500" for="password_confirmation">Konfirmasi Password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-[1.2rem] border border-slate-200 bg-white/80 px-4 py-3 text-sm focus:border-[#f5a623] focus:outline-none focus:ring-0" />
        </div>

        <div class="md:col-span-2 flex flex-wrap gap-3 pt-1">
          <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#08132e] px-6 py-3 text-sm font-bold text-white shadow-[0_16px_28px_rgba(8,19,46,0.18)] transition hover:brightness-110">
            <span class="material-symbols-outlined text-[18px]">save</span>
            Simpan Perubahan
          </button>
          <a href="{{ route('supervisor.dashboard') }}" class="inline-flex items-center rounded-full border border-slate-200 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">
            Kembali ke Dashboard
          </a>
        </div>
      </form>
    </section>

    <aside class="space-y-6">
      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Akun Aktif</p>
        <div class="mt-4 rounded-[1.4rem] border border-slate-200/80 bg-white/75 p-4">
          <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
          <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
          <span class="mt-4 inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-sky-700">
            {{ $user->role }}
          </span>
        </div>
      </article>

      <article class="rounded-[2rem] border border-white/70 bg-[rgba(255,255,255,0.72)] p-6 shadow-[0_24px_70px_rgba(15,23,42,0.10)] backdrop-blur-[24px]">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#f5a623]">Catatan</p>
        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
          <li>Supervisor memiliki akses monitoring dan pengelolaan lintas modul internal.</li>
          <li>Setiap perubahan order, transaksi, penawaran, dan test drive akan diberi jejak role pelaksana.</li>
          <li>Ganti password hanya jika memang diperlukan untuk keamanan akun.</li>
        </ul>
      </article>
    </aside>
  </div>
@endsection
