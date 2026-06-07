<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use App\Support\DashboardNotificationBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalAvailableCars = Car::where('status', 'available')->count();
        $totalSold = Car::where('status', 'sold')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $verifiedPayments = Payment::where('status', 'verified')->count();
        $completedPayments = Payment::where('status', 'verified')->count();
        $totalTestDrives = TestDrive::count();
        $totalOffers = Offer::count();

        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : "MONTH(created_at)";
        $yearExpr = $driver === 'sqlite'
            ? "strftime('%Y', created_at)"
            : "YEAR(created_at)";

        $yearCounts = Order::query()
            ->select(DB::raw("$yearExpr as year"), DB::raw('COUNT(*) as total'))
            ->groupBy('year')
            ->orderByDesc('total')
            ->get();

        $availableYears = $yearCounts
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->filter(fn ($y) => $y > 0)
            ->values()
            ->all();
        rsort($availableYears);

        $requestedYear = (int) $request->query('year', 0);
        if ($requestedYear > 0 && in_array($requestedYear, $availableYears, true)) {
            $year = $requestedYear;
        } else {
            // Prefer the historical Excel year if present.
            $year = in_array(2025, $availableYears, true)
                ? 2025
                : ((int) ($yearCounts->first()?->year ?? now()->year));
        }

        $paymentBehaviorRows = Order::query()
            ->select(DB::raw("$monthExpr as month"))
            ->selectRaw("SUM(CASE WHEN payment_method IN ('cash', 'transfer') THEN 1 ELSE 0 END) as cash_total")
            ->selectRaw("SUM(CASE WHEN payment_method IN ('credit', 'va') THEN 1 ELSE 0 END) as credit_total")
            ->whereRaw("$yearExpr = ?", [$year])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $paymentBehaviorLabels = collect(range(1, 12))
            ->map(fn (int $month) => Carbon::create(2000, $month, 1)->translatedFormat('M'))
            ->values();

        $monthlyCashTransactions = collect(range(1, 12))
            ->map(function (int $month) use ($paymentBehaviorRows) {
                $row = $paymentBehaviorRows->firstWhere('month', $month);
                return (int) ($row->cash_total ?? 0);
            })
            ->values();

        $monthlyCreditTransactions = collect(range(1, 12))
            ->map(function (int $month) use ($paymentBehaviorRows) {
                $row = $paymentBehaviorRows->firstWhere('month', $month);
                return (int) ($row->credit_total ?? 0);
            })
            ->values();
        $yearlyCashPurchases = (int) $monthlyCashTransactions->sum();
        $yearlyCreditPurchases = (int) $monthlyCreditTransactions->sum();

        $monthlyRevenue = Order::select(DB::raw("$monthExpr as month"), DB::raw('SUM(total) as total'))
            ->whereRaw("$yearExpr = ?", [$year])
            ->whereIn('status', ['paid', 'completed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $paymentStatus = Payment::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $reportRange = (string) $request->query('report_range', 'monthly');
        if (!in_array($reportRange, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
            $reportRange = 'monthly';
        }

        $availableMonths = collect(range(1, 12))
            ->mapWithKeys(fn ($m) => [$m => Carbon::create(2000, $m, 1)->translatedFormat('F')])
            ->all();

        $requestedMonth = (int) $request->query('month', 0);
        $reportMonth = ($requestedMonth >= 1 && $requestedMonth <= 12) ? $requestedMonth : null;

        if ($reportRange === 'monthly' && $reportMonth === null) {
            $monthCounts = Order::query()
                ->select(DB::raw("$monthExpr as month"), DB::raw('COUNT(*) as total'))
                ->whereRaw("$yearExpr = ?", [$year])
                ->whereIn('status', ['paid', 'completed'])
                ->groupBy('month')
                ->orderByDesc('total')
                ->get();

            $reportMonth = (int) ($monthCounts->first()?->month ?? now()->month);
        }

        $salesReport = $this->buildSalesReport($reportRange, $year, $reportMonth);
        ['start' => $activeRangeStart, 'end' => $activeRangeEnd, 'label' => $activeRangeLabel] = $this->resolveReportWindow($reportRange, $year, $reportMonth);
        $reportNote = $this->reportNote($reportRange, $year, $reportMonth, $availableMonths, $salesReport['range_label']);

        $topBrands = Order::query()
            ->join('cars', 'orders.car_id', '=', 'cars.id')
            ->select('cars.merk', DB::raw('COUNT(orders.id) as total'))
            ->whereIn('orders.status', ['paid', 'completed'])
            ->whereBetween('orders.created_at', [$activeRangeStart, $activeRangeEnd])
            ->groupBy('cars.merk')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        if ($request->expectsJson()) {
            $panel = (string) $request->query('panel');

            if ($panel === 'sales-trend') {
                return response()->json($this->salesTrendPayload(
                    $paymentBehaviorLabels->all(),
                    $monthlyCashTransactions->all(),
                    $monthlyCreditTransactions->all(),
                    $monthlyRevenue,
                    $yearlyCashPurchases,
                    $yearlyCreditPurchases
                ));
            }

            if ($panel === 'sales-report') {
                return response()->json($this->salesReportPayload(
                    $salesReport,
                    $reportNote,
                    $activeRangeLabel,
                    $topBrands
                ));
            }
        }

        $recentOrders = Order::with(['user', 'car'])->latest()->take(5)->get();
        $recentPayments = Payment::with(['order.user', 'order.car'])->latest()->take(5)->get();
        $recentTestDrives = TestDrive::with(['user', 'car'])->latest()->take(5)->get();

        $activity = collect()
            ->merge(Order::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'order',
                    'label' => 'Pesanan baru masuk',
                    'detail' => $item->order_reference,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Payment::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'payment',
                    'label' => 'Pembayaran baru',
                    'detail' => 'Payment #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(User::whereIn('role', ['owner', 'supervisor', 'marketing'])->latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'user',
                    'label' => 'User internal baru',
                    'detail' => $item->name,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Car::with('createdBy:id,name,role')->latest()->take(5)->get()->map(function ($item) {
                $sourceLabel = match ($item->createdBy?->role) {
                    'marketing' => 'Marketing',
                    'supervisor' => 'Supervisor',
                    default => 'Sumber belum tercatat',
                };

                return [
                    'type' => 'car',
                    'label' => 'Mobil baru ditambahkan',
                    'detail' => $item->merk . ' ' . $item->tipe . ' - ' . $sourceLabel,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(TestDrive::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'testdrive',
                    'label' => 'Booking test drive',
                    'detail' => 'ID #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->merge(Offer::latest()->take(5)->get()->map(function ($item) {
                return [
                    'type' => 'offer',
                    'label' => 'Penawaran baru',
                    'detail' => 'ID #' . $item->id,
                    'time' => $item->created_at,
                ];
            }))
            ->sortByDesc('time')
            ->take(10)
            ->values();

        $dashboardNotifications = DashboardNotificationBuilder::supervisor();

        return view('pages.dashboard-supervisor', compact(
            'availableYears',
            'year',
            'availableMonths',
            'reportMonth',
            'totalCustomers',
            'totalOrders',
            'totalAvailableCars',
            'totalSold',
            'pendingPayments',
            'verifiedPayments',
            'completedPayments',
            'totalTestDrives',
            'totalOffers',
            'paymentBehaviorLabels',
            'monthlyCashTransactions',
            'monthlyCreditTransactions',
            'yearlyCashPurchases',
            'yearlyCreditPurchases',
            'monthlyRevenue',
            'paymentStatus',
            'reportRange',
            'salesReport',
            'reportNote',
            'activeRangeLabel',
            'topBrands',
            'recentOrders',
            'recentPayments',
            'recentTestDrives',
            'activity',
            'dashboardNotifications'
        ));
    }

    private function buildSalesReport(string $range, int $year, ?int $month): array
    {
        $now = now();
        $periods = [];
        $bucketFormat = 'Y-m-d';
        $labelFormat = 'd M';
        $groupByCallback = fn (Carbon $date) => $date->format($bucketFormat);

        if ($range === 'daily') {
            $start = $now->copy()->startOfDay();
            for ($hour = 0; $hour < 24; $hour++) {
                $period = $start->copy()->addHours($hour);
                $periods[$period->format('Y-m-d H:00')] = [
                    'label' => $period->format('H:i'),
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }
            $bucketFormat = 'Y-m-d H:00';
            $groupByCallback = fn (Carbon $date) => $date->copy()->startOfHour()->format($bucketFormat);
            $labelFormat = 'H:i';
            $rangeStart = $start;
            $rangeEnd = $start->copy()->endOfDay();
        } elseif ($range === 'weekly') {
            $start = $now->copy()->subDays(6)->startOfDay();
            for ($day = 0; $day < 7; $day++) {
                $period = $start->copy()->addDays($day);
                $periods[$period->format('Y-m-d')] = [
                    'label' => $period->translatedFormat('D, d M'),
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }
            $rangeStart = $start;
            $rangeEnd = $now->copy()->endOfDay();
        } elseif ($range === 'yearly') {
            // Full Jan–Dec for the selected year (works well for imported historical data).
            $start = Carbon::create($year, 1, 1)->startOfMonth();
            for ($month = 0; $month < 12; $month++) {
                $period = $start->copy()->addMonths($month);
                $periods[$period->format('Y-m')] = [
                    'label' => $period->translatedFormat('M Y'),
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }
            $bucketFormat = 'Y-m';
            $groupByCallback = fn (Carbon $date) => $date->copy()->startOfMonth()->format($bucketFormat);
            $labelFormat = 'M Y';
            $rangeStart = $start;
            $rangeEnd = $start->copy()->endOfYear();
        } else {
            // Monthly: show a specific month in a specific year.
            $month = ($month && $month >= 1 && $month <= 12) ? $month : (int) $now->month;
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($end)) {
                $periods[$cursor->format('Y-m-d')] = [
                    'label' => $cursor->format('d M'),
                    'revenue' => 0,
                    'orders' => 0,
                ];
                $cursor->addDay();
            }
            $rangeStart = $start;
            $rangeEnd = $end;
        }

        $salesRowsQuery = Order::query()
            ->whereIn('status', ['paid', 'completed'])
            ->where('created_at', '>=', $rangeStart);

        if (isset($rangeEnd)) {
            $salesRowsQuery->where('created_at', '<=', $rangeEnd);
        }

        $salesRows = $salesRowsQuery->get(['created_at', 'total']);

        foreach ($salesRows as $row) {
            $date = Carbon::parse($row->created_at);
            $key = $groupByCallback($date);

            if (!isset($periods[$key])) {
                $periods[$key] = [
                    'label' => $date->format($labelFormat),
                    'revenue' => 0,
                    'orders' => 0,
                ];
            }

            $periods[$key]['revenue'] += (float) $row->total;
            $periods[$key]['orders']++;
        }

        $labels = array_values(array_column($periods, 'label'));
        $revenues = array_values(array_map(fn ($period) => round((float) $period['revenue'], 2), $periods));
        $orders = array_values(array_map(fn ($period) => (int) $period['orders'], $periods));
        $totalRevenue = array_sum($revenues);
        $totalOrderCount = array_sum($orders);

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orders,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrderCount,
            'average_order_value' => $totalOrderCount > 0 ? $totalRevenue / $totalOrderCount : 0,
            'range_label' => ucfirst($range),
            'last_updated' => $now,
        ];
    }

    /**
     * @param array<int, string> $availableMonths
     */
    private function reportNote(string $reportRange, int $year, ?int $reportMonth, array $availableMonths, string $rangeLabel): string
    {
        if ($reportRange === 'monthly') {
            return 'Grafik bulanan sedang menampilkan data untuk ' . ($availableMonths[$reportMonth] ?? 'Bulan terpilih') . ' ' . $year . '.';
        }

        if ($reportRange === 'yearly') {
            return 'Grafik tahunan sedang menampilkan akumulasi penjualan selama ' . $year . '.';
        }

        return 'Filter tahun tetap aktif di laporan ini, sementara grafik ' . strtolower($rangeLabel) . ' mengikuti rentang waktu berjalan.';
    }

    /**
     * @param \Illuminate\Support\Collection<int, mixed> $monthlyRevenue
     * @return array<string, mixed>
     */
    private function salesTrendPayload(array $labels, array $cashTotals, array $creditTotals, $monthlyRevenue, int $yearlyCashPurchases, int $yearlyCreditPurchases): array
    {
        return [
            'sales_chart' => [
                'labels' => $labels,
                'cash_totals' => $cashTotals,
                'credit_totals' => $creditTotals,
                'cash_purchase_total' => $yearlyCashPurchases,
                'credit_purchase_total' => $yearlyCreditPurchases,
            ],
            'revenue_chart' => [
                'labels' => $monthlyRevenue->pluck('month')->values(),
                'totals' => $monthlyRevenue->pluck('total')->map(fn ($value) => (float) $value)->values(),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $salesReport
     * @param \Illuminate\Support\Collection<int, mixed> $topBrands
     * @return array<string, mixed>
     */
    private function salesReportPayload(array $salesReport, string $reportNote, string $activeRangeLabel, $topBrands): array
    {
        return [
            'period_note' => $reportNote,
            'active_range_label' => $activeRangeLabel,
            'summary' => [
                'range_active' => $salesReport['range_label'],
                'total_revenue' => \App\Support\CurrencyFormatter::rupiah($salesReport['total_revenue']),
                'total_orders' => number_format((int) $salesReport['total_orders']),
                'average_order_value' => \App\Support\CurrencyFormatter::rupiah($salesReport['average_order_value']),
                'last_updated' => $salesReport['last_updated']->format('d M Y H:i'),
            ],
            'chart' => [
                'labels' => array_values($salesReport['labels']),
                'revenues' => array_values($salesReport['revenues']),
                'orders' => array_values($salesReport['orders']),
            ],
            'top_brands' => [
                'labels' => $topBrands->pluck('merk')->map(fn ($merk) => $merk ?: 'Tidak diketahui')->values(),
                'totals' => $topBrands->pluck('total')->map(fn ($value) => (int) $value)->values(),
                'empty' => $topBrands->isEmpty(),
                'empty_message' => 'Belum ada data penjualan pada periode ' . strtolower($activeRangeLabel) . '.',
            ],
        ];
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function resolveReportWindow(string $range, int $year, ?int $month): array
    {
        $now = now();

        if ($range === 'daily') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();

            return [
                'start' => $start,
                'end' => $end,
                'label' => 'Hari ini',
            ];
        }

        if ($range === 'weekly') {
            $start = $now->copy()->subDays(6)->startOfDay();
            $end = $now->copy()->endOfDay();

            return [
                'start' => $start,
                'end' => $end,
                'label' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
            ];
        }

        if ($range === 'yearly') {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = $start->copy()->endOfYear();

            return [
                'start' => $start,
                'end' => $end,
                'label' => 'Jan - Des ' . $year,
            ];
        }

        $month = ($month && $month >= 1 && $month <= 12) ? $month : (int) $now->month;
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        return [
            'start' => $start,
            'end' => $end,
            'label' => $start->translatedFormat('F Y'),
        ];
    }
}
