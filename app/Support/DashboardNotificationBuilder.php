<?php

namespace App\Support;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductReview;
use App\Models\TestDrive;
use App\Models\User;
use App\Support\CurrencyFormatter;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DashboardNotificationBuilder
{
    /**
     * @return array{count:int,items:array<int,array<string,mixed>>,empty_text:string}
     */
    public static function customer(User $user): array
    {
        $items = collect();

        $orders = $user->orders()
            ->with(['car', 'payment'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        foreach ($orders as $order) {
            $carName = self::carName($order->car?->merk, $order->car?->tipe);
            $payment = $order->payment;

            if ($payment && $payment->status === 'rejected') {
                $items->push(self::item(
                    'warning',
                    'Pembayaran perlu dikonfirmasi ulang',
                    'Status pembayaran untuk order ' . $order->order_reference . ' belum valid. Tim showroom akan menghubungi Anda kembali.',
                    $payment->updated_at ?? $order->updated_at,
                    route('order.tracking', ['order' => $order->id]),
                    'rose',
                    true
                ));
                continue;
            }

            if ($payment && $payment->status === 'verified') {
                $items->push(self::item(
                    'verified',
                    'Pembayaran sudah diverifikasi',
                    'Pembayaran untuk order ' . $order->order_reference . ' pada unit ' . $carName . ' sudah diverifikasi tim.',
                    $payment->verified_at ?? $payment->updated_at ?? $order->updated_at,
                    route('order.tracking', ['order' => $order->id]),
                    'emerald'
                ));
                continue;
            }

            $items->push(self::item(
                'receipt_long',
                'Pesanan sedang diproses',
                'Order ' . $order->order_reference . ' untuk ' . $carName . ' sedang menunggu validasi transaksi dari supervisor.',
                $order->updated_at ?? $order->created_at,
                route('order.tracking', ['order' => $order->id]),
                'navy',
                $order->status !== 'completed'
            ));
        }

        $offers = $user->offers()
            ->with('car')
            ->latest('updated_at')
            ->take(4)
            ->get();

        foreach ($offers as $offer) {
            $carName = self::carName($offer->car?->merk, $offer->car?->tipe);

            if ($offer->status === 'countered') {
                $items->push(self::item(
                    'sell',
                    'Ada tawar balik dari showroom',
                    'Supervisor mengirim harga baru ' . CurrencyFormatter::rupiah($offer->counter_price ?? $offer->offer_price) . ' untuk ' . $carName . '.',
                    $offer->updated_at ?? $offer->created_at,
                    route('offers.page'),
                    'sky',
                    true
                ));
                continue;
            }

            if ($offer->status === 'accepted') {
                $items->push(self::item(
                    'payments',
                    'Harga deal siap dibayar',
                    'Penawaran Anda untuk ' . $carName . ' sudah disepakati. Lanjut checkout dengan harga final ' . CurrencyFormatter::rupiah($offer->negotiated_price) . '.',
                    $offer->updated_at ?? $offer->created_at,
                    route('checkout.cash', ['offer_id' => $offer->id, 'car_id' => $offer->car_id]),
                    'emerald',
                    true
                ));
                continue;
            }

            if ($offer->status === 'pending') {
                $items->push(self::item(
                    'schedule',
                    'Penawaran sedang ditinjau',
                    'Penawaran Anda untuk ' . $carName . ' sedang menunggu tanggapan supervisor.',
                    $offer->updated_at ?? $offer->created_at,
                    route('offers.page'),
                    'amber'
                ));
                continue;
            }

            if ($offer->status === 'rejected') {
                $items->push(self::item(
                    'warning',
                    'Negosiasi belum deal',
                    'Penawaran untuk ' . $carName . ' belum mencapai kesepakatan. Data tetap disimpan untuk follow-up berikutnya.',
                    $offer->updated_at ?? $offer->created_at,
                    route('offers.page'),
                    'rose'
                ));
            }
        }

        $testDrives = $user->testDrives()
            ->with('car')
            ->latest('updated_at')
            ->take(4)
            ->get();

        foreach ($testDrives as $testDrive) {
            $carName = self::carName($testDrive->car?->merk, $testDrive->car?->tipe);
            $detail = 'Jadwal test drive untuk ' . $carName . ' pada '
                . optional($testDrive->booking_date)->format('d M Y')
                . ' ' . substr((string) $testDrive->booking_time, 0, 5)
                . '.';

            $items->push(self::item(
                'event_available',
                'Update test drive',
                trim($detail . ' Status saat ini: ' . strtoupper((string) $testDrive->status) . '.'),
                $testDrive->updated_at ?? $testDrive->created_at,
                route('customer.test-drives.index'),
                $testDrive->status === 'pending' ? 'amber' : 'sky',
                $testDrive->status === 'pending'
            ));
        }

        $sorted = self::sorted($items)->take(8)->values();

        return [
            'count' => max($sorted->where('is_actionable', true)->count(), 0),
            'items' => $sorted->all(),
            'empty_text' => 'Belum ada notifikasi baru untuk akun Anda.',
        ];
    }

    /**
     * @return array{count:int,items:array<int,array<string,mixed>>,empty_text:string}
     */
    public static function marketing(): array
    {
        $items = collect();

        $pendingOffers = Offer::query()
            ->with(['user', 'car'])
            ->whereIn('status', ['pending', 'countered'])
            ->latest()
            ->take(4)
            ->get();
        foreach ($pendingOffers as $offer) {
            $items->push(self::item(
                'sell',
                $offer->status === 'countered' ? 'Menunggu respons customer' : 'Penawaran baru perlu follow-up',
                $offer->status === 'countered'
                    ? ($offer->user?->name ?? 'Customer') . ' belum merespons tawar balik untuk ' . self::carName($offer->car?->merk, $offer->car?->tipe) . '.'
                    : ($offer->user?->name ?? 'Customer') . ' mengajukan penawaran untuk ' . self::carName($offer->car?->merk, $offer->car?->tipe) . '.',
                $offer->updated_at ?? $offer->created_at,
                route('marketing.offers.index'),
                $offer->status === 'countered' ? 'sky' : 'amber',
                true
            ));
        }

        $pendingPayments = Payment::query()
            ->with(['order.user', 'order.car'])
            ->where('status', 'pending')
            ->latest()
            ->take(4)
            ->get();

        foreach ($pendingPayments as $payment) {
            $items->push(self::item(
                'payments',
                'Pembayaran customer perlu dipantau',
                ($payment->order?->user?->name ?? 'Customer') . ' memiliki transaksi ' . self::carName($payment->order?->car?->merk, $payment->order?->car?->tipe) . ' yang menunggu validasi internal.',
                $payment->updated_at ?? $payment->created_at,
                route('marketing.transactions.index'),
                'emerald',
                true
            ));
        }

        $recentOrders = Order::query()->with(['user', 'car'])->latest()->take(3)->get();
        foreach ($recentOrders as $order) {
            $items->push(self::item(
                'receipt_long',
                'Pesanan baru masuk',
                'Order ' . $order->order_reference . ' dari ' . ($order->user?->name ?? 'Customer') . ' untuk ' . self::carName($order->car?->merk, $order->car?->tipe) . '.',
                $order->created_at,
                route('marketing.orders.show', $order),
                'navy'
            ));
        }

        $sorted = self::sorted($items)->take(8)->values();

        return [
            'count' => $sorted->where('is_actionable', true)->count(),
            'items' => $sorted->all(),
            'empty_text' => 'Belum ada notifikasi kerja yang perlu ditindaklanjuti.',
        ];
    }

    /**
     * @return array{count:int,items:array<int,array<string,mixed>>,empty_text:string}
     */
    public static function supervisor(): array
    {
        $items = collect();

        $pendingOrders = Order::query()
            ->with(['user', 'car'])
            ->where('status', 'pending')
            ->whereNull('handled_by')
            ->latest()
            ->take(4)
            ->get();

        foreach ($pendingOrders as $order) {
            $isCreditOrder = in_array((string) $order->payment_method, ['credit', 'va'], true);

            $items->push(self::item(
                'receipt_long',
                $isCreditOrder ? 'Pengajuan kredit baru menunggu approval' : 'Pesanan online baru belum diproses',
                $isCreditOrder
                    ? ($order->user?->name ?? 'Customer') . ' ingin membeli ' . self::carName($order->car?->merk, $order->car?->tipe) . ' secara kredit. Tinjau pengajuan lalu setujui agar customer bisa melanjutkan pembayaran DP.'
                    : ($order->user?->name ?? 'Customer') . ' membuat order ' . $order->order_reference . ' untuk ' . self::carName($order->car?->merk, $order->car?->tipe) . '. Proses pesanan agar status unit dan pembayaran jelas.',
                $order->created_at,
                route('supervisor.orders.show', $order),
                $isCreditOrder ? 'navy' : 'amber',
                true
            ));
        }

        $pendingPayments = Payment::query()->with(['order.user', 'order.car'])->where('status', 'pending')->latest()->take(4)->get();
        foreach ($pendingPayments as $payment) {
            $items->push(self::item(
                'verified',
                'Pembayaran menunggu verifikasi',
                ($payment->order?->user?->name ?? 'Customer') . ' menunggu verifikasi pembayaran untuk ' . self::carName($payment->order?->car?->merk, $payment->order?->car?->tipe) . '.',
                $payment->updated_at ?? $payment->created_at,
                route('supervisor.payments.index'),
                'amber',
                true
            ));
        }

        $recentReviews = ProductReview::query()->with(['user', 'car'])->latest()->take(3)->get();
        foreach ($recentReviews as $review) {
            $items->push(self::item(
                'rate_review',
                'Review customer terbaru',
                ($review->user?->name ?? 'Customer') . ' membagikan review untuk ' . self::carName($review->car?->merk, $review->car?->tipe) . '.',
                $review->created_at,
                route('supervisor.reviews.index'),
                'rose'
            ));
        }

        $pendingOffers = Offer::query()
            ->with(['user', 'car'])
            ->whereIn('status', ['pending', 'countered'])
            ->latest()
            ->take(3)
            ->get();
        foreach ($pendingOffers as $offer) {
            $items->push(self::item(
                'sell',
                $offer->status === 'countered' ? 'Tawar balik belum dibalas customer' : 'Penawaran customer masih pending',
                $offer->status === 'countered'
                    ? ($offer->user?->name ?? 'Customer') . ' belum merespons tawar balik untuk ' . self::carName($offer->car?->merk, $offer->car?->tipe) . '.'
                    : ($offer->user?->name ?? 'Customer') . ' menunggu arahan untuk penawaran ' . self::carName($offer->car?->merk, $offer->car?->tipe) . '.',
                $offer->updated_at ?? $offer->created_at,
                route('supervisor.offers.index'),
                $offer->status === 'countered' ? 'sky' : 'navy',
                true
            ));
        }

        $pendingTestDrives = TestDrive::query()->with(['user', 'car'])->where('status', 'pending')->latest()->take(3)->get();
        foreach ($pendingTestDrives as $testDrive) {
            $items->push(self::item(
                'event_available',
                'Booking test drive belum diputuskan',
                ($testDrive->user?->name ?? 'Customer') . ' menunggu jadwal test drive untuk ' . self::carName($testDrive->car?->merk, $testDrive->car?->tipe) . '.',
                $testDrive->created_at,
                route('supervisor.testdrives.index'),
                'sky',
                true
            ));
        }

        $sorted = self::sorted($items)->take(8)->values();

        return [
            'count' => $sorted->where('is_actionable', true)->count(),
            'items' => $sorted->all(),
            'empty_text' => 'Belum ada notifikasi pengawasan baru.',
        ];
    }

    /**
     * @return array{count:int,items:array<int,array<string,mixed>>,empty_text:string}
     */
    public static function owner(): array
    {
        $items = collect();

        $internalCars = Car::query()
            ->with('createdBy')
            ->whereHas('createdBy', fn ($query) => $query->whereIn('role', ['marketing', 'supervisor']))
            ->latest()
            ->take(4)
            ->get();

        foreach ($internalCars as $car) {
            $actorRole = $car->createdBy?->role === 'marketing' ? 'Marketing' : 'Supervisor';
            $items->push(self::item(
                'directions_car',
                $actorRole . ' menambahkan unit baru',
                ($car->createdBy?->name ?? $actorRole) . ' menambahkan ' . self::carName($car->merk, $car->tipe) . ' ke sistem.',
                $car->created_at,
                null,
                $car->createdBy?->role === 'marketing' ? 'amber' : 'sky'
            ));
        }

        $soldOrders = Order::query()
            ->with(['car', 'user', 'handledBy'])
            ->whereIn('status', ['paid', 'completed'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($soldOrders as $order) {
            $actorRole = match ($order->handled_role) {
                'marketing' => 'Marketing',
                'supervisor' => 'Supervisor',
                default => 'Tim Internal',
            };

            $actorName = $order->handledBy?->name ?? $actorRole;
            $items->push(self::item(
                'sell',
                'Mobil berhasil terjual',
                $actorName . ' menyelesaikan penjualan ' . self::carName($order->car?->merk, $order->car?->tipe) . ' untuk ' . ($order->user?->name ?? 'customer') . '.',
                $order->updated_at ?? $order->created_at,
                null,
                $order->handled_role === 'marketing' ? 'amber' : ($order->handled_role === 'supervisor' ? 'sky' : 'rose')
            ));
        }

        $handledOrders = Order::query()
            ->with(['car', 'handledBy'])
            ->whereNotNull('handled_at')
            ->whereIn('handled_role', ['marketing', 'supervisor'])
            ->latest('handled_at')
            ->take(4)
            ->get();

        foreach ($handledOrders as $order) {
            $actorRole = $order->handled_role === 'marketing' ? 'Marketing' : 'Supervisor';
            $items->push(self::item(
                'receipt_long',
                $actorRole . ' menangani pesanan',
                ($order->handledBy?->name ?? $actorRole) . ' memproses order ' . $order->order_reference . ' untuk ' . self::carName($order->car?->merk, $order->car?->tipe) . '.',
                $order->handled_at,
                null,
                $order->handled_role === 'marketing' ? 'amber' : 'sky'
            ));
        }

        $handledOffers = Offer::query()
            ->with(['car', 'user', 'handledBy'])
            ->whereNotNull('handled_at')
            ->whereIn('handled_role', ['marketing', 'supervisor'])
            ->latest('handled_at')
            ->take(4)
            ->get();

        foreach ($handledOffers as $offer) {
            $actorRole = $offer->handled_role === 'marketing' ? 'Marketing' : 'Supervisor';
            $items->push(self::item(
                'sell',
                $actorRole . ' menindaklanjuti penawaran',
                ($offer->handledBy?->name ?? $actorRole) . ' memproses penawaran dari ' . ($offer->user?->name ?? 'Customer') . ' untuk ' . self::carName($offer->car?->merk, $offer->car?->tipe) . '.',
                $offer->handled_at,
                null,
                $offer->handled_role === 'marketing' ? 'amber' : 'sky'
            ));
        }

        $handledTestDrives = TestDrive::query()
            ->with(['car', 'user', 'handledBy'])
            ->whereNotNull('handled_at')
            ->whereIn('handled_role', ['marketing', 'supervisor'])
            ->latest('handled_at')
            ->take(4)
            ->get();

        foreach ($handledTestDrives as $testDrive) {
            $actorRole = $testDrive->handled_role === 'marketing' ? 'Marketing' : 'Supervisor';
            $items->push(self::item(
                'event_available',
                $actorRole . ' menindaklanjuti test drive',
                ($testDrive->handledBy?->name ?? $actorRole) . ' memperbarui booking test drive ' . ($testDrive->user?->name ?? 'Customer') . ' untuk ' . self::carName($testDrive->car?->merk, $testDrive->car?->tipe) . '.',
                $testDrive->handled_at,
                null,
                $testDrive->handled_role === 'marketing' ? 'amber' : 'sky'
            ));
        }

        $handledPayments = Payment::query()
            ->with(['order.car', 'order.user', 'handledBy'])
            ->whereNotNull('handled_at')
            ->whereIn('handled_role', ['marketing', 'supervisor'])
            ->latest('handled_at')
            ->take(4)
            ->get();

        foreach ($handledPayments as $payment) {
            $actorRole = $payment->handled_role === 'marketing' ? 'Marketing' : 'Supervisor';
            $items->push(self::item(
                'payments',
                $actorRole . ' memperbarui transaksi',
                ($payment->handledBy?->name ?? $actorRole) . ' memperbarui transaksi ' . ($payment->order?->user?->name ?? 'Customer') . ' untuk ' . self::carName($payment->order?->car?->merk, $payment->order?->car?->tipe) . '.',
                $payment->handled_at,
                null,
                $payment->handled_role === 'marketing' ? 'amber' : 'sky'
            ));
        }

        $sorted = self::sorted($items)
            ->unique(fn (array $item) => $item['title'] . '|' . $item['detail'] . '|' . optional($item['sort_time'])->format('Y-m-d H:i:s'))
            ->take(12)
            ->values();
        $todayCount = $sorted->filter(fn (array $item) => ($item['sort_time'] ?? now())->isToday())->count();

        return [
            'count' => $todayCount > 0 ? $todayCount : $sorted->count(),
            'items' => $sorted->all(),
            'empty_text' => 'Belum ada aktivitas terbaru dari marketing atau supervisor.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function item(
        string $icon,
        string $title,
        string $detail,
        ?CarbonInterface $time,
        ?string $href,
        string $tone = 'navy',
        bool $isActionable = false
    ): array {
        $timestamp = $time ?? now();

        return [
            'icon' => $icon,
            'title' => $title,
            'detail' => $detail,
            'href' => $href,
            'tone' => $tone,
            'time_label' => $timestamp->diffForHumans(),
            'sort_time' => $timestamp,
            'is_actionable' => $isActionable,
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     * @return Collection<int, array<string, mixed>>
     */
    private static function sorted(Collection $items): Collection
    {
        return $items
            ->sortByDesc(fn (array $item) => $item['sort_time'])
            ->values();
    }

    private static function carName(?string $brand, ?string $type): string
    {
        $name = trim((string) $brand . ' ' . (string) $type);

        return $name !== '' ? $name : 'unit pilihan customer';
    }
}
