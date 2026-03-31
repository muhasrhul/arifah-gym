<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran - ARIFAH GYM</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body { font-family: sans-serif; padding: 20px; color: #333; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; }
        th { background-color: #f97316; color: white; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .total { font-weight: bold; background-color: #eee; }
        .nominal { text-align: right; }
        .expense-total { background-color: #ef4444; color: white; }
        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1 style="margin: 0;">ARIFAH GYM MAKASSAR</h1>
        <h2 style="margin: 5px 0; color: #555;">LAPORAN PENGELUARAN{{ $dateFilterText ?? '' }}{{ $additionalFilterText ?? '' }}</h2>
        <p style="margin: 0; font-size: 12px;">Tanggal Cetak: {{ date('d F Y, H:i') }} WITA</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 10%; text-align: center;">Tanggal</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 25%;">Item/Barang</th>
                <th style="width: 8%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Total Harga</th>
                <th style="width: 12%;">No. Nota</th>
                <th style="width: 10%;">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total = 0;
                $no = 1;
            @endphp
            @foreach($data as $row)
            @php 
                $total += $row->amount;
            @endphp
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->expense_date)->format('d/m/Y') }}</td>
                <td>{{ $row->category }}</td>
                <td>{{ $row->item }}</td>
                <td style="text-align: center;">{{ $row->quantity }}</td>
                <td class="nominal">Rp {{ number_format($row->amount, 0, ',', '.') }}</td>
                <td>{{ $row->receipt_number ?? '-' }}</td>
                <td>{{ $row->creator ? $row->creator->name : 'User Dihapus' }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="7" style="text-align: right;">TOTAL PENGELUARAN:</td>
                <td class="nominal expense-total">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: right; font-size: 10px;">
        <p>Makassar, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>Owner MUSDHALIFAH</strong></p>
    </div>

    <p class="no-print" style="color: red; margin-top: 20px; font-size: 12px;">
        *Gunakan <strong>Ctrl+P</strong> jika kotak dialog printer tidak muncul otomatis.
    </p>
</body>
</html>