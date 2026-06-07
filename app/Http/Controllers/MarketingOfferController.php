<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\OfferHistory;
use Illuminate\Http\Request;

class MarketingOfferController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->filled('status') ? (string) $request->input('status') : 'all';
        $search = trim((string) $request->input('q', ''));
        $year = $request->filled('year') ? (int) $request->input('year') : null;

        if (!in_array($status, ['all', 'pending', 'countered', 'accepted', 'rejected'], true)) {
            $status = 'all';
        }

        if ($year !== null && ($year < 2000 || $year > 2100)) {
            $year = null;
        }

        $query = Offer::query()->with(['user', 'car', 'handledBy', 'histories'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($year !== null) {
            $query->whereYear('created_at', $year);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('handledBy', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('car', function ($carQuery) use ($search) {
                        $carQuery
                            ->where('merk', 'like', '%' . $search . '%')
                            ->orWhere('tipe', 'like', '%' . $search . '%')
                            ->orWhere('kode_unit', 'like', '%' . $search . '%')
                            ->orWhere('tahun', 'like', '%' . $search . '%');
                    });
            });
        }

        $offers = $query->paginate(15)->withQueryString();

        return view('marketing.offers.index', compact('offers', 'status', 'search', 'year'));
    }

    public function updateStatus(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,countered,accepted,rejected'],
            'counter_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lost_reason' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $counterPrice = $validated['counter_price'] ?? null;

        if ($validated['status'] === 'countered' && empty($counterPrice)) {
            return back()
                ->withErrors(['counter_price' => 'Isi harga tawar balik terlebih dahulu.'])
                ->withInput();
        }

        $followUpStatus = match ($validated['status']) {
            'countered' => 'followed_up',
            'accepted' => 'closed_won',
            'rejected' => !empty($validated['next_follow_up_at']) ? 'needs_follow_up' : 'closed_lost',
            default => 'needs_follow_up',
        };

        $finalPrice = $validated['status'] === 'accepted'
            ? (float) ($counterPrice ?? $offer->counter_price ?? $offer->offer_price)
            : null;

        $offer->update([
            'status' => $validated['status'],
            'counter_price' => $validated['status'] === 'countered'
                ? ($counterPrice ?? $offer->counter_price)
                : ($validated['status'] === 'accepted' ? ($offer->counter_price ?? $counterPrice) : null),
            'final_price' => $finalPrice,
            'follow_up_status' => $followUpStatus,
            'lost_reason' => $validated['status'] === 'rejected' ? ($validated['lost_reason'] ?? null) : null,
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'notes' => $validated['notes'] ?? $offer->notes,
            'last_offer_by' => 'marketing',
            'handled_by' => $request->user()->id,
            'handled_role' => (string) $request->user()->role,
            'handled_at' => now(),
        ]);

        OfferHistory::create([
            'offer_id' => $offer->id,
            'actor_role' => 'marketing',
            'action' => match ($validated['status']) {
                'countered' => 'countered',
                'accepted' => 'accepted',
                'rejected' => 'rejected',
                default => 'message',
            },
            'offered_price' => $validated['status'] === 'countered'
                ? (float) ($counterPrice ?? 0)
                : ($validated['status'] === 'accepted' ? $finalPrice : (float) ($offer->offer_price ?? 0)),
            'note' => $validated['notes'] ?? $validated['lost_reason'] ?? null,
        ]);

        $statusLabel = match ($validated['status']) {
            'pending' => 'Menunggu Tanggapan',
            'countered' => 'Tawar Balik Supervisor',
            'accepted' => 'Harga Disepakati',
            'rejected' => 'Negosiasi Ditutup',
            default => strtoupper((string) $validated['status']),
        };

        return back()->with('success', 'Penawaran telah ditindak lanjuti dengan status ' . $statusLabel . '.');
    }
}
