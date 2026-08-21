@php
  use App\Support\CurrencyFormatter;

  $car = $order->car;
  $buyer = $order->user;
  $profile = $documentProfile ?? [];
  $documentNumber = 'KWT-' . now()->format('Y') . '-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
  $issuedAt = $order->approved_at ?? $order->updated_at ?? now();
  $verificationAt = $profile['verification_timestamp'] ?? null;
  $isCreditPurchase = (bool) ($profile['is_credit_purchase'] ?? false);
  $creditDpAmount = (float) ($profile['credit_dp_amount'] ?? 0);
  $leasingSettlementAmount = (float) ($profile['leasing_settlement_amount'] ?? 0);
  $unitDescription = implode(', ', collect([
      trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '')),
      $car?->tahun ? 'Tahun ' . $car->tahun : null,
      $car?->warna ? 'Warna ' . $car->warna : null,
      $car?->transmisi ? 'Transmisi ' . $car->transmisi : null,
      isset($car?->kilometer) ? 'Kilometer ' . number_format((int) $car->kilometer, 0, ',', '.') . ' KM' : null,
      $car?->kode_unit ? 'Kode Unit ' . $car->kode_unit : null,
  ])->filter()->all());
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $documentTitle }}</title>
  <style>
    @page { margin: 14px 14px 18px; }
    body { color: #0f172a; font-family: DejaVu Sans, sans-serif; font-size: 9.4px; line-height: 1.36; }
    .page { border: 1px solid #dbe3ef; padding: 12px 12px 10px; position: relative; overflow: hidden; }
    .watermark {
      position: fixed;
      color: rgba(11, 26, 64, 0.055);
      font-size: 19px;
      font-weight: 800;
      letter-spacing: .1em;
      transform: rotate(-32deg);
      white-space: nowrap;
      z-index: 0;
    }
    .content { position: relative; z-index: 1; }
    .top { width: 100%; border-bottom: 4px solid #0b1a40; padding-bottom: 8px; }
    .top td { vertical-align: top; }
    .brand { font-size: 21px; font-weight: 800; color: #0b1a40; letter-spacing: .02em; }
    .subtitle { color: #475569; margin-top: 2px; font-size: 9px; }
    .contact { text-align: right; color: #334155; font-size: 9px; line-height: 1.45; }
    .heading-table { width: 100%; margin-top: 8px; }
    .heading-table td { vertical-align: middle; }
    .heading { font-size: 17px; font-weight: 800; text-transform: uppercase; color: #0b1a40; }
    .muted { color: #64748b; }
    .seal { width: 56px; height: 56px; }
    .receipt-meta { width: 100%; margin-top: 8px; }
    .receipt-meta td { vertical-align: top; }
    .meta-card { border: 1px solid #dbe3ef; background: #fbfdff; padding: 8px 10px; min-height: 78px; }
    .meta-line { margin-top: 3px; }
    .meta-line:first-child { margin-top: 0; }
    .label { color: #64748b; }
    .amount-box { margin-top: 8px; border: 1px solid #dbe3ef; background: #f8fbff; padding: 8px 10px; }
    .amount-main { font-size: 15px; font-weight: 800; color: #0b1a40; margin-top: 4px; }
    .section { margin-top: 8px; border: 1px solid #dbe3ef; padding: 8px 10px; background: #fff; }
    .section-title { font-size: 10px; font-weight: 800; color: #0b1a40; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
    .narrative { margin: 0; }
    .summary-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .summary-table td { border-top: 1px solid #e2e8f0; padding: 4px 0; vertical-align: top; }
    .summary-table tr:first-child td { border-top: none; padding-top: 0; }
    .summary-label { width: 34%; color: #64748b; }
    .signature-card { border: 1px solid #dbe3ef; padding: 8px 10px; min-height: 86px; margin-top: 8px; }
    .signature-head { font-weight: 800; color: #0b1a40; margin-bottom: 4px; text-transform: uppercase; font-size: 10px; letter-spacing: .08em; }
    .materai-badge {
      display: inline-block;
      margin: 6px 0 4px;
      padding: 8px 12px;
      border: 2px dashed #dc2626;
      border-radius: 10px;
      color: #b91c1c;
      font-size: 9px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
      background: rgba(255, 245, 245, 0.96);
      transform: rotate(-7deg);
    }
    .signature-image { height: 34px; width: auto; max-width: 170px; margin: 5px 0 1px; }
    .signature-line { border-top: 1px solid #0f172a; margin-top: 4px; padding-top: 4px; }
    .footer-note { margin-top: 6px; font-size: 8px; color: #64748b; }
    .receipt-meta, .amount-box, .section, .signature-card { page-break-inside: avoid; }
  </style>
</head>
<body>
  <div class="watermark" style="top: 85px; left: 10px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 85px; left: 270px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 195px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 195px; left: 320px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 305px; left: 5px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 305px; left: 265px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 415px; left: 50px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 415px; left: 315px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 525px; left: 10px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 525px; left: 270px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 635px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 635px; left: 320px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 745px; left: 15px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 745px; left: 280px;">MAHARANI MOBIL</div>

  <div class="page">
    <div class="content">
      <table class="top">
        <tr>
          <td>
            <div class="brand">{{ $profile['showroom_name'] ?? 'Maharani Mobil' }}</div>
            <div class="subtitle">Kwitansi resmi penerimaan pembayaran kendaraan</div>
            <div class="subtitle">{{ $profile['showroom_address'] ?? '-' }}</div>
            <div class="subtitle">Telp Showroom: {{ $profile['showroom_phone'] ?? '-' }} | WhatsApp: {{ $profile['showroom_whatsapp'] ?? '-' }}</div>
          </td>
          <td class="contact">
            <div><strong>No. Kwitansi:</strong> {{ $documentNumber }}</div>
            <div><strong>Tanggal Terbit:</strong> {{ $issuedAt->format('d M Y H:i') }}</div>
            <div><strong>Ref. Verifikasi:</strong> {{ $profile['verification_reference'] ?? '-' }}</div>
          </td>
        </tr>
      </table>

      <table class="heading-table">
        <tr>
          <td>
            <div class="heading">Kwitansi Jual Beli Mobil</div>
            <div class="muted">Bukti sah penerimaan pembayaran lunas atas transaksi pembelian unit kendaraan.</div>
          </td>
          <td style="width: 90px; text-align: right;">
            @if (!empty($profile['verification_seal_data_uri']))
              <img class="seal" src="{{ $profile['verification_seal_data_uri'] }}" alt="Segel verifikasi dokumen">
            @endif
          </td>
        </tr>
      </table>

      <table class="receipt-meta">
        <tr>
          <td style="padding-right: 8px; width: 58%;">
            <div class="meta-card">
              <div class="meta-line"><span class="label">Sudah diterima dari:</span> <strong>{{ $buyer?->name ?? 'Customer Maharani Mobil' }}</strong></div>
              <div class="meta-line"><span class="label">Email:</span> {{ $profile['buyer_email'] ?? '-' }}</div>
              <div class="meta-line"><span class="label">Telepon:</span> {{ $profile['buyer_phone'] ?? '-' }}</div>
              <div class="meta-line"><span class="label">Metode pembelian:</span> {{ $order->purchase_method_label }}</div>
              <div class="meta-line"><span class="label">Metode bayar:</span> {{ $order->payment?->internal_method_label ?? $order->internal_payment_method_label }}</div>
              <div class="meta-line"><span class="label">Sumber transaksi:</span> {{ $order->transaction_channel_label }}</div>
              @if ($isCreditPurchase)
                <div class="meta-line"><span class="label">Oleh leasing:</span> {{ $profile['leasing_partner'] ?? '-' }}</div>
              @endif
            </div>
          </td>
          <td style="padding-left: 8px;">
            <div class="meta-card">
              <div class="meta-line"><span class="label">Nomor order:</span> {{ $order->order_reference }}</div>
              <div class="meta-line"><span class="label">Validator:</span> {{ $profile['payment_validator'] ?? '-' }}</div>
              <div class="meta-line"><span class="label">Validasi:</span> {{ $verificationAt ? $verificationAt->format('d M Y H:i') : '-' }}</div>
            </div>
          </td>
        </tr>
      </table>

      <div class="amount-box">
        <div class="label">Jumlah pembayaran diterima</div>
        <div class="amount-main">{{ CurrencyFormatter::rupiah($order->total) }}</div>
      </div>

      <div class="section">
        <div class="section-title">Uraian Pembayaran</div>
        <p class="narrative">
          Pembayaran ini diterima untuk transaksi pembelian 1 unit mobil {{ $unitDescription !== '' ? $unitDescription : 'sesuai data kendaraan pada sistem Maharani Mobil' }}.
        </p>
        <table class="summary-table">
          <tr>
            <td class="summary-label">Status pembayaran</td>
            <td><strong>Lunas</strong></td>
          </tr>
          <tr>
            <td class="summary-label">Tujuan transaksi</td>
            <td>Pembelian penuh kendaraan sebagaimana detail unit di atas.</td>
          </tr>
          @if ($isCreditPurchase)
            <tr>
              <td class="summary-label">Pembayaran DP</td>
              <td>{{ CurrencyFormatter::rupiah($creditDpAmount) }}</td>
            </tr>
            <tr>
              <td class="summary-label">Pelunasan leasing</td>
              <td>{{ CurrencyFormatter::rupiah($leasingSettlementAmount) }}</td>
            </tr>
            <tr>
              <td class="summary-label">Oleh leasing</td>
              <td>{{ $profile['leasing_partner'] ?? '-' }}</td>
            </tr>
          @endif
        </table>
      </div>

      <div class="section" style="background: #fffef8;">
        Dengan ini dinyatakan bahwa {{ $profile['showroom_name'] ?? 'Maharani Mobil' }} telah menerima pembayaran secara lunas dari pembeli sebagaimana tercantum pada dokumen ini.
      </div>

      <div class="signature-card">
        <div class="signature-head">Pihak Showroom</div>
        <div class="muted">Diterbitkan dan diotorisasi oleh:</div>
        <div class="materai-badge">Materai Rp10.000</div>
        @if (!empty($profile['owner_signature_data_uri']))
          <img class="signature-image" src="{{ $profile['owner_signature_data_uri'] }}" alt="Tanda tangan owner">
        @endif
        <div class="signature-line">
          <strong>{{ $profile['owner_name'] ?? 'Rarendra' }}</strong><br>
          {{ $profile['owner_title'] ?? 'Pemilik Maharani Mobil' }}
        </div>
      </div>

      <div class="footer-note">
        Ini merupakan kwitansi asli yang diterbitkan oleh pihak Maharani Mobil.
      </div>
    </div>
  </div>
</body>
</html>
