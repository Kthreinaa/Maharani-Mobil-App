<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Landing page public (/) menampilkan highlight dan "Unit Terbaru" dari database (bukan dummy).
     */
    public function landing()
    {
        $newestCars = Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->latest()
            ->take(3)
            ->get();

        $brandOptions = Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->select('merk')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk');

        return view('pages.index', compact('newestCars', 'brandOptions'));
    }

    /**
     * Menampilkan halaman home customer/public dengan overview stok mobil yang sama seperti katalog.
     */
    public function home()
    {
        $catalogCars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $homeOverviewCars = $catalogCars->take(6);

        return view('pages.home', compact('catalogCars', 'homeOverviewCars'));
    }

    /**
     * Menampilkan halaman katalog dengan seluruh stok mobil yang tersedia.
     */
    public function catalog(Request $request)
    {
        $catalogQuery = Car::query()
            ->where('status', 'available');

        // Filter: brand (merk)
        if ($request->filled('brand') && $request->input('brand') !== 'all') {
            $brand = trim((string) $request->input('brand'));
            $catalogQuery->where('merk', 'like', '%' . $brand . '%');
        }

        // Filter: model/keyword (tipe/merk)
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $catalogQuery->where(function ($sub) use ($q) {
                $sub->where('merk', 'like', '%' . $q . '%')
                    ->orWhere('tipe', 'like', '%' . $q . '%');
            });
        }

        // Filter: year minimum (user input bebas, dibatasi minimal 2010)
        $yearMinInput = $request->input('year_min', $request->input('year'));
        if ($yearMinInput !== null && $yearMinInput !== '') {
            $yearMin = (int) $yearMinInput;
            if ($yearMin >= 2010) {
                $catalogQuery->where('tahun', '>=', $yearMin);
            }
        }

        // Filter: kilometer (di UI ditulis "Kilometer" tanpa label max).
        // Perlakuan filter tetap "maksimal", supaya user bisa cari unit dengan KM <= input.
        $kmInput = $request->input('kilometer', $request->input('km_max', $request->input('km')));
        if ($kmInput !== null && $kmInput !== '') {
            $kmMax = (int) preg_replace('/[^\d]/', '', (string) $kmInput);
            if ($kmMax > 0) {
                $catalogQuery->whereNotNull('kilometer')
                    ->where('kilometer', '<=', $kmMax);
            }
        }

        // Filter: price range (input juta -> rupiah)
        $normalizePrice = function ($value) {
            if ($value === null || $value === '') {
                return null;
            }
            $num = (float) $value;
            if ($num <= 0) {
                return null;
            }
            // If user enters "juta" (e.g. 250), convert to rupiah.
            if ($num < 1000000) {
                return (int) round($num * 1000000);
            }
            return (int) round($num);
        };

        $priceMin = $normalizePrice($request->input('price_min'));
        $priceMax = $normalizePrice($request->input('price_max'));
        if ($priceMin !== null) {
            $catalogQuery->where('harga', '>=', $priceMin);
        }
        if ($priceMax !== null) {
            $catalogQuery->where('harga', '<=', $priceMax);
        }

        // Rekomendasi: jika user mengisi "harga" (target), urutkan dari harga terdekat (bukan strict range).
        $priceTarget = $normalizePrice($request->input('price_target', $request->input('price')));
        if ($priceTarget !== null) {
            // ABS pada decimal MySQL aman untuk sorting rekomendasi.
            $catalogQuery->orderByRaw('ABS(harga - ?) asc', [$priceTarget])
                ->latest();
        } else {
            // Default sorting
            $catalogQuery->latest();
        }

        $catalogCars = $catalogQuery->get();

        $brandOptions = Car::query()
            ->where('status', 'available')
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->select('merk')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk');

        return view('pages.catalog', compact('catalogCars', 'brandOptions'));
    }
    /**
     * Menampilkan detail mobil berdasarkan ID dengan fallback 404 jika data tidak ditemukan.
     */
    public function carDetail($id)
    {
        $car = Car::query()->findOrFail($id);

        $relatedCars = Car::query()
            ->where('status', 'available')
            ->where('id', '!=', $car->id)
            ->orderByRaw('merk = ? desc', [$car->merk])
            ->latest()
            ->take(3)
            ->get();

        return view('pages.car-detail', compact('car', 'relatedCars'));
    }
    /**
     * Menampilkan halaman form booking test drive (customer).
     * Optional: terima query `car_id` agar unit langsung terpilih.
     */
    public function testDrive(\Illuminate\Http\Request $request)
    {
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
    public function cart() { return view('pages.cart'); }
    public function checkout() { return view('pages.checkout'); }
    public function payment() { return view('pages.payment'); }
    public function paymentUpload() { return view('pages.payment-upload'); }
    public function orderTracking() { return view('pages.order-tracking'); }
    public function about() { return view('pages.about'); }
    public function financing() { return view('pages.financing'); }
    public function reviews() { return view('pages.reviews'); }
    public function offers() { return view('pages.offers'); }
    public function login() { return view('pages.login'); }
    public function register() { return view('pages.register'); }
    public function privacy() { return view('pages.privacy'); }
    public function terms() { return view('pages.terms'); }
    public function faq() { return view('pages.faq'); }

    public function marketingDashboard() { return view('pages.dashboard-marketing'); }
    public function marketingUpload() { return view('pages.marketing-upload'); }
    public function marketingProducts() { return view('pages.marketing-products'); }
    public function marketingOrders() { return view('pages.marketing-orders'); }
    public function marketingOffers() { return view('pages.marketing-offers'); }

    public function supervisorDashboard() { return view('pages.dashboard-supervisor'); }
    public function supervisorPayments() { return view('pages.supervisor-payments'); }
    public function supervisorTransactions() { return view('pages.supervisor-transactions'); }
    public function supervisorUsers() { return view('pages.supervisor-users'); }
    public function supervisorActivity() { return view('pages.supervisor-activity'); }

    public function ownerDashboard() { return view('pages.dashboard-owner'); }
    public function ownerSales() { return view('pages.owner-sales'); }
    public function ownerRevenue() { return view('pages.owner-revenue'); }
    public function ownerPerformance() { return view('pages.owner-performance'); }
}
