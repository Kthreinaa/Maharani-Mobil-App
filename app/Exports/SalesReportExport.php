<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->map(function ($row) {
            return [
                'tanggal' => $row->tanggal,
                'customer' => $row->customer,
                'mobil' => $row->mobil,
                'merk' => $row->merk,
                'tipe' => $row->tipe,
                'metode_pembayaran' => $row->metode_pembayaran,
                'nominal' => $row->nominal,
                'status_pembayaran' => $row->status_pembayaran,
                'status_order' => $row->status_order,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Customer',
            'Mobil',
            'Merk',
            'Tipe',
            'Metode Pembayaran',
            'Nominal',
            'Status Pembayaran',
            'Status Order',
        ];
    }
}
