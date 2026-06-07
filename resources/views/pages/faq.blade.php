<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ __('FAQ') }} | Maharani Mobil</title>
  <meta name="description" content="{{ __('Pertanyaan yang sering diajukan seputar pembelian mobil, pembayaran, kredit, dan proses transaksi di Maharani Mobil.') }}"/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
  @include('components.public-site-header', ['active' => 'faq'])

  <main class="flex-grow">
    <section class="relative overflow-hidden bg-[#031636] pb-16 pt-40 text-white md:pb-20 md:pt-36">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(245,166,35,0.18),transparent_32%)]"></div>
      <div class="relative mx-auto w-full max-w-[920px] px-4 text-center md:px-6">
        <p class="text-[10px] font-bold uppercase tracking-[0.26em] text-[#f5a623]">FAQ</p>
        <h1 class="mt-3 font-headline text-[38px] font-extrabold leading-tight sm:text-[52px]">{{ __('Pusat Pertanyaan Pelanggan') }}</h1>
        <p class="mt-5 text-[15px] leading-7 text-slate-200">
          {{ __('Semua informasi penting tentang kondisi mobil, proses pembelian, pembayaran, kredit, hingga pembatalan kami rangkum di sini agar Anda bisa bertransaksi dengan lebih tenang.') }}
        </p>
      </div>
    </section>

    <section class="py-14 md:py-16">
      <div class="mx-auto grid w-full max-w-[1280px] gap-8 px-4 md:grid-cols-[0.78fr_1.5fr] md:px-6">
        <aside class="ios-faq-help h-fit rounded-[2rem] p-7 text-white editorial-shadow">
          <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#f5a623]">{{ __('Butuh Bantuan?') }}</p>
          <h2 class="mt-3 font-headline text-[28px] font-extrabold text-white">{{ __('Tim Kami Siap Membantu') }}</h2>
          <p class="mt-4 text-[14px] leading-7 text-slate-100/88">
            {{ __('Jika jawaban yang Anda cari belum tersedia, Anda tetap bisa menghubungi tim Maharani Mobil secara langsung untuk konsultasi unit, test drive, atau proses transaksi.') }}
          </p>
          <div class="mt-6 space-y-3">
            <a class="inline-flex w-full items-center justify-center rounded-full bg-[#071b47] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#0d2a67]" href="https://wa.me/628117584617?text=Halo%20Maharani%20Mobil%2C%20saya%20ingin%20bertanya%20lebih%20lanjut." target="_blank" rel="noopener noreferrer">
              {{ __('Hubungi via WhatsApp') }}
            </a>
            @auth
              <a class="inline-flex w-full items-center justify-center rounded-full border border-white/18 bg-[rgba(255,255,255,0.05)] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[rgba(255,255,255,0.09)]" href="{{ route('test-drive.form') }}">
                {{ __('Ajukan Test Drive') }}
              </a>
            @else
              <a class="js-login-required inline-flex w-full items-center justify-center rounded-full border border-white/18 bg-[rgba(255,255,255,0.05)] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[rgba(255,255,255,0.09)]" href="{{ route('login') }}" data-popup-message="{{ __('Please login first') }}">
                {{ __('Ajukan Test Drive') }}
              </a>
            @endauth
          </div>
        </aside>

        <div class="space-y-4">
          @foreach ($faqItems ?? [] as $item)
            <details class="ios-faq-item group rounded-[1.6rem] p-6 editorial-shadow transition">
              <summary class="flex cursor-pointer list-none items-start justify-between gap-4 font-headline text-[21px] font-bold leading-7 text-white">
                <span>{{ $item['question'] }}</span>
                <span class="material-symbols-outlined mt-0.5 text-[#f5a623] transition duration-300 group-open:rotate-45">add</span>
              </summary>

              <div class="mt-4 space-y-4 text-[14px] leading-7 text-white">
                @foreach ($item['paragraphs'] ?? [] as $paragraph)
                  <p>{{ $paragraph }}</p>
                @endforeach

                @if (!empty($item['bullets']))
                  <ul class="space-y-2 pl-5 text-white">
                    @foreach ($item['bullets'] as $bullet)
                      <li class="list-disc">{{ $bullet }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>
            </details>
          @endforeach
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin bertanya lebih lanjut tentang proses pembelian mobil.'])
  @include('components.ui-system-footer')
</body>
</html>
