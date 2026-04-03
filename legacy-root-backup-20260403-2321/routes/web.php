<?php

use App\Core\Router;
use App\Http\Controllers\HomeController;

$router = new Router();
$home = new HomeController();

// Public
$router->get('/', [$home, 'landing']);
$router->get('/home', [$home, 'home']);
$router->get('/catalog', [$home, 'catalog']);
$router->get('/car-detail', [$home, 'carDetail']);
$router->get('/test-drive', [$home, 'testDrive']);
$router->get('/cart', [$home, 'cart']);
$router->get('/checkout', [$home, 'checkout']);
$router->get('/payment', [$home, 'payment']);
$router->get('/payment-upload', [$home, 'paymentUpload']);
$router->get('/order-tracking', [$home, 'orderTracking']);
$router->get('/about', [$home, 'about']);
$router->get('/financing', [$home, 'financing']);
$router->get('/reviews', [$home, 'reviews']);
$router->get('/offers', [$home, 'offers']);
$router->get('/login', [$home, 'login']);
$router->get('/register', [$home, 'register']);
$router->get('/privacy', [$home, 'privacy']);
$router->get('/terms', [$home, 'terms']);
$router->get('/faq', [$home, 'faq']);

// Marketing
$router->get('/dashboard-marketing', [$home, 'marketingDashboard']);
$router->get('/marketing-upload', [$home, 'marketingUpload']);
$router->get('/marketing-products', [$home, 'marketingProducts']);
$router->get('/marketing-orders', [$home, 'marketingOrders']);
$router->get('/marketing-offers', [$home, 'marketingOffers']);

// Supervisor
$router->get('/dashboard-supervisor', [$home, 'supervisorDashboard']);
$router->get('/supervisor-payments', [$home, 'supervisorPayments']);
$router->get('/supervisor-transactions', [$home, 'supervisorTransactions']);
$router->get('/supervisor-users', [$home, 'supervisorUsers']);
$router->get('/supervisor-activity', [$home, 'supervisorActivity']);

// Owner
$router->get('/dashboard-owner', [$home, 'ownerDashboard']);
$router->get('/owner-sales', [$home, 'ownerSales']);
$router->get('/owner-revenue', [$home, 'ownerRevenue']);
$router->get('/owner-performance', [$home, 'ownerPerformance']);

return $router;
