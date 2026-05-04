<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $rows = $this->buildReportQuery($request)->get();
        return view('reports.index', compact('rows'));
    }

    public function exportPdf(Request $request)
    {
        $rows = $this->buildReportQuery($request)->get();
        $pdf = Pdf::loadView('reports.pdf.sales', compact('rows'));
        return $pdf->download('sales-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $rows = $this->buildReportQuery($request)->get();
        return Excel::download(new SalesReportExport($rows), 'sales-report.xlsx');
    }

    private function buildReportQuery(Request $request)
    {
        $period = $request->get('period', 'monthly'); // weekly|monthly|yearly
        $date = $request->get('date');

        $driver = DB::getDriverName();
        $carNameExpr = $driver === 'sqlite'
            ? "cars.merk || ' ' || cars.tipe"
            : "CONCAT(cars.merk, ' ', cars.tipe)";

        $query = Order::query()
            ->select([
                DB::raw('DATE(orders.created_at) as tanggal'),
                'users.name as customer',
                DB::raw("$carNameExpr as mobil"),
                'cars.merk',
                'cars.tipe',
                'orders.payment_method as metode_pembayaran',
                'orders.total as nominal',
                'payments.status as status_pembayaran',
                'orders.status as status_order',
            ])
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('cars', 'cars.id', '=', 'orders.car_id')
            ->leftJoin('payments', 'payments.order_id', '=', 'orders.id');

        if ($date) {
            $base = Carbon::parse($date);
        } else {
            $base = Carbon::now();
        }

        if ($period === 'weekly') {
            $query->whereBetween('orders.created_at', [$base->startOfWeek(), $base->endOfWeek()]);
        } elseif ($period === 'yearly') {
            $query->whereYear('orders.created_at', $base->year);
        } else {
            $query->whereYear('orders.created_at', $base->year)
                ->whereMonth('orders.created_at', $base->month);
        }

        return $query->orderByDesc('orders.created_at');
    }
}
