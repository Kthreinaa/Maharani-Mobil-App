<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'landing']);
Route::get('/home', [HomeController::class, 'home']);
Route::get('/catalog', [HomeController::class, 'catalog']);
Route::get('/car-detail', [HomeController::class, 'carDetail']);
Route::get('/test-drive', [HomeController::class, 'testDrive']);
Route::get('/cart', [HomeController::class, 'cart']);
Route::get('/checkout', [HomeController::class, 'checkout']);
Route::get('/payment', [HomeController::class, 'payment']);
Route::get('/payment-upload', [HomeController::class, 'paymentUpload']);
Route::get('/order-tracking', [HomeController::class, 'orderTracking']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/financing', [HomeController::class, 'financing']);
Route::get('/reviews', [HomeController::class, 'reviews']);
Route::get('/offers', [HomeController::class, 'offers']);
Route::get('/login', [HomeController::class, 'login']);
Route::get('/register', [HomeController::class, 'register']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/faq', [HomeController::class, 'faq']);

Route::get('/dashboard-marketing', [HomeController::class, 'marketingDashboard']);
Route::get('/marketing-upload', [HomeController::class, 'marketingUpload']);
Route::get('/marketing-products', [HomeController::class, 'marketingProducts']);
Route::get('/marketing-orders', [HomeController::class, 'marketingOrders']);
Route::get('/marketing-offers', [HomeController::class, 'marketingOffers']);

Route::get('/dashboard-supervisor', [HomeController::class, 'supervisorDashboard']);
Route::get('/supervisor-payments', [HomeController::class, 'supervisorPayments']);
Route::get('/supervisor-transactions', [HomeController::class, 'supervisorTransactions']);
Route::get('/supervisor-users', [HomeController::class, 'supervisorUsers']);
Route::get('/supervisor-activity', [HomeController::class, 'supervisorActivity']);

Route::get('/dashboard-owner', [HomeController::class, 'ownerDashboard']);
Route::get('/owner-sales', [HomeController::class, 'ownerSales']);
Route::get('/owner-revenue', [HomeController::class, 'ownerRevenue']);
Route::get('/owner-performance', [HomeController::class, 'ownerPerformance']);
