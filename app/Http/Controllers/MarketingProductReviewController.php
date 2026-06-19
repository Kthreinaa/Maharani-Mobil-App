<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MarketingProductReviewController extends Controller
{
    public function index(Request $request, Car $car)
    {
        abort_unless(Schema::hasTable('product_reviews'), 404);

        $query = ProductReview::query()
            ->with(['user', 'car'])
            ->where('car_id', $car->id)
            ->latest();

        if ($request->filled('source') && in_array($request->input('source'), ['purchase', 'test_drive'], true)) {
            $query->where('source_type', $request->input('source'));
        }

        $reviews = $query->paginate(10)->withQueryString();

        $summaryQuery = ProductReview::query()->where('car_id', $car->id);
        $reviewCount = (clone $summaryQuery)->count();
        $withPhotoCount = (clone $summaryQuery)
            ->where(function ($builder) {
                $builder->whereNotNull('media_path')->orWhereNotNull('media_paths');
            })
            ->count();
        $averageRating = round((float) ((clone $summaryQuery)->avg('rating') ?? 0), 1);

        return view('marketing.reviews.index', compact(
            'car',
            'reviews',
            'reviewCount',
            'withPhotoCount',
            'averageRating'
        ));
    }
}
