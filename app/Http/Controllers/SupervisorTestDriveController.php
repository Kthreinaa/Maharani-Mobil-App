<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\TestDrive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupervisorTestDriveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->filled('status') ? (string) $request->input('status') : 'all';
        if (!in_array($status, ['all', 'pending', 'approved', 'rejected', 'completed', 'cancelled'], true)) {
            $status = 'all';
        }

        $query = TestDrive::query()->with(['user', 'car', 'handledBy', 'order'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $testDrives = $query->paginate(15)->withQueryString();

        return view('supervisor.testdrives.index', compact('testDrives', 'status'));
    }

    public function create()
    {
        $cars = Car::query()
            ->where('status', 'available')
            ->orderBy('merk')
            ->orderBy('tipe')
            ->get();

        return view('internal.testdrives.create', [
            'layout' => 'layouts.supervisor',
            'title' => 'Input Test Drive',
            'pageTitle' => 'Input Test Drive',
            'cars' => $cars,
            'submitRoute' => route('supervisor.testdrives.store'),
            'backRoute' => route('supervisor.testdrives.index'),
            'workspaceLabel' => 'Supervisor',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_city' => ['nullable', 'string', 'max:120'],
            'car_id' => ['required', 'exists:cars,id'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'customer_channel' => ['required', 'in:online,offline'],
            'lead_source' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'in:pending,approved,rejected,completed,cancelled'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $car = Car::query()->findOrFail((int) $validated['car_id']);
        if ($car->status !== 'available') {
            return back()
                ->withErrors(['car_id' => 'Unit ini sudah terpesan atau terjual, sehingga tidak bisa dijadwalkan test drive baru.'])
                ->withInput();
        }

        $customer = $this->resolveCustomer($validated);
        $followUpStatus = match ($validated['status']) {
            'completed' => 'followed_up',
            'approved' => 'appointment',
            'rejected', 'cancelled' => !empty($validated['next_follow_up_at']) ? 'needs_follow_up' : 'closed_lost',
            default => 'appointment',
        };

        $notes = collect([
            filled($validated['lead_source'] ?? null) ? 'Sumber lead: ' . $validated['lead_source'] : null,
            filled($validated['customer_phone'] ?? null) ? 'WhatsApp customer: ' . $validated['customer_phone'] : null,
            filled($validated['customer_city'] ?? null) ? 'Domisili customer: ' . $validated['customer_city'] : null,
            filled($validated['location'] ?? null) ? 'Lokasi test drive: ' . $validated['location'] : null,
            filled($validated['notes'] ?? null) ? 'Catatan: ' . $validated['notes'] : null,
        ])->filter()->implode("\n");

        TestDrive::create([
            'user_id' => $customer->id,
            'car_id' => (int) $validated['car_id'],
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'status' => $validated['status'],
            'customer_channel' => $validated['customer_channel'],
            'notes' => $notes !== '' ? $notes : null,
            'follow_up_status' => $followUpStatus,
            'lost_reason' => in_array($validated['status'], ['rejected', 'cancelled'], true) ? ($validated['lost_reason'] ?? null) : null,
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'handled_by' => $request->user()->id,
            'handled_role' => (string) $request->user()->role,
            'handled_at' => now(),
        ]);

        return redirect()
            ->route('supervisor.testdrives.index')
            ->with('success', 'Jadwal test drive berhasil dicatat oleh supervisor.');
    }

    public function show(TestDrive $testDrive)
    {
        $testDrive->load(['user', 'car', 'handledBy', 'order']);
        return view('supervisor.testdrives.show', compact('testDrive'));
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

        return back()->with('success', 'Status test drive berhasil diperbarui oleh supervisor.');
    }

    private function resolveCustomer(array $data): User
    {
        $email = filled($data['customer_email'] ?? null)
            ? Str::lower(trim((string) $data['customer_email']))
            : 'testdrive-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(5)) . '@maharani.local';

        $existingUser = User::query()->where('email', $email)->first();
        if ($existingUser && $existingUser->role !== 'customer') {
            $email = 'testdrive-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(5)) . '@maharani.local';
        }

        $customer = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => trim((string) $data['customer_name']),
                'phone' => $data['customer_phone'] ?? null,
                'role' => 'customer',
            ]
        );

        $customer->forceFill([
            'name' => trim((string) $data['customer_name']),
            'phone' => $data['customer_phone'] ?? $customer->phone,
            'role' => 'customer',
        ])->save();

        return $customer;
    }
}
