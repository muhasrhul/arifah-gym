<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan ARIFAH GYM</title>
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
        .status-paid { color: #10b981; font-weight: bold; }
        .status-pending { color: #fbbf24; font-weight: bold; }
        .status-failed { color: #ef4444; font-weight: bold; }
        .status-refund { color: #ef4444; font-weight: bold; }
        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1 style="margin: 0;">ARIFAH GYM MAKASSAR</h1>
        <h2 style="margin: 5px 0; color: #555;">LAPORAN KEUANGAN</h2>
        <p style="margin: 0; font-size: 12px;">Tanggal Cetak: {{ date('d F Y, H:i') }} WITA</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 5%; text-align: center;">ID</th>
                <th style="width: 10%;">Order ID</th>
                <th style="width: 10%; text-align: center;">Waktu</th>
                <th style="width: 15%;">Nama Customer</th>
                <th style="width: 7%; text-align: center;">Member ID</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 8%; text-align: center;">Status</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 12%; text-align: right;">Nominal</th>
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
                $statusText = ucfirst($row->status ?? 'paid');
                $statusClass = 'status-paid';
                if ($row->status === 'pending') {
                    $statusClass = 'status-pending';
                } elseif ($row->status === 'failed' || $row->status === 'refund') {
                    $statusClass = 'status-failed';
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td style="text-align: center;">{{ $row->id }}</td>
                <td>{{ $row->order_id }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->payment_date)->format('d/m/Y H:i') }}</td>
                <td>{{ $row->member ? $row->member->name : ($row->guest_name ?? 'Umum/Tamu') }}</td>
                <td style="text-align: center;">{{ $row->member_id ?? '-' }}</td>
                <td>{{ $row->type }}</td>
                <td style="text-align: center;" class="{{ $statusClass }}">{{ $statusText }}</td>
                <td>{{ $row->payment_method }}</td>
                <td class="nominal">Rp {{ number_format($row->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="9" style="text-align: right;">TOTAL PENDAPATAN:</td>
                <td class="nominal">Rp {{ number_format($total, 0, ',', '.') }}</td>
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