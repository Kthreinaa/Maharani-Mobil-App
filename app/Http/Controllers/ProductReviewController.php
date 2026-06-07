<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\TestDrive;
use App\Support\ReviewPhotoWatermarker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviewsTableExists = Schema::hasTable('product_reviews');
        $carsTableExists = Schema::hasTable('cars');

        $reviewQuery = $reviewsTableExists
            ? ProductReview::query()->with(['user', 'car'])->where('status', 'approved')
            : ProductReview::query()->whereRaw('1 = 0');

        $selectedCarId = $carsTableExists ? (int) $request->query('car', 0) : 0;
        if ($selectedCarId > 0 && $reviewsTableExists) {
            $reviewQuery->where('car_id', $selectedCarId);
        }

        $sort = $request->query('sort', 'latest');
        if ($reviewsTableExists) {
            if ($sort === 'highest') {
                $reviewQuery->orderByDesc('rating')->latest();
            } else {
                $reviewQuery->latest();
            }
        }

        $reviews = $reviewsTableExists
            ? $reviewQuery->paginate(9)->withQueryString()
            : collect();

        $summaryQuery = $reviewsTableExists ? ProductReview::query()->where('status', 'approved') : null;
        if ($summaryQuery && $selectedCarId > 0) {
            $summaryQuery->where('car_id', $selectedCarId);
        }

        $averageRating = $summaryQuery ? round((float) $summaryQuery->avg('rating'), 1) : 0.0;
        $totalReviews = $summaryQuery ? (int) $summaryQuery->count() : 0;

        $eligibleCars = collect();
        if ($request->user() && $request->user()->role === 'customer' && $carsTableExists) {
            $eligibleCars = $this->eligibleReviewCars($request->user()->id);
        }

        $allCars = $carsTableExists
            ? Car::query()->orderBy('merk')->orderBy('tipe')->get()
            : collect();

        $selectedCar = $selectedCarId > 0 ? $allCars->firstWhere('id', $selectedCarId) : null;

        return view('pages.reviews', compact(
            'reviews',
            'averageRating',
            'totalReviews',
            'eligibleCars',
            'allCars',
            'selectedCarId',
            'selectedCar',
            'sort'
        ));
    }

    public function store(Request $request)
    {
        abort_unless($request->user() && $request->user()->role === 'customer', 403);
        abort_unless(Schema::hasTable('product_reviews') && Schema::hasTable('cars'), 404);

        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'min:15', 'max:3000'],
            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $eligibleSource = $this->resolveEligibleSource($request->user()->id, (int) $validated['car_id']);
        if (!$eligibleSource) {
            return back()->withErrors([
                'car_id' => 'Anda hanya bisa menulis ulasan untuk unit yang sudah Anda beli atau test drive.',
            ])->withInput();
        }

        $alreadySubmitted = ProductReview::query()
            ->where('user_id', $request->user()->id)
            ->where('car_id', $validated['car_id'])
            ->where('source_type', $eligibleSource['source_type'])
            ->where('source_id', $eligibleSource['source_id'])
            ->exists();

        if ($alreadySubmitted) {
            return back()->withErrors([
                'review_text' => 'Ulasan untuk unit ini sudah pernah Anda kirim dan sedang/ sudah diproses.',
            ])->withInput();
        }

        $mediaPaths = collect($request->file('media', []))
            ->take(5)
            ->map(fn ($file) => ReviewPhotoWatermarker::storeUploaded($file))
            ->values()
            ->all();

        ProductReview::create([
            'user_id' => $request->user()->id,
            'car_id' => $validated['car_id'],
            'source_type' => $eligibleSource['source_type'],
            'source_id' => $eligibleSource['source_id'],
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
            'media_path' => $mediaPaths[0] ?? null,
            'media_paths' => $mediaPaths,
            'status' => 'approved',
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('reviews.page', ['car' => $validated['car_id']])
            ->with('success', 'Review Anda berhasil dikirim dan langsung tampil di halaman ulasan customer.');
    }

    public function create(Request $request)
    {
        abort_unless($request->user() && $request->user()->role === 'customer', 403);
        abort_unless(Schema::hasTable('product_reviews') && Schema::hasTable('cars'), 404);

        $eligibleCars = $this->eligibleReviewCars($request->user()->id);
        $submittedReviews = ProductReview::query()
            ->with('car')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $selectedCarId = (int) old('car_id', $request->query('car', 0));
        $selectedCarOption = $selectedCarId > 0
            ? $eligibleCars->first(fn ($item) => (int) $item['car']->id === $selectedCarId)
            : $eligibleCars->first();

        return view('customer.reviews-create', compact(
            'eligibleCars',
            'submittedReviews',
            'selectedCarId',
            'selectedCarOption'
        ));
    }

    private function eligibleReviewCars(int $userId)
    {
        $purchasedCars = Order::query()
            ->with('car')
            ->where('user_id', $userId)
            ->whereIn('status', ['paid', 'completed'])
            ->get()
            ->map(function ($order) {
                return [
                    'car' => $order->car,
                    'source_type' => 'purchase',
                    'source_id' => $order->id,
                    'label' => 'Pembelian terverifikasi',
                ];
            });

        $testDriveCars = TestDrive::query()
            ->with('car')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'completed'])
            ->get()
            ->map(function ($testDrive) {
                return [
                    'car' => $testDrive->car,
                    'source_type' => 'test_drive',
                    'source_id' => $testDrive->id,
                    'label' => 'Test drive terverifikasi',
                ];
            });

        return $purchasedCars
            ->merge($testDriveCars)
            ->filter(fn ($item) => $item['car'] !== null)
            ->unique(fn ($item) => $item['car']->id)
            ->values();
    }

    private function resolveEligibleSource(int $userId, int $carId): ?array
    {
        $purchase = Order::query()
            ->where('user_id', $userId)
            ->where('car_id', $carId)
            ->whereIn('status', ['paid', 'completed'])
            ->latest()
            ->first();

        if ($purchase) {
            return [
                'source_type' => 'purchase',
                'source_id' => $purchase->id,
            ];
        }

        $testDrive = TestDrive::query()
            ->where('user_id', $userId)
            ->where('car_id', $carId)
            ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->first();

        if ($testDrive) {
            return [
                'source_type' => 'test_drive',
                'source_id' => $testDrive->id,
            ];
        }

        return null;
    }
}
