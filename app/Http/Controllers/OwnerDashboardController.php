<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\DashboardNotificationBuilder;
use App\Support\CurrencyFormatter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = $this->availableYears();
        $selectedYear = $this->resolveSelectedYear($request, $availableYears);
        $dashboard = $this->buildDashboard($selectedYear, $availableYears);

        if ($request->expectsJson()) {
            return response()->json($this->dashboardPayload($dashboard));
        }

        return view('pages.dashboard-owner', $dashboard);
    }

    private function buildDashboard(int $selectedYear, Collection $availableYears): array
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', orders.created_at) AS INTEGER)"
            : 'YEAR(orders.created_at)';
        $monthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', orders.created_at) AS INTEGER)"
            : 'MONTH(orders.created_at)';

        $paidOrders = Order::query()->whereIn('orders.status', ['paid', 'completed']);

        $monthlyRows = (clone $paidOrders)
            ->selectRaw($monthExpr . ' as month_number, COUNT(*) as total_orders, SUM(orders.total) as total_revenue')
            ->whereRaw($yearExpr . ' = ?', [$selectedYear])
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->get()
            ->keyBy(fn ($row) => (int) $row->month_number);

        $monthlyPerformance = collect(range(1, 12))->map(function (int $month) use ($selectedYear, $monthlyRows) {
            $row = $monthlyRows->get($month);
            $date = Carbon::create($selectedYear, $month, 1);

            return [
                'month_number' => $month,
                'label' => $date->translatedFormat('M'),
                'full_label' => $date->translatedFormat('F Y'),
                'orders' => (int) ($row->total_orders ?? 0),
                'revenue' => round((float) ($row->total_revenue ?? 0), 2),
            ];
        })->values();

        $annualRows = (clone $paidOrders)
            ->selectRaw($yearExpr . ' as sale_year, COUNT(*) as total_orders, SUM(orders.total) as total_revenue')
            ->groupBy('sale_year')
            ->orderBy('sale_year')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => (int) $row->sale_year,
                    'orders' => (int) $row->total_orders,
                    'revenue' => round((float) $row->total_revenue, 2),
                ];
            })
            ->values();

        $monthlyGrowth = $monthlyPerformance->values()->map(function (array $item, int $index) use ($monthlyPerformance) {
            $previousRevenue = $index > 0 ? (float) $monthlyPerformance[$index - 1]['revenue'] : null;

            return [
                'label' => $item['label'],
                'growth' => $this->percentageChange($item['revenue'], $previousRevenue),
            ];
        });

        $yearlyGrowth = $annualRows->values()->map(function (array $item, int $index) use ($annualRows) {
            $previousRevenue = $index > 0 ? (float) $annualRows[$index - 1]['revenue'] : null;

            return [
                'label' => (string) $item['year'],
                'growth' => $this->percentageChange($item['revenue'], $previousRevenue),
            ];
        });

        $topBrands = (clone $paidOrders)
            ->join('cars', 'cars.id', '=', 'orders.car_id')
            ->selectRaw('cars.merk as brand, COUNT(orders.id) as total_units, SUM(orders.total) as total_revenue')
            ->whereRaw($yearExpr . ' = ?', [$selectedYear])
            ->groupBy('cars.merk')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get()
            ->map(function ($row) use ($monthlyPerformance) {
                $yearRevenue = (float) $monthlyPerformance->sum('revenue');
                $revenue = round((float) $row->total_revenue, 2);

                return [
                    'brand' => (string) $row->brand,
                    'units' => (int) $row->total_units,
                    'revenue' => $revenue,
                    'revenue_formatted' => CurrencyFormatter::rupiah($revenue),
                    'share' => $yearRevenue > 0 ? round(($revenue / $yearRevenue) * 100, 1) : 0.0,
                ];
            })
            ->values();

        $salesRows = (clone $paidOrders)
            ->join('cars', 'cars.id', '=', 'orders.car_id')
            ->selectRaw('orders.created_at, orders.total, cars.merk, cars.tipe')
            ->whereRaw($yearExpr . ' = ?', [$selectedYear])
            ->orderBy('orders.created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'created_at' => Carbon::parse($row->created_at),
                    'revenue' => round((float) $row->total, 2),
                    'merk' => (string) $row->merk,
                    'tipe' => (string) $row->tipe,
                    'category' => $this->classifyCategory((string) $row->merk, (string) $row->tipe),
                ];
            });

        $totalRevenue = round((float) $monthlyPerformance->sum('revenue'), 2);
        $totalUnits = (int) $monthlyPerformance->sum('orders');
        $averageOrder = $totalUnits > 0 ? round($totalRevenue / $totalUnits, 2) : 0.0;
        $previousYearRevenue = (float) ($annualRows->firstWhere('year', $selectedYear - 1)['revenue'] ?? 0);
        $yearOverYearGrowth = $this->percentageChange($totalRevenue, $previousYearRevenue > 0 ? $previousYearRevenue : null);
        $summaryCashOrders = $this->purchaseMethodCount($paidOrders, 'cash');
        $summaryCreditOrders = $this->purchaseMethodCount($paidOrders, 'credit');
        $topBrand = $topBrands->first();
        $topMonth = $monthlyPerformance->sortByDesc('revenue')->first();

        $strategicInsights = $this->strategicInsights(
            $selectedYear,
            $salesRows,
            $topBrands,
            $monthlyPerformance,
            $annualRows,
            $yearOverYearGrowth
        );

        return [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'summaryCards' => [
                [
                    'label' => 'Total Pendapatan',
                    'value' => CurrencyFormatter::rupiah($totalRevenue),
                    'note' => 'Pendapatan dari transaksi paid dan completed pada tahun ' . $selectedYear . '.',
                    'tone' => 'sky',
                ],
                [
                    'label' => 'Unit Terjual',
                    'value' => number_format($totalUnits),
                    'note' => 'Jumlah transaksi selesai pada tahun aktif.',
                    'tone' => 'amber',
                ],
                [
                    'label' => 'Rata-rata Transaksi',
                    'value' => CurrencyFormatter::rupiah($averageOrder),
                    'note' => 'Nilai rata-rata per transaksi sepanjang tahun ini.',
                    'tone' => 'emerald',
                ],
                [
                    'label' => 'Pertumbuhan Tahunan',
                    'value' => $yearOverYearGrowth > 0 ? '+' . number_format($yearOverYearGrowth, 1) . '%' : number_format($yearOverYearGrowth, 1) . '%',
                    'note' => $selectedYear > 0
                        ? 'Dibandingkan performa tahun ' . ($selectedYear - 1) . '.'
                        : 'Belum ada pembanding tahun sebelumnya.',
                    'tone' => 'violet',
                ],
            ],
            'topBrandHeadline' => [
                'name' => $topBrand['brand'] ?? '-',
                'share' => $topBrand['share'] ?? 0,
                'units' => $topBrand['units'] ?? 0,
            ],
            'reportSummary' => [
                'title' => 'Laporan tahun ' . $selectedYear,
                'total_revenue' => CurrencyFormatter::rupiah($totalRevenue),
                'total_orders' => number_format($totalUnits),
                'average_order' => CurrencyFormatter::rupiah($averageOrder),
                'peak_month' => $topMonth && $topMonth['revenue'] > 0
                    ? $topMonth['full_label'] . ' (' . CurrencyFormatter::rupiah($topMonth['revenue']) . ')'
                    : 'Belum ada puncak penjualan tercatat.',
            ],
            'purchaseBehavior' => [
                [
                    'label' => 'Pembelian Cash',
                    'value' => number_format($summaryCashOrders),
                    'count' => (int) $summaryCashOrders,
                    'share' => $totalUnits > 0 ? round(($summaryCashOrders / $totalUnits) * 100, 1) : 0.0,
                    'note' => 'Total transaksi pembelian cash pada tahun ' . $selectedYear . '.',
                ],
                [
                    'label' => 'Pembelian Kredit',
                    'value' => number_format($summaryCreditOrders),
                    'count' => (int) $summaryCreditOrders,
                    'share' => $totalUnits > 0 ? round(($summaryCreditOrders / $totalUnits) * 100, 1) : 0.0,
                    'note' => 'Total transaksi pembelian kredit pada tahun ' . $selectedYear . '.',
                ],
            ],
            'monthlyPerformance' => $monthlyPerformance,
            'annualRevenue' => $annualRows,
            'monthlyGrowth' => $monthlyGrowth,
            'yearlyGrowth' => $yearlyGrowth,
            'topBrands' => $topBrands,
            'strategicInsights' => $strategicInsights,
            'reportLinks' => [
                'center' => route('owner.reports.index', ['period' => 'yearly', 'year' => $selectedYear]),
                'pdf' => route('owner.reports.exportPdf', ['period' => 'yearly', 'year' => $selectedYear]),
                'excel' => route('owner.reports.exportExcel', ['period' => 'yearly', 'year' => $selectedYear]),
            ],
            'dashboardNotifications' => DashboardNotificationBuilder::owner(),
        ];
    }

    private function dashboardPayload(array $dashboard): array
    {
        return [
            'selected_year' => $dashboard['selectedYear'],
            'summary_cards' => $dashboard['summaryCards'],
            'top_brand_headline' => $dashboard['topBrandHeadline'],
            'report_summary' => $dashboard['reportSummary'],
            'purchase_behavior' => $dashboard['purchaseBehavior'],
            'sales_overview_chart' => [
                'labels' => $dashboard['monthlyPerformance']->pluck('label')->values(),
                'revenues' => $dashboard['monthlyPerformance']->pluck('revenue')->values(),
                'orders' => $dashboard['monthlyPerformance']->pluck('orders')->values(),
            ],
            'annual_revenue_chart' => [
                'labels' => $dashboard['annualRevenue']->pluck('year')->map(fn ($year) => (string) $year)->values(),
                'revenues' => $dashboard['annualRevenue']->pluck('revenue')->values(),
            ],
            'growth_charts' => [
                'monthly' => [
                    'labels' => $dashboard['monthlyGrowth']->pluck('label')->values(),
                    'values' => $dashboard['monthlyGrowth']->pluck('growth')->values(),
                ],
                'yearly' => [
                    'labels' => $dashboard['yearlyGrowth']->pluck('label')->values(),
                    'values' => $dashboard['yearlyGrowth']->pluck('growth')->values(),
                ],
            ],
            'top_brands' => [
                'items' => $dashboard['topBrands']->values(),
                'empty' => $dashboard['topBrands']->isEmpty(),
                'empty_message' => 'Belum ada merk dominan pada tahun ' . $dashboard['selectedYear'] . '.',
            ],
            'strategic_insights' => $dashboard['strategicInsights'],
            'dashboard_notifications' => $dashboard['dashboardNotifications'],
            'report_links' => $dashboard['reportLinks'],
        ];
    }

    private function availableYears(): Collection
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER)"
            : 'YEAR(created_at)';

        $years = Order::query()
            ->whereIn('status', ['paid', 'completed'])
            ->selectRaw($yearExpr . ' as report_year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('report_year')
            ->pluck('report_year')
            ->map(fn ($year) => (int) $year)
            ->filter(fn (int $year) => $year > 0)
            ->values();

        return $years->isNotEmpty() ? $years : collect([(int) now()->year]);
    }

    private function resolveSelectedYear(Request $request, Collection $availableYears): int
    {
        $requestedYear = (int) $request->integer('year', 0);

        if ($requestedYear > 0 && $availableYears->contains($requestedYear)) {
            return $requestedYear;
        }

        return (int) ($availableYears->first() ?? now()->year);
    }

    private function purchaseMethodCount($paidOrders, string $method): int
    {
        $query = clone $paidOrders;

        if ($method === 'credit') {
            return (int) $query->whereIn('orders.payment_method', ['credit', 'va'])->count();
        }

        return (int) $query->where('orders.payment_method', 'cash')->count();
    }

    private function strategicInsights(
        int $selectedYear,
        Collection $salesRows,
        Collection $topBrands,
        Collection $monthlyPerformance,
        Collection $annualRevenue,
        float $yearOverYearGrowth
    ): array {
        $insights = [];
        $activeMonths = $monthlyPerformance->where('revenue', '>', 0)->values();
        $latestMonth = $activeMonths->last();
        $previousMonth = $activeMonths->count() > 1 ? $activeMonths->slice(-2, 1)->first() : null;

        if ($latestMonth) {
            $latestMonthNumber = (int) $latestMonth['month_number'];
            $previousMonthNumber = $previousMonth ? (int) $previousMonth['month_number'] : null;

            $latestCategoryRows = $salesRows->filter(fn (array $row) => (int) $row['created_at']->month === $latestMonthNumber);
            $previousCategoryRows = $previousMonthNumber
                ? $salesRows->filter(fn (array $row) => (int) $row['created_at']->month === $previousMonthNumber)
                : collect();

            $currentCategories = $latestCategoryRows->groupBy('category')->map->count();
            $previousCategories = $previousCategoryRows->groupBy('category')->map->count();

            $bestCategory = $currentCategories
                ->map(function (int $count, string $category) use ($previousCategories) {
                    $previousCount = (int) ($previousCategories[$category] ?? 0);

                    return [
                        'category' => $category,
                        'count' => $count,
                        'growth' => $this->percentageChange($count, $previousCount > 0 ? $previousCount : null),
                    ];
                })
                ->sortByDesc('growth')
                ->first();

            if ($bestCategory && $bestCategory['count'] > 0) {
                $topModels = $latestCategoryRows
                    ->filter(fn (array $row) => $row['category'] === $bestCategory['category'])
                    ->groupBy(fn (array $row) => trim($row['merk'] . ' ' . $row['tipe']))
                    ->map->count()
                    ->sortDesc()
                    ->keys()
                    ->take(2)
                    ->values()
                    ->all();

                $modelText = count($topModels) > 0 ? implode(' dan ', $topModels) : 'unit di kategori ini';
                $monthLabel = Carbon::create($selectedYear, $latestMonthNumber, 1)->translatedFormat('F Y');
                $comparisonText = $previousMonthNumber
                    ? 'dibanding ' . Carbon::create($selectedYear, $previousMonthNumber, 1)->translatedFormat('F Y')
                    : 'dibanding periode sebelumnya';

                $insights[] = [
                    'title' => 'Wawasan Kategori Terkuat',
                    'detail' => sprintf(
                        'Penjualan kategori %s meningkat %s pada %s %s. Rekomendasi: prioritaskan stok %s untuk periode berikutnya.',
                        $bestCategory['category'],
                        ($bestCategory['growth'] > 0 ? '+' : '') . number_format($bestCategory['growth'], 1) . '%',
                        $monthLabel,
                        $comparisonText,
                        $modelText
                    ),
                ];
            }
        }

        $topBrand = $topBrands->first();
        if ($topBrand) {
            $insights[] = [
                'title' => 'Merk Paling Menguntungkan',
                'detail' => sprintf(
                    'Merk %s menyumbang %s%% dari omzet tahun %d dengan %d unit terjual. Rekomendasi: jaga stok lini %s dan pertahankan materi promosi untuk model yang paling cepat laku.',
                    $topBrand['brand'],
                    number_format((float) $topBrand['share'], 1),
                    $selectedYear,
                    (int) $topBrand['units'],
                    $topBrand['brand']
                ),
            ];
        }

        $selectedYearRow = $annualRevenue->firstWhere('year', $selectedYear);
        if ($selectedYearRow) {
            $insights[] = [
                'title' => 'Arah Pertumbuhan Pendapatan',
                'detail' => $selectedYear > 0 && $annualRevenue->firstWhere('year', $selectedYear - 1)
                    ? sprintf(
                        'Pendapatan tahun %d bergerak %s dibanding %d, dengan total omzet %s. Rekomendasi: fokuskan akuisisi pada bulan-bulan yang sudah terbukti menghasilkan omzet terbesar.',
                        $selectedYear,
                        ($yearOverYearGrowth > 0 ? 'naik ' : 'turun ') . number_format(abs($yearOverYearGrowth), 1) . '%',
                        $selectedYear - 1,
                        CurrencyFormatter::rupiah((float) $selectedYearRow['revenue'])
                    )
                    : sprintf(
                        'Tahun %d mencatat omzet %s. Data pembanding tahun sebelumnya belum cukup, jadi rekomendasinya adalah menjadikan ritme penjualan tahun ini sebagai baseline target berikutnya.',
                        $selectedYear,
                        CurrencyFormatter::rupiah((float) $selectedYearRow['revenue'])
                    ),
            ];
        }

        if ($insights === []) {
            $insights[] = [
                'title' => 'Wawasan Belum Tersedia',
                'detail' => 'Belum ada transaksi paid atau completed yang cukup untuk membentuk analisis strategis. Owner bisa mulai melihat insight ketika data penjualan sudah bertambah.',
            ];
        }

        return array_slice($insights, 0, 3);
    }

    private function classifyCategory(string $brand, string $type): string
    {
        $haystack = strtolower(trim($brand . ' ' . $type));

        if ($this->containsAny($haystack, ['fortuner', 'pajero', 'terios', 'rush', 'crv', 'cr-v', 'xtrail', 'x-trail', 'captiva', 'mu-x', 'suv'])) {
            return 'SUV';
        }

        if ($this->containsAny($haystack, ['avanza', 'xenia', 'sigra', 'calya', 'innova', 'ertiga', 'livina', 'mobilio', 'serena', 'alphard', 'vellfire', 'luxio', 'carens', 'mpv'])) {
            return 'MPV';
        }

        if ($this->containsAny($haystack, ['brio', 'jazz', 'yaris', 'agya', 'ayla', 'hatchback'])) {
            return 'Hatchback';
        }

        if ($this->containsAny($haystack, ['corolla', 'civic', 'camry', 'vios', 'altis', 'sedan'])) {
            return 'Sedan';
        }

        if ($this->containsAny($haystack, ['hilux', 'triton', 'pickup', 'pick up', 'carry', 'gran max'])) {
            return 'Pickup';
        }

        return 'Lainnya';
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function percentageChange(float|int $current, float|int|null $previous): float
    {
        $currentValue = (float) $current;

        if ($previous === null) {
            return 0.0;
        }

        $previousValue = (float) $previous;

        if ($previousValue === 0.0) {
            return $currentValue > 0 ? 100.0 : 0.0;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }
}
