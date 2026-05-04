<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Favorit Saya</title>
  <link href="/assets/app.css" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen p-8">
  <h1 class="text-2xl font-bold text-primary mb-6">Mobil Favorit</h1>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($favorites as $car)
      <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 p-4">
        <h3 class="font-bold text-primary">{{ $car->merk }} {{ $car->tipe }}</h3>
        <p class="text-sm text-on-surface-variant">Rp {{ number_format($car->harga) }}</p>
        <form method="POST" action="{{ route('customer.favorites.destroy', $car) }}">
          @csrf
          @method('DELETE')
          <button class="mt-3 px-3 py-2 rounded-lg border border-outline-variant text-xs font-bold">Hapus Favorit</button>
        </form>
      </div>
    @empty
      <p class="text-on-surface-variant">Belum ada favorit.</p>
    @endforelse
  </div>
</body>
</html>
