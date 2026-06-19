<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Models\OfferHistory;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'offer_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'customer_channel' => ['nullable', 'in:online,offline'],
        ]);

        $car = Car::query()->findOrFail((int) $validated['car_id']);
        if ($car->status !== 'available') {
            return back()
                ->withErrors(['car_id' => 'Unit ini sudah terpesan atau terjual, sehingga tidak bisa diajukan penawaran lagi.'])
                ->withInput();
        }

        $offer = Offer::create([
            'user_id' => $request->user()->id,
            'car_id' => $validated['car_id'],
            'offer_price' => $validated['offer_price'],
            'status' => 'pending',
            'customer_channel' => $validated['customer_channel'] ?? 'online',
            'notes' => $validated['notes'] ?? null,
            'follow_up_status' => 'new_lead',
            'negotiation_round' => 1,
            'last_offer_by' => 'customer',
        ]);

        OfferHistory::create([
            'offer_id' => $offer->id,
            'actor_role' => 'customer',
            'action' => 'submitted',
            'offered_price' => $offer->offer_price,
            'note' => $offer->notes,
        ]);

        return back()->with('success', 'Penawaran berhasil dikirim dan sedang menunggu tanggapan supervisor.');
    }

    public function respond(Request $request, Offer $offer)
    {
        abort_unless($offer->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'response_action' => ['required', 'in:accept_counter,new_offer,reject_counter'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['response_action'] === 'accept_counter') {
            if ($offer->status !== 'countered' || !$offer->counter_price) {
                return back()->with('error', 'Belum ada tawar balik supervisor yang bisa disetujui.');
            }

            $offer->update([
                'status' => 'accepted',
                'final_price' => $offer->counter_price,
                'follow_up_status' => 'closed_won',
                'last_offer_by' => 'customer',
            ]);

            OfferHistory::create([
                'offer_id' => $offer->id,
                'actor_role' => 'customer',
                'action' => 'accepted',
                'offered_price' => $offer->counter_price,
                'note' => $validated['notes'] ?? 'Customer menyetujui tawar balik supervisor.',
            ]);

            return back()->with('success', 'Harga deal disetujui. Anda sekarang bisa lanjut checkout dengan harga hasil negosiasi.');
        }

        if ($validated['response_action'] === 'reject_counter') {
            $offer->update([
                'status' => 'rejected',
                'follow_up_status' => filled($offer->next_follow_up_at) ? 'needs_follow_up' : 'closed_lost',
                'lost_reason' => $validated['notes'] ?? 'Customer menghentikan negosiasi.',
                'last_offer_by' => 'customer',
            ]);

            OfferHistory::create([
                'offer_id' => $offer->id,
                'actor_role' => 'customer',
                'action' => 'rejected',
                'offered_price' => $offer->counter_price ?? $offer->offer_price,
                'note' => $validated['notes'] ?? 'Customer menolak tawar balik supervisor.',
            ]);

            return back()->with('success', 'Negosiasi ditutup. Data penawaran tetap tersimpan untuk kebutuhan follow-up.');
        }

        if ($offer->status !== 'countered') {
            return back()->with('error', 'Tawaran baru hanya bisa diajukan setelah supervisor memberi tawar balik.');
        }

        $newOfferPrice = (float) ($validated['offer_price'] ?? 0);
        if ($newOfferPrice <= 0) {
            return back()->withErrors(['offer_price' => 'Masukkan nominal penawaran baru terlebih dahulu.'])->withInput();
        }

        $offer->update([
            'offer_price' => $newOfferPrice,
            'counter_price' => null,
            'final_price' => null,
            'status' => 'pending',
            'follow_up_status' => 'needs_follow_up',
            'notes' => $validated['notes'] ?? $offer->notes,
            'negotiation_round' => (int) $offer->negotiation_round + 1,
            'last_offer_by' => 'customer',
            'lost_reason' => null,
        ]);

        OfferHistory::create([
            'offer_id' => $offer->id,
            'actor_role' => 'customer',
            'action' => 'submitted',
            'offered_price' => $newOfferPrice,
            'note' => $validated['notes'] ?? 'Customer mengirim tawaran baru.',
        ]);

        return back()->with('success', 'Tawaran baru berhasil dikirim. Sistem mencatat riwayat negosiasi secara otomatis.');
    }
}
