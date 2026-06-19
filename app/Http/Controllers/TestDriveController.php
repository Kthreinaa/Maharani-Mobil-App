<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\TestDrive;
use Illuminate\Http\Request;

class TestDriveController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'customer_channel' => ['nullable', 'in:online,offline'],
        ]);

        $car = Car::query()->findOrFail((int) $validated['car_id']);
        if ($car->status !== 'available') {
            return back()
                ->withErrors(['car_id' => 'Unit ini sudah terpesan atau terjual, sehingga tidak bisa dijadwalkan test drive.'])
                ->withInput();
        }

        $notesParts = [];
        if (!empty($validated['location'])) {
            $notesParts[] = 'Lokasi: ' . $validated['location'];
        }
        if (!empty($validated['notes'])) {
            $notesParts[] = 'Catatan: ' . $validated['notes'];
        }
        $finalNotes = count($notesParts) ? implode("\n", $notesParts) : null;

        TestDrive::create([
            'user_id' => $request->user()->id,
            'car_id' => $validated['car_id'],
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'status' => 'pending',
            'customer_channel' => $validated['customer_channel'] ?? 'online',
            'notes' => $finalNotes,
            'follow_up_status' => 'appointment',
        ]);

        return redirect()
            ->route('customer.home')
            ->with('success', 'Janji test drive telah di booking.');
    }
}
