<?php

namespace App\Exports;

use App\Exports\Sheets\ArrayReportSheet;
use App\Support\CurrencyFormatter;
use App\Support\TransactionLabelFormatter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesReportExport implements WithMultipleSheets
{
    /**
     * @param array<string, mixed> $report
     */
    public function __construct(private array $report)
    {
    }

    public function sheets(): array
    {
        return [
            new ArrayReportSheet('Ringkasan', $this->overviewRows()),
            new ArrayReportSheet('Aktivitas Pembelian', $this->activityRows()),
            new ArrayReportSheet('Tren Penjualan', $this->trendRows()),
            new ArrayReportSheet('Performa Merk', $this->brandRows()),
            new ArrayReportSheet('Metode Beli', $this->paymentRows()),
            new ArrayReportSheet('Detail Transaksi', $this->transactionRows()),
        ];
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function overviewRows(): array
    {
        $summary = $this->report['summary'];
        $analysis = $this->report['analysis'];

        return [
            ['Laporan Penjualan Maharani Mobil'],
            ['Periode', $summary['range_label']],
            ['Dibuat pada', $this->report['generated_at']->format('d M Y H:i')],
            [],
            ['KPI Utama', 'Nilai'],
            ['Total Transaksi', (int) $summary['total_orders']],
            ['Transaksi Selesai', (int) $summary['completed_orders']],
            ['Pembayaran Terverifikasi', (int) $summary['paid_orders']],
            ['Omzet', CurrencyFormatter::rupiah($summary['omzet'])],
            ['Rata-rata Nilai Transaksi', CurrencyFormatter::rupiah($summary['average_order'])],
            ['Transaksi Langsung Melalui Website', (int) $summary['online_orders']],
            ['Transaksi Input Supervisor', (int) $summary['offline_orders']],
            ['Metode Cash', (int) $summary['cash_orders']],
            ['Metode Kredit', (int) $summary['credit_orders']],
            ['Porsi Website', $summary['online_share'] . '%'],
            ['Porsi Input Supervisor', $summary['offline_share'] . '%'],
            ['Porsi Cash', $summary['cash_share'] . '%'],
            ['Porsi Kredit', $summary['credit_share'] . '%'],
            ['Tingkat Selesai', $summary['completion_rate'] . '%'],
            [],
            ['Ringkasan Periode'],
            ...collect($analysis)->map(fn (array $item) => [$item['title'], $item['detail']])->all(),
        ];
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function activityRows(): array
    {
        $rows = [['Kategori', 'Nilai', 'Keterangan']];

        foreach ($this->report['crm_overview'] as $item) {
            $rows[] = [
                $item['label'],
                $item['value'],
                $item['note'],
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function trendRows(): array
    {
        $rows = [['Periode', 'Jumlah Transaksi', 'Omzet', 'Bar Omzet']];

        foreach ($this->report['trend'] as $item) {
            $rows[] = [
                $item['label'],
                (int) $item['orders'],
                CurrencyFormatter::rupiah($item['revenue']),
                $item['revenue_visual'],
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function brandRows(): array
    {
        $rows = [['Merk', 'Unit Terjual', 'Omzet', 'Porsi', 'Bar']];

        foreach ($this->report['brand_performance'] as $item) {
            $rows[] = [
                $item['brand'],
                (int) $item['units'],
                CurrencyFormatter::rupiah($item['revenue']),
                $item['share'] . '%',
                $item['visual'],
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function paymentRows(): array
    {
        $rows = [['Metode', 'Jumlah Transaksi', 'Porsi', 'Bar']];

        foreach ($this->report['payment_mix'] as $item) {
            $rows[] = [
                $item['method'],
                (int) $item['orders'],
                $item['share'] . '%',
                $item['visual'],
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function transactionRows(): array
    {
        $rows = [[
            'Tanggal',
            'Customer',
            'Kode Unit',
            'Mobil',
            'Merk',
            'Tipe',
            'Metode Beli',
            'Metode Bayar',
            'Nominal',
            'Status Pembayaran',
            'Status Order',
            'Dikelola Oleh',
            'PIC Internal',
        ]];

        /** @var Collection<int, object> $transactions */
        $transactions = $this->report['rows'];
        foreach ($transactions as $row) {
            $rows[] = [
                (string) $row->tanggal,
                (string) $row->customer,
                (string) $row->kode_unit,
                (string) $row->mobil,
                (string) $row->merk,
                (string) $row->tipe,
                TransactionLabelFormatter::purchaseMethod((string) $row->metode_pembayaran),
                TransactionLabelFormatter::paymentMethod($row->metode_bayar),
                CurrencyFormatter::rupiah($row->nominal),
                (string) $row->status_pembayaran,
                (string) $row->status_order,
                self::handledRoleLabel((string) $row->handled_role),
                (string) ($row->handled_by_name ?: '-'),
            ];
        }

        return $rows;
    }

    private static function handledRoleLabel(string $role): string
    {
        return match ($role) {
            'marketing' => 'Marketing',
            'supervisor' => 'Supervisor',
            default => 'Belum Ditandai',
        };
    }
}
