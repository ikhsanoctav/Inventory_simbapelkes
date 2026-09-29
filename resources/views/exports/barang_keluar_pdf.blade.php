<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Keluar ({{ ucfirst($period) }})</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Barang Keluar ({{ ucfirst($period) }})</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $itemOut)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $itemOut->item->name ?? '-' }}</td>
                    <td>{{ $itemOut->quantity }}</td>
                    <td>Rp {{ number_format($itemOut->item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($itemOut->total_price, 0, ',', '.') }}</td>
                    <td>{{ $itemOut->created_at->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
