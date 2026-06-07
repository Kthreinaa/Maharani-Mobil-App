<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerTestDriveController extends Controller
{
    public function index(Request $request)
    {
        $testDrives = $request->user()
            ->testDrives()
            ->with('car')
            ->latest('booking_date')
            ->latest('booking_time')
            ->get();

        return view('customer.test-drives', compact('testDrives'));
    }
}
