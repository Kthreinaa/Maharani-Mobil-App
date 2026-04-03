<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function landing() { return view('pages.index'); }
    public function home() { return view('pages.home'); }
    public function catalog() { return view('pages.catalog'); }
    public function carDetail() { return view('pages.car-detail'); }
    public function testDrive() { return view('pages.test-drive'); }
    public function cart() { return view('pages.cart'); }
    public function checkout() { return view('pages.checkout'); }
    public function payment() { return view('pages.payment'); }
    public function paymentUpload() { return view('pages.payment-upload'); }
    public function orderTracking() { return view('pages.order-tracking'); }
    public function about() { return view('pages.about'); }
    public function financing() { return view('pages.financing'); }
    public function reviews() { return view('pages.reviews'); }
    public function offers() { return view('pages.offers'); }
    public function login() { return view('pages.login'); }
    public function register() { return view('pages.register'); }
    public function privacy() { return view('pages.privacy'); }
    public function terms() { return view('pages.terms'); }
    public function faq() { return view('pages.faq'); }

    public function marketingDashboard() { return view('pages.dashboard-marketing'); }
    public function marketingUpload() { return view('pages.marketing-upload'); }
    public function marketingProducts() { return view('pages.marketing-products'); }
    public function marketingOrders() { return view('pages.marketing-orders'); }
    public function marketingOffers() { return view('pages.marketing-offers'); }

    public function supervisorDashboard() { return view('pages.dashboard-supervisor'); }
    public function supervisorPayments() { return view('pages.supervisor-payments'); }
    public function supervisorTransactions() { return view('pages.supervisor-transactions'); }
    public function supervisorUsers() { return view('pages.supervisor-users'); }
    public function supervisorActivity() { return view('pages.supervisor-activity'); }

    public function ownerDashboard() { return view('pages.dashboard-owner'); }
    public function ownerSales() { return view('pages.owner-sales'); }
    public function ownerRevenue() { return view('pages.owner-revenue'); }
    public function ownerPerformance() { return view('pages.owner-performance'); }
}
