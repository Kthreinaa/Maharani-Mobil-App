@extends('layouts.supervisor')

@php
  $title = 'Edit User';
  $pageTitle = 'Edit User / Karyawan';
@endphp

@section('content')
  <form class="max-w-2xl bg-white border border-slate-200 rounded-xl p-6" method="POST" action="{{ route('supervisor.users.update', $user) }}">
    @csrf
    @method('PUT')
    <div class="space-y-4">
      <div>
        <label class="text-sm font-semibold">Nama</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="name" value="{{ old('name', $user->name) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">Email</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="email" type="email" value="{{ old('email', $user->email) }}" required/>
      </div>
      <div>
        <label class="text-sm font-semibold">No Telepon</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="phone" value="{{ old('phone', $user->phone) }}" />
      </div>
      <div>
        <label class="text-sm font-semibold">Role</label>
        <select class="mt-2 w-full rounded-lg border-slate-200" name="role" required>
          <option value="owner" @selected(old('role', $user->role)==='owner')>Owner</option>
          <option value="supervisor" @selected(old('role', $user->role)==='supervisor')>Supervisor</option>
          <option value="marketing" @selected(old('role', $user->role)==='marketing')>Marketing</option>
        </select>
      </div>
      <div>
        <label class="text-sm font-semibold">Password Baru (opsional)</label>
        <input class="mt-2 w-full rounded-lg border-slate-200" name="password" type="password" />
      </div>
    </div>

    <div class="mt-6 flex gap-3">
      <a class="rounded-lg border border-slate-300 px-4 py-2" href="{{ route('supervisor.users.index') }}">Batal</a>
      <button class="rounded-lg bg-slate-900 text-white px-4 py-2">Update</button>
    </div>
  </form>
@endsection
