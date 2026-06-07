<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Http\Request;

class MarketingCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'customer')
            ->select('users.*')
            ->withCount(['orders', 'offers', 'favorites', 'productReviews'])
            ->selectSub(
                Order::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('user_id', 'users.id'),
                'latest_order_at'
            )
            ->selectSub(
                Offer::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('user_id', 'users.id'),
                'latest_offer_at'
            )
            ->selectSub(
                TestDrive::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('user_id', 'users.id'),
                'latest_test_drive_at'
            )
            ->selectSub(
                Favorite::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('user_id', 'users.id'),
                'latest_favorite_at'
            )
            ->selectSub(
                ProductReview::query()
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('user_id', 'users.id'),
                'latest_review_at'
            )
            ->selectRaw("
                CASE
                    WHEN users.email LIKE 'customer-import-%@import.maharanimobil.local' THEN 1
                    ELSE 0
                END as is_import_customer
            ")
            ->selectRaw("
                CASE
                    WHEN EXISTS(SELECT 1 FROM orders WHERE orders.user_id = users.id)
                      OR EXISTS(SELECT 1 FROM offers WHERE offers.user_id = users.id)
                      OR EXISTS(SELECT 1 FROM test_drives WHERE test_drives.user_id = users.id)
                      OR EXISTS(SELECT 1 FROM favorites WHERE favorites.user_id = users.id)
                      OR EXISTS(SELECT 1 FROM product_reviews WHERE product_reviews.user_id = users.id)
                    THEN 1
                    ELSE 0
                END as has_recorded_activity
            ")
            ->selectSub(function ($activityQuery) {
                $activityQuery
                    ->fromSub(
                        Order::query()
                            ->select('created_at')
                            ->whereColumn('user_id', 'users.id')
                            ->unionAll(
                                Offer::query()
                                    ->select('created_at')
                                    ->whereColumn('user_id', 'users.id')
                            )
                            ->unionAll(
                                TestDrive::query()
                                    ->select('created_at')
                                    ->whereColumn('user_id', 'users.id')
                            )
                            ->unionAll(
                                Favorite::query()
                                    ->select('created_at')
                                    ->whereColumn('user_id', 'users.id')
                            )
                            ->unionAll(
                                ProductReview::query()
                                    ->select('created_at')
                                    ->whereColumn('user_id', 'users.id')
                            ),
                        'activity_timestamps'
                    )
                    ->selectRaw('MAX(created_at)');
            }, 'latest_activity_at');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where(function ($subQuery) use ($q) {
                $subQuery
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%');
            });
        }

        $query
            ->orderByDesc('has_recorded_activity')
            ->orderBy('is_import_customer')
            ->orderByDesc('latest_activity_at')
            ->orderByDesc('created_at');

        $customers = $query->paginate(10)->withQueryString();

        return view('marketing.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'customer', 404);

        $user->load(['orders.car', 'orders.payment', 'offers.car']);

        return view('marketing.customers.show', compact('user'));
    }
}
