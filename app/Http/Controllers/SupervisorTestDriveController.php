<?php

namespace App\Http\Controllers;

use App\Models\TestDrive;
use Illuminate\Http\Request;

class SupervisorTestDriveController extends Controller
{
    public function index(Request $request)
    {
        $query = TestDrive::with(['user', 'car'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $testDrives = $query->paginate(10);

        return view('supervisor.testdrives.index', compact('testDrives'));
    }

    public function show(TestDrive $testDrive)
    {
        $testDrive->load(['user', 'car']);
        return view('supervisor.testdrives.show', compact('testDrive'));
    }

    public function approve(TestDrive $testDrive)
    {
        $testDrive->update(['status' => 'approved']);
        return back()->with('success', 'Booking test drive disetujui.');
    }

    public function reject(TestDrive $testDrive)
    {
        $testDrive->update(['status' => 'rejected']);
        return back()->with('success', 'Booking test drive ditolak.');
    }
}
