@php
  $aboutHeroImage = file_exists(public_path('assets/about-showroom.png'))
      ? asset('assets/about-showroom.png')
      : (file_exists(public_path('assets/about-showroom.jpg'))
          ? asset('assets/about-showroom.jpg')
          : asset('assets/landing-hero.jpg'));
  $aboutCustomerCutout = file_exists(public_path('assets/about-customer-cutout.webp'))
      ? asset('assets/about-customer-cutout.webp')
      : (file_exists(public_path('assets/about-customer-cutout.png'))
          ? asset('assets/about-customer-cutout.png')
          : null);
  $aboutCustomerCutoutMirror = file_exists(public_path('assets/about-customer-cutout-mirror.webp'))
      ? asset('assets/about-customer-cutout-mirror.webp')
      : (file_exists(public_path('assets/about-customer-cutout-mirror-clean.webp'))
          ? asset('assets/about-customer-cutout-mirror-clean.webp')
          : null);
  $aboutCustomerCutoutMirror = $aboutCustomerCutoutMirror ?: (file_exists(public_path('assets/about-customer-cutout-mirror.webp'))
      ? asset('assets/about-customer-cutout-mirror.webp')
      : $aboutCustomerCutout);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tentang Kami | Maharani Mobil Pekanbaru</title>
  <meta name="description" content="Tentang Maharani Mobil Pekanbaru, lokasi showroom, jam operasional, dan standar pelayanan kami."/>
  @include('components.ui-system-head')
  <script src="{{ asset('assets/tailwind.config.js') }}?v={{ filemtime(public_path('assets/tailwind.config.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
  <link href="{{ asset('assets/app.css') }}?v={{ filemtime(public_path('assets/app.css')) }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background min-h-screen font-body flex flex-col">
  @include('components.public-site-header', ['active' => 'about'])

  <main class="flex-grow">
    <section class="relative min-h-[360px] overflow-hidden md:min-h-[460px]">
      <div class="absolute inset-0">
        <img
          alt="Showroom Maharani Mobil"
          class="h-full w-full scale-[1.02] object-cover"
          src="{{ $aboutHeroImage }}"
        />
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(6,16,37,0.22)_0%,rgba(6,16,37,0.30)_34%,rgba(6,16,37,0.58)_100%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_20%,rgba(255,255,255,0.18)_0%,rgba(255,255,255,0.06)_24%,transparent_48%)]"></div>
        <div class="absolute inset-x-0 bottom-0 h-[58%] bg-[linear-gradient(180deg,rgba(4,13,32,0.00)_0%,rgba(4,13,32,0.58)_100%)]"></div>
        <div class="absolute inset-y-0 right-0 w-[28%] bg-[linear-gradient(270deg,rgba(8,19,46,0.20)_0%,rgba(8,19,46,0.00)_100%)]"></div>
      </div>

      <div class="relative mx-auto flex min-h-[360px] w-full max-w-[1280px] items-center justify-center px-5 py-20 text-center md:min-h-[460px] md:px-6">
        <div class="max-w-[760px] rounded-[2rem] border border-white/10 bg-[rgba(255,255,255,0.06)] px-8 py-8 shadow-[0_24px_80px_rgba(2,8,23,0.20)] backdrop-blur-xl md:px-12 md:py-10">
          <p class="text-[12px] font-bold uppercase tracking-[0.24em] text-[#f5a623]">Established 2017</p>
          <h1 class="mt-4 font-headline text-[32px] font-extrabold text-white md:text-[44px]">Tentang Kami</h1>
          <p class="mx-auto mt-5 max-w-[620px] text-[14px] leading-7 text-slate-100/90 md:text-[16px]">
            Maharani Mobil adalah showroom mobil bekas di Pekanbaru yang menghadirkan mobil bekas berkualitas dengan pelayanan yang nyaman dan terpercaya.
          </p>
        </div>
      </div>
    </section>

    <section class="mx-auto w-full max-w-[1280px] px-5 py-12 md:px-6 md:py-16">
      <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.45fr_0.7fr]">
        <article class="rounded-[2rem] bg-white p-8 shadow-[0_24px_70px_rgba(7,27,71,0.08)] md:p-10">
          <div class="space-y-6 text-[15px] leading-8 text-slate-700">
            <p>
              Maharani Mobil adalah showroom mobil bekas terpercaya yang berlokasi di Jl. Arifin Ahmad No.113, Sidomulyo Timur,
              Kecamatan Marpoyan Damai, Kota Pekanbaru, Riau. Berdiri sejak tahun 2017, Maharani Mobil telah berkembang menjadi
              salah satu dealer mobil bekas yang dikenal di Pekanbaru berkat komitmennya dalam menghadirkan kendaraan berkualitas
              serta pelayanan yang jujur dan transparan kepada pelanggan.
            </p>
            <p>
              Selama lebih dari enam tahun beroperasi, Maharani Mobil telah melayani ratusan pelanggan dengan menyediakan berbagai
              pilihan mobil bekas dari beragam merek ternama seperti Toyota, Honda, Daihatsu, Nissan, Mitsubishi, dan Suzuki.
              Setiap unit yang ditawarkan telah melalui proses seleksi sehingga tetap layak digunakan dan memenuhi kebutuhan
              konsumen, baik untuk penggunaan pribadi maupun keluarga.
            </p>
          </div>

          <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-[1.5rem] border border-slate-200 bg-[#f8fafc] p-6">
              <span class="material-symbols-outlined text-[22px] text-[#071b47]">payments</span>
              <h3 class="mt-4 font-headline text-[24px] font-bold text-[#071b47]">Fleksibilitas Pembayaran</h3>
              <p class="mt-3 text-[14px] leading-7 text-slate-600">
                Dalam proses penjualan, Maharani Mobil memberikan kemudahan bagi pelanggan melalui pilihan pembayaran yang fleksibel,
                baik secara tunai maupun kredit.
              </p>
            </div>

            <div class="rounded-[1.5rem] border border-slate-200 bg-[#f8fafc] p-6">
              <span class="material-symbols-outlined text-[22px] text-[#071b47]">verified_user</span>
              <h3 class="mt-4 font-headline text-[24px] font-bold text-[#071b47]">Jaminan Kualitas</h3>
              <p class="mt-3 text-[14px] leading-7 text-slate-600">
                Beberapa unit juga dilengkapi dengan jaminan dealer serta opsi perlindungan tambahan seperti asuransi atau pembiayaan.
              </p>
            </div>
          </div>

          <div class="mt-8 space-y-6 text-[15px] leading-8 text-slate-700">
            <p>
              Sebagai showroom yang terus berkembang, Maharani Mobil berkomitmen untuk meningkatkan kualitas layanan dengan
              mengedepankan transparansi informasi kendaraan, kemudahan akses, serta pengalaman pembelian yang lebih nyaman.
              Hal ini juga didukung oleh kehadiran platform digital yang memudahkan pelanggan dalam melihat katalog mobil,
              mencari unit sesuai kebutuhan, hingga melakukan transaksi dengan lebih praktis.
            </p>
          </div>

          <blockquote class="mt-8 border-t border-slate-200 pt-6 text-[15px] italic leading-8 text-slate-600">
            "Maharani Mobil hadir tidak hanya sebagai tempat jual beli mobil, tetapi juga sebagai mitra terpercaya dalam membantu
            Anda menemukan kendaraan yang tepat."
          </blockquote>
        </article>

        <aside class="space-y-6">
          <section class="rounded-[2rem] bg-[#071b47] p-7 text-white shadow-[0_24px_70px_rgba(7,27,71,0.18)]">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-[#f5a623]">location_on</span>
              <h2 class="font-headline text-[28px] font-bold">Lokasi Kami</h2>
            </div>

            <div class="mt-6 space-y-5 text-[15px] leading-7 text-slate-200">
              <div>
                <p class="font-bold text-[#f5a623]">Alamat:</p>
                <p>Jl. Arifin Ahmad No.113, Pekanbaru</p>
              </div>
              <div>
                <p class="font-bold text-[#f5a623]">Kontak:</p>
                <p>+62 811-7584-617</p>
              </div>
            </div>

            <div class="mt-6 border-t border-white/10 pt-6">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#f5a623]">schedule</span>
                <h3 class="font-headline text-[20px] font-bold">Jam Operasional</h3>
              </div>
              <div class="mt-4 space-y-3 text-[14px] text-slate-200">
                <div class="flex items-center justify-between gap-4">
                  <span>Senin - Sabtu</span>
                  <span>08.00 - 17.30</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                  <span>Minggu</span>
                  <span>09.00 - 17.30</span>
                </div>
              </div>
            </div>
          </section>

          <section class="rounded-[2rem] bg-[#f5a623] p-7 text-[#362300] shadow-[0_24px_70px_rgba(245,166,35,0.22)]">
            <span class="material-symbols-outlined">workspace_premium</span>
            <h3 class="mt-4 font-headline text-[30px] font-bold leading-tight">The Maharani Standard</h3>
            <p class="mt-3 text-[15px] leading-7">
              Rating tinggi dan reputasi terjaga selama 6+ tahun beroperasi di Pekanbaru.
            </p>
          </section>

          <section class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-[0_24px_70px_rgba(7,27,71,0.08)]">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-[#071b47]">handshake</span>
              <h3 class="font-headline text-[26px] font-bold text-[#071b47]">Pengalaman yang terpercaya</h3>
            </div>
            <p class="mt-4 text-[15px] leading-7 text-slate-600">
              Setiap pelanggan mendapatkan pelayanan langsung pleh pihak showroom saat melihat unit, konsultasi kebutuhan, hingga pembahasan  transaksi dilakukan.
            </p>
            <div class="mt-5 flex flex-wrap gap-2">
              <span class="rounded-full bg-[#edf3ff] px-4 py-2 text-[13px] font-semibold text-[#071b47]">Inspeksi terjamin</span>
              <span class="rounded-full bg-[#fff1da] px-4 py-2 text-[13px] font-semibold text-[#8a5800]">Pelayanan yang nyaman</span>
              <span class="rounded-full bg-[#eefbf3] px-4 py-2 text-[13px] font-semibold text-[#17623a]">Unit terpilih</span>
            </div>
          </section>
        </aside>
      </div>
    </section>

    <section class="mx-auto -mt-2 mb-16 w-full max-w-[1280px] px-5 md:px-6">
      <div class="overflow-hidden rounded-[2.25rem] border border-slate-200 bg-[linear-gradient(180deg,#ffffff_0%,#fbfdff_100%)] shadow-[0_24px_70px_rgba(7,27,71,0.08)]">
        <div class="grid grid-cols-1 lg:grid-cols-[1.22fr_0.78fr]">
          <div class="p-6 md:p-8 xl:px-10 xl:py-9">
            <div class="flex items-center justify-between gap-4">
              <div>
                <p class="text-[12px] font-bold uppercase tracking-[0.24em] text-[#f5a623]">Visi &amp; Misi</p>
                <p class="mt-3 max-w-[620px] text-[15px] leading-7 text-slate-600">
                  Prinsip utama Maharani Mobil dalam memberikan pelayanan yang aman, nyaman, dan terpercaya untuk setiap pelanggan.
                </p>
              </div>
            </div>

            <div class="mt-7 grid grid-cols-1 gap-4 xl:grid-cols-[0.94fr_1.06fr]">
              <article class="rounded-[1.75rem] border border-[#d9e5f6] bg-white p-6 shadow-[0_16px_40px_rgba(7,27,71,0.06)]">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#edf3ff] text-[#071b47]">
                  <span class="material-symbols-outlined text-[24px]">visibility</span>
                </div>
                <h3 class="mt-5 font-headline text-[24px] font-bold text-[#071b47]">Visi</h3>
                <p class="mt-4 text-[15px] leading-8 text-slate-700">
                  Menjadi showroom mobil bekas terpercaya yang mampu memberikan pelayanan terbaik, kualitas unit yang terjamin,
                  serta solusi kendaraan yang sesuai dengan kebutuhan pelanggan.
                </p>
              </article>

              <article class="rounded-[1.75rem] border border-[#d9e5f6] bg-white p-6 shadow-[0_16px_40px_rgba(7,27,71,0.06)]">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff1da] text-[#c77d05]">
                  <span class="material-symbols-outlined text-[24px]">flag</span>
                </div>
                <h3 class="mt-5 font-headline text-[24px] font-bold text-[#071b47]">Misi</h3>
                <ol class="mt-4 space-y-3 text-[14px] leading-7 text-slate-700">
                  <li class="flex gap-3">
                    <span class="mt-1 inline-flex h-7 w-7 flex-none items-center justify-center rounded-full bg-[#071b47] text-[11px] font-bold text-white">1</span>
                    <span>Menyediakan unit mobil bekas berkualitas, layak pakai, dan kompetitif.</span>
                  </li>
                  <li class="flex gap-3">
                    <span class="mt-1 inline-flex h-7 w-7 flex-none items-center justify-center rounded-full bg-[#071b47] text-[11px] font-bold text-white">2</span>
                    <span>Memberikan pelayanan profesional, transparan, dan berorientasi pada kepuasan pelanggan.</span>
                  </li>
                  <li class="flex gap-3">
                    <span class="mt-1 inline-flex h-7 w-7 flex-none items-center justify-center rounded-full bg-[#071b47] text-[11px] font-bold text-white">3</span>
                    <span>Menjaga kepercayaan pelanggan melalui kejujuran dan tanggung jawab.</span>
                  </li>
                </ol>
              </article>
            </div>
          </div>

          <div class="relative min-h-[320px] overflow-hidden bg-white lg:min-h-[100%]">
            @if ($aboutCustomerCutoutMirror)
              <div class="pointer-events-none absolute right-[0.5%] top-[1.5%] z-20 h-[39%] w-[40%] bg-white [clip-path:polygon(16%_0,100%_0,100%_100%,77%_100%,36%_38%)]"></div>
              <div class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-end px-0">
                <img
                  alt="Tim Maharani Mobil melayani pelanggan"
                  class="h-auto w-[101%] max-w-[560px] translate-x-[2.5%] object-contain object-bottom-right lg:w-[99%] lg:max-w-[620px] lg:translate-x-[2%]"
                  src="{{ $aboutCustomerCutoutMirror }}"
                />
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto mb-16 w-full max-w-[1280px] px-5 md:px-6">
      <div class="overflow-hidden rounded-[2rem] bg-white shadow-[0_24px_70px_rgba(7,27,71,0.08)]">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 md:flex-row md:items-center md:justify-between md:px-8">
          <div>
            <p class="text-[14px] font-semibold text-[#071b47]">Kunjungi Showroom Kami</p>
            <p class="mt-1 text-[14px] text-slate-500">Jl. Arifin Ahmad No.113, Sidomulyo Timur, Marpoyan Damai.</p>
          </div>
          <a
            class="inline-flex items-center justify-center rounded-full bg-[#071b47] px-6 py-3 text-[13px] font-bold text-white transition hover:bg-[#0d2a67]"
            href="https://www.google.com/maps/dir/?api=1&destination=Maharani%20Mobil%2C%20Jl.%20Arifin%20Ahmad%20No.113%2C%20Sidomulyo%20Timur%2C%20Marpoyan%20Damai%2C%20Kota%20Pekanbaru%2C%20Riau&travelmode=driving"
            target="_blank"
            rel="noopener noreferrer"
          >
            <span class="material-symbols-outlined mr-2 text-[18px]">route</span>
            Petunjuk Arah
          </a>
        </div>

        <div class="relative h-[240px] overflow-hidden bg-slate-100 md:h-[360px]">
          <iframe
            title="Lokasi Showroom Maharani Mobil"
            class="h-full w-full"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q=Maharani%20Mobil%2C%20Jl.%20Arifin%20Ahmad%20No.113%2C%20Sidomulyo%20Timur%2C%20Marpoyan%20Damai%2C%20Kota%20Pekanbaru%2C%20Riau&hl=id&z=16&output=embed"
          ></iframe>
        </div>
      </div>
    </section>
  </main>

  @include('components.public-site-footer')
  @include('components.whatsapp-float', ['message' => 'Halo Maharani Mobil, saya ingin mengetahui lebih lanjut tentang showroom Maharani Mobil.'])
  @include('components.ui-system-footer')
</body>
</html>
