<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GymStatusController extends Controller
{
    public function index()
    {
        // Hitung member yang check-in dalam 2 jam terakhir
        $twoHoursAgo = Carbon::now('Asia/Makassar')->subHours(2);
        
        $activeMembers = Attendance::where('created_at', '>=', $twoHoursAgo)
            ->distinct('member_id')
            ->count('member_id');
        
        // Tentukan status gym berdasarkan jumlah member aktif
        $status = 'SEPI';
        $statusColor = '#10b981'; // Hijau
        
        if ($activeMembers >= 20) {
            $status = 'RAMAI';
            $statusColor = '#ef4444'; // Merah
        } elseif ($activeMembers >= 10) {
            $status = 'NORMAL';
            $statusColor = '#fbbf24'; // Kuning
        }
        
        // Ambil data jam teramai (bulan ini)
        $now = Carbon::now('Asia/Makassar');
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $hourlyData = Attendance::selectRaw('HOUR(created_at) as hour, count(*) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')
            ->all();
        
        // Format data untuk chart (00-23 jam)
        $chartData = [];
        for ($i = 0; $i < 24; $i++) {
            $chartData[$i] = $hourlyData[$i] ?? 0;
        }
        
        // Ambil data hari teramai (bulan ini)
        $dayData = Attendance::selectRaw('DAYOFWEEK(created_at) as day_of_week, count(*) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->pluck('total', 'day_of_week')
            ->all();
        
        // Format data untuk chart hari (index 0-6 untuk Minggu-Sabtu)
        // DAYOFWEEK: 1=Minggu, 2=Senin, ..., 7=Sabtu
        $dayChartData = [
            $dayData[1] ?? 0, // Minggu
            $dayData[2] ?? 0, // Senin
            $dayData[3] ?? 0, // Selasa
            $dayData[4] ?? 0, // Rabu
            $dayData[5] ?? 0, // Kamis
            $dayData[6] ?? 0, // Jumat
            $dayData[7] ?? 0, // Sabtu
        ];
        
        return view('gym-status', compact('activeMembers', 'status', 'statusColor', 'chartData', 'dayChartData'));
    }
}
