<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TransactionDocumentProfile
{
    /**
     * @return array<string, mixed>
     */
    public static function build(Order $order): array
    {
        $buyer = $order->user;
        $payment = $order->payment;
        $issuedAt = $order->approved_at ?? $order->updated_at ?? now();
        $verifiedAt = $payment?->verified_at ?? $payment?->paid_at ?? $issuedAt;
        $showroom = config('documents.showroom', []);
        $owner = config('documents.owner', []);

        $buyerPhone = self::resolveBuyerPhone($order);
        $buyerEmail = self::resolveBuyerEmail($order);
        $creditDpAmount = (float) ($order->credit_dp_amount ?? 0);
        $leasingSettlementAmount = max((float) $order->total - $creditDpAmount, 0);
        $gatewayReference = (string) ($payment?->gateway_reference ?: $payment?->gateway_external_id ?: 'MM-PAY-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT));
        $verificationReference = self::formatVerificationReference(
            hash('sha256', implode('|', [
                (string) $order->id,
                (string) $order->user_id,
                (string) $order->car_id,
                (string) $order->total,
                optional($verifiedAt)->format('Y-m-d H:i:s'),
                (string) $gatewayReference,
            ]))
        );

        return [
            'showroom_name' => (string) ($showroom['name'] ?? 'Maharani Mobil'),
            'showroom_phone' => (string) ($showroom['phone'] ?? '-'),
            'showroom_whatsapp' => (string) ($showroom['whatsapp'] ?? '-'),
            'showroom_address' => (string) ($showroom['address'] ?? '-'),
            'owner_name' => (string) ($owner['name'] ?? 'Pak Rarendra'),
            'owner_title' => (string) ($owner['title'] ?? 'Pemilik Maharani Mobil'),
            'owner_signature_data_uri' => self::assetToDataUri((string) ($owner['signature_asset'] ?? '')),
            'verification_seal_data_uri' => self::assetToDataUri((string) Arr::get(config('documents.verification', []), 'seal_asset', '')),
            'buyer_phone' => $buyerPhone,
            'buyer_email' => $buyerEmail,
            'buyer_identity_statement' => self::buildBuyerIdentityStatement($order, $buyerEmail, $buyerPhone),
            'verification_reference' => $verificationReference,
            'payment_reference' => $gatewayReference,
            'verification_timestamp' => $verifiedAt,
            'payment_channel' => $payment?->gateway_channel_label ?? strtoupper((string) ($payment?->method ?? $order->payment_method ?? 'transfer')),
            'payment_validator' => $payment?->handled_role === 'gateway'
                ? 'Gateway pembayaran otomatis'
                : ($payment?->verifier?->name ?? $order->approvedBy?->name ?? 'Validasi internal Maharani Mobil'),
            'is_credit_purchase' => $order->is_credit_purchase,
            'credit_dp_amount' => $creditDpAmount,
            'leasing_settlement_amount' => $leasingSettlementAmount,
            'leasing_partner' => $order->leasing_partner_label,
            'customer_consent_note' => 'Persetujuan pembeli dibuktikan melalui akun customer terdaftar, data identitas akun, catatan transaksi, dan validasi pembayaran pada sistem Maharani Mobil. Salinan digital ini tidak memerlukan tanda tangan basah pembeli.',
            'paperless_note' => 'Dokumen ini diterbitkan secara elektronik sebagai bagian dari proses paperless Maharani Mobil dan dapat diverifikasi melalui referensi dokumen, data order, dan riwayat pembayaran pada sistem.',
        ];
    }

    private static function resolveBuyerPhone(Order $order): string
    {
        $candidates = [
            $order->user?->phone,
            Arr::get($order->payment?->gateway_payload ?? [], 'customer.mobile_number'),
            self::extractLineValue((string) $order->notes, 'WhatsApp'),
            self::extractLineValue((string) $order->notes, 'Telepon'),
        ];

        foreach ($candidates as $candidate) {
            if (filled($candidate)) {
                return trim((string) $candidate);
            }
        }

        return 'Belum dilengkapi pada akun customer';
    }

    private static function resolveBuyerEmail(Order $order): string
    {
        if (filled($order->user?->email)) {
            return trim((string) $order->user?->email);
        }

        return 'Belum dilengkapi pada akun customer';
    }

    private static function extractLineValue(string $notes, string $label): ?string
    {
        if ($notes === '') {
            return null;
        }

        preg_match('/^' . preg_quote($label, '/') . ':\s*(.+)$/mi', $notes, $matches);

        return isset($matches[1]) ? trim((string) $matches[1]) : null;
    }

    private static function buildBuyerIdentityStatement(Order $order, string $email, string $phone): string
    {
        $buyerName = trim((string) ($order->user?->name ?? 'Customer Maharani Mobil'));

        return sprintf(
            'Dokumen ini diterbitkan atas nama %s dengan identitas akun %s dan nomor telepon %s, berdasarkan order %s yang tercatat pada sistem Maharani Mobil.',
            $buyerName,
            $email,
            $phone,
            $order->order_reference
        );
    }

    private static function formatVerificationReference(string $hash): string
    {
        return Str::upper(collect(str_split(substr($hash, 0, 24), 6))->implode('-'));
    }

    private static function assetToDataUri(string $assetPath): ?string
    {
        if ($assetPath === '') {
            return null;
        }

        $path = public_path(ltrim($assetPath, '/'));
        if (!is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
