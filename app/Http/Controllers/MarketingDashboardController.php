<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\User;
use App\Support\DashboardNotificationBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MarketingDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = now()->year;
        $availableSignalYears = $this->availableSignalYears();
        $selectedSignalYear = (int) $request->integer('signal_year', (int) $availableSignalYears->first());
        $selectedSignalMonth = (int) $request->integer('signal_month', 0);

        if (!$availableSignalYears->contains($selectedSignalYear)) {
            $selectedSignalYear = (int) $availableSignalYears->first();
        }

        if ($selectedSignalMonth < 0 || $selectedSignalMonth > 12) {
            $selectedSignalMonth = 0;
        }

        $activeInventory = Car::where('status', 'available')->count();
        $customerCount = User::where('role', 'customer')->count();
        $recentOrders = Order::with(['user', 'car'])->latest()->take(5)->get();
        $recentPayments = Payment::with(['order.user', 'order.car', 'handledBy'])->latest()->take(5)->get();
        $offersThisYear = Offer::whereYear('created_at', $currentYear)->count();

        $signalsThisYear = Favorite::whereYear('created_at', $currentYear)->count() + $offersThisYear;

        $pendingLeadCount = Offer::where('status', 'pending')->count();

        $totalLeadCount = $offersThisYear;

        $handledLeadCount = Offer::whereYear('created_at', $currentYear)->whereNotNull('handled_at')->count();

        $leadResponseRate = $totalLeadCount > 0
            ? round(($handledLeadCount / $totalLeadCount) * 100)
            : 0;

        $interestTrend = $this->interestTrend($selectedSignalYear, $selectedSignalMonth);
        $signalPeriodLabel = $selectedSignalMonth > 0
            ? 'Menampilkan rincian harian untuk ' . Carbon::create($selectedSignalYear, $selectedSignalMonth, 1)->translatedFormat('F Y') . '.'
            : 'Menampilkan tren bulanan sepanjang tahun ' . $selectedSignalYear . '.';

        if ($request->expectsJson() && $request->query('panel') === 'signals') {
            return response()->json($this->signalPanelPayload(
                $interestTrend,
                $signalPeriodLabel,
                $selectedSignalYear,
                $selectedSignalMonth
            ));
        }

        $topOpportunities = Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->withCount([
                'favoredByUsers as favorites_count' => fn ($query) => $query->whereYear('favorites.created_at', $currentYear),
                'offers as offers_count' => fn ($query) => $query->whereYear('created_at', $currentYear),
                'orders as orders_count' => fn ($query) => $query->whereYear('created_at', $currentYear),
            ])
            ->get()
            ->map(function (Car $car) {
                $favorites = (int) $car->favorites_count;
                $offers = (int) $car->offers_count;
                $orders = (int) $car->orders_count;
                $engagement = $favorites + $offers;

                if ($favorites >= 2 && $offers === 0) {
                    $insight = 'Banyak disimpan, tetapi belum menghasilkan penawaran. Cocok untuk dorongan chat dan follow-up awal.';
                } elseif ($offers >= 2 && $orders === 0) {
                    $insight = 'Sudah ada customer yang menawar. Marketing bisa menjaga komunikasi sampai siap diproses supervisor.';
                } elseif ($orders > 0) {
                    $insight = 'Sudah pernah closing. Unit seperti ini cocok dijadikan materi promosi dan testimoni customer.';
                } else {
                    $insight = 'Perlu dorongan ulang lewat konten, copy pemasaran, atau angle promosi yang lebih jelas.';
                }

                return [
                    'car' => $car,
                    'favorites' => $favorites,
                    'offers' => $offers,
                    'orders' => $orders,
                    'engagement' => $engagement,
                    'opportunity_score' => ($favorites * 4) + ($offers * 3) - ($orders * 4),
                    'insight' => $insight,
                ];
            })
            ->filter(fn (array $item) => ($item['favorites'] + $item['offers'] + $item['orders']) > 0)
            ->sortByDesc(fn (array $item) => ($item['opportunity_score'] * 1000) + $item['engagement'])
            ->take(5)
            ->values();

        $dashboardNotifications = DashboardNotificationBuilder::marketing();

        return view('pages.dashboard-marketing', compact(
            'activeInventory',
            'customerCount',
            'offersThisYear',
            'signalsThisYear',
            'pendingLeadCount',
            'leadResponseRate',
            'availableSignalYears',
            'selectedSignalYear',
            'selectedSignalMonth',
            'signalPeriodLabel',
            'topOpportunities',
            'recentOrders',
            'recentPayments',
            'interestTrend',
            'dashboardNotifications'
        ));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'kode_unit' => ['required', 'string', 'max:50', 'unique:cars,kode_unit'],
            'merk' => ['required', 'string', 'max:100'],
            'tipe' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'harga' => ['required', 'numeric', 'min:0'],
            'kilometer' => ['required', 'numeric', 'min:0'],
            'transmisi' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:50'],
            'bahan_bakar' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:available,reserved,sold'],
            'deskripsi' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $photos = $validated['photos'];
        unset($validated['photos']);

        $validated['created_by'] = $request->user()->id;
        $car = Car::create($validated);

        $storedPhotos = [];
        foreach ($photos as $photo) {
            $storedPhotos[] = $photo->store("cars/{$car->id}", 'public');
        }

        $car->update(['photos' => $storedPhotos]);

        return redirect()
            ->route('marketing.products.index')
            ->with('success', 'Produk baru berhasil dipublikasikan ke katalog.');
    }

    /**
     * @return Collection<int, int>
     */
    private function availableSignalYears(): Collection
    {
        $years = Favorite::query()->pluck('created_at')
            ->merge(Offer::query()->pluck('created_at'))
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->unique()
            ->sortDesc()
            ->values();

        return $years->isNotEmpty() ? $years : collect([(int) now()->year]);
    }

    /**
     * @return Collection<int, array<string, int|string>>
     */
    private function interestTrend(int $year, int $month = 0): Collection
    {
        if ($month > 0) {
            $period = Carbon::create($year, $month, 1);

            return collect(range(1, $period->daysInMonth))->map(function (int $day) use ($year, $month) {
                return [
                    'label' => str_pad((string) $day, 2, '0', STR_PAD_LEFT),
                    'favorites' => Favorite::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->whereDay('created_at', $day)
                        ->count(),
                    'leads' => Offer::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->whereDay('created_at', $day)
                        ->count(),
                    'follow_ups' => Offer::whereYear('handled_at', $year)
                        ->whereMonth('handled_at', $month)
                        ->whereDay('handled_at', $day)
                        ->whereNotNull('handled_at')
                        ->count(),
                ];
            });
        }

        return collect(range(1, 12))->map(function (int $selectedMonth) use ($year) {
            $date = Carbon::create($year, $selectedMonth, 1);

            return [
                'label' => $date->translatedFormat('M'),
                'favorites' => Favorite::whereYear('created_at', $year)->whereMonth('created_at', $selectedMonth)->count(),
                'leads' => Offer::whereYear('created_at', $year)->whereMonth('created_at', $selectedMonth)->count(),
                'follow_ups' => Offer::whereYear('handled_at', $year)->whereMonth('handled_at', $selectedMonth)->whereNotNull('handled_at')->count(),
            ];
        });
    }

    /**
     * @param Collection<int, array<string, int|string>> $interestTrend
     * @return array<string, mixed>
     */
    private function signalPanelPayload(Collection $interestTrend, string $signalPeriodLabel, int $year, int $month): array
    {
        return [
            'period_label' => $signalPeriodLabel,
            'chart' => [
                'labels' => $interestTrend->pluck('label')->values(),
                'favorites' => $interestTrend->pluck('favorites')->map(fn ($value) => (int) $value)->values(),
                'leads' => $interestTrend->pluck('leads')->map(fn ($value) => (int) $value)->values(),
                'follow_ups' => $interestTrend->pluck('follow_ups')->map(fn ($value) => (int) $value)->values(),
            ],
        ];
    }
}
