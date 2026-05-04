<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class SupervisorOfferController extends Controller
{
    public function index()
    {
        $offers = Offer::with(['user', 'car'])->latest()->paginate(10);
        return view('supervisor.offers.index', compact('offers'));
    }
}
