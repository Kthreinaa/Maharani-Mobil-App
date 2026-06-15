<?php

namespace App\Support;

class TransactionLabelFormatter
{
    /**
     * Pusat label transaksi agar istilah di dashboard, laporan, dan dokumen tetap sama.
     */
    public static function purchaseMethod(?string $method): string
    {
        return match ((string) $method) {
            'cash', 'transfer' => 'Cash',
            'credit', 'va' => 'Kredit',
            default => 'Belum dipilih',
        };
    }

    /**
     * Metode bayar dibedakan dari metode beli:
     * - Tunai untuk pembayaran langsung cash
     * - Transfer untuk transfer bank, VA, atau pembayaran kredit yang masuk lewat leasing/bank
     */
    public static function paymentMethod(?string $method): string
    {
        return match ((string) $method) {
            'cash' => 'Tunai',
            'transfer', 'va', 'credit' => 'Transfer',
            default => 'Belum dipilih',
        };
    }

    public static function transactionChannel(?string $channel): string
    {
        return match ((string) $channel) {
            'offline' => 'Datang ke Showroom',
            default => 'Online Website',
        };
    }

    public static function salesFlow(?string $salesFlow): string
    {
        return match ((string) $salesFlow) {
            'after_test_drive' => 'Dengan Test Drive',
            'offline_showroom' => 'Datang ke Showroom + Test Drive',
            default => 'Tanpa Test Drive',
        };
    }
}
