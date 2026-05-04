<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketingDashboardController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SupervisorActivityController;
use App\Http\Controllers\SupervisorCarController;
use App\Http\Controllers\SupervisorCustomerController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\SupervisorOfferController;
use App\Http\Controllers\SupervisorOrderController;
use App\Http\Controllers\SupervisorPaymentController;
use App\Http\Controllers\SupervisorTestDriveController;
use App\Http\Controllers\SupervisorUserController;
use App\Http\Controllers\TestDriveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Route yang dapat diakses semua pengunjung tanpa login.
*/
Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/home', [HomeController::class, 'home'])->name('home.public');
Route::get('/catalog', [HomeController::class, 'catalog'])->name('catalog');
Route::get('/cars/{id}', [HomeController::class, 'carDetail'])->name('cars.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/testimonials', [HomeController::class, 'reviews'])->name('testimonials');
Route::get('/financing', [HomeController::class, 'financing'])->name('financing');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/offers', [HomeController::class, 'offers'])->name('offers.page');
Route::get('/payment', [HomeController::class, 'payment'])->name('payment.page');
Route::get('/payment-upload', [HomeController::class, 'paymentUpload'])->name('payment.upload.page');
Route::get('/order-tracking', [HomeController::class, 'orderTracking'])->name('order.tracking');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');

/*
|--------------------------------------------------------------------------
| LOCALIZATION ROUTE
|--------------------------------------------------------------------------
| Menyimpan pilihan bahasa user ke session lalu kembali ke halaman sebelumnya.
*/
Route::get('/locale/{locale}', function (string $locale, Request $request) {
    if (!in_array($locale, ['id', 'en'], true)) {
        abort(404);
    }

    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| PROTEKSI AKSI GUEST
|--------------------------------------------------------------------------
| Jika user belum login lalu mencoba aksi beli/favorit/booking,
| user akan diarahkan ke login dengan pesan:
| "Silakan login terlebih dahulu".
*/
Route::middleware('action.auth')->group(function () {
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/test-drive', [HomeController::class, 'testDrive'])->name('test-drive.form');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
| Route untuk proses login, register, logout, dan social login Google.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Backward compatibility untuk URL lama.
    Route::get('/auth/google/redirect', fn () => redirect()->route('auth.google'));
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES (auth + role:customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [CustomerDashboardController::class, 'index'])->name('home');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('/favorites/{car}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');

    Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
});

// Aksi sensitif customer dengan proteksi pesan login khusus.
Route::middleware(['action.auth', 'auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::post('/favorites/{car}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/test-drive', [TestDriveController::class, 'store'])->name('test-drive.store');
});

/*
|--------------------------------------------------------------------------
| SUPERVISOR ROUTES (auth + role:supervisor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/cars', [SupervisorCarController::class, 'index'])->name('cars.index');
    Route::get('/cars/create', [SupervisorCarController::class, 'create'])->name('cars.create');
    Route::post('/cars', [SupervisorCarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{car}/edit', [SupervisorCarController::class, 'edit'])->name('cars.edit');
    Route::put('/cars/{car}', [SupervisorCarController::class, 'update'])->name('cars.update');
    Route::delete('/cars/{car}', [SupervisorCarController::class, 'destroy'])->name('cars.destroy');

    Route::get('/orders', [SupervisorOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [SupervisorOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [SupervisorOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/payments', [SupervisorPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [SupervisorPaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/verify', [SupervisorPaymentController::class, 'verify'])->name('payments.verify');
    Route::patch('/payments/{payment}/reject', [SupervisorPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('/customers', [SupervisorCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [SupervisorCustomerController::class, 'show'])->name('customers.show');

    Route::get('/users', [SupervisorUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [SupervisorUserController::class, 'create'])->name('users.create');
    Route::post('/users', [SupervisorUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [SupervisorUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [SupervisorUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [SupervisorUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/test-drives', [SupervisorTestDriveController::class, 'index'])->name('testdrives.index');
    Route::get('/test-drives/{testDrive}', [SupervisorTestDriveController::class, 'show'])->name('testdrives.show');
    Route::patch('/test-drives/{testDrive}/approve', [SupervisorTestDriveController::class, 'approve'])->name('testdrives.approve');
    Route::patch('/test-drives/{testDrive}/reject', [SupervisorTestDriveController::class, 'reject'])->name('testdrives.reject');

    Route::get('/offers', [SupervisorOfferController::class, 'index'])->name('offers.index');
    Route::get('/activity', [SupervisorActivityController::class, 'index'])->name('activity.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
});

/*
|--------------------------------------------------------------------------
| MARKETING ROUTES (auth + role:marketing)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:marketing'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', [MarketingDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [HomeController::class, 'marketingOrders'])->name('orders.index');
    Route::get('/offers', [HomeController::class, 'marketingOffers'])->name('offers.index');
    Route::get('/products', [HomeController::class, 'marketingProducts'])->name('products.index');
    Route::get('/upload', [HomeController::class, 'marketingUpload'])->name('products.upload');
});

/*
|--------------------------------------------------------------------------
| OWNER ROUTES (auth + role:owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [OwnerReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [OwnerReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/excel', [OwnerReportController::class, 'exportExcel'])->name('reports.exportExcel');
});
