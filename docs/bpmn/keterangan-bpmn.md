# Keterangan BPMN Maharani Mobil App

Dokumen ini menjelaskan BPMN proses bisnis setelah adanya sistem Maharani Mobil App.

## File Gambar

- `docs/bpmn/bpmn-penjualan-setelah-sistem.png`
- `docs/bpmn/bpmn-rekapitulasi-penjualan-setelah-sistem.png`

## 1. BPMN Penjualan Setelah Sistem

Lane yang digunakan:

- `Customer`
- `Sistem Maharani Mobil App`
- `Supervisor`

Alur utama:

1. Customer mengakses website atau aplikasi.
2. Customer login atau register.
3. Customer melihat katalog dan detail mobil.
4. Customer memilih unit lalu mengirim pesanan.
5. Sistem menyimpan order dan metode beli.
6. Supervisor meninjau pesanan atau pengajuan.
7. Sistem menampilkan instruksi pembayaran atau status pengajuan.
8. Customer upload bukti bayar atau kirim pengajuan kredit.
9. Supervisor memvalidasi pembayaran atau DP.
10. Sistem memperbarui status pesanan dan menyiapkan dokumen.
11. Supervisor melakukan serah terima unit.
12. Customer menerima unit dan dokumen.

## 2. BPMN Rekapitulasi Penjualan Setelah Sistem

Lane yang digunakan:

- `Marketing`
- `Supervisor`
- `Sistem Maharani Mobil App`
- `Owner`

Alur utama:

1. Marketing menginput atau memperbarui data produk dan aktivitas penjualan.
2. Supervisor memvalidasi transaksi, pesanan, dan pembayaran.
3. Sistem menyimpan seluruh data penjualan.
4. Supervisor melengkapi status transaksi sampai siap direkap.
5. Sistem mengolah data penjualan per periode.
6. Owner memilih periode laporan.
7. Sistem menampilkan dashboard, grafik, dan rekap penjualan.
8. Owner mengekspor laporan ke PDF atau Excel.

