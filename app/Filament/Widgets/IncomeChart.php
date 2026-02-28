<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Transaction; 
use Carbon\Carbon;

class IncomeChart extends LineChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan (7 Hari Terakhir)';
    protected static ?int $sort = 2;
    
    // Polling setiap 60 detik
    protected static ?string $pollingInterval = '60s';
    
    // Lazy load widget
    protected static bool $isLazy = true;

    // 1. MEMBUAT GRAFIK FULL KE SAMPING
    protected int | string | array $columnSpan = 'full';

    // 2. MEMBATASI TINGGI GRAFIK AGAR TIDAK JAUH SCROLL (250px)
    protected function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Cache selama 10 menit
        return cache()->remember('chart_income_7days', 600, function () {
            $dataUang = [];
            $dataTanggal = [];

            // Ambil data sekaligus dengan query yang lebih efisien
            $revenueStartDate = env('REVENUE_START_DATE', '2026-03-05'); // Default: 5 Maret 2026
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            
            // Pastikan tidak mengambil data sebelum revenue start date
            if ($startDate->format('Y-m-d') < $revenueStartDate) {
                $startDate = Carbon::parse($revenueStartDate)->startOfDay();
            }
            
            $transactions = Transaction::selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                ->where('payment_date', '>=', $startDate)
                ->where('payment_date', '>=', $revenueStartDate) // Filter tambahan untuk revenue start date
                ->groupBy('date')
                ->pluck('total', 'date');

            // Loop 7 hari ke belakang
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = Carbon::now()->subDays($i);
                $dateKey = $tanggal->format('Y-m-d');
                
                $dataTanggal[] = $tanggal->format('d M');
                $dataUang[] = $transactions[$dateKey] ?? 0;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Pendapatan (Rp)',
                        'data' => $dataUang,
                        'borderColor' => '#F59E0B', // Oranye ARIFAH Gym
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'fill' => true,
                        'tension' => 0.4, 
                    ],
                ],
                'labels' => $dataTanggal,
            ];
        });
    }

    // 3. PENGATURAN TAMBAHAN AGAR GRAFIK LEBIH CEPER/PENDEK
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'ticks' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }
}