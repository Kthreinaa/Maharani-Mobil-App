<?php

namespace App\Support;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportBuilder
{
    /**
     * @return array{
     *   period:string,
     *   base_date:Carbon,
     *   selected_year:int,
     *   selected_month:int,
     *   available_years:Collection<int, int>,
     *   range_label:string,
     *   generated_at:Carbon,
     *   rows:Collection<int, object>,
     *   summary:array<string, mixed>,
     *   crm_overview:array<int, array<string, mixed>>,
     *   trend:Collection<int, array<string, mixed>>,
     *   brand_performance:Collection<int, array<string, mixed>>,
     *   payment_mix:Collection<int, array<string, mixed>>,
     *   analysis:array<int, array{title:string, detail:string}>,
     *   recommendations:array<int, array{title:string, detail:string}>
     * }
     */
    public static function build(Request $request): array
    {
        $period = (string) $request->get('period', $request->get('report_range', 'monthly'));
        if (!in_array($period, ['weekly', 'monthly', 'yearly'], true)) {
            $period = 'monthly';
        }

        $availableYears = self::availableYears();
        $latestOrderDate = Order::query()->latest('created_at')->value('created_at');
        $fallbackDate = $latestOrderDate ? Carbon::parse($latestOrderDate) : Carbon::now();

        if ($request->filled('date')) {
            $baseDate = Carbon::parse((string) $request->get('date'));
        } else {
            $selectedYear = (int) $request->integer('year', (int) $fallbackDate->year);
            $selectedMonth = (int) $request->integer('month', (int) $fallbackDate->month);

            if ($availableYears->isNotEmpty() && !$availableYears->contains($selectedYear)) {
                $selectedYear = (int) $availableYears->first();
            }

            if ($selectedMonth < 1 || $selectedMonth > 12) {
                $selectedMonth = (int) $fallbackDate->month;
            }

            $baseDate = Carbon::create($selectedYear, $selectedMonth, 1);
        }

        [$startDate, $endDate, $rangeLabel] = self::resolveWindow($period, $baseDate);
        $rows = self::decorateRows(self::buildReportQuery($startDate, $endDate)->get());

        $summary = self::buildSummary($rows, $rangeLabel, $startDate, $endDate);
        $crmOverview = self::buildCrmOverview($summary);
        $trend = self::buildTrend($rows, $period, $startDate, $endDate);
        $brandPerformance = self::buildBrandPerformance($rows);
        $teamPerformance = self::buildTeamPerformance($rows);
        $paymentMix = self::buildPaymentMix($rows);
        $analysis = self::buildAnalysis($summary, $brandPerformance, $teamPerformance, $paymentMix);
        $recommendations = self::buildRecommendations($summary, $brandPerformance, $teamPerformance, $paymentMix);

        return [
            'period' => $period,
            'base_date' => $baseDate,
            'selected_year' => (int) $baseDate->year,
            'selected_month' => (int) $baseDate->month,
            'available_years' => $availableYears,
            'range_label' => $rangeLabel,
            'generated_at' => now(),
            'rows' => $rows,
            'summary' => $summary,
            'crm_overview' => $crmOverview,
            'trend' => $trend,
            'brand_performance' => $brandPerformance,
            'team_performance' => $teamPerformance,
            'payment_mix' => $paymentMix,
            'analysis' => $analysis,
            'recommendations' => $recommendations,
        ];
    }

    private static function buildReportQuery(Carbon $startDate, Carbon $endDate)
    {
        $driver = DB::getDriverName();
        $carNameExpr = $driver === 'sqlite'
            ? "cars.merk || ' ' || cars.tipe"
            : "CONCAT(cars.merk, ' ', cars.tipe)";

        return Order::query()
            ->select([
                'orders.id',
                'orders.created_at',
                DB::raw('DATE(orders.created_at) as tanggal'),
                'users.name as customer',
                DB::raw("$carNameExpr as mobil"),
                'cars.kode_unit',
                'cars.merk',
                'cars.tipe',
                'cars.created_at as unit_created_at',
                'orders.payment_method as metode_pembayaran',
                'payments.method as metode_bayar',
                'orders.transaction_channel',
                'orders.sales_flow',
                'orders.total as nominal',
                'payments.status as status_pembayaran',
                'orders.status as status_order',
                'orders.handled_role',
                'handlers.name as handled_by_name',
            ])
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('cars', 'cars.id', '=', 'orders.car_id')
            ->leftJoin('payments', 'payments.order_id', '=', 'orders.id')
            ->leftJoin('users as handlers', 'handlers.id', '=', 'orders.handled_by')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderByDesc('orders.created_at');
    }

    /**
     * Tambahkan label siap pakai agar Blade dan export tidak perlu mengulang logika yang sama.
     *
     * @param Collection<int, object> $rows
     * @return Collection<int, object>
     */
    private static function decorateRows(Collection $rows): Collection
    {
        return $rows->map(function ($row) {
            $transactionAt = Carbon::parse($row->created_at);

            $row->tanggal_label = $transactionAt->format('d-m-Y');
            $row->tahun_transaksi = (int) $transactionAt->format('Y');
            $row->jam_transaksi = $transactionAt->format('H:i:s');
            $row->waktu_transaksi_label = $transactionAt->format('d-m-Y H:i:s');
            $row->metode_beli_label = TransactionLabelFormatter::purchaseMethod((string) $row->metode_pembayaran);
            $row->metode_bayar_label = TransactionLabelFormatter::paymentMethod($row->metode_bayar);
            $row->transaction_channel_label = TransactionLabelFormatter::transactionChannel((string) $row->transaction_channel);
            $row->sales_flow_label = TransactionLabelFormatter::salesFlow((string) $row->sales_flow);

            return $row;
        });
    }

    /**
     * @return Collection<int, int>
     */
    private static function availableYears(): Collection
    {
        $driver = DB::getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "CAST(strftime('%Y', created_at) AS INTEGER)"
            : 'YEAR(created_at)';

        $years = Order::query()
            ->selectRaw($yearExpr . ' as report_year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('report_year')
            ->pluck('report_year')
            ->map(fn ($year) => (int) $year)
            ->filter(fn (int $year) => $year > 0)
            ->values();

        return $years->isNotEmpty()
            ? $years
            : collect([(int) now()->year]);
    }

    /**
     * @return array{0:Carbon,1:Carbon,2:string}
     */
    private static function resolveWindow(string $period, Carbon $baseDate): array
    {
        if ($period === 'weekly') {
            $start = $baseDate->copy()->startOfWeek();
            $end = $baseDate->copy()->endOfWeek();

            return [$start, $end, 'Minggu ' . $start->format('d M Y') . ' - ' . $end->format('d M Y')];
        }

        if ($period === 'yearly') {
            $start = $baseDate->copy()->startOfYear();
            $end = $baseDate->copy()->endOfYear();

            return [$start, $end, 'Laporan tahun ' . $baseDate->format('Y')];
        }

        $start = $baseDate->copy()->startOfMonth();
        $end = $baseDate->copy()->endOfMonth();

        return [$start, $end, 'Laporan ' . $baseDate->translatedFormat('F Y')];
    }

    /**
     * @param Collection<int, object> $rows
     * @return array<string, mixed>
     */
    private static function buildSummary(Collection $rows, string $rangeLabel, Carbon $startDate, Carbon $endDate): array
    {
        $totalOrders = $rows->count();
        $omzet = (float) $rows->sum(fn ($row) => (float) $row->nominal);
        $completedOrders = $rows->where('status_order', 'completed')->count();
        $paidOrders = $rows->filter(function ($row) {
            return in_array((string) $row->status_pembayaran, ['verified', 'paid'], true);
        })->count();
        $averageOrder = $totalOrders > 0 ? $omzet / $totalOrders : 0;
        $cashOrders = $rows->filter(fn ($row) => $row->metode_beli_label === 'Cash')->count();
        $creditOrders = $rows->filter(fn ($row) => $row->metode_beli_label === 'Kredit')->count();
        $onlineOrders = $rows->where('transaction_channel', 'online')->count();
        $offlineOrders = $rows->where('transaction_channel', 'offline')->count();
        $withTestDriveOrders = $rows->where('sales_flow', 'after_test_drive')->count();
        $withoutTestDriveOrders = $rows->where('sales_flow', 'direct_purchase')->count();

        $hasOffersTable = DB::getSchemaBuilder()->hasTable('offers');
        $hasTestDrivesTable = DB::getSchemaBuilder()->hasTable('test_drives');
        $offerLeads = $hasOffersTable
            ? DB::table('offers')->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->count()
            : 0;
        $testDriveLeads = $hasTestDrivesTable
            ? DB::table('test_drives')->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->count()
            : 0;
        $totalLeads = $totalOrders + $offerLeads + $testDriveLeads;

        $fastestSellingUnit = $rows
            ->filter(fn ($row) => !empty($row->unit_created_at) && in_array((string) $row->status_order, ['completed', 'paid'], true))
            ->map(function ($row) {
                $listedAt = Carbon::parse($row->unit_created_at);
                $soldAt = Carbon::parse($row->created_at);
                $days = max(0, $listedAt->diffInDays($soldAt));

                return [
                    'label' => trim((string) $row->mobil) . (filled($row->kode_unit) ? ' (' . $row->kode_unit . ')' : ''),
                    'days' => $days,
                ];
            })
            ->sortBy('days')
            ->first();

        return [
            'range_label' => $rangeLabel,
            'total_orders' => $totalOrders,
            'omzet' => $omzet,
            'completed_orders' => $completedOrders,
            'paid_orders' => $paidOrders,
            'average_order' => $averageOrder,
            'cash_orders' => $cashOrders,
            'credit_orders' => $creditOrders,
            'cash_share' => $totalOrders > 0 ? round(($cashOrders / $totalOrders) * 100, 1) : 0,
            'credit_share' => $totalOrders > 0 ? round(($creditOrders / $totalOrders) * 100, 1) : 0,
            'online_orders' => $onlineOrders,
            'offline_orders' => $offlineOrders,
            'online_share' => $totalOrders > 0 ? round(($onlineOrders / $totalOrders) * 100, 1) : 0,
            'offline_share' => $totalOrders > 0 ? round(($offlineOrders / $totalOrders) * 100, 1) : 0,
            'with_test_drive_orders' => $withTestDriveOrders,
            'without_test_drive_orders' => $withoutTestDriveOrders,
            'with_test_drive_share' => $totalOrders > 0 ? round(($withTestDriveOrders / $totalOrders) * 100, 1) : 0,
            'without_test_drive_share' => $totalOrders > 0 ? round(($withoutTestDriveOrders / $totalOrders) * 100, 1) : 0,
            'offer_leads' => $offerLeads,
            'test_drive_leads' => $testDriveLeads,
            'total_leads' => $totalLeads,
            'conversion_rate' => $totalLeads > 0 ? round(($completedOrders / $totalLeads) * 100, 1) : 0,
            'fastest_selling_unit' => $fastestSellingUnit['label'] ?? '-',
            'fastest_selling_days' => $fastestSellingUnit['days'] ?? null,
            'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0,
        ];
    }

    /**
     * @param array<string, mixed> $summary
     * @return array<int, array<string, mixed>>
     */
    private static function buildCrmOverview(array $summary): array
    {
        return [
            [
                'label' => 'Transaksi Langsung Melalui Website',
                'value' => number_format((int) ($summary['online_orders'] ?? 0)),
                'note' => ($summary['online_share'] ?? 0) . '% dari seluruh transaksi pada periode ini.',
            ],
            [
                'label' => 'Transaksi Input Supervisor',
                'value' => number_format((int) ($summary['offline_orders'] ?? 0)),
                'note' => ($summary['offline_share'] ?? 0) . '% berasal dari pencatatan showroom oleh supervisor.',
            ],
            [
                'label' => 'Metode Cash',
                'value' => number_format((int) ($summary['cash_orders'] ?? 0)),
                'note' => 'Pembelian cash mencapai ' . ($summary['cash_share'] ?? 0) . '% dari seluruh transaksi pada periode ini.',
            ],
            [
                'label' => 'Metode Kredit',
                'value' => number_format((int) ($summary['credit_orders'] ?? 0)),
                'note' => 'Pembelian kredit mencapai ' . ($summary['credit_share'] ?? 0) . '% dari seluruh transaksi pada periode ini.',
            ],
        ];
    }

    /**
     * @param Collection<int, object> $rows
     * @return Collection<int, array<string, mixed>>
     */
    private static function buildTrend(Collection $rows, string $period, Carbon $startDate, Carbon $endDate): Collection
    {
        $grouped = $rows->groupBy(function ($row) use ($period) {
            $date = Carbon::parse($row->created_at);

            return $period === 'yearly'
                ? $date->format('Y-m')
                : $date->format('Y-m-d');
        });

        $labels = collect();
        if ($period === 'yearly') {
            for ($month = 1; $month <= 12; $month++) {
                $labels->push($startDate->copy()->month($month)->format('Y-m'));
            }
        } else {
            $labels = collect(CarbonPeriod::create($startDate, $endDate))
                ->map(fn (Carbon $date) => $date->format('Y-m-d'));
        }

        $maxRevenue = 0.0;
        $items = $labels->map(function (string $key) use ($grouped, $period, &$maxRevenue) {
            $bucket = $grouped->get($key, collect());
            $revenue = (float) $bucket->sum(fn ($row) => (float) $row->nominal);
            $orders = $bucket->count();
            $label = $period === 'yearly'
                ? Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y')
                : Carbon::parse($key)->translatedFormat('d M');

            $maxRevenue = max($maxRevenue, $revenue);

            return [
                'key' => $key,
                'label' => $label,
                'orders' => $orders,
                'revenue' => $revenue,
            ];
        });

        return $items->map(function (array $item) use ($maxRevenue) {
            $item['revenue_visual'] = self::visualBar($item['revenue'], $maxRevenue);
            return $item;
        });
    }

    /**
     * @param Collection<int, object> $rows
     * @return Collection<int, array<string, mixed>>
     */
    private static function buildBrandPerformance(Collection $rows): Collection
    {
        $groups = $rows->groupBy(fn ($row) => trim((string) $row->merk) !== '' ? (string) $row->merk : 'Tidak diketahui');
        $totalOrders = max($rows->count(), 1);
        $maxRevenue = max((float) $groups->map(fn (Collection $items) => $items->sum('nominal'))->max(), 1);

        return $groups
            ->map(function (Collection $items, string $brand) use ($totalOrders, $maxRevenue) {
                $units = $items->count();
                $revenue = (float) $items->sum(fn ($row) => (float) $row->nominal);
                return [
                    'brand' => $brand,
                    'units' => $units,
                    'revenue' => $revenue,
                    'share' => round(($units / $totalOrders) * 100, 1),
                    'visual' => self::visualBar($revenue, $maxRevenue),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();
    }

    /**
     * @param Collection<int, object> $rows
     * @return Collection<int, array<string, mixed>>
     */
    private static function buildTeamPerformance(Collection $rows): Collection
    {
        $groups = $rows->groupBy(function ($row) {
            return match ((string) $row->handled_role) {
                'marketing' => 'Marketing',
                'supervisor' => 'Supervisor',
                default => 'Belum Ditandai',
            };
        });

        $totalOrders = max($rows->count(), 1);
        $maxRevenue = max((float) $groups->map(fn (Collection $items) => $items->sum('nominal'))->max(), 1);

        return $groups
            ->map(function (Collection $items, string $team) use ($totalOrders, $maxRevenue) {
                $orders = $items->count();
                $revenue = (float) $items->sum(fn ($row) => (float) $row->nominal);

                return [
                    'team' => $team,
                    'orders' => $orders,
                    'revenue' => $revenue,
                    'share' => round(($orders / $totalOrders) * 100, 1),
                    'visual' => self::visualBar($revenue, $maxRevenue),
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }

    /**
     * @param Collection<int, object> $rows
     * @return Collection<int, array<string, mixed>>
     */
    private static function buildPaymentMix(Collection $rows): Collection
    {
        $groups = $rows->groupBy(fn ($row) => $row->metode_beli_label);
        $totalOrders = max($rows->count(), 1);
        $maxOrders = max((int) $groups->map(fn (Collection $items) => $items->count())->max(), 1);

        return $groups
            ->map(function (Collection $items, string $label) use ($totalOrders, $maxOrders) {
                $orders = $items->count();
                return [
                    'method' => $label,
                    'orders' => $orders,
                    'share' => round(($orders / $totalOrders) * 100, 1),
                    'visual' => self::visualBar($orders, $maxOrders),
                ];
            })
            ->sortByDesc('orders')
            ->values();
    }

    /**
     * @param array<string, mixed> $summary
     * @param Collection<int, array<string, mixed>> $brandPerformance
     * @param Collection<int, array<string, mixed>> $teamPerformance
     * @param Collection<int, array<string, mixed>> $paymentMix
     * @return array<int, array{title:string, detail:string}>
     */
    private static function buildAnalysis(
        array $summary,
        Collection $brandPerformance,
        Collection $teamPerformance,
        Collection $paymentMix
    ): array {
        $topBrand = $brandPerformance->first();
        $topMethod = $paymentMix->first();

        return [
            [
                'title' => 'Sorotan penjualan',
                'detail' => $summary['total_orders'] > 0
                    ? sprintf(
                        'Pada %s, tercatat %d transaksi dengan total penjualan %s. Rata-rata nilai per transaksi ada di %s.',
                        $summary['range_label'],
                        $summary['total_orders'],
                        CurrencyFormatter::rupiah($summary['omzet']),
                        CurrencyFormatter::rupiah($summary['average_order'])
                    )
                    : sprintf('Belum ada transaksi yang tercatat pada periode %s.', $summary['range_label']),
            ],
            [
                'title' => 'Merk penggerak utama',
                'detail' => $topBrand
                    ? sprintf(
                        'Merk %s paling menonjol pada periode ini. Totalnya %d unit terjual, dengan porsi %s%% dari seluruh transaksi.',
                        $topBrand['brand'],
                        $topBrand['units'],
                        $topBrand['share']
                    )
                    : 'Belum ada merk dominan karena belum ada penjualan pada periode ini.',
            ],
            [
                'title' => 'Pola metode pembelian',
                'detail' => $topMethod
                    ? sprintf(
                        'Metode pembelian yang paling sering dipakai adalah %s, yaitu %s%% dari total transaksi pada periode ini.',
                        $topMethod['method'],
                        $topMethod['share']
                    )
                    : 'Belum ada cukup data untuk membaca pola metode pembelian pada periode ini.',
            ],
            [
                'title' => 'Pembelian customer',
                'detail' => sprintf(
                    'Transaksi dengan test drive tercatat %d order, sedangkan tanpa test drive %d order. Conversion rate lead ke transaksi selesai saat ini berada di %s%%.',
                    (int) ($summary['with_test_drive_orders'] ?? 0),
                    (int) ($summary['without_test_drive_orders'] ?? 0),
                    $summary['conversion_rate'] ?? 0
                ),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $summary
     * @param Collection<int, array<string, mixed>> $brandPerformance
     * @param Collection<int, array<string, mixed>> $teamPerformance
     * @param Collection<int, array<string, mixed>> $paymentMix
     * @return array<int, array{title:string, detail:string}>
     */
    private static function buildRecommendations(
        array $summary,
        Collection $brandPerformance,
        Collection $teamPerformance,
        Collection $paymentMix
    ): array {
        $recommendations = [];
        $topBrand = $brandPerformance->first();
        $topTeam = $teamPerformance->first();
        $cashShare = (float) ($summary['cash_share'] ?? 0);
        $creditShare = (float) ($summary['credit_share'] ?? 0);

        if ((int) $summary['total_orders'] === 0) {
            return [
                [
                    'title' => 'Aktifkan lead generation',
                    'detail' => 'Fokuskan marketing pada pencarian lead baru, follow-up customer lama, dan promosi unit yang paling siap jual agar periode berikutnya kembali menghasilkan transaksi.',
                ],
            ];
        }

        if ($topBrand && (float) $topBrand['share'] >= 45) {
            $recommendations[] = [
                'title' => 'Jaga stok merk unggulan',
                'detail' => sprintf(
                    'Permintaan untuk merk %s sedang kuat. Jaga ketersediaan unit sejenis, lalu dorong juga promosi untuk merk lain agar penjualan lebih seimbang.',
                    $topBrand['brand']
                ),
            ];
        }

        if ($creditShare >= 45) {
            $recommendations[] = [
                'title' => 'Perkuat materi pembiayaan',
                'detail' => 'Karena transaksi kredit cukup dominan, siapkan simulasi cicilan, pilihan tenor, dan alur approval yang cepat supaya proses closing lebih lancar.',
            ];
        } elseif ($cashShare >= 60) {
            $recommendations[] = [
                'title' => 'Dorong upsell transaksi cash',
                'detail' => 'Pembayaran cash sudah dominan. Manfaatkan momentum ini untuk menawarkan paket tambahan, aksesoris, atau upgrade unit dengan nilai transaksi lebih tinggi.',
            ];
        }

        if ($topTeam && $topTeam['team'] === 'Supervisor' && (float) $topTeam['share'] >= 70) {
            $recommendations[] = [
                'title' => 'Seimbangkan distribusi eksekusi',
                'detail' => 'Kontribusi supervisor masih sangat dominan. Bagi lebih banyak follow-up dan eksekusi ke marketing agar kerja tim lebih merata dan supervisor bisa fokus mengawasi.',
            ];
        }

        if ((float) $summary['completion_rate'] < 85) {
            $recommendations[] = [
                'title' => 'Percepat penyelesaian order',
                'detail' => 'Masih ada order yang belum selesai penuh. Cek hambatan di pembayaran, verifikasi dokumen, dan komunikasi antar tim agar status lebih cepat selesai.',
            ];
        }

        if ($recommendations === []) {
            $recommendations[] = [
                'title' => 'Pertahankan ritme performa',
                'detail' => 'Kinerja periode ini sudah cukup stabil. Langkah berikutnya adalah menjaga kualitas follow-up, memperbarui stok unit yang cepat laku, dan meneruskan promosi pada merk yang sedang kuat.',
            ];
        }

        return array_slice($recommendations, 0, 4);
    }

    private static function visualBar(float|int $value, float|int $max, int $length = 12): string
    {
        if ($value <= 0 || $max <= 0) {
            return '';
        }

        $filled = max(1, (int) round(((float) $value / (float) $max) * $length));

        return str_repeat('|', min($filled, $length));
    }
}
