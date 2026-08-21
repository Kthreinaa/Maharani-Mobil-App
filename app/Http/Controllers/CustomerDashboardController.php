<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\TestDrive;
use App\Support\DashboardNotificationBuilder;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CustomerDashboardController extends Controller
{
    /**
     * Menampilkan home khusus customer dengan sumber data mobil yang sama seperti katalog (stok available).
     */
    public function index()
    {
        $user = request()->user();

        $catalogCars = Car::query()
            ->where('status', 'available')
            ->latest()
            ->get();

        $homeOverviewCars = $catalogCars->take(6);
        $newCatalogCarIds = $catalogCars
            ->take(6)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $dashboardNotifications = DashboardNotificationBuilder::customer($user);
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

        $customerSummary = [
            'favorites' => Favorite::query()->where('user_id', $user->id)->count(),
            'orders' => Order::query()->where('user_id', $user->id)->count(),
            'reviews' => Schema::hasTable('product_reviews')
                ? ProductReview::query()->where('user_id', $user->id)->count()
                : 0,
            'test_drives' => TestDrive::query()->where('user_id', $user->id)->count(),
        ];

        $recentActivities = $this->buildRecentActivities($user->id);

        return view('pages.home', compact(
            'catalogCars',
            'homeOverviewCars',
            'dashboardNotifications',
            'featuredReviews',
            'featuredReviewCount',
            'featuredReviewAverage',
            'newCatalogCarIds',
            'customerSummary',
            'recentActivities'
        ));
    }

    /**
     * Menyusun riwayat aktivitas terbaru customer dari favorit, pesanan, test drive, dan review.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function buildRecentActivities(int $userId): Collection
    {
        $activities = collect();

        Favorite::query()
            ->with('car')
            ->where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get()
            ->each(function (Favorite $favorite) use ($activities): void {
                $activities->push($this->makeActivityItem(
                    'favorite',
                    'Menyimpan unit ke favorit',
                    $this->carLabel($favorite->car?->merk, $favorite->car?->tipe, $favorite->car?->tahun) . ' disimpan agar lebih mudah dipantau kembali.',
                    $favorite->created_at,
                    route('customer.favorites.index')
                ));
            });

        Order::query()
            ->with(['car', 'payment'])
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function (Order $order) use ($activities): void {
                $activities->push($this->makeActivityItem(
                    'receipt_long',
                    'Pesanan diperbarui',
                    'Order ' . $order->order_reference . ' untuk ' . $this->carLabel($order->car?->merk, $order->car?->tipe, $order->car?->tahun) . ' saat ini berstatus ' . $order->customer_purchase_status_label . '.',
                    $order->updated_at ?? $order->created_at,
                    route('order.tracking', ['order' => $order->id])
                ));
            });

        TestDrive::query()
            ->with('car')
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->take(4)
            ->get()
            ->each(function (TestDrive $testDrive) use ($activities): void {
                $schedule = trim(optional($testDrive->booking_date)->format('d M Y') . ' ' . substr((string) $testDrive->booking_time, 0, 5));

                $activities->push($this->makeActivityItem(
                    'event_available',
                    'Jadwal test drive tercatat',
                    'Test drive untuk ' . $this->carLabel($testDrive->car?->merk, $testDrive->car?->tipe, $testDrive->car?->tahun) . ' dijadwalkan ' . $schedule . ' dengan status ' . strtoupper((string) $testDrive->status) . '.',
                    $testDrive->updated_at ?? $testDrive->created_at,
                    route('customer.test-drives.index')
                ));
            });

        if (Schema::hasTable('product_reviews')) {
            ProductReview::query()
                ->with('car')
                ->where('user_id', $userId)
                ->latest()
                ->take(4)
                ->get()
                ->each(function (ProductReview $review) use ($activities): void {
                    $activities->push($this->makeActivityItem(
                        'rate_review',
                        'Review berhasil dikirim',
                        'Anda memberikan rating ' . number_format((float) $review->rating, 0) . '/5 untuk ' . $this->carLabel($review->car?->merk, $review->car?->tipe, $review->car?->tahun) . '.',
                        $review->created_at,
                        route('reviews.page')
                    ));
                });
        }

        return $activities
            ->sortByDesc(fn (array $activity) => $activity['sort_time'])
            ->take(8)
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function makeActivityItem(
        string $icon,
        string $title,
        string $detail,
        ?CarbonInterface $time,
        string $href
    ): array {
        $timestamp = $time ?? now();

        return [
            'icon' => $icon,
            'title' => $title,
            'detail' => $detail,
            'href' => $href,
            'time_label' => $timestamp->diffForHumans(),
            'sort_time' => $timestamp,
        ];
    }

    private function carLabel(?string $brand, ?string $type, ?int $year): string
    {
        $label = trim(collect([$brand, $type, $year])->filter()->implode(' '));

        return $label !== '' ? $label : 'unit pilihan Anda';
    }
}
