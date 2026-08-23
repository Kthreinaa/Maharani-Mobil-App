<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Laporan Penjualan Maharani Mobil</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 12px 14px;
    }
    body {
      margin: 0;
      font-family: DejaVu Sans, sans-serif;
      font-size: 11px;
      color: #0f172a;
      background: #ffffff;
    }
    .page {
      position: relative;
      z-index: 1;
      padding: 8px 10px 12px;
    }
    .report-shell {
      width: 100%;
      max-width: none;
      margin: 0 auto;
    }
    .watermark-layer {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      pointer-events: none;
      opacity: 0.07;
    }
    .watermark-mark {
      position: absolute;
      transform: translate(-50%, -50%) rotate(-32deg);
      font-size: 16px;
      font-weight: bold;
      letter-spacing: 1.6px;
      color: #102a63;
      white-space: nowrap;
    }
    .hero {
      background: #102a63;
      color: #ffffff;
      border: 1px solid #d7e4ff;
      border-radius: 16px;
      padding: 18px 18px 16px;
    }
    .hero small {
      color: #f7c35f;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: bold;
    }
    .hero h1 {
      margin: 8px 0 8px;
      font-size: 23px;
      line-height: 1.3;
    }
    .hero p {
      margin: 0;
      line-height: 1.65;
      color: #dbe7ff;
    }
    .hero-meta {
      margin-top: 12px;
      padding-top: 10px;
      border-top: 1px solid rgba(255,255,255,0.18);
      font-size: 10px;
      line-height: 1.7;
    }
    .section {
      margin-top: 16px;
    }
    .section-title {
      margin: 0 0 10px;
      padding: 0 2px;
      font-size: 15px;
      color: #102a63;
      letter-spacing: 0.4px;
      font-weight: bold;
    }
    .section-head {
      margin-bottom: 10px;
      padding: 12px 14px;
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      background: rgba(255,255,255,0.96);
      page-break-after: avoid;
      page-break-inside: avoid;
    }
    .section-shell {
      padding: 12px;
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      background: rgba(255,255,255,0.96);
      page-break-inside: avoid;
    }
    .muted {
      color: #64748b;
    }
    .card {
      min-height: 82px;
      padding: 10px 11px;
      border: 1px solid #dbe5f3;
      border-radius: 12px;
      background: #f8fbff;
      page-break-inside: avoid;
    }
    .card .label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #64748b;
      font-weight: bold;
    }
    .card .value {
      margin-top: 6px;
      font-size: 15px;
      font-weight: bold;
      color: #0f172a;
      line-height: 1.3;
    }
    .card .value-money {
      font-size: 12px;
      line-height: 1.35;
      overflow-wrap: anywhere;
      word-break: break-word;
    }
    .card .note {
      margin-top: 5px;
      color: #64748b;
      line-height: 1.45;
      font-size: 10px;
    }
    .kpi-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 10px 0;
      table-layout: fixed;
    }
    .kpi-table td {
      border: 0;
      padding: 0;
      vertical-align: top;
    }
    .box {
      margin-bottom: 10px;
      padding: 12px;
      border: 1px solid #dbe5f3;
      border-radius: 12px;
      background: #ffffff;
      page-break-inside: avoid;
    }
    .box:last-child {
      margin-bottom: 0;
    }
    .panel-table {
      width: 100%;
      margin-bottom: 10px;
      border: 1px solid #dbe5f3;
      border-radius: 12px;
      border-collapse: separate;
      border-spacing: 0;
      background: #ffffff;
      page-break-inside: avoid;
    }
    .panel-table:last-child {
      margin-bottom: 0;
    }
    .panel-table td {
      border: 0;
      padding: 12px 14px;
    }
    .panel-title {
      font-size: 12px;
      font-weight: bold;
      color: #0f172a;
      margin-bottom: 6px;
    }
    .box-header-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }
    .box-header-table td {
      border: 0;
      padding: 0;
      vertical-align: middle;
    }
    .box-header-title {
      font-weight: bold;
      font-size: 12px;
      color: #0f172a;
    }
    .box-header-meta {
      text-align: right;
    }
    .pill {
      display: inline-block;
      border: 1px solid #dbe5f3;
      border-radius: 999px;
      padding: 4px 10px;
      font-size: 10px;
      font-weight: bold;
      color: #334155;
      background: #f8fafc;
    }
    .bar-wrap {
      margin-top: 8px;
      height: 10px;
      overflow: hidden;
      border-radius: 999px;
      background: #e9eff7;
    }
    .bar {
      height: 10px;
      border-radius: 999px;
    }
    .bar-blue {
      background: #2563eb;
    }
    .table-frame {
      border: 1px solid #dbe5f3;
      border-radius: 14px;
      overflow: hidden;
      background: #ffffff;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    thead {
      display: table-header-group;
    }
    tr {
      page-break-inside: avoid;
    }
    th, td {
      border: 1px solid #dbe5f3;
      padding: 6px 7px;
      text-align: left;
      vertical-align: top;
      line-height: 1.45;
      word-break: normal;
      overflow-wrap: break-word;
    }
    th {
      background: #eff5ff;
      color: #334155;
      text-transform: uppercase;
      font-size: 8px;
      letter-spacing: 0.8px;
    }
    .detail-table {
      table-layout: fixed;
      font-size: 9px;
    }
    .detail-time { width: 13%; }
    .detail-customer { width: 12%; }
    .detail-unit { width: 17%; }
    .detail-flow { width: 14%; }
    .detail-method { width: 13%; }
    .detail-value { width: 11%; }
    .detail-status { width: 10%; }
    .detail-handler { width: 10%; }
    .table-caption {
      margin-bottom: 10px;
      font-size: 10px;
      line-height: 1.6;
      color: #64748b;
    }
    .analysis-list,
    .brand-list {
      page-break-inside: auto;
    }
    .stacked-meta {
      line-height: 1.45;
    }
    .stacked-meta strong {
      display: block;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .stacked-meta span {
      display: block;
      color: #64748b;
      font-size: 8.5px;
    }
  </style>
</head>
<body>
  @php
    $summary = $report['summary'];
    $rows = $report['rows'];
    $generatedAt = $report['generated_at'];
    $watermarkTopPositions = [6, 18, 30, 42, 54, 66, 78, 90];
    $watermarkLeftPositions = [10, 30, 50, 70, 90];
    $watermarkMarkup = '';

    foreach ($watermarkTopPositions as $top) {
        foreach ($watermarkLeftPositions as $left) {
            $watermarkMarkup .= '<span class="watermark-mark" style="top: ' . $top . '%; left: ' . $left . '%;">MAHARANI MOBIL</span>';
        }
    }
  @endphp

  <div class="watermark-layer" aria-hidden="true">{!! $watermarkMarkup !!}</div>

  <div class="page">
    <div class="report-shell">
      <div class="hero">
        <small>Sales Report Center</small>
        <h1>Laporan Penjualan & Analisis</h1>
        <p>Laporan ini berisi ringkasan penjualan, pola pembelian, aktivitas pembelian customer, dan performa merk mobil pada periode yang dipilih.</p>
        <div class="hero-meta">
          <strong>Periode:</strong> {{ $summary['range_label'] }} |
          <strong>Dibuat:</strong> {{ $generatedAt->format('d M Y H:i') }}
        </div>
      </div>

      <div class="section">
        <div class="section-shell">
          <table class="kpi-table">
            <tr>
              <td>
                <div class="card">
                  <div class="label">Total Transaksi</div>
                  <div class="value">{{ number_format((int) $summary['total_orders']) }}</div>
                  <div class="note">Jumlah transaksi pada periode ini.</div>
                </div>
              </td>
              <td>
                <div class="card">
                  <div class="label">Omzet</div>
                  <div class="value value-money">{{ \App\Support\CurrencyFormatter::rupiah($summary['omzet']) }}</div>
                  <div class="note">Total nilai penjualan.</div>
                </div>
              </td>
              <td>
                <div class="card">
                  <div class="label">Rata-rata</div>
                  <div class="value value-money">{{ \App\Support\CurrencyFormatter::rupiah($summary['average_order']) }}</div>
                  <div class="note">Rata-rata nilai transaksi.</div>
                </div>
              </td>
              <td>
                <div class="card">
                  <div class="label">Tingkat Selesai</div>
                  <div class="value">{{ $summary['completion_rate'] }}%</div>
                  <div class="note">Transaksi yang sudah selesai penuh.</div>
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="section">
        <div class="section-title">Ringkasan Aktivitas Pembelian</div>
        <div class="section-shell">
          <table class="kpi-table">
            <tr>
              @foreach ($report['crm_overview'] as $item)
                <td>
                  <div class="card">
                    <div class="label">{{ $item['label'] }}</div>
                    <div class="value">{{ $item['value'] }}</div>
                    <div class="note">{{ $item['note'] }}</div>
                  </div>
                </td>
              @endforeach
            </tr>
          </table>
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <div class="section-title" style="margin-bottom:0;">Ringkasan Periode Ini</div>
        </div>
        <div class="analysis-list">
          @foreach ($report['analysis'] as $item)
            <table class="panel-table">
              <tbody>
                <tr>
                  <td>
                    <div class="panel-title">{{ $item['title'] }}</div>
                    <div class="muted" style="line-height:1.6;">{{ $item['detail'] }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          @endforeach
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <div class="section-title" style="margin-bottom:0;">Merk Mobil Paling Laris</div>
        </div>
        <div class="brand-list">
          @forelse ($report['brand_performance'] as $item)
            <table class="panel-table">
              <tbody>
                <tr>
                  <td>
                    <table class="box-header-table">
                      <tr>
                        <td><div class="box-header-title">{{ $item['brand'] }}</div></td>
                        <td class="box-header-meta"><span class="pill">{{ $item['share'] }}%</span></td>
                      </tr>
                    </table>
                    <div class="bar-wrap">
                      <div class="bar bar-blue" style="width: {{ min(100, max(8, round($item['share']))) }}%;"></div>
                    </div>
                    <div class="muted" style="margin-top:8px;">
                      {{ $item['units'] }} unit | {{ \App\Support\CurrencyFormatter::rupiah($item['revenue']) }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          @empty
            <table class="panel-table">
              <tbody>
                <tr>
                  <td class="muted">Belum ada data merk pada periode ini.</td>
                </tr>
              </tbody>
            </table>
          @endforelse
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <div class="section-title" style="margin-bottom:0;">Tren Penjualan</div>
        </div>
        <div class="table-frame">
          <table>
            <thead>
              <tr>
                <th>Periode</th>
                <th>Jumlah Order</th>
                <th>Omzet</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($report['trend'] as $item)
                <tr>
                  <td>{{ $item['label'] }}</td>
                  <td>{{ $item['orders'] }}</td>
                  <td>{{ \App\Support\CurrencyFormatter::rupiah($item['revenue']) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="muted">Belum ada data tren penjualan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="section">
        <div class="section-head">
          <div class="section-title" style="margin-bottom:8px;">Rincian Penjualan</div>
          <div class="table-caption" style="margin-bottom:0;">
            Rincian berikut merangkum tanggal, tahun, jam pembelian, customer, unit, alur pembelian, metode beli, metode bayar, nominal transaksi, dan pengelola internal pada periode laporan.
          </div>
        </div>
        <div class="table-frame">
          <table class="detail-table">
            <colgroup>
              <col class="detail-time">
              <col class="detail-customer">
              <col class="detail-unit">
              <col class="detail-flow">
              <col class="detail-method">
              <col class="detail-value">
              <col class="detail-status">
              <col class="detail-handler">
            </colgroup>
            <thead>
              <tr>
                <th>Waktu Transaksi</th>
                <th>Customer</th>
                <th>Unit</th>
                <th>Alur</th>
                <th>Metode Beli</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Pengelola</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($rows as $row)
                <tr>
                  <td>
                    <div class="stacked-meta">
                      <strong>{{ $row->tanggal_label }}</strong>
                      <span>Tahun {{ $row->tahun_transaksi }}</span>
                      <span>Jam {{ $row->jam_transaksi }}</span>
                    </div>
                  </td>
                  <td>{{ $row->customer }}</td>
                  <td>
                    {{ $row->mobil }}<br>
                    <span class="muted">{{ $row->kode_unit }}</span>
                  </td>
                  <td>
                    {{ $row->transaction_channel_label }}<br>
                    <span class="muted">{{ $row->sales_flow_label }}</span>
                  </td>
                  <td>{{ $row->metode_beli_label }}<br><span class="muted">Dibayar dengan {{ $row->metode_bayar_label }}</span></td>
                  <td>{{ \App\Support\CurrencyFormatter::rupiah($row->nominal) }}</td>
                  <td>{{ $row->status_order }} / {{ $row->status_pembayaran }}</td>
                  <td>
                    {{ $row->handled_role === 'marketing' ? 'Marketing' : ($row->handled_role === 'supervisor' ? 'Supervisor' : 'Belum Ditandai') }}<br>
                    <span class="muted">{{ $row->handled_by_name ?: '-' }}</span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="muted">Belum ada data penjualan untuk periode ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
