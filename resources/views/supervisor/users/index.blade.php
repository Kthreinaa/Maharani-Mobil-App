@extends('layouts.supervisor')

@php
  $title = 'Manajemen User';
  $pageTitle = 'Manajemen User / Karyawan';
@endphp

@section('content')
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
      <h2 class="text-lg font-bold">User Internal</h2>
      <p class="text-sm text-slate-500">Kelola akun owner, supervisor, dan marketing.</p>
    </div>
    <a href="{{ route('supervisor.users.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-white">Tambah User</a>
  </div>

  <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-slate-500">
        <tr>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-4 py-3 text-left">Telepon</th>
          <th class="px-4 py-3 text-left">Role</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($users as $user)
          <tr>
            <td class="px-4 py-3">{{ $user->name }}</td>
            <td class="px-4 py-3">{{ $user->email }}</td>
            <td class="px-4 py-3">{{ $user->phone ?? '-' }}</td>
            <td class="px-4 py-3">{{ $user->role }}</td>
            <td class="px-4 py-3 text-right">
              <a class="text-slate-700 hover:underline" href="{{ route('supervisor.users.edit', $user) }}">Edit</a>
              <form class="inline" method="POST" action="{{ route('supervisor.users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:underline ml-2" onclick="return confirm('Hapus user ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-slate-500" colspan="5">Belum ada user internal.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
