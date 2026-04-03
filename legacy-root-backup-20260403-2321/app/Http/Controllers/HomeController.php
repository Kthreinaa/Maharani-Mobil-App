<?php

namespace App\Http\Controllers;

use App\Core\View;

class HomeController
{
    public function landing(): string { return View::render('pages/index'); }
    public function home(): string { return View::render('pages/home'); }
    public function catalog(): string { return View::render('pages/catalog'); }
    public function carDetail(): string { return View::render('pages/car-detail'); }
    public function testDrive(): string { return View::render('pages/test-drive'); }
    public function cart(): string { return View::render('pages/cart'); }
    public function checkout(): string { return View::render('pages/checkout'); }
    public function payment(): string { return View::render('pages/payment'); }
    public function paymentUpload(): string { return View::render('pages/payment-upload'); }
    public function orderTracking(): string { return View::render('pages/order-tracking'); }
    public function about(): string { return View::render('pages/about'); }
    public function financing(): string { return View::render('pages/financing'); }
    public function reviews(): string { return View::render('pages/reviews'); }
    public function offers(): string { return View::render('pages/offers'); }
    public function login(): string { return View::render('pages/login'); }
    public function register(): string { return View::render('pages/register'); }
    public function privacy(): string { return View::render('pages/privacy'); }
    public function terms(): string { return View::render('pages/terms'); }
    public function faq(): string { return View::render('pages/faq'); }

    public function marketingDashboard(): string { return View::render('pages/dashboard-marketing'); }
    public function marketingUpload(): string { return View::render('pages/marketing-upload'); }
    public function marketingProducts(): string { return View::render('pages/marketing-products'); }
    public function marketingOrders(): string { return View::render('pages/marketing-orders'); }
    public function marketingOffers(): string { return View::render('pages/marketing-offers'); }

    public function supervisorDashboard(): string { return View::render('pages/dashboard-supervisor'); }
    public function supervisorPayments(): string { return View::render('pages/supervisor-payments'); }
    public function supervisorTransactions(): string { return View::render('pages/supervisor-transactions'); }
    public function supervisorUsers(): string { return View::render('pages/supervisor-users'); }
    public function supervisorActivity(): string { return View::render('pages/supervisor-activity'); }

    public function ownerDashboard(): string { return View::render('pages/dashboard-owner'); }
    public function ownerSales(): string { return View::render('pages/owner-sales'); }
    public function ownerRevenue(): string { return View::render('pages/owner-revenue'); }
    public function ownerPerformance(): string { return View::render('pages/owner-performance'); }
}
