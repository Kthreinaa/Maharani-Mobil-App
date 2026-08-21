<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Support\SalesReportBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $report = SalesReportBuilder::build($request);
        return view('reports.supervisor-index', compact('report'));
    }

    public function exportPdf(Request $request)
    {
        $report = SalesReportBuilder::build($request);
        $pdf = Pdf::loadView('reports.pdf.sales', compact('report'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('sales-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $report = SalesReportBuilder::build($request);
        return Excel::download(new SalesReportExport($report), 'sales-report.xlsx');
    }
}
