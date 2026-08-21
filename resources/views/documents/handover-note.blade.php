@php
  $car = $order->car;
  $buyer = $order->user;
  $profile = $documentProfile ?? [];
  $documentNumber = 'BAST-' . now()->format('Y') . '-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
  $issuedAt = $order->approved_at ?? $order->updated_at ?? now();
  $stnkStatus = $order->document_status['stnk'] ?? 'available';
  $bpkbStatus = $order->document_status['bpkb'] ?? 'available';
  $stnkAvailable = in_array($stnkStatus, ['ready', 'submitted', 'done', 'available', 'tersedia', 'pending'], true);
  $bpkbAvailable = in_array($bpkbStatus, ['ready', 'submitted', 'done', 'available', 'tersedia', 'pending'], true);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $documentTitle }}</title>
  <style>
    @page { margin: 14px 14px 18px; }
    body { color: #111827; font-family: DejaVu Sans, sans-serif; font-size: 9.4px; line-height: 1.34; }
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
    .pill { display: inline-block; margin-top: 6px; border: 1px solid #f5a623; color: #b45309; border-radius: 999px; padding: 3px 8px; font-size: 8px; font-weight: 800; }
    .seal { width: 56px; height: 56px; }
    .meta-grid { width: 100%; margin-top: 8px; }
    .meta-grid td { width: 50%; vertical-align: top; }
    .panel { border: 1px solid #dbe3ef; background: #fbfdff; padding: 8px 10px; min-height: 70px; }
    .panel-title { font-weight: 800; color: #0b1a40; margin-bottom: 6px; text-transform: uppercase; font-size: 10px; letter-spacing: .08em; }
    .label { color: #64748b; }
    .section-title { margin-top: 8px; font-size: 10px; font-weight: 800; color: #0b1a40; text-transform: uppercase; letter-spacing: .06em; }
    .table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    .table th, .table td { border: 1px solid #dbe3ef; padding: 4px 6px; vertical-align: top; }
    .table th { background: #f8fafc; color: #334155; font-weight: 800; text-align: left; font-size: 10px; }
    .legal-box { margin-top: 8px; border: 1px solid #dbe3ef; padding: 8px 10px; background: #fffef8; }
    .signature-card { border: 1px solid #dbe3ef; padding: 8px 10px; min-height: 84px; margin-top: 8px; }
    .signature-head { font-weight: 800; color: #0b1a40; margin-bottom: 3px; text-transform: uppercase; font-size: 10px; letter-spacing: .08em; }
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
    }
    .signature-placeholder {
      margin-top: 10px;
      min-height: 48px;
      border-bottom: 1px solid #0f172a;
    }
    .signature-note {
      margin-top: 6px;
      font-size: 8px;
      color: #64748b;
    }
    .signature-image { height: 32px; width: auto; max-width: 165px; margin: 5px 0 1px; }
    .signature-line { border-top: 1px solid #0f172a; margin-top: 4px; padding-top: 4px; }
    .footer-note { margin-top: 6px; font-size: 8px; color: #64748b; }
    .meta-grid, .table, .legal-box, .signature-card { page-break-inside: avoid; }
  </style>
</head>
<body>
  <div class="watermark" style="top: 70px; left: 8px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 70px; left: 205px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 70px; left: 402px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 165px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 165px; left: 252px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 165px; left: 449px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 260px; left: 8px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 260px; left: 205px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 260px; left: 402px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 355px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 355px; left: 252px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 355px; left: 449px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 450px; left: 8px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 450px; left: 205px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 450px; left: 402px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 545px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 545px; left: 252px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 545px; left: 449px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 640px; left: 8px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 640px; left: 205px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 640px; left: 402px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 735px; left: 55px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 735px; left: 252px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 735px; left: 449px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 830px; left: 8px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 830px; left: 205px;">MAHARANI MOBIL</div>
  <div class="watermark" style="top: 830px; left: 402px;">MAHARANI MOBIL</div>

  <div class="page">
    <div class="content">
      <table class="top">
        <tr>
          <td>
            <div class="brand">{{ $profile['showroom_name'] ?? 'Maharani Mobil' }}</div>
            <div class="subtitle">Berita Acara Serah Terima Kendaraan</div>
            <div class="subtitle">{{ $profile['showroom_address'] ?? '-' }}</div>
            <div class="subtitle">Telp Showroom: {{ $profile['showroom_phone'] ?? '-' }} | WhatsApp: {{ $profile['showroom_whatsapp'] ?? '-' }}</div>
          </td>
          <td class="contact">
            <div><strong>No. BAST:</strong> {{ $documentNumber }}</div>
            <div><strong>Tanggal Terbit:</strong> {{ $issuedAt->format('d M Y H:i') }}</div>
            <div><strong>Ref. Verifikasi:</strong> {{ $profile['verification_reference'] ?? '-' }}</div>
          </td>
        </tr>
      </table>

      <table class="heading-table">
        <tr>
          <td>
            <div class="heading">Berita Acara Serah Terima Kendaraan</div>
            <div class="muted">Dokumen administratif resmi serah terima unit kendaraan.</div>
            <div class="pill">SERAH TERIMA UNIT</div>
          </td>
          <td style="width: 90px; text-align: right;">
            @if (!empty($profile['verification_seal_data_uri']))
              <img class="seal" src="{{ $profile['verification_seal_data_uri'] }}" alt="Segel verifikasi dokumen">
            @endif
          </td>
        </tr>
      </table>

      <div class="legal-box">
        Pada hari {{ $issuedAt->translatedFormat('l, d F Y') }}, telah dilakukan serah terima kendaraan berdasarkan transaksi yang telah diselesaikan dan divalidasi melalui sistem Maharani Mobil. Dokumen ini diterbitkan sebagai bukti administratif bahwa unit telah diserahterimakan sesuai data order dan riwayat pembayaran yang tercatat pada sistem.
      </div>

      <table class="meta-grid">
        <tr>
          <td style="padding-right: 8px;">
            <div class="panel">
              <div class="panel-title">Pihak Showroom</div>
              <div>{{ $profile['showroom_name'] ?? 'Maharani Mobil' }}</div>
              <div><span class="label">Owner:</span> {{ $profile['owner_name'] ?? 'Rarendra' }}</div>
              <div><span class="label">Jabatan:</span> {{ $profile['owner_title'] ?? 'Pemilik Maharani Mobil' }}</div>
              <div><span class="label">Tanggal serah terima:</span> {{ $issuedAt->translatedFormat('d F Y') }}</div>
            </div>
          </td>
          <td style="padding-left: 8px;">
            <div class="panel">
              <div class="panel-title">Pihak Pembeli</div>
              <div>{{ $buyer?->name ?? 'Customer Maharani Mobil' }}</div>
              <div><span class="label">Email:</span> {{ $profile['buyer_email'] ?? '-' }}</div>
              <div><span class="label">Telepon:</span> {{ $profile['buyer_phone'] ?? '-' }}</div>
              <div><span class="label">Nomor order:</span> {{ $order->order_reference }}</div>
              <div><span class="label">Sumber transaksi:</span> {{ $order->transaction_channel_label }}</div>
            </div>
          </td>
        </tr>
      </table>

      <div class="section-title">Data Kendaraan</div>
      <table class="table">
        <tr>
          <th style="width: 28%;">Merek / Tipe</th>
          <td>{{ trim(($car?->merk ?? '') . ' ' . ($car?->tipe ?? '')) ?: '-' }}</td>
        </tr>
        <tr>
          <th>Tahun</th>
          <td>{{ $car?->tahun ?: '-' }}</td>
        </tr>
        <tr>
          <th>Warna</th>
          <td>{{ $car?->warna ?: '-' }}</td>
        </tr>
        <tr>
          <th>Kode Unit</th>
          <td>{{ $car?->kode_unit ?: '-' }}</td>
        </tr>
        <tr>
          <th>Transmisi / Kilometer</th>
          <td>{{ $car?->transmisi ?: '-' }} / {{ number_format((int) ($car?->kilometer ?? 0), 0, ',', '.') }} KM</td>
        </tr>
      </table>

      <div class="section-title">Dokumen dan Status Administratif</div>
      <table class="table">
        <thead>
          <tr>
            <th>Dokumen / Status</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Faktur pembelian</td>
            <td>Telah diterbitkan oleh sistem</td>
          </tr>
          <tr>
            <td>Kwitansi jual beli mobil</td>
            <td>Telah diterbitkan oleh sistem</td>
          </tr>
          <tr>
            <td>Status ketersediaan STNK</td>
            <td>{{ $stnkAvailable ? 'Tersedia' : 'Tidak tersedia' }}</td>
          </tr>
          <tr>
            <td>Status ketersediaan BPKB</td>
            <td>{{ $bpkbAvailable ? 'Tersedia' : 'Tidak tersedia' }}</td>
          </tr>
        </tbody>
      </table>

      <div class="legal-box">
        Dengan ini dinyatakan bahwa kendaraan sebagaimana tersebut di atas telah diserahkan oleh pihak showroom kepada pembeli dalam keadaan sesuai data transaksi.
      </div>

      <div class="signature-card">
        <div class="signature-head">Pihak Showroom</div>
        <div class="muted">Diterbitkan dan diotorisasi oleh:</div>
        <div class="materai-badge">Materai Rp10.000</div>
        <div class="signature-placeholder"></div>
        <div class="signature-note">Area ini disiapkan untuk tanda tangan manual showroom yang dibubuhkan langsung di atas materai.</div>
        <div class="signature-line">
          <strong>{{ $profile['owner_name'] ?? 'Rarendra' }}</strong><br>
          {{ $profile['owner_title'] ?? 'Pemilik Maharani Mobil' }}
        </div>
      </div>

      <div class="footer-note">
        Ini merupakan surat Berita Acara Serah Terima asli yang diterbitkan oleh pihak Maharani Mobil.
      </div>
    </div>
  </div>
</body>
</html>
