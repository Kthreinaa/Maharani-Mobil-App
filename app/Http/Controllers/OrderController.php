<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Services\CheckoutDraftService;
use App\Support\CreditSimulationCatalog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const BOOKING_FEE = 2500000;

    public function store(Request $request, CheckoutDraftService $checkoutDrafts)
    {
        $validated = $request->validate([
            'car_id' => ['required', 'exists:cars,id'],
            'offer_id' => ['nullable', 'exists:offers,id'],
            'payment_method' => ['nullable', 'in:cash,transfer,va,credit'],
            'payment_plan' => ['nullable', 'in:booking,full'],
            'sales_flow' => ['nullable', 'in:direct_purchase,after_test_drive'],
            'notes' => ['nullable', 'string'],
            'booking_fee_agreement' => ['nullable'],
            'leasing_partner' => ['nullable', 'string', 'max:120'],
            'credit_dp_percentage' => ['nullable', 'numeric'],
            'credit_dp_amount' => ['nullable', 'numeric'],
            'credit_tenor_months' => ['nullable', 'integer'],
            'credit_monthly_installment' => ['nullable', 'numeric'],
            'credit_interest_rate' => ['nullable', 'numeric'],
        ]);

        $sourceOffer = null;
        if (!empty($validated['offer_id'])) {
            $sourceOffer = Offer::query()
                ->where('id', $validated['offer_id'])
                ->where('user_id', $request->user()->id)
                ->where('status', 'accepted')
                ->firstOrFail();
        }

        $car = Car::query()->findOrFail($sourceOffer?->car_id ?? $validated['car_id']);
        if ($car->status !== 'available') {
            return back()->withErrors([
                'car_id' => $this->unavailableCarMessage($car),
            ])->withInput();
        }

        $purchaseMethod = (string) ($validated['payment_method'] ?? 'cash');
        $paymentPlan = (string) ($validated['payment_plan'] ?? 'booking');
        $totalAmount = (float) ($sourceOffer?->negotiated_price ?? $car->harga);
        $salesFlow = $sourceOffer ? 'direct_purchase' : ($validated['sales_flow'] ?? 'direct_purchase');
        $creditSimulation = null;

        if ($purchaseMethod === 'credit') {
            $selectedPartner = CreditSimulationCatalog::find((string) ($validated['leasing_partner'] ?? ''));
            if (!$selectedPartner) {
                return back()
                    ->withErrors(['leasing_partner' => 'Pilih salah satu leasing rekanan Maharani Mobil terlebih dahulu.'])
                    ->withInput();
            }

            $selectedTenor = (int) ($validated['credit_tenor_months'] ?? 0);
            if (!in_array($selectedTenor, CreditSimulationCatalog::tenors(), true)) {
                return back()
                    ->withErrors(['credit_tenor_months' => 'Pilih tenor kredit 36, 48, atau 60 bulan.'])
                    ->withInput();
            }

            $selectedDpAmount = (float) ($validated['credit_dp_amount'] ?? 0);
            $minimumDpAmount = round($totalAmount * (CreditSimulationCatalog::minimumDpPercentage() / 100), 2);

            if ($selectedDpAmount < $minimumDpAmount) {
                return back()
                    ->withErrors(['credit_dp_amount' => 'DP minimum untuk pengajuan kredit adalah 20% dari harga mobil.'])
                    ->withInput();
            }

            if ($selectedDpAmount >= $totalAmount) {
                return back()
                    ->withErrors(['credit_dp_amount' => 'Untuk pembelian kredit, DP harus lebih kecil dari harga mobil.'])
                    ->withInput();
            }

            $creditSimulation = CreditSimulationCatalog::simulate(
                $totalAmount,
                $selectedDpAmount,
                $selectedTenor,
                $selectedPartner['code']
            );
        } elseif (empty($validated['booking_fee_agreement'])) {
            return back()
                ->withErrors(['booking_fee_agreement' => 'Mohon centang persetujuan biaya booking sebelum melanjutkan ke pembayaran.'])
                ->withInput();
        }

        $notes = collect([
            $sourceOffer ? 'Order berasal dari unit ' . ($car->kode_unit ?: 'tanpa kode unit') . ' dengan harga deal ' . number_format((float) $sourceOffer->negotiated_price, 0, ',', '.') : null,
            $purchaseMethod === 'credit'
                ? 'Skema pembelian online: kredit leasing.'
                : 'Skema pembelian online: harga cash.',
            $purchaseMethod !== 'credit'
                ? 'Pilihan pembayaran cash online: ' . ($paymentPlan === 'full' ? 'Bayar Lunas Full' : 'Booking Fee')
                : null,
            $purchaseMethod === 'credit'
                ? 'Leasing dipilih: ' . $creditSimulation['partner']['name']
                : 'Biaya booking online: ' . number_format(self::BOOKING_FEE, 0, ',', '.'),
            $purchaseMethod === 'credit'
                ? 'Rencana DP: ' . number_format((float) $creditSimulation['dp_amount'], 0, ',', '.')
                : null,
            $purchaseMethod === 'credit'
                ? 'Tenor dipilih: ' . $creditSimulation['tenor_months'] . ' bulan'
                : null,
            $purchaseMethod === 'credit'
                ? 'Estimasi cicilan per bulan: ' . number_format((float) $creditSimulation['monthly_installment'], 0, ',', '.')
                : null,
            !empty($validated['notes']) ? 'Catatan: ' . $validated['notes'] : null,
        ])->filter()->implode("\n");

        $draftToken = $checkoutDrafts->store($request->user(), [
            'car_id' => $car->id,
            'offer_id' => $sourceOffer?->id,
            'payment_plan' => $paymentPlan,
            'sales_flow' => $salesFlow,
            'total_amount' => $totalAmount,
            'active_payment_amount' => $purchaseMethod === 'credit'
                ? (float) ($creditSimulation['dp_amount'] ?? 0)
                : ($paymentPlan === 'full' ? $totalAmount : min($totalAmount, (float) config('payments.booking_fee', self::BOOKING_FEE))),
            'remaining_balance' => max(
                $totalAmount - ($purchaseMethod === 'credit'
                    ? (float) ($creditSimulation['dp_amount'] ?? 0)
                    : ($paymentPlan === 'full' ? $totalAmount : min($totalAmount, (float) config('payments.booking_fee', self::BOOKING_FEE)))),
                0
            ),
            'order_payment_method' => $purchaseMethod === 'credit' ? 'credit' : 'transfer',
            'notes' => $notes,
        ]);

        $request->session()->forget('active_order_id');
        $request->session()->put('active_checkout_draft', $draftToken);
        $request->session()->put('checkout_car_id', $car->id);

        if ($purchaseMethod === 'credit') {
            return redirect()
                ->route('payment.page', ['draft' => $draftToken])
                ->with('success', 'Lanjutkan pembayaran DP kredit terlebih dahulu. Pesanan baru akan tercatat setelah pembayaran berhasil masuk.');
        }

        return redirect()
            ->route('payment.page', ['draft' => $draftToken])
            ->with('success', $paymentPlan === 'full'
                ? 'Draft pembayaran lunas full sudah disiapkan. Pesanan akan tercatat setelah pembayaran berhasil.'
                : 'Draft pembayaran booking fee sudah disiapkan. Pesanan akan tercatat setelah pembayaran berhasil.');
    }

    private function unavailableCarMessage(Car $car): string
    {
        return $car->status === 'sold'
            ? 'Unit ini sudah terjual dan tidak bisa dipesan lagi.'
            : 'Unit ini sudah terpesan dan sedang menunggu proses supervisor, sehingga tidak bisa dipesan lagi.';
    }
}
