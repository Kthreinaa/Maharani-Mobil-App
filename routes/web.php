<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerSettingsController;
use App\Http\Controllers\CustomerTestDriveController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InternalSalesImportController;
use App\Http\Controllers\MarketingDashboardController;
use App\Http\Controllers\MarketingCarController;
use App\Http\Controllers\MarketingCustomerController;
use App\Http\Controllers\MarketingOfferController;
use App\Http\Controllers\MarketingOrderController;
use App\Http\Controllers\MarketingSettingsController;
use App\Http\Controllers\MarketingTestDriveController;
use App\Http\Controllers\MarketingTransactionController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\OwnerSettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SupervisorActivityController;
use App\Http\Controllers\SupervisorCarController;
use App\Http\Controllers\SupervisorCustomerController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\SupervisorOfferController;
use App\Http\Controllers\SupervisorOrderController;
use App\Http\Controllers\SupervisorPaymentController;
use App\Http\Controllers\SupervisorProductReviewController;
use App\Http\Controllers\SupervisorSettingsController;
use App\Http\Controllers\SupervisorTestDriveController;
use App\Http\Controllers\SupervisorUserController;
use App\Http\Controllers\TestDriveController;
use App\Http\Controllers\TransactionDocumentController;
use App\Http\Controllers\XenditWebhookController;
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
Route::get('/reviews', [ProductReviewController::class, 'index'])->name('reviews.page');
Route::get('/testimonials', fn () => redirect()->route('reviews.page'))->name('testimonials');
Route::get('/financing', [HomeController::class, 'financing'])->name('financing');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
Route::post('/webhooks/xendit/invoices', [XenditWebhookController::class, 'invoice'])->name('webhooks.xendit.invoices');

/*
|--------------------------------------------------------------------------
| PROTEKSI AKSI GUEST
|--------------------------------------------------------------------------
| Jika user belum login lalu mencoba aksi beli/favorit/booking,
| user akan diarahkan ke login dengan pesan:
| "Silakan login terlebih dahulu".
*/
Route::middleware(['action.auth', 'auth', 'role:customer'])->group(function () {
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/cash', [HomeController::class, 'checkoutCash'])->name('checkout.cash');
    Route::get('/checkout/credit', [HomeController::class, 'checkoutCredit'])->name('checkout.credit');
    Route::get('/test-drive', [HomeController::class, 'testDrive'])->name('test-drive.form');
    Route::get('/offers', [HomeController::class, 'offers'])->name('offers.page');
    Route::get('/payment', [HomeController::class, 'payment'])->name('payment.page');
    Route::get('/payment-upload', [HomeController::class, 'paymentUpload'])->name('payment.upload.page');
    Route::get('/order-tracking', [HomeController::class, 'orderTracking'])->name('order.tracking');
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
    Route::get('/documents/orders/{order}/{type}', [TransactionDocumentController::class, 'download'])->name('documents.orders.download');
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
    Route::get('/test-drives', [CustomerTestDriveController::class, 'index'])->name('test-drives.index');
    Route::get('/reviews/create', [ProductReviewController::class, 'create'])->name('reviews.create');
    Route::get('/settings', [CustomerSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [CustomerSettingsController::class, 'update'])->name('settings.update');

    Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
});

// Aksi sensitif customer dengan proteksi pesan login khusus.
Route::middleware(['action.auth', 'auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::post('/favorites/{car}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/simulate-success', [PaymentController::class, 'simulateSuccess'])->name('payments.simulate-success');
    Route::post('/payments/upload', [PaymentController::class, 'upload'])->name('payments.upload');
    Route::patch('/offers/{offer}/respond', [OfferController::class, 'respond'])->name('offers.respond');
    Route::post('/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');
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
    Route::patch('/cars/{car}/photos/{photoIndex}', [SupervisorCarController::class, 'replacePhoto'])->name('cars.photos.replace');
    Route::delete('/cars/{car}/photos/{photoIndex}', [SupervisorCarController::class, 'destroyPhoto'])->name('cars.photos.destroy');
    Route::delete('/cars/{car}', [SupervisorCarController::class, 'destroy'])->name('cars.destroy');

    Route::get('/orders', [SupervisorOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [SupervisorOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [SupervisorOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [SupervisorOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [SupervisorOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/payment', [SupervisorOrderController::class, 'syncPayment'])->name('orders.syncPayment');
    Route::get('/imports/sales', [InternalSalesImportController::class, 'create'])->name('imports.sales.create');
    Route::post('/imports/sales', [InternalSalesImportController::class, 'store'])->name('imports.sales.store');
    Route::delete('/imports/sales', [InternalSalesImportController::class, 'destroy'])->name('imports.sales.destroy');

    Route::get('/offers', [SupervisorOfferController::class, 'index'])->name('offers.index');
    Route::patch('/offers/{offer}/status', [SupervisorOfferController::class, 'updateStatus'])->name('offers.updateStatus');
    Route::get('/test-drives', [SupervisorTestDriveController::class, 'index'])->name('testdrives.index');
    Route::get('/test-drives/create', [SupervisorTestDriveController::class, 'create'])->name('testdrives.create');
    Route::post('/test-drives', [SupervisorTestDriveController::class, 'store'])->name('testdrives.store');
    Route::get('/test-drives/{testDrive}', [SupervisorTestDriveController::class, 'show'])->name('testdrives.show');
    Route::patch('/test-drives/{testDrive}/status', [SupervisorTestDriveController::class, 'updateStatus'])->name('testdrives.updateStatus');
    Route::get('/payments', [SupervisorPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [SupervisorOrderController::class, 'create'])->name('payments.create');
    Route::post('/payments/create', [SupervisorOrderController::class, 'store'])->name('payments.store');
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

    Route::get('/reviews', [SupervisorProductReviewController::class, 'index'])->name('reviews.index');
    Route::get('/activity', [SupervisorActivityController::class, 'index'])->name('activity.index');
    Route::get('/settings', [SupervisorSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SupervisorSettingsController::class, 'update'])->name('settings.update');

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
    Route::get('/imports/sales', [InternalSalesImportController::class, 'create'])->name('imports.sales.create');
    Route::post('/imports/sales', [InternalSalesImportController::class, 'store'])->name('imports.sales.store');
    Route::delete('/imports/sales', [InternalSalesImportController::class, 'destroy'])->name('imports.sales.destroy');
    Route::get('/orders', [MarketingOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [MarketingOrderController::class, 'show'])->name('orders.show');
    Route::get('/offers', [MarketingOfferController::class, 'index'])->name('offers.index');
    Route::get('/products', [MarketingCarController::class, 'index'])->name('products.index');
    Route::get('/products/create', [MarketingCarController::class, 'create'])->name('products.create');
    Route::get('/upload', [MarketingCarController::class, 'create'])->name('products.upload');
    Route::post('/products', [MarketingCarController::class, 'store'])->name('products.store');
    Route::get('/products/{car}/edit', [MarketingCarController::class, 'edit'])->name('products.edit');
    Route::put('/products/{car}', [MarketingCarController::class, 'update'])->name('products.update');
    Route::patch('/products/{car}/photos/{photoIndex}', [MarketingCarController::class, 'replacePhoto'])->name('products.photos.replace');
    Route::delete('/products/{car}/photos/{photoIndex}', [MarketingCarController::class, 'destroyPhoto'])->name('products.photos.destroy');
    Route::delete('/products/{car}', [MarketingCarController::class, 'destroy'])->name('products.destroy');
    Route::get('/test-drives', [MarketingTestDriveController::class, 'index'])->name('testdrives.index');
    Route::get('/transactions', [MarketingTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/customers', [MarketingCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [MarketingCustomerController::class, 'show'])->name('customers.show');
    Route::get('/settings', [MarketingSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [MarketingSettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| OWNER ROUTES (auth + role:owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [OwnerSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [OwnerSettingsController::class, 'update'])->name('settings.update');
    Route::get('/reports', [OwnerReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [OwnerReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/excel', [OwnerReportController::class, 'exportExcel'])->name('reports.exportExcel');
});
