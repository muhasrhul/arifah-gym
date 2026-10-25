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
        
        // Ambil data hari teramai (rata-rata per hari dalam bulan ini)
        $dayData = Attendance::selectRaw('DAYOFWEEK(created_at) as day_of_week, count(*) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->pluck('total', 'day_of_week')
            ->all();
        
        // Hitung berapa kali setiap hari muncul dalam bulan ini
        $dayOccurrences = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dayOfWeek = $currentDate->dayOfWeekIso === 7 ? 1 : $currentDate->dayOfWeekIso + 1; // Convert to DAYOFWEEK format
            $dayOccurrences[$dayOfWeek] = ($dayOccurrences[$dayOfWeek] ?? 0) + 1;
            $currentDate->addDay();
        }
        
        // Format data untuk chart hari (index 0-6 untuk Minggu-Sabtu)
        // Hitung rata-rata: total check-in / jumlah kemunculan hari (dibulatkan ke atas)
        // DAYOFWEEK: 1=Minggu, 2=Senin, ..., 7=Sabtu
        $dayChartData = [
            isset($dayData[1]) && isset($dayOccurrences[1]) ? ceil($dayData[1] / $dayOccurrences[1]) : 0, // Minggu
            isset($dayData[2]) && isset($dayOccurrences[2]) ? ceil($dayData[2] / $dayOccurrences[2]) : 0, // Senin
            isset($dayData[3]) && isset($dayOccurrences[3]) ? ceil($dayData[3] / $dayOccurrences[3]) : 0, // Selasa
            isset($dayData[4]) && isset($dayOccurrences[4]) ? ceil($dayData[4] / $dayOccurrences[4]) : 0, // Rabu
            isset($dayData[5]) && isset($dayOccurrences[5]) ? ceil($dayData[5] / $dayOccurrences[5]) : 0, // Kamis
            isset($dayData[6]) && isset($dayOccurrences[6]) ? ceil($dayData[6] / $dayOccurrences[6]) : 0, // Jumat
            isset($dayData[7]) && isset($dayOccurrences[7]) ? ceil($dayData[7] / $dayOccurrences[7]) : 0, // Sabtu
        ];
        
        return view('gym-status', compact('activeMembers', 'status', 'statusColor', 'chartData', 'dayChartData'));
    }
}
