<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('car', 'payment')->latest()->get();
        return view('customer.orders', compact('orders'));
    }
}
