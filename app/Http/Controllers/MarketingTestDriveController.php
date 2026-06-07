<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingTestDriveController extends Controller
{
    public function index(Request $request)
    {
        return redirect()
            ->route('marketing.dashboard')
            ->withErrors([
                'marketing_testdrives_access' => 'Jadwal test drive dikelola oleh supervisor. Marketing cukup fokus pada customer dan follow-up awal.',
            ]);
    }

    public function updateStatus(Request $request, TestDrive $testDrive)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,completed,cancelled'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $followUpStatus = match ($validated['status']) {
            'completed' => 'followed_up',
            'approved' => 'appointment',
            'rejected', 'cancelled' => !empty($validated['next_follow_up_at']) ? 'needs_follow_up' : 'closed_lost',
            default => 'appointment',
        };

        $testDrive->update([
            'status' => $validated['status'],
            'follow_up_status' => $followUpStatus,
            'lost_reason' => in_array($validated['status'], ['rejected', 'cancelled'], true) ? ($validated['lost_reason'] ?? null) : null,
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'handled_by' => $request->user()->id,
            'handled_role' => (string) $request->user()->role,
            'handled_at' => now(),
        ]);

        return back()->with('success', 'Status test drive berhasil diperbarui.');
    }
}
