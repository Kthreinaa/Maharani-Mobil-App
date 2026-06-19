# Skenario Use Case Maharani Mobil App

Dokumen ini dibuat berdasarkan fitur yang aktif di `routes/web.php`, controller, dan halaman yang sedang dipakai pada sistem Maharani Mobil App.

## Aktor Sistem

- `Pengunjung`: pengguna yang belum login.
- `Customer`: pengguna yang membeli unit, booking test drive, memberi review, dan memantau pesanan.
- `Supervisor`: pengguna internal yang memvalidasi transaksi dan mengelola operasional.
- `Marketing`: pengguna internal yang memantau produk, penawaran, transaksi, dan customer.
- `Owner`: pimpinan yang melihat dashboard dan laporan penjualan.
- `Xendit / Gateway Pembayaran`: sistem eksternal yang mengirim notifikasi pembayaran.

## Use Case Utama Customer

### 1. Lihat katalog mobil

- Aktor: `Pengunjung`, `Customer`
- Tujuan: melihat daftar mobil yang tersedia.
- Prasyarat: data mobil sudah ada di sistem.
- Alur utama:
  1. Pengguna membuka halaman katalog.
  2. Sistem menampilkan daftar mobil yang statusnya tersedia.
  3. Pengguna dapat melakukan filter dan melihat detail mobil.
- Hasil akhir: pengguna mendapatkan informasi unit mobil yang ingin dilihat.

### 2. Booking test drive

- Aktor: `Customer`
- Tujuan: membuat jadwal test drive unit mobil.
- Prasyarat:
  - customer sudah login
  - unit mobil tersedia
- Alur utama:
  1. Customer membuka form test drive.
  2. Customer memilih mobil, tanggal, jam, dan lokasi.
  3. Customer mengirim form booking.
  4. Sistem menyimpan data test drive dengan status `pending`.
- Hasil akhir: booking test drive tercatat dan menunggu tindak lanjut.

### 3. Ajukan penawaran harga

- Aktor: `Customer`
- Tujuan: mengirim penawaran harga untuk unit mobil.
- Prasyarat:
  - customer sudah login
  - unit mobil tersedia
- Alur utama:
  1. Customer membuka detail mobil.
  2. Customer mengisi nominal penawaran.
  3. Sistem menyimpan penawaran dan menunggu respons internal.
- Hasil akhir: penawaran customer tercatat di sistem.

### 4. Pesan mobil

- Aktor: `Customer`
- Tujuan: membuat pesanan pembelian mobil.
- Prasyarat:
  - customer sudah login
  - unit belum terjual
- Alur utama:
  1. Customer memilih unit mobil.
  2. Customer memilih metode beli `cash` atau `kredit`.
  3. Jika `cash`, customer melanjutkan ke pembayaran booking / transfer.
  4. Jika `kredit`, customer mengisi data simulasi kredit dan mengirim pengajuan.
  5. Sistem membuat data pesanan dengan status `pending`.
- Hasil akhir: pesanan tersimpan dan masuk ke proses tindak lanjut.

### 5. Upload pembayaran

- Aktor: `Customer`
- Tujuan: mengirim bukti pembayaran.
- Prasyarat:
  - customer sudah punya pesanan
  - customer sudah berada pada tahap pembayaran
- Alur utama:
  1. Customer membuka halaman pembayaran.
  2. Customer memilih rekening / metode yang tersedia.
  3. Customer mengunggah bukti pembayaran.
  4. Sistem menyimpan data pembayaran untuk diverifikasi supervisor.
- Hasil akhir: pembayaran tercatat dan menunggu validasi.

### 6. Lihat tracking pesanan

- Aktor: `Customer`
- Tujuan: melihat perkembangan pesanan.
- Prasyarat: customer sudah memiliki pesanan.
- Alur utama:
  1. Customer membuka halaman tracking.
  2. Sistem menampilkan status pesanan, pembayaran, dan dokumen.
- Hasil akhir: customer mengetahui progres pembelian.

### 7. Tulis review customer

- Aktor: `Customer`
- Tujuan: memberikan ulasan setelah pembelian atau test drive selesai.
- Prasyarat:
  - customer sudah login
  - customer memiliki unit yang eligible untuk direview
  - unit tersebut belum pernah direview oleh customer yang sama
- Alur utama:
  1. Customer membuka form review.
  2. Sistem hanya menampilkan unit yang belum direview.
  3. Customer memilih unit, mengisi rating, komentar, dan foto.
  4. Sistem menyimpan review ke data ulasan customer.
- Hasil akhir: review tampil pada halaman ulasan sesuai status yang berlaku.

## Use Case Utama Supervisor

### 8. Kelola pesanan

- Aktor: `Supervisor`
- Tujuan: menindaklanjuti pesanan customer.
- Prasyarat: supervisor sudah login.
- Alur utama:
  1. Supervisor membuka menu manajemen pesanan.
  2. Supervisor melihat detail order.
  3. Supervisor memperbarui status pesanan, catatan follow-up, dan data transaksi.
- Hasil akhir: status pesanan terbarui sesuai proses bisnis showroom.

### 9. Validasi pembayaran

- Aktor: `Supervisor`
- Tujuan: memverifikasi pembayaran yang masuk.
- Prasyarat: ada data pembayaran dari customer.
- Alur utama:
  1. Supervisor membuka menu transaksi & pembayaran.
  2. Supervisor memilih data pembayaran.
  3. Supervisor memeriksa bukti bayar.
  4. Supervisor memilih `verifikasi` atau `tolak`.
- Hasil akhir: pembayaran berubah status sesuai hasil pemeriksaan.

### 10. Kelola data mobil

- Aktor: `Supervisor`
- Tujuan: menambah, mengubah, mengganti foto, atau menghapus data mobil.
- Prasyarat: supervisor sudah login.
- Alur utama:
  1. Supervisor membuka menu data mobil.
  2. Supervisor menambah atau mengedit informasi mobil.
  3. Supervisor menyimpan perubahan data.
- Hasil akhir: data mobil showroom selalu terbarui.

### 11. Unduh dokumen transaksi

- Aktor: `Supervisor`, `Customer`
- Tujuan: melihat atau mengunduh dokumen transaksi.
- Prasyarat: dokumen transaksi sudah tersedia.
- Alur utama:
  1. Pengguna membuka dokumen order.
  2. Sistem menghasilkan faktur, kwitansi, atau BAST sesuai data order.
- Hasil akhir: dokumen transaksi dapat diunduh.

## Use Case Utama Marketing

### 12. Pantau transaksi dan pesanan

- Aktor: `Marketing`
- Tujuan: memantau hasil aktivitas penjualan tanpa mengubah validasi pembayaran.
- Prasyarat: marketing sudah login.
- Alur utama:
  1. Marketing membuka dashboard atau menu pantau transaksi.
  2. Sistem menampilkan data transaksi, metode beli, metode bayar, dan status.
- Hasil akhir: marketing dapat membaca kondisi penjualan dan aktivitas customer.

### 13. Kelola data produk mobil

- Aktor: `Marketing`
- Tujuan: menambah dan memperbarui data produk mobil.
- Prasyarat: marketing sudah login.
- Alur utama:
  1. Marketing membuka menu produk.
  2. Marketing menambah atau mengubah data mobil.
  3. Marketing menyimpan data.
- Hasil akhir: data produk untuk promosi dan katalog tetap up to date.

## Use Case Utama Owner

### 14. Lihat dashboard owner

- Aktor: `Owner`
- Tujuan: melihat ringkasan kinerja bisnis.
- Prasyarat: owner sudah login.
- Alur utama:
  1. Owner membuka dashboard.
  2. Sistem menampilkan total transaksi, omzet, rata-rata transaksi, pola pembelian cash/kredit, dan ringkasan lainnya.
- Hasil akhir: owner mengetahui performa bisnis secara cepat.

### 15. Lihat dan export laporan penjualan

- Aktor: `Owner`
- Tujuan: melihat laporan penjualan dan mengunduh hasilnya.
- Prasyarat: data transaksi sudah tersedia.
- Alur utama:
  1. Owner membuka menu laporan.
  2. Owner memilih periode laporan.
  3. Sistem menampilkan rekap penjualan.
  4. Owner dapat export ke PDF atau Excel.
- Hasil akhir: laporan penjualan tersedia untuk analisis atau presentasi.

## Cara Memakai di Goose UML

### Opsi paling aman

1. Buka file `docs/usecase/maharani-mobil-usecase.puml`.
2. Jika Goose UML mendukung PlantUML, buka atau preview file itu langsung.
3. Jika Goose UML meminta input teks diagram, salin isi file `.puml` tersebut.
4. Gunakan file `docs/usecase/skenario-usecase.md` sebagai dasar penjelasan skenario use case di laporan Anda.

### Cara menghubungkan dengan sistem yang sudah ada

- Dasar use case diambil dari `routes/web.php`.
- Nama aktor diambil dari middleware role:
  - `customer`
  - `supervisor`
  - `marketing`
  - `owner`
- Nama proses diambil dari controller dan halaman yang benar-benar aktif di sistem.

### Saran penulisan di laporan

- Gunakan diagram use case ini untuk bab analisis / perancangan.
- Gunakan skenario use case ini untuk tabel:
  - nama use case
  - aktor
  - tujuan
  - prasyarat
  - alur utama
  - hasil akhir
