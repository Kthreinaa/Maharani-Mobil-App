<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\User;
use App\Support\CreditSimulationCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function landing()
    {
        if ($this->authenticatedCustomer()) {
            return redirect()->route('customer.home');
        }

        $faqItems = $this->faqItems();

        if (!$this->hasCarsTable()) {
            return view('pages.index', [
                'newestCars' => collect(),
                'brandOptions' => collect(),
                'faqItems' => $faqItems,
                'featuredReviews' => collect(),
                'featuredReviewCount' => 0,
                'featuredReviewAverage' => 0.0,
            ]);
        }

        $newestCars = Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->latest()
            ->take(3)
            ->get();

        $brandOptions = $this->brandOptions();
        $featuredReviews = collect();
        if (Schema::hasTable('product_reviews')) {
            $featuredReviews = ProductReview::query()
                ->with(['user', 'car'])
                ->where('status', 'approved')
                ->latest()
                ->take(12)
                ->get()
                ->sortByDesc(fn (ProductReview $review) => !empty($review->review_photos))
                ->take(3)
                ->values();
        }
        $featuredReviewCount = Schema::hasTable('product_reviews')
            ? ProductReview::query()->where('status', 'approved')->count()
            : 0;
        $featuredReviewAverage = Schema::hasTable('product_reviews') && $featuredReviewCount > 0
            ? round((float) ProductReview::query()->where('status', 'approved')->avg('rating'), 1)
            : 0.0;

        return view('pages.index', compact('newestCars', 'brandOptions', 'faqItems', 'featuredReviews', 'featuredReviewCount', 'featuredReviewAverage'));
    }

    public function home()
    {
        if ($this->authenticatedCustomer()) {
            return redirect()->route('customer.home');
        }

        if (!$this->hasCarsTable()) {
            return view('pages.home', [
                'catalogCars' => collect(),
                'homeOverviewCars' => collect(),
                'featuredReviews' => collect(),
                'featuredReviewCount' => 0,
                'featuredReviewAverage' => 0.0,
            ]);
        }

        $catalogCars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $homeOverviewCars = $catalogCars->take(6);
        $featuredReviews = collect();
        if (Schema::hasTable('product_reviews')) {
            $featuredReviews = ProductReview::query()
                ->with(['user', 'car'])
                ->where('status', 'approved')
                ->latest()
                ->take(12)
                ->get()
                ->sortByDesc(fn (ProductReview $review) => !empty($review->review_photos))
                ->take(3)
                ->values();
        }
        $featuredReviewCount = Schema::hasTable('product_reviews')
            ? ProductReview::query()->where('status', 'approved')->count()
            : 0;
        $featuredReviewAverage = Schema::hasTable('product_reviews') && $featuredReviewCount > 0
            ? round((float) ProductReview::query()->where('status', 'approved')->avg('rating'), 1)
            : 0.0;

        return view('pages.home', compact('catalogCars', 'homeOverviewCars', 'featuredReviews', 'featuredReviewCount', 'featuredReviewAverage'));
    }

    /**
     * Ambil user login khusus customer agar controller tetap mudah dibaca
     * dan editor tidak salah menandai akses properti role sebagai error.
     */
    private function authenticatedCustomer(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User || $user->role !== 'customer') {
            return null;
        }

        return $user;
    }

    public function catalog(Request $request)
    {
        if (!$this->hasCarsTable()) {
            return view('pages.catalog', [
                'catalogCars' => collect(),
                'brandOptions' => collect(),
                'catalogState' => [
                    'sort' => 'latest',
                    'hasFilters' => false,
                    'activeFilters' => [],
                ],
            ]);
        }

        $catalogQuery = Car::query()
            ->where('status', 'available');

        $activeFilters = [];

        if ($request->filled('brand') && $request->input('brand') !== 'all') {
            $brand = trim((string) $request->input('brand'));
            $catalogQuery->where('merk', 'like', '%' . $brand . '%');
            $activeFilters['brand'] = $brand;
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $catalogQuery->where(function ($sub) use ($q) {
                $sub->where('merk', 'like', '%' . $q . '%')
                    ->orWhere('tipe', 'like', '%' . $q . '%');
            });
            $activeFilters['q'] = $q;
        }

        $yearMinInput = $request->input('year_min', $request->input('year'));
        if ($yearMinInput !== null && $yearMinInput !== '') {
            $yearMin = (int) $yearMinInput;
            if ($yearMin >= 2010) {
                $catalogQuery->where('tahun', '>=', $yearMin);
                $activeFilters['year_min'] = $yearMin;
            }
        }

        $kmInput = $request->input('kilometer', $request->input('km_max', $request->input('km')));
        if ($kmInput !== null && $kmInput !== '') {
            $kmMax = (int) preg_replace('/[^\d]/', '', (string) $kmInput);
            if ($kmMax > 0) {
                $catalogQuery->whereNotNull('kilometer')
                    ->where('kilometer', '<=', $kmMax);
                $activeFilters['kilometer'] = $kmMax;
            }
        }

        $normalizePrice = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }

            $num = (float) $value;
            if ($num <= 0) {
                return null;
            }

            if ($num < 1000000) {
                return (int) round($num * 1000000);
            }

            return (int) round($num);
        };

        $priceMin = $normalizePrice($request->input('price_min'));
        $priceMax = $normalizePrice($request->input('price_max'));
        if ($priceMin !== null) {
            $catalogQuery->where('harga', '>=', $priceMin);
            $activeFilters['price_min'] = $priceMin;
        }
        if ($priceMax !== null) {
            $catalogQuery->where('harga', '<=', $priceMax);
            $activeFilters['price_max'] = $priceMax;
        }

        $priceTarget = $normalizePrice($request->input('price_target', $request->input('price')));
        if ($priceTarget !== null) {
            $catalogQuery->orderByRaw('ABS(harga - ?) asc', [$priceTarget]);
            $activeFilters['price_target'] = $priceTarget;
        }

        $sort = (string) $request->input('sort', 'latest');
        $allowedSorts = [
            'latest',
            'oldest',
            'price_low',
            'price_high',
            'year_newest',
            'year_oldest',
            'km_low',
            'km_high',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        switch ($sort) {
            case 'oldest':
                $catalogQuery->oldest();
                break;
            case 'price_low':
                $catalogQuery->orderBy('harga')->latest('id');
                break;
            case 'price_high':
                $catalogQuery->orderByDesc('harga')->latest('id');
                break;
            case 'year_newest':
                $catalogQuery->orderByDesc('tahun')->latest('id');
                break;
            case 'year_oldest':
                $catalogQuery->orderBy('tahun')->latest('id');
                break;
            case 'km_low':
                $catalogQuery->orderBy('kilometer')->latest('id');
                break;
            case 'km_high':
                $catalogQuery->orderByDesc('kilometer')->latest('id');
                break;
            case 'latest':
            default:
                $catalogQuery->latest();
                break;
        }

        $catalogCars = $catalogQuery->get();
        $brandOptions = $this->brandOptions();
        $catalogState = [
            'sort' => $sort,
            'hasFilters' => !empty($activeFilters),
            'activeFilters' => $activeFilters,
        ];

        return view('pages.catalog', compact('catalogCars', 'brandOptions', 'catalogState'));
    }

    public function carDetail($id)
    {
        abort_unless($this->hasCarsTable(), 404);

        $car = Car::query()->findOrFail($id);

        $relatedCars = Car::query()
            ->where('status', 'available')
            ->where('id', '!=', $car->id)
            ->orderByRaw('merk = ? desc', [$car->merk])
            ->latest()
            ->take(3)
            ->get();

        $approvedReviewsCount = Schema::hasTable('product_reviews')
            ? ProductReview::query()
                ->where('car_id', $car->id)
                ->where('status', 'approved')
                ->count()
            : 0;

        $approvedReviewsAverage = Schema::hasTable('product_reviews') && $approvedReviewsCount > 0
            ? round((float) ProductReview::query()
                ->where('car_id', $car->id)
                ->where('status', 'approved')
                ->avg('rating'), 1)
            : 0;

        return view('pages.car-detail', compact('car', 'relatedCars', 'approvedReviewsCount', 'approvedReviewsAverage'));
    }

    public function testDrive(Request $request)
    {
        if (!$this->hasCarsTable()) {
            return view('pages.test-drive', [
                'cars' => collect(),
                'selectedCar' => null,
            ]);
        }

        $cars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $selectedCarId = (int) $request->query('car_id', 0);
        $selectedCar = $selectedCarId > 0
            ? $cars->firstWhere('id', $selectedCarId)
            : $cars->first();

        return view('pages.test-drive', compact('cars', 'selectedCar'));
    }

    public function cart(Request $request)
    {
        $car = $this->resolveCarFromRequest($request);

        return view('pages.cart', compact('car'));
    }

    public function checkout(Request $request)
    {
        $params = array_filter([
            'car_id' => $request->query('car_id'),
            'offer_id' => $request->query('offer_id'),
        ], fn ($value) => filled($value));

        return redirect()->route('checkout.cash', $params);
    }

    public function checkoutCash(Request $request)
    {
        [$car, $sourceOffer, $cashPrice, $bookingFee, $remainingBalance] = $this->resolveCheckoutContext($request);

        if (!$car) {
            return redirect()->route('catalog')->with('error', 'Pilih unit mobil terlebih dahulu sebelum masuk ke checkout.');
        }

        return view('pages.checkout', compact('car', 'cashPrice', 'bookingFee', 'remainingBalance', 'sourceOffer'));
    }

    public function checkoutCredit(Request $request)
    {
        $params = array_filter([
            'car_id' => $request->query('car_id'),
            'offer_id' => $request->query('offer_id'),
        ], fn ($value) => filled($value));

        return redirect()
            ->route('checkout.cash', $params)
            ->with('success', 'Pembelian online saat ini menggunakan skema cash via transfer. Fitur simulasi kredit telah dinonaktifkan.');
    }

    public function payment(Request $request)
    {
        $order = $this->resolveCustomerOrder($request);
        if (!$order) {
            return redirect()->route('customer.orders.index')->with('error', 'Belum ada pesanan yang bisa diproses untuk pembayaran.');
        }

        if ($order->is_credit_purchase) {
            if ($order->status === 'pending') {
                return redirect()
                    ->route('order.tracking', ['order' => $order->id])
                    ->with('error', 'Pengajuan kredit Anda masih menunggu persetujuan supervisor. Setelah disetujui, customer bisa melanjutkan pembayaran DP ke pihak showroom.');
            }
        }

        $payment = $order->payment;
        $bookingFee = $order->is_credit_purchase
            ? (float) ($order->credit_dp_amount ?? 0)
            : $this->bookingFeeAmount();
        $selectedPaymentPlan = $order->is_credit_purchase
            ? 'credit_dp'
            : $this->resolveCashPaymentPlan($order);
        $activePaymentAmount = $order->is_credit_purchase
            ? $bookingFee
            : ($selectedPaymentPlan === 'full' ? (float) $order->total : $bookingFee);
        $remainingBalance = max((float) $order->total - $activePaymentAmount, 0);
        $bankAccounts = $this->showroomBankAccounts();
        $xenditEnabled = !$order->is_credit_purchase && filled(config('services.xendit.secret_key'));
        $settlementAccount = config('payments.settlement');

        return view('pages.payment', compact(
            'order',
            'payment',
            'bookingFee',
            'remainingBalance',
            'bankAccounts',
            'xenditEnabled',
            'settlementAccount',
            'selectedPaymentPlan',
            'activePaymentAmount'
        ));
    }

    public function paymentUpload(Request $request)
    {
        $order = $this->resolveCustomerOrder($request);
        if (!$order) {
            return redirect()->route('customer.orders.index')->with('error', 'Belum ada pesanan yang bisa diproses.');
        }

        return redirect()
            ->route('order.tracking', ['order' => $order->id])
            ->with('error', 'Customer tidak perlu upload bukti bayar. Pembayaran divalidasi oleh supervisor di sistem.');
    }

    public function orderTracking(Request $request)
    {
        $order = $this->resolveCustomerOrder($request);
        if (!$order) {
            return redirect()->route('customer.orders.index')->with('error', 'Belum ada pesanan yang bisa dilacak.');
        }

        $payment = $order->payment;

        return view('pages.order-tracking', compact('order', 'payment'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function financing()
    {
        return view('pages.financing');
    }

    public function reviews()
    {
        return view('pages.reviews');
    }

    public function offers(Request $request)
    {
        if (!$this->hasCarsTable()) {
            return view('pages.offers', [
                'cars' => collect(),
                'selectedCar' => null,
                'myOffers' => collect(),
            ]);
        }

        $cars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $selectedCarId = (int) $request->query('car_id', 0);
        $selectedCar = $selectedCarId > 0
            ? $cars->firstWhere('id', $selectedCarId)
            : $cars->first();

        $myOffers = $request->user()
            ? $request->user()->offers()->with(['car', 'handledBy', 'histories'])->latest()->get()
            : collect();

        return view('pages.offers', compact('cars', 'selectedCar', 'myOffers'));
    }

    public function login()
    {
        return view('pages.login');
    }

    public function register()
    {
        return view('pages.register');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function faq()
    {
        $faqItems = $this->faqItems();

        return view('pages.faq', compact('faqItems'));
    }

    private function faqItems(): array
    {
        return [
            [
                'question' => __('Apakah mobil yang dijual benar-benar sesuai dengan kondisi yang ditampilkan di website?'),
                'paragraphs' => [
                    __('Ya. Kami berkomitmen menampilkan informasi kendaraan secara jujur dan transparan.'),
                    __('Setiap unit dilengkapi dengan foto asli kendaraan, detail spesifikasi, dan informasi kondisi kendaraan.'),
                    __('Namun, kami tetap menyarankan pelanggan untuk melakukan pengecekan langsung atau test drive agar lebih yakin sebelum membeli.'),
                ],
            ],
            [
                'question' => __('Bagaimana cara memastikan mobil dalam kondisi baik sebelum membeli?'),
                'paragraphs' => [
                    __('Sebelum membeli, Anda dapat melihat detail kendaraan di website, menghubungi tim kami untuk konsultasi, dan mengajukan test drive langsung di showroom.'),
                    __('Tim kami juga akan membantu menjelaskan kondisi mobil secara detail agar Anda dapat mengambil keputusan dengan lebih percaya diri.'),
                ],
            ],
            [
                'question' => __('Apakah harga mobil sudah termasuk semua biaya (OTR)?'),
                'paragraphs' => [
                    __('Harga mobil umumnya adalah harga OTR (On The Road), namun detail biaya dapat berbeda tergantung unit dan kesepakatan.'),
                    __('Informasi lengkap akan dijelaskan sebelum transaksi dilakukan.'),
                ],
                'bullets' => [
                    __('balik nama'),
                    __('pajak'),
                    __('administrasi tambahan'),
                ],
            ],
            [
                'question' => __('Bagaimana sistem pembayaran yang aman di Maharani Mobil?'),
                'paragraphs' => [
                    __('Untuk menjaga keamanan transaksi, pembayaran hanya dilakukan melalui instruksi resmi dari pihak Maharani Mobil.'),
                    __('Setiap transaksi akan diverifikasi oleh supervisor, dan status pembayaran dapat dipantau melalui sistem.'),
                    __('Kami sangat menyarankan untuk tidak melakukan pembayaran di luar jalur resmi.'),
                ],
            ],
            [
                'question' => __('Bagaimana proses pembelian mobil secara kredit?'),
                'paragraphs' => [
                    __('Untuk pembelian kredit, Anda memilih unit yang diinginkan lalu tim kami akan membantu menghubungkan dengan pihak leasing.'),
                    __('Proses persetujuan dilakukan oleh leasing, dan setelah disetujui transaksi dapat dilanjutkan.'),
                    __('Perlu diketahui bahwa proses kredit tidak langsung diproses di dalam sistem website.'),
                ],
            ],
            [
                'question' => __('Apa saja syarat administrasi untuk pengajuan kredit?'),
                'paragraphs' => [
                    __('Untuk pengajuan kredit, calon pembeli perlu menyiapkan beberapa dokumen administrasi berikut.'),
                ],
                'bullets' => [
                    __('Fotokopi KTP pasangan, atau KTP calon pembeli dan orang tua jika belum menikah'),
                    __('Kartu Keluarga (KK)'),
                    __('Nomor Pokok Wajib Pajak (NPWP)'),
                    __('Bukti tagihan listrik atau surat kepemilikan rumah'),
                    __('Rekening giro selama 3 bulan terakhir untuk melihat perputaran keuangan calon nasabah'),
                    __('Surat keterangan domisili jika alamat KTP tidak sesuai dengan tempat tinggal'),
                    __('Sertifikat bisnis untuk wiraswasta'),
                    __('Slip gaji atau surat keterangan kerja untuk karyawan'),
                ],
            ],
            [
                'question' => __('Leasing apa saja yang bekerja sama dengan showroom ini untuk pembelian kredit?'),
                'paragraphs' => [
                    __('Untuk pembelian secara kredit, Maharani Mobil bekerja sama dengan beberapa perusahaan leasing berikut.'),
                ],
                'bullets' => [
                    __('Clipan Finance'),
                    __('Cesul Finance'),
                    __('BCA Finance'),
                    __('CIMB Niaga Finance'),
                    __('BRI Finance'),
                    __('Mandiri Utama Finance'),
                    __('Mizuo Finance'),
                    __('Adira Finance'),
                    __('Oto Mukti Arta Finance'),
                    __('BFI Syariah'),
                ],
            ],
            [
                'question' => __('Apa saja faktor yang mempengaruhi persetujuan kredit?'),
                'paragraphs' => [
                    __('Persetujuan kredit biasanya dipengaruhi oleh beberapa faktor utama.'),
                    __('Setiap leasing memiliki kebijakan masing-masing dalam menentukan persetujuan.'),
                ],
                'bullets' => [
                    __('kelengkapan dokumen'),
                    __('riwayat kredit'),
                    __('penghasilan'),
                    __('kemampuan membayar'),
                ],
            ],
            [
                'question' => __('Apakah saya bisa memesan mobil tanpa langsung datang ke showroom?'),
                'paragraphs' => [
                    __('Ya, Anda dapat melakukan pemesanan melalui website.'),
                    __('Namun, untuk memastikan kesesuaian kendaraan, kami tetap menyarankan melakukan test drive atau melihat unit secara langsung.'),
                ],
            ],
            [
                'question' => __('Apakah mobil yang sudah dipesan bisa dibatalkan?'),
                'paragraphs' => [
                    __('Kebijakan pembatalan tergantung pada status transaksi, kesepakatan awal, dan metode pembayaran.'),
                    __('Silakan hubungi tim kami untuk informasi lebih lanjut terkait pembatalan.'),
                ],
            ],
            [
                'question' => __('Apakah mobil yang sudah dibeli bisa dikembalikan?'),
                'paragraphs' => [
                    __('Secara umum, pembelian mobil bersifat final.'),
                    __('Namun, jika terdapat kondisi khusus, hal tersebut dapat dibicarakan langsung dengan pihak showroom sesuai kebijakan yang berlaku.'),
                ],
            ],
            [
                'question' => __('Bagaimana Maharani Mobil menjaga kepercayaan pelanggan?'),
                'paragraphs' => [
                    __('Kami menjaga kepercayaan pelanggan dengan memberikan informasi kendaraan secara transparan, menyediakan proses transaksi yang jelas, dan memastikan komunikasi yang terbuka dengan pelanggan.'),
                    __('Kami percaya bahwa kepercayaan adalah hal utama dalam jual beli kendaraan.'),
                ],
            ],
            [
                'question' => __('Apakah data mobil dan statusnya selalu diperbarui?'),
                'paragraphs' => [
                    __('Ya, sistem akan memperbarui status mobil secara otomatis sehingga informasi yang Anda lihat selalu relevan dan akurat.'),
                ],
                'bullets' => [
                    __('tersedia'),
                    __('dalam proses pemesanan'),
                    __('terjual'),
                ],
            ],
            [
                'question' => __('Bagaimana jika saya masih ragu sebelum membeli?'),
                'paragraphs' => [
                    __('Jika Anda masih ragu, Anda dapat berkonsultasi langsung dengan tim kami, menghubungi melalui WhatsApp, atau datang langsung ke showroom.'),
                    __('Kami siap membantu Anda menemukan mobil yang paling sesuai dengan kebutuhan Anda.'),
                ],
            ],
        ];
    }

    public function marketingDashboard()
    {
        return view('pages.dashboard-marketing');
    }

    public function marketingUpload()
    {
        return view('pages.marketing-upload');
    }

    public function marketingProducts(Request $request)
    {
        if (!$this->hasCarsTable()) {
            return view('pages.marketing-products', ['cars' => collect()]);
        }

        $query = Car::query()->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('merk', 'like', '%' . $q . '%')
                    ->orWhere('tipe', 'like', '%' . $q . '%')
                    ->orWhere('kode_unit', 'like', '%' . $q . '%');
            });
        }

        $cars = $query->get();

        return view('pages.marketing-products', compact('cars'));
    }

    public function marketingOrders(Request $request)
    {
        $query = Order::query()
            ->with(['user', 'car', 'payment'])
            ->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', fn ($user) => $user->where('name', 'like', '%' . $q . '%'))
                    ->orWhereHas('car', fn ($car) => $car->where('merk', 'like', '%' . $q . '%')->orWhere('tipe', 'like', '%' . $q . '%'))
                    ->orWhere('id', $q);
            });
        }

        $orders = $query->get();

        return view('pages.marketing-orders', compact('orders'));
    }

    public function marketingOffers(Request $request)
    {
        $query = Offer::query()
            ->with(['user', 'car'])
            ->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $offers = $query->get();

        return view('pages.marketing-offers', compact('offers'));
    }

    public function supervisorDashboard()
    {
        return view('pages.dashboard-supervisor');
    }

    public function supervisorPayments()
    {
        return view('pages.supervisor-payments');
    }

    public function supervisorTransactions()
    {
        return view('pages.supervisor-transactions');
    }

    public function supervisorUsers()
    {
        return view('pages.supervisor-users');
    }

    public function supervisorActivity()
    {
        return view('pages.supervisor-activity');
    }

    public function ownerDashboard()
    {
        return view('pages.dashboard-owner');
    }

    public function ownerSales()
    {
        return view('pages.owner-sales');
    }

    public function ownerRevenue()
    {
        return view('pages.owner-revenue');
    }

    public function ownerPerformance()
    {
        return view('pages.owner-performance');
    }

    private function brandOptions()
    {
        if (!$this->hasCarsTable()) {
            return collect();
        }

        return Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->select('merk')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk');
    }

    private function resolveCarFromRequest(Request $request): ?Car
    {
        if (!$this->hasCarsTable()) {
            return null;
        }

        $carId = (int) $request->query('car_id', $request->session()->get('checkout_car_id', 0));
        if ($carId <= 0) {
            return null;
        }

        $car = Car::query()->find($carId);
        if ($car) {
            $request->session()->put('checkout_car_id', $car->id);
        }

        return $car;
    }

    private function resolveCustomerOrder(Request $request): ?Order
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        $orderId = (int) $request->query('order', $request->session()->get('active_order_id', 0));

        $query = Order::query()
            ->with(['car', 'payment'])
            ->where('user_id', $user->id);

        $order = $orderId > 0
            ? (clone $query)->where('id', $orderId)->first()
            : null;

        if (!$order) {
            $order = $query->latest()->first();
        }

        if ($order) {
            $request->session()->put('active_order_id', $order->id);
        }

        return $order;
    }

    private function bookingFeeAmount(): int
    {
        return (int) config('payments.booking_fee', 2500000);
    }

    private function resolveCashPaymentPlan(Order $order): string
    {
        $paidAmount = (float) ($order->payment?->amount ?? 0);
        if ($paidAmount >= (float) $order->total && (float) $order->total > 0) {
            return 'full';
        }

        $notes = (string) ($order->notes ?? '');
        if (str_contains($notes, 'Pilihan pembayaran cash online: Bayar Lunas Full')) {
            return 'full';
        }

        return 'booking';
    }

    private function resolveCheckoutContext(Request $request): array
    {
        $offerId = (int) $request->query('offer_id', 0);
        $sourceOffer = null;

        if ($offerId > 0 && $request->user()) {
            $sourceOffer = Offer::query()
                ->with('car')
                ->where('id', $offerId)
                ->where('user_id', $request->user()->id)
                ->where('status', 'accepted')
                ->first();
        }

        $car = $sourceOffer?->car ?? $this->resolveCarFromRequest($request);
        $cashPrice = (float) ($sourceOffer?->negotiated_price ?? ($car->harga ?? 0));
        $bookingFee = $this->bookingFeeAmount();
        $remainingBalance = max($cashPrice - $bookingFee, 0);

        return [$car, $sourceOffer, $cashPrice, $bookingFee, $remainingBalance];
    }

    private function showroomBankAccounts(): array
    {
        return [
            [
                'code' => 'mandiri',
                'bank' => (string) config('payments.settlement.bank', 'Bank Mandiri'),
                'account_name' => (string) config('payments.settlement.account_name', 'Diki Susanto'),
                'account_number' => (string) config('payments.settlement.account_number', '1080093012152'),
            ],
        ];
    }

    private function hasCarsTable(): bool
    {
        return Schema::hasTable('cars');
    }
}
