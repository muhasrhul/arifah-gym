<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class JamTeramaiChart extends LineChartWidget
{
    protected static ?string $heading = 'Analisis Jam Teramai (Hari Ini)';
    protected static ?int $sort = 3;
    
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
        // Cache selama 5 menit
        return cache()->remember('chart_jam_teramai_' . now()->format('Y-m-d'), 300, function () {
            // Mengambil data jumlah orang per jam untuk hari ini menggunakan created_at
            $data = Attendance::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as total'))
                ->whereDate('created_at', now())
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->all();

            // Menyusun label jam (00:00 - 23:00)
            $labels = [];
            $values = [];
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $values[] = $data[$i] ?? 0;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Member Latihan',
                        'data' => $values,
                        'borderColor' => '#3b82f6', // Warna Biru
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4, // Membuat grafik melengkung halus agar estetik
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    // 3. PENGATURAN TAMBAHAN AGAR GRAFIK LEBIH CEPER
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1, // Agar skala angka di samping (1, 2, 3) lebih rapi
                    ],
                ],
            ],
        ];
    }
}