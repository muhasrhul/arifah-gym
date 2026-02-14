<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Transaction; 
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    // Polling setiap 60 detik (lebih jarang = lebih cepat)
    protected static ?string $pollingInterval = '60s';
    
    // Lazy load widget - tidak langsung load saat halaman dibuka
    protected static bool $isLazy = true;
    
    protected function getCards(): array
    {
        // Cache selama 30 detik untuk update lebih cepat
        $omsetHariIni = cache()->remember('stats_omset_hari_ini', 30, function () {
            return Transaction::whereDate('payment_date', now())->sum('amount');
        });

        $totalOmzet = cache()->remember('stats_total_omzet', 30, function () {
            return Transaction::sum('amount');
        });

        $totalMember = cache()->remember('stats_total_member', 30, function () {
            return Member::where('is_active', true)
                ->whereDate('expiry_date', '>=', now()) 
                ->count();
        });

        $sedangLatihan = cache()->remember('stats_latihan_hari_ini', 30, function () {
            return Attendance::whereDate('created_at', now())->count();
        });

        return [
            // KARTU 1: PENDAPATAN HARI INI
            Card::make('Pendapatan Hari Ini', 'Rp ' . number_format($omsetHariIni, 0, ',', '.'))
                ->description('Total uang masuk hari ini')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),

            // KARTU 2: TOTAL OMZET KESELURUHAN (Mencolok dengan warna primary/biru/orange)
            Card::make('Total Omzet Keseluruhan', 'Rp ' . number_format($totalOmzet, 0, ',', '.'))
                ->description('Total pendapatan sejak awal')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('primary'),

            // KARTU 3: TOTAL MEMBER AKTIF
            Card::make('Total Member Aktif', $totalMember . ' Orang')
                ->description('Member dengan status aktif')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('primary'),
            
            // KARTU 4: LOG ABSENSI HARI INI
            Card::make('Absensi Hari Ini', $sedangLatihan . ' Check-in')
                ->description('Jumlah orang latihan hari ini')
                ->descriptionIcon('heroicon-s-clipboard-check')
                ->color('warning'),
        ];
    }
}