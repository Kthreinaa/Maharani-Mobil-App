@php
  use App\Support\CurrencyFormatter;

  $car = $order->car;
  $buyer = $order->user;
  $payment = $order->payment;
  $profile = $documentProfile ?? [];
  $documentNumber = 'INV-' . now()->format('Y') . '-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
  $issuedAt = $order->approved_at ?? $order->updated_at ?? now();
  $verificationAt = $profile['verification_timestamp'] ?? null;
  $isCreditPurchase = (bool) ($profile['is_credit_purchase'] ?? false);
  $creditDpAmount = (float) ($profile['credit_dp_amount'] ?? 0);
  $leasingSettlementAmount = (float) ($profile['leasing_settlement_amount'] ?? 0);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $documentTitle }}</title>
  <style>
    @page { margin: 14px 14px 18px; }
    body { color: #0f172a; font-family: DejaVu Sans, sans-serif; font-size: 9.4px; line-height: 1.34; }
    .page { border: 1px solid #dbe3ef; padding: 12px 12px 10px; position: relative; overflow: hidden; }
    .watermark {
      position: fixed;
      color: rgba(11, 26, 64, 0.06);
      font-size: 19px;
      font-weight: 800;
      letter-spacing: .1em;
      transform: rotate(-32deg);
      white-space: nowrap;
      z-index: 0;
    }
    .top { width: 100%; border-bottom: 4px solid #0b1a40; padding-bottom: 8px; }
    .top td { vertical-align: top; }
    .brand { font-size: 21px; font-weight: 800; color: #0b1a40; letter-spacing: .02em; }
    .subtitle { color: #475569; margin-top: 2px; font-size: 9px; }
    .contact { text-align: right; color: #334155; font-size: 9px; line-height: 1.45; }
    .doc-pill { display: inline-block; border: 1px solid #f5a623; color: #b45309; border-radius: 999px; padding: 3px 9px; font-size: 8px; font-weight: 800; letter-spacing: .04em; }
    .heading-table { width: 100%; margin-top: 8px; }
    .heading-table td { vertical-align: middle; }
    .heading { font-size: 17px; font-weight: 800; text-transform: uppercase; color: #0b1a40; }
    .muted { color: #64748b; }
    .meta-grid { width: 100%; margin-top: 8px; }
    .meta-grid td { vertical-align: top; width: 50%; }
    .panel { border: 1px solid #dbe3ef; background: #fbfdff; padding: 8px 10px; min-height: 84px; }
    .panel-title { font-weight: 800; color: #0b1a40; margin-bottom: 6px; text-transform: uppercase; font-size: 10px; letter-spacing: .08em; }
    .label { color: #64748b; }
    .section-title { margin-top: 9px; font-size: 10px; font-weight: 800; color: #0b1a40; text-transform: uppercase; letter-spacing: .06em; }
    .table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    .table th, .table td { border: 1px solid #dbe3ef; padding: 4px 6px; vertical-align: top; }
    .table th { background: #f8fafc; color: #334155; font-weight: 800; text-align: left; font-size: 10px; }
    .amount-box { margin-top: 8px; border: 1px solid #dbe3ef; background: #f8fbff; padding: 8px 10px; }
    .amount-line { font-size: 15px; font-weight: 800; color: #0b1a40; }
    .legal-box { margin-top: 8px; border: 1px solid #dbe3ef; padding: 8px 10px; background: #fffef8; }
    .signature-card { border: 1px solid #dbe3ef; padding: 8px 10px; min-height: 88px; margin-top: 8px; position: relative; z-index: 1; }
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
    .seal { width: 56px; height: 56px; }
    .footer-note { margin-top: 6px; font-size: 8px; color: #64748b; }
    .table, .amount-box, .legal-box, .signature-card, .meta-grid { page-break-inside: avoid; }
    .right { text-align: right; }
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
  <div class="watermark" style="top: 855px; left: 65px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 855px; left: 330px;">MAHARANI MOBIL</div>
  <div class="page">
    <table class="top">
      <tr>
        <td>
          <div class="brand">{{ $profile['showroom_name'] ?? 'Maharani Mobil' }}</div>
          <div class="subtitle">Faktur Pembelian Kendaraan</div>
          <div class="subtitle">{{ $profile['showroom_address'] ?? '-' }}</div>
          <div class="subtitle">Telp Showroom: {{ $profile['showroom_phone'] ?? '-' }} | WhatsApp: {{ $profile['showroom_whatsapp'] ?? '-' }}</div>
        </td>
        <td class="contact">
          <div class="doc-pill">FAKTUR RESMI SHOWROOM</div>
          <div style="margin-top: 10px;"><strong>No. Faktur:</strong> {{ $documentNumber }}</div>
          <div><strong>Tanggal Terbit:</strong> {{ $issuedAt->format('d M Y H:i') }}</div>
          <div><strong>Ref. Verifikasi:</strong> {{ $profile['verification_reference'] ?? '-' }}</div>
          <div><strong>Ref. Pembayaran:</strong> {{ $profile['payment_reference'] ?? '-' }}</div>
        </td>
      </tr>
    </table>

    <table class="heading-table">
      <tr>
        <td>
          <div class="heading">Faktur Pembelian</div>
          <div class="muted">Dokumen administratif resmi transaksi penjualan unit kendaraan.</div>
        </td>
        <td class="right" style="width: 90px;">
          @if (!empty($profile['verification_seal_data_uri']))
            <img class="seal" src="{{ $profile['verification_seal_data_uri'] }}" alt="Segel verifikasi dokumen">
          @endif
        </td>
      </tr>
    </table>

    <table class="meta-grid">
      <tr>
        <td style="padding-right: 8px;">
          <div class="panel">
            <div class="panel-title">Data Pembeli</div>
            <div><span class="label">Nama:</span> {{ $buyer?->name ?? 'Customer Maharani Mobil' }}</div>
            <div><span class="label">Email:</span> {{ $profile['buyer_email'] ?? '-' }}</div>
            <div><span class="label">Telepon:</span> {{ $profile['buyer_phone'] ?? '-' }}</div>
            <div><span class="label">Status Akun:</span> Customer terdaftar pada sistem Maharani Mobil</div>
          </div>
        </td>
        <td style="padding-left: 8px;">
            <div class="panel">
              <div class="panel-title">Data Transaksi</div>
              <div><span class="label">Nomor Order:</span> {{ $order->order_reference }}</div>
              <div><span class="label">Metode Pembelian:</span> {{ $order->purchase_method_label }}</div>
              <div><span class="label">Metode Bayar:</span> {{ $order->payment?->internal_method_label ?? $order->internal_payment_method_label }}</div>
              <div><span class="label">Sumber Transaksi:</span> {{ $order->transaction_channel_label }}</div>
              <div><span class="label">Status Transaksi:</span> Lunas</div>
              @if ($isCreditPurchase)
                <div><span class="label">Oleh Leasing:</span> {{ $profile['leasing_partner'] ?? '-' }}</div>
              @endif
          </div>
        </td>
      </tr>
    </table>

    <div class="section-title">Identitas Unit Kendaraan</div>
    <table class="table">
      <thead>
        <tr>
          <th>Merek / Tipe</th>
          <th>Kode Unit</th>
          <th>Tahun</th>
          <th>Warna</th>
          <th>Transmisi</th>
          <th>Kilometer</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '')) ?: '-' }}</td>
          <td>{{ $car?->kode_unit ?: '-' }}</td>
          <td>{{ $car?->tahun ?: '-' }}</td>
          <td>{{ $car?->warna ?: '-' }}</td>
          <td>{{ $car?->transmisi ?: '-' }}</td>
          <td>{{ number_format((int) ($car?->kilometer ?? 0), 0, ',', '.') }} KM</td>
        </tr>
      </tbody>
    </table>

    <div class="section-title">Rincian Transaksi</div>
    <table class="table">
      <thead>
        <tr>
          <th>Uraian</th>
          <th style="width: 32%;">Keterangan / Nilai</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Harga unit kendaraan</td>
          <td>{{ CurrencyFormatter::rupiah($order->total) }}</td>
        </tr>
        @if ($isCreditPurchase)
          <tr>
            <td>Pembayaran DP</td>
            <td>{{ CurrencyFormatter::rupiah($creditDpAmount) }}</td>
          </tr>
          <tr>
            <td>Pelunasan leasing</td>
            <td>{{ CurrencyFormatter::rupiah($leasingSettlementAmount) }}</td>
          </tr>
          <tr>
            <td>Oleh leasing</td>
            <td>{{ $profile['leasing_partner'] ?? '-' }}</td>
          </tr>
        @endif
        <tr>
          <td>Validator pembayaran</td>
          <td>{{ $profile['payment_validator'] ?? '-' }}</td>
        </tr>
        <tr>
          <td>Waktu validasi pembayaran</td>
          <td>{{ $verificationAt ? $verificationAt->format('d M Y H:i') : '-' }}</td>
        </tr>
        <tr>
          <td>Status pembayaran</td>
          <td>Lunas</td>
        </tr>
      </tbody>
    </table>

    <div class="amount-box">
      <div class="label">Total Nilai Faktur</div>
      <div class="amount-line">{{ CurrencyFormatter::rupiah($order->total) }}</div>
    </div>

    <div class="legal-box">
      <strong>Keterangan Administratif</strong><br>
      Faktur ini diterbitkan oleh Maharani Mobil sebagai bukti administratif atas transaksi pembelian kendaraan yang telah diproses.
    </div>

    <div class="signature-card">
      <div class="panel-title">Pihak Showroom</div>
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
      Ini merupakan faktur asli yang diterbitkan oleh pihak Maharani Mobil.
    </div>
  </div>
</body>
</html>
