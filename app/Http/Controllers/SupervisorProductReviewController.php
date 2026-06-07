<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupervisorProductReviewController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Schema::hasTable('product_reviews'), 404);

        $query = ProductReview::query()
            ->with(['user', 'car'])
            ->latest();

        if ($request->filled('source') && in_array($request->input('source'), ['purchase', 'test_drive'], true)) {
            $query->where('source_type', $request->input('source'));
        }

        $reviews = $query->paginate(10)->withQueryString();
        $reviewCount = ProductReview::query()->count();
        $withPhotoCount = ProductReview::query()
            ->where(function ($builder) {
                $builder->whereNotNull('media_path')->orWhereNotNull('media_paths');
            })
            ->count();
        $averageRating = round((float) ProductReview::query()->avg('rating'), 1);

        return view('supervisor.reviews.index', compact('reviews', 'reviewCount', 'withPhotoCount', 'averageRating'));
    }
}
