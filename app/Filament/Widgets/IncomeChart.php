<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\CashFlow; 
use Carbon\Carbon;

class IncomeChart extends LineChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan Bulan Ini';
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
        return cache()->remember('chart_income_monthly_daily', 600, function () {
            $dataUang = [];
            $dataTanggal = [];
            
            $now = Carbon::now('Asia/Makassar');
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            // Ambil data CashFlow untuk bulan ini, group by tanggal
            $cashFlows = CashFlow::selectRaw('DATE(date) as date, SUM(amount) as total')
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->where('type', 'income')
                ->groupBy('date')
                ->pluck('total', 'date');

            // Loop setiap hari dalam bulan ini
            $currentDate = $startOfMonth->copy();
            while ($currentDate <= $endOfMonth) {
                $dateKey = $currentDate->format('Y-m-d');
                
                $dataTanggal[] = $currentDate->format('d M'); // 01 Mar, 02 Mar, dst
                $dataUang[] = $cashFlows[$dateKey] ?? 0;
                
                $currentDate->addDay();
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
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "Pendapatan: Rp " + context.parsed.y.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
        ];
    }
}