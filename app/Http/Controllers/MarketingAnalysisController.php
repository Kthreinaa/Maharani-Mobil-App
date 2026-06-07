<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Favorite;
use App\Models\Offer;
use App\Models\Order;
use App\Models\TestDrive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarketingAnalysisController extends Controller
{
    private string $monthExpression;

    public function index(Request $request)
    {
        $this->monthExpression = $this->monthExpression('created_at');

        $availableYears = $this->availableYears();
        $selectedYear = (int) $request->integer('year', (int) $availableYears->first());
        $selectedMonth = (int) $request->integer('month', 0);

        if (!$availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->first();
        }

        if ($selectedMonth < 0 || $selectedMonth > 12) {
            $selectedMonth = 0;
        }

        $signalsTrend = $this->signalsTrend($selectedYear, $selectedMonth);
        $brandInterest = $this->brandInterest($selectedYear, $selectedMonth);
        $responseHealth = $this->responseHealth($selectedYear, $selectedMonth);
        $opportunityCars = $this->opportunityCars($selectedYear, $selectedMonth);
        $actionRecommendations = $this->actionRecommendations($brandInterest, $responseHealth, $opportunityCars);
        $periodLabel = $selectedMonth > 0
            ? 'Menampilkan rincian harian untuk ' . Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') . '.'
            : 'Menampilkan tren bulanan sepanjang tahun ' . $selectedYear . '.';

        $summary = [
            'tahun' => $selectedYear,
            'bulan' => $selectedMonth,
            'sinyal_minat' => Favorite::whereYear('created_at', $selectedYear)
                ->when($selectedMonth > 0, fn ($query) => $query->whereMonth('created_at', $selectedMonth))
                ->count()
                + Offer::whereYear('created_at', $selectedYear)
                    ->when($selectedMonth > 0, fn ($query) => $query->whereMonth('created_at', $selectedMonth))
                    ->count()
                + TestDrive::whereYear('created_at', $selectedYear)
                    ->when($selectedMonth > 0, fn ($query) => $query->whereMonth('created_at', $selectedMonth))
                    ->count(),
            'lead_perlu_aksi' => $responseHealth['overall']['pending'],
            'rasio_follow_up' => $responseHealth['overall']['response_rate'],
            'rata_respon_jam' => $responseHealth['overall']['avg_response_hours'],
        ];

        return view('marketing.analysis.index', compact(
            'availableYears',
            'selectedYear',
            'selectedMonth',
            'periodLabel',
            'summary',
            'signalsTrend',
            'brandInterest',
            'responseHealth',
            'opportunityCars',
            'actionRecommendations'
        ));
    }

    /**
     * @return Collection<int, int>
     */
    private function availableYears(): Collection
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER)"
            : 'YEAR(created_at)';

        $sources = [
            Order::query()->selectRaw($yearExpr . ' as report_year')->whereNotNull('created_at'),
            Offer::query()->selectRaw($yearExpr . ' as report_year')->whereNotNull('created_at'),
            TestDrive::query()->selectRaw($yearExpr . ' as report_year')->whereNotNull('created_at'),
            Car::query()->selectRaw($yearExpr . ' as report_year')->whereNotNull('created_at'),
            Favorite::query()->selectRaw($yearExpr . ' as report_year')->whereNotNull('created_at'),
        ];

        $years = collect();
        foreach ($sources as $query) {
            $years = $years->merge(
                $query->pluck('report_year')->map(fn ($year) => (int) $year)->filter(fn (int $year) => $year > 0)
            );
        }

        $years = $years->unique()->sortDesc()->values();

        return $years->isNotEmpty() ? $years : collect([(int) now()->year]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function signalsTrend(int $year, int $month = 0): Collection
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
                        ->count()
                        + TestDrive::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->whereDay('created_at', $day)
                            ->count(),
                    'follow_ups' => Offer::whereYear('handled_at', $year)
                        ->whereMonth('handled_at', $month)
                        ->whereDay('handled_at', $day)
                        ->whereNotNull('handled_at')
                        ->count()
                        + TestDrive::whereYear('handled_at', $year)
                            ->whereMonth('handled_at', $month)
                            ->whereDay('handled_at', $day)
                            ->whereNotNull('handled_at')
                            ->count(),
                ];
            });
        }

        $favoriteCounts = Favorite::whereYear('created_at', $year)
            ->selectRaw($this->monthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $offerCounts = Offer::whereYear('created_at', $year)
            ->selectRaw($this->monthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $testDriveCounts = TestDrive::whereYear('created_at', $year)
            ->selectRaw($this->monthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $handledMonthExpression = $this->monthExpression('handled_at');

        $handledOfferCounts = Offer::whereYear('handled_at', $year)
            ->whereNotNull('handled_at')
            ->selectRaw($handledMonthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $handledTestDriveCounts = TestDrive::whereYear('handled_at', $year)
            ->whereNotNull('handled_at')
            ->selectRaw($handledMonthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        return collect(range(1, 12))->map(function (int $month) use ($year, $favoriteCounts, $offerCounts, $testDriveCounts, $handledOfferCounts, $handledTestDriveCounts) {
            return [
                'label' => Carbon::create($year, $month, 1)->translatedFormat('M'),
                'favorites' => (int) ($favoriteCounts[$month] ?? 0),
                'leads' => (int) ($offerCounts[$month] ?? 0) + (int) ($testDriveCounts[$month] ?? 0),
                'follow_ups' => (int) ($handledOfferCounts[$month] ?? 0) + (int) ($handledTestDriveCounts[$month] ?? 0),
            ];
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function brandInterest(int $year, int $month = 0): Collection
    {
        $favoriteScores = Favorite::query()
            ->join('cars', 'cars.id', '=', 'favorites.car_id')
            ->whereYear('favorites.created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('favorites.created_at', $month))
            ->selectRaw('cars.merk as brand, COUNT(*) as total')
            ->groupBy('cars.merk')
            ->pluck('total', 'brand');

        $offerScores = Offer::query()
            ->join('cars', 'cars.id', '=', 'offers.car_id')
            ->whereYear('offers.created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('offers.created_at', $month))
            ->selectRaw('cars.merk as brand, COUNT(*) as total')
            ->groupBy('cars.merk')
            ->pluck('total', 'brand');

        $testDriveScores = TestDrive::query()
            ->join('cars', 'cars.id', '=', 'test_drives.car_id')
            ->whereYear('test_drives.created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('test_drives.created_at', $month))
            ->selectRaw('cars.merk as brand, COUNT(*) as total')
            ->groupBy('cars.merk')
            ->pluck('total', 'brand');

        $saleScores = Order::query()
            ->join('cars', 'cars.id', '=', 'orders.car_id')
            ->whereYear('orders.created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('orders.created_at', $month))
            ->where('handled_role', 'marketing')
            ->selectRaw('cars.merk as brand, COUNT(*) as total')
            ->groupBy('cars.merk')
            ->pluck('total', 'brand');

        $brands = collect($offerScores->keys())
            ->merge($favoriteScores->keys())
            ->merge($testDriveScores->keys())
            ->merge($saleScores->keys())
            ->unique()
            ->filter()
            ->values();

        return $brands->map(function (string $brand) use ($favoriteScores, $offerScores, $testDriveScores, $saleScores) {
            $favoriteCount = (int) ($favoriteScores[$brand] ?? 0);
            $offerCount = (int) ($offerScores[$brand] ?? 0);
            $testDriveCount = (int) ($testDriveScores[$brand] ?? 0);
            $salesCount = (int) ($saleScores[$brand] ?? 0);

            return [
                'brand' => $brand,
                'favorites' => $favoriteCount,
                'offers' => $offerCount,
                'test_drives' => $testDriveCount,
                'sales' => $salesCount,
                'interest_score' => $favoriteCount + ($offerCount * 2) + ($testDriveCount * 3) + ($salesCount * 4),
            ];
        })->sortByDesc('interest_score')->take(5)->values();
    }

    /**
     * @return array{cards: Collection<int, array<string, mixed>>, overall: array<string, int|float|null>}
     */
    private function responseHealth(int $year, int $month = 0): array
    {
        $offerRows = Offer::whereYear('created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('created_at', $month))
            ->get(['status', 'created_at', 'handled_at']);
        $testDriveRows = TestDrive::whereYear('created_at', $year)
            ->when($month > 0, fn ($query) => $query->whereMonth('created_at', $month))
            ->get(['status', 'created_at', 'handled_at']);

        $offerMetric = $this->buildResponseMetric('Penawaran', $offerRows);
        $testDriveMetric = $this->buildResponseMetric('Test Drive', $testDriveRows);

        $total = $offerMetric['total'] + $testDriveMetric['total'];
        $pending = $offerMetric['pending'] + $testDriveMetric['pending'];
        $handled = $offerMetric['handled'] + $testDriveMetric['handled'];

        $combinedHandledRows = $offerRows->whereNotNull('handled_at')->concat(
            $testDriveRows->whereNotNull('handled_at')
        );

        $avgResponseHours = $combinedHandledRows->isNotEmpty()
            ? $combinedHandledRows->avg(function ($row) {
                return Carbon::parse($row->created_at)->diffInHours(Carbon::parse($row->handled_at));
            })
            : null;

        $oldestPendingDays = collect([$offerMetric['oldest_pending_days'], $testDriveMetric['oldest_pending_days']])
            ->filter(fn ($value) => $value !== null)
            ->max();

        return [
            'cards' => collect([$offerMetric, $testDriveMetric]),
            'overall' => [
                'total' => $total,
                'pending' => $pending,
                'handled' => $handled,
                'response_rate' => $total > 0 ? round(($handled / $total) * 100) : 0,
                'avg_response_hours' => $avgResponseHours !== null ? round((float) $avgResponseHours, 1) : null,
                'oldest_pending_days' => $oldestPendingDays !== null ? (int) $oldestPendingDays : null,
            ],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function opportunityCars(int $year, int $month = 0): Collection
    {
        return Car::query()
            ->whereIn('status', ['available', 'reserved'])
            ->withCount([
                'favoredByUsers as favorites_count' => fn ($query) => $query
                    ->whereYear('favorites.created_at', $year)
                    ->when($month > 0, fn ($innerQuery) => $innerQuery->whereMonth('favorites.created_at', $month)),
                'offers as offers_count' => fn ($query) => $query
                    ->whereYear('created_at', $year)
                    ->when($month > 0, fn ($innerQuery) => $innerQuery->whereMonth('created_at', $month)),
                'testDrives as test_drives_count' => fn ($query) => $query
                    ->whereYear('created_at', $year)
                    ->when($month > 0, fn ($innerQuery) => $innerQuery->whereMonth('created_at', $month)),
                'orders as orders_count' => fn ($query) => $query
                    ->whereYear('created_at', $year)
                    ->when($month > 0, fn ($innerQuery) => $innerQuery->whereMonth('created_at', $month)),
            ])
            ->get()
            ->map(function (Car $car) {
                $favorites = (int) $car->favorites_count;
                $offers = (int) $car->offers_count;
                $testDrives = (int) $car->test_drives_count;
                $orders = (int) $car->orders_count;
                $leadCount = $offers + $testDrives;

                if ($favorites > 0 && $leadCount === 0) {
                    $segment = 'awareness';
                    $action = 'Banyak disimpan, tetapi belum jadi calon pembeli. Perkuat CTA chat, caption harga, atau promo ringan.';
                } elseif ($leadCount > 0 && $orders === 0) {
                    $segment = 'closing';
                    $action = 'Sudah ada sinyal negosiasi. Cocok untuk follow-up personal, penawaran terbatas, atau reminder test drive.';
                } elseif ($orders > 0) {
                    $segment = 'proof';
                    $action = 'Sudah terbukti laku. Bagus dijadikan materi iklan, testimoni, atau pembanding harga.';
                } else {
                    $segment = 'nurture';
                    $action = 'Belum ada sinyal kuat. Cek ulang foto utama, copy listing, dan positioning harga.';
                }

                return [
                    'car' => $car,
                    'favorites' => $favorites,
                    'offers' => $offers,
                    'test_drives' => $testDrives,
                    'orders' => $orders,
                    'segment' => $segment,
                    'action' => $action,
                    'priority_score' => ($favorites * 4) + ($offers * 3) + ($testDrives * 3) + ($orders * 2),
                ];
            })
            ->filter(fn (array $item) => ($item['favorites'] + $item['offers'] + $item['test_drives'] + $item['orders']) > 0)
            ->sortByDesc('priority_score')
            ->take(5)
            ->values();
    }

    /**
     * @param Collection<int, mixed> $rows
     * @return array<string, mixed>
     */
    private function buildResponseMetric(string $label, Collection $rows): array
    {
        $total = $rows->count();
        $pendingRows = $rows->filter(fn ($row) => $row->status === 'pending');
        $handledRows = $rows->filter(fn ($row) => $row->handled_at !== null);

        $avgResponseHours = $handledRows->isNotEmpty()
            ? round($handledRows->avg(function ($row) {
                return Carbon::parse($row->created_at)->diffInHours(Carbon::parse($row->handled_at));
            }), 1)
            : null;

        $oldestPendingDays = $pendingRows->isNotEmpty()
            ? Carbon::parse($pendingRows->min('created_at'))->diffInDays(now())
            : null;

        return [
            'label' => $label,
            'total' => $total,
            'pending' => $pendingRows->count(),
            'handled' => $handledRows->count(),
            'response_rate' => $total > 0 ? round(($handledRows->count() / $total) * 100) : 0,
            'avg_response_hours' => $avgResponseHours,
            'oldest_pending_days' => $oldestPendingDays,
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $brandInterest
     * @param array{cards: Collection<int, array<string, mixed>>, overall: array<string, int|float|null>} $responseHealth
     * @param Collection<int, array<string, mixed>> $opportunityCars
     * @return array<int, array<string, string>>
     */
    private function actionRecommendations(Collection $brandInterest, array $responseHealth, Collection $opportunityCars): array
    {
        $topBrand = $brandInterest->first();
        $awarenessCar = $opportunityCars->firstWhere('segment', 'awareness');
        $closingCar = $opportunityCars->firstWhere('segment', 'closing');
        $oldestPendingDays = (int) ($responseHealth['overall']['oldest_pending_days'] ?? 0);

        return array_values(array_filter([
            $topBrand ? [
                'title' => 'Konten merk prioritas',
                'metric' => $topBrand['brand'],
                'action' => 'Dorong konten untuk merk ini lebih dulu karena paling banyak menghasilkan sinyal minat dan interaksi.',
            ] : null,
            $awarenessCar ? [
                'title' => 'Remarketing unit favorit',
                'metric' => $awarenessCar['car']->merk . ' ' . $awarenessCar['car']->tipe,
                'action' => 'Unit ini sering disimpan, tetapi belum banyak jadi calon pembeli. Fokuskan CTA WhatsApp, promo, dan headline harga.',
            ] : null,
            $closingCar ? [
                'title' => 'Calon pembeli siap di-closing',
                'metric' => $closingCar['car']->merk . ' ' . $closingCar['car']->tipe,
                'action' => 'Minatnya sudah kuat, tetapi belum ada order. Prioritaskan follow-up personal dan penawaran yang lebih spesifik.',
            ] : null,
            [
                'title' => 'Backlog follow-up',
                'metric' => (string) $responseHealth['overall']['pending'] . ' customer pending',
                'action' => $oldestPendingDays >= 3
                    ? 'Ada customer pending yang sudah menunggu ' . $oldestPendingDays . ' hari. Ini perlu dibersihkan lebih dulu sebelum tambah kampanye baru.'
                    : 'Backlog masih cukup terkendali. Jaga ritme follow-up agar customer baru tidak menumpuk.',
            ],
        ]));
    }

    private function monthExpression(string $column = 'created_at'): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', {$column}) AS INTEGER)"
            : "MONTH({$column})";
    }
}
