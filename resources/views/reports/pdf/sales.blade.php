<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <title>Sales Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h2>Laporan Penjualan Maharani Mobil</h2>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Mobil</th>
        <th>Metode</th>
        <th>Nominal</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $row)
        <tr>
          <td>{{ $row->tanggal }}</td>
          <td>{{ $row->customer }}</td>
          <td>{{ $row->mobil }}</td>
          <td>{{ $row->metode_pembayaran }}</td>
          <td>Rp {{ number_format($row->nominal) }}</td>
          <td>{{ $row->status_order }} / {{ $row->status_pembayaran }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
