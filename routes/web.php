<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Attendance;
use App\Models\Transaction; 
use App\Http\Controllers\FrontMemberController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Filament\Notifications\Notification;

/*
|--------------------------------------------------------------------------
| Web Routes - ARIFAH Gym Makassar
|--------------------------------------------------------------------------
*/

// 1. HALAMAN UTAMA & SEARCH
Route::get('/', [FrontMemberController::class, 'index'])->name('home');

// 1.1 FORGOT PASSWORD & RESET PASSWORD (KHUSUS ADMIN/OWNER)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// 2. PROSES PENDAFTARAN
Route::get('/daftar', function () {
    $pakets = \App\Models\Paket::where('is_active', true)->get();
    return view('daftar', compact('pakets'));
});

Route::post('/daftar', [FrontMemberController::class, 'store'])->name('member.register');

// 2.1 UPDATE METODE PEMBAYARAN
Route::post('/update-payment-method', [FrontMemberController::class, 'updatePaymentMethod'])->name('member.updatePaymentMethod');

// 3. PROSES ABSENSI
Route::get('/absen', function () {
    return view('absen');
});

Route::post('/absen', function (Request $request) {
    // 1. Bersihkan Nomor HP
    $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
    if (str_starts_with($cleanPhone, '62')) {
        $cleanPhone = '0' . substr($cleanPhone, 2);
    }

    // 2. Cari Member
    $member = Member::where('phone', 'like', "%$cleanPhone%")->first();

    if (!$member) {
        return back()->with('error', 'Nomor tidak terdaftar! Silakan hubungi kasir.');
    }

    if (!$member->is_active) {
        return back()->with('error', 'Member Anda Non-Aktif/Expired. Silakan lapor ke kasir.');
    }

    // 3. Cek Apakah Sudah Absen Hari Ini (Kecuali Tamu Harian)
    if ($member->name !== 'Tamu Harian') {
        $sudahAbsen = Attendance::where('member_id', $member->id)
                        ->whereDate('created_at', now())
                        ->exists();

        if ($sudahAbsen) {
            return back()->with('error', "Maaf {$member->name}, Anda sudah melakukan absen hari ini.");
        }
    }

    // 4. Catat Absen Baru
    Attendance::create([
        'member_id' => $member->id,
        'created_at' => now(),
    ]);

    // --- FITUR BARU: HITUNG STATISTIK LATIHAN (UNTUK DITAMPILKAN DI LAYAR) ---
    $bulanIni = \Carbon\Carbon::now('Asia/Makassar')->month;
    $tahunIni = \Carbon\Carbon::now('Asia/Makassar')->year;

    $totalLatihan = Attendance::where('member_id', $member->id)
        ->whereMonth('created_at', $bulanIni)
        ->whereYear('created_at', $tahunIni)
        ->count();

    // Tentukan Level Motivasi & Badge
    $motivasi = 'Semangat!';
    $badge = 'BEGINNER';
    
    if ($totalLatihan >= 15) {
        $motivasi = 'Luar Biasa! Anda adalah Iron Warrior!';
        $badge = 'IRON WARRIOR';
    } elseif ($totalLatihan >= 8) {
        $motivasi = 'Konsistensi yang mantap!';
        $badge = 'CONSISTENT';
    }

    // 5. Kirim Notifikasi ke Admin (Updated dengan Info Total Latihan)
    $allAdmins = \App\Models\User::all(); 
    foreach ($allAdmins as $admin) {
        // JALUR 1: Filament (Untuk memicu lonceng live)
        Notification::make()
            ->title('Member Absen Baru!')
            ->body("**{$member->name}** baru saja melakukan absensi. (Total: {$totalLatihan}x bulan ini)")
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->sendToDatabase($admin);
            
    }

    // 6. Kembalikan Respon Sukses + Data Statistik ke View
    return back()->with([
        'success'      => true,
        'member_name'  => $member->name,
        'member_id'    => $member->id, // Kirim ID asli
        'order_id'     => $member->order_id ?? 'REG-' . str_pad($member->id, 5, '0', STR_PAD_LEFT), // INI DIA KUNCINYA!
        'paket_nama'   => $member->type ?? 'MEMBER REGULAR', // Sesuaikan dengan kolom 'type'
        'expiry_date'  => $member->expiry_date 
            ? \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') 
            : 'Member Harian',
        'total_latihan'=> $totalLatihan,
        'badge'        => $badge,
        'motivasi'     => $motivasi
    ]);
});

// 4. LAPORAN KEUANGAN
Route::get('/cetak-laporan', function (Request $request) {
    $data = Transaction::with('member')->orderBy('payment_date', 'desc')->get();
    
    $data->transform(function ($item) {
        $item->type = preg_replace('/[^\x20-\x7E]/', '', $item->type);
        $item->type = trim($item->type);
        return $item;
    });

    if ($request->query('format') == 'pdf') {
        return view('laporan_pdf', compact('data'));
    }

    $filename = "Laporan_Keuangan_ARIFAH_GYM_" . date('d-m-Y') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    $total = 0;
    $output = "<table border='1'>
                <tr>
                    <th colspan='10' style='background-color: #f97316; font-size: 16px; height: 35px; color: white;'>LAPORAN KEUANGAN ARIFAH GYM</th>
                </tr>
                <tr style='background-color: #eeeeee;'>
                    <th>No</th>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Tanggal Bayar</th>
                    <th>Nama Customer</th>
                    <th>Member ID</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Nominal</th>
                </tr>";
                
    $no = 1;
    foreach ($data as $row) {
        $total += $row->amount;
        $namaTampil = $row->member ? $row->member->name : ($row->guest_name ?? 'Umum/Tamu');
        $memberId = $row->member_id ?? '-';
        
        // Status dengan warna
        $statusText = ucfirst($row->status ?? 'paid');
        $statusColor = '#000000'; // Default hitam
        if ($row->status === 'completed' || $row->status === 'paid') {
            $statusColor = '#10b981'; // Hijau
        } elseif ($row->status === 'pending') {
            $statusColor = '#fbbf24'; // Kuning
        } elseif ($row->status === 'failed' || $row->status === 'refund') {
            $statusColor = '#ef4444'; // Merah
        }

        $output .= "<tr>
                        <td style='text-align: center;'>{$no}</td>
                        <td style='text-align: center;'>{$row->id}</td>
                        <td>{$row->order_id}</td>
                        <td>" . \Carbon\Carbon::parse($row->payment_date)->format('d/m/Y H:i') . "</td>
                        <td>" . $namaTampil . "</td>
                        <td style='text-align: center;'>" . $memberId . "</td>
                        <td>" . $row->type . "</td>
                        <td style='text-align: center; color: {$statusColor}; font-weight: bold;'>" . $statusText . "</td>
                        <td>" . $row->payment_method . "</td>
                        <td style='text-align: right;'>Rp " . number_format($row->amount, 0, ',', '.') . "</td>
                    </tr>";
        $no++;
    }
    
    $output .= "<tr>
                <th colspan='9' style='text-align:right; background-color: #eeeeee;'>TOTAL PENDAPATAN:</th>
                <th style='background-color: #2ecc71; text-align: right;'>Rp " . number_format($total, 0, ',', '.') . "</th>
              </tr>";
    $output .= "</table>";

    return Response::make($output);
})->name('cetak-laporan');

// 5. EXPORT DAFTAR MEMBER
Route::get('/export-members', function (Request $request) {
    $data = Member::orderBy('created_at', 'desc')->get();

    if ($request->query('format') == 'pdf') {
        return view('members_pdf', compact('data'));
    }

    $filename = "Daftar_Member_ARIFAH_GYM_" . date('d-m-Y') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    $output = "<table border='1'>
                <tr>
                    <th colspan='11' style='background-color: #f97316; font-size: 16px; height: 35px; color: white;'>DAFTAR MEMBER ARIFAH GYM</th>
                </tr>
                <tr style='background-color: #eeeeee;'>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Fingerprint</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Tipe Member</th>
                    <th>Tanggal Bergabung</th>
                    <th>Tanggal Berakhir</th>
                    <th>Status</th>
                </tr>";
                
    $no = 1;
    foreach ($data as $row) {
        $joinDate = $row->join_date ? \Carbon\Carbon::parse($row->join_date)->format('d/m/Y') : '-';
        $expiryDate = $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->format('d/m/Y') : '-';
        
        // Tentukan status
        $today = \Carbon\Carbon::now('Asia/Makassar')->startOfDay();
        if (!$row->is_active && !$row->expiry_date) {
            $status = 'Pendaftar Baru';
            $statusColor = '#fbbf24'; // Kuning
        } elseif (!$row->is_active && $row->expiry_date) {
            $expiry = \Carbon\Carbon::parse($row->expiry_date)->startOfDay();
            if ($today->gt($expiry)) {
                $status = 'Masa Aktif Habis';
                $statusColor = '#ef4444'; // Merah
            } else {
                $status = 'Non-Aktif';
                $statusColor = '#000000'; // Hitam
            }
        } elseif ($row->is_active) {
            $status = 'Aktif';
            $statusColor = '#10b981'; // Hijau
        } else {
            $status = 'Non-Aktif';
            $statusColor = '#000000'; // Hitam
        }

        $output .= "<tr>
                        <td style='text-align: center;'>{$no}</td>
                        <td style='text-align: center;'>{$row->id}</td>
                        <td>{$row->name}</td>
                        <td>" . ($row->nik ?? '-') . "</td>
                        <td style='text-align: center;'>" . ($row->fingerprint_id ?? '-') . "</td>
                        <td>{$row->email}</td>
                        <td>{$row->phone}</td>
                        <td>{$row->type}</td>
                        <td style='text-align: center;'>{$joinDate}</td>
                        <td style='text-align: center;'>{$expiryDate}</td>
                        <td style='text-align: center; color: {$statusColor}; font-weight: bold;'>{$status}</td>
                    </tr>";
        $no++;
    }
    
    $output .= "<tr>
                <th colspan='11' style='text-align:center; background-color: #eeeeee;'>TOTAL MEMBER: " . $data->count() . " orang</th>
              </tr>";
    $output .= "</table>";

    return Response::make($output);
})->name('export-members');

// 6. EXPORT LOG ABSENSI
Route::get('/export-attendance', function (Request $request) {
    $data = Attendance::with('member')->orderBy('created_at', 'desc')->get();

    if ($request->query('format') == 'pdf') {
        return view('attendance_pdf', compact('data'));
    }

    $filename = "Log_Absensi_ARIFAH_GYM_" . date('d-m-Y') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    $output = "<table border='1'>
                <tr>
                    <th colspan='6' style='background-color: #f97316; font-size: 16px; height: 35px; color: white;'>LOG ABSENSI ARIFAH GYM</th>
                </tr>
                <tr style='background-color: #eeeeee;'>
                    <th>No</th>
                    <th>Nama Member</th>
                    <th>Tipe Member</th>
                    <th>WhatsApp</th>
                    <th>Tanggal Absen</th>
                    <th>Jam Absen</th>
                </tr>";
                
    $no = 1;
    foreach ($data as $row) {
        $memberName = $row->member ? $row->member->name : 'Member Dihapus';
        $memberType = $row->member ? $row->member->type : '-';
        $memberPhone = $row->member ? $row->member->phone : '-';
        $tanggal = \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        $jam = \Carbon\Carbon::parse($row->created_at)->format('H:i');

        $output .= "<tr>
                        <td style='text-align: center;'>{$no}</td>
                        <td>{$memberName}</td>
                        <td>{$memberType}</td>
                        <td>{$memberPhone}</td>
                        <td style='text-align: center;'>{$tanggal}</td>
                        <td style='text-align: center;'>{$jam}</td>
                    </tr>";
        $no++;
    }
    
    $output .= "<tr>
                <th colspan='6' style='text-align:center; background-color: #eeeeee;'>TOTAL ABSENSI: " . $data->count() . " kali</th>
              </tr>";
    $output .= "</table>";

    return Response::make($output);
})->name('export-attendance');

// 7. BACKUP DATABASE
Route::get('/backup-database', function () {
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');
    $filename = "backup_irongym_" . date('Y-m-d_His') . ".sql";
    $filePath = storage_path('app/' . $filename);
    $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
    $command = "{$mysqldumpPath} --user={$dbUser} --password={$dbPass} {$dbName} > \"{$filePath}\"";
    exec($command);
    if (file_exists($filePath) && filesize($filePath) > 0) {
        return response()->download($filePath)->deleteFileAfterSend(true);
    } else {
        return "Gagal melakukan backup. Silakan cek path XAMPP.";
    }
})->name('backup-database');