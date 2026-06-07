<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\ProductReview;
use App\Support\DashboardNotificationBuilder;
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

        return view('pages.home', compact(
            'catalogCars',
            'homeOverviewCars',
            'dashboardNotifications',
            'featuredReviews',
            'featuredReviewCount',
            'featuredReviewAverage'
        ));
    }
}
