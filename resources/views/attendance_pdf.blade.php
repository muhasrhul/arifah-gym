<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Log Absensi - ARIFAH GYM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f97316;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        .stat-card {
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #f97316;
        }
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOG ABSENSI</h1>
        <p>ARIFAH GYM MAKASSAR</p>
        <p style="font-size: 12px;">Dicetak: {{ \Carbon\Carbon::now('Asia/Makassar')->format('d F Y, H:i') }} WITA</p>
    </div>

    @php
        $today = \Carbon\Carbon::now('Asia/Makassar')->startOfDay();
        $thisMonth = \Carbon\Carbon::now('Asia/Makassar')->month;
        $thisYear = \Carbon\Carbon::now('Asia/Makassar')->year;
        
        $todayCount = $data->filter(function($item) use ($today) {
            return \Carbon\Carbon::parse($item->created_at)->startOfDay()->eq($today);
        })->count();
        
        $thisMonthCount = $data->filter(function($item) use ($thisMonth, $thisYear) {
            $date = \Carbon\Carbon::parse($item->created_at);
            return $date->month == $thisMonth && $date->year == $thisYear;
        })->count();
        
        $uniqueMembers = $data->pluck('member_id')->unique()->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $todayCount }}</div>
            <div class="stat-label">Absen Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $thisMonthCount }}</div>
            <div class="stat-label">Absen Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $uniqueMembers }}</div>
            <div class="stat-label">Member Unik</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Nama Member</th>
                <th style="width: 20%;">Tipe Member</th>
                <th style="width: 15%;">WhatsApp</th>
                <th style="width: 15%; text-align: center;">Tanggal</th>
                <th style="width: 10%; text-align: center;">Jam</th>
                <th style="width: 10%; text-align: center;">Hari</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            
            @foreach($data as $attendance)
                @php
                    $date = \Carbon\Carbon::parse($attendance->created_at);
                    $memberName = $attendance->member ? $attendance->member->name : 'Member Dihapus';
                    $memberType = $attendance->member ? $attendance->member->type : '-';
                    $memberPhone = $attendance->member ? $attendance->member->phone : '-';
                @endphp
                
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $memberName }}</td>
                    <td>{{ $memberType }}</td>
                    <td>{{ $memberPhone }}</td>
                    <td style="text-align: center;">{{ $date->format('d/m/Y') }}</td>
                    <td style="text-align: center;">{{ $date->format('H:i') }}</td>
                    <td style="text-align: center;">{{ $date->translatedFormat('l') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p style="margin: 0;">TOTAL ABSENSI: {{ $data->count() }} kali</p>
        <p style="margin: 5px 0 0 0; font-size: 11px; font-weight: normal;">
            Hari Ini: {{ $todayCount }} | 
            Bulan Ini: {{ $thisMonthCount }} | 
            Member Unik: {{ $uniqueMembers }}
        </p>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari sistem ARIFAH GYM</p>
        <p>Makassar. Jln Skarda N, No.13</p>
    </div>

    <script>
        // Auto print saat halaman dibuka
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
