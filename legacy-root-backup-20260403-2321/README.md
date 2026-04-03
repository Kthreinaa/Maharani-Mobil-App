# Maharani Mobil Web UI

Ini adalah kumpulan halaman UI/UX untuk sistem "Sistem Informasi Penjualan Mobil Bekas – Maharani Mobil Pekanbaru".

## Struktur Halaman

Public (Customer)
- `index.html` Landing Page
- `home.html` Home Page
- `catalog.html` Katalog Mobil
- `car-detail.html` Detail Mobil
- `test-drive.html` Booking Test Drive
- `cart.html` Keranjang (1 unit)
- `checkout.html` Checkout
- `payment.html` Pembayaran
- `payment-upload.html` Upload Bukti Pembayaran
- `order-tracking.html` Tracking Pesanan
- `about.html` About Us
- `financing.html` Financing
- `reviews.html` Review & Testimoni
- `offers.html` Ajukan Penawaran
- `login.html` Login
- `register.html` Register
- `privacy.html` Privacy Policy
- `terms.html` Terms of Service
- `faq.html` Help Center

Dashboard (Role Based)
- `dashboard-marketing.html` Overview
- `marketing-upload.html` Upload Produk
- `marketing-products.html` Manajemen Produk
- `marketing-orders.html` Manajemen Pesanan
- `marketing-offers.html` Manajemen Penawaran
- `dashboard-supervisor.html` Overview
- `supervisor-payments.html` Verifikasi Pembayaran
- `supervisor-transactions.html` Manajemen Transaksi
- `supervisor-users.html` Manajemen User
- `supervisor-activity.html` Monitoring Aktivitas
- `dashboard-owner.html` Overview
- `owner-sales.html` Laporan Penjualan
- `owner-revenue.html` Grafik Pendapatan
- `owner-performance.html` Analisis Performa

## API Integration Map

Landing / Home
- GET `/api/cars?limit=6`
- GET `/api/cars/popular`
- GET `/api/reviews`

Katalog Mobil
- GET `/api/cars` with query params: `brand`, `year`, `price_min`, `price_max`, `status`

Detail Mobil
- GET `/api/cars/{id}`
- GET `/api/cars/{id}/images`
- GET `/api/cars/{id}/reviews`
- GET `/api/cars/{id}/recommendations`
- POST `/api/test-drive`
- POST `/api/offers`

Test Drive
- POST `/api/test-drive`

Keranjang
- POST `/api/cart`
- GET `/api/cart`
- DELETE `/api/cart/{id}`

Checkout
- POST `/api/orders`

Pembayaran
- POST `/api/payments`
- GET `/api/payment-methods`

Upload Bukti
- POST `/api/payments/upload`

Tracking Pesanan
- GET `/api/orders/{id}`

Marketing
- POST `/api/cars`
- PUT `/api/cars/{id}`
- DELETE `/api/cars/{id}`
- GET `/api/orders`

Supervisor
- GET `/api/payments`
- PATCH `/api/payments/{id}/verify`
- GET `/api/users`

Owner
- GET `/api/reports/sales`
- GET `/api/reports/revenue`
- GET `/api/reports/top-cars`

Auth
- POST `/api/auth/login`
- POST `/api/auth/register`
- GET `/api/auth/me`

## Notes
- Seluruh UI menggunakan TailwindCSS CDN dan shared config di `public/assets/tailwind.config.js`.
- Komponen global dan styling helper berada di `public/assets/app.css`.
- Jalankan lokal (tanpa Laravel full) dengan: `php -S localhost:8000 -t public`
- Shortcut: `run.bat` (Windows) atau `run.ps1`
