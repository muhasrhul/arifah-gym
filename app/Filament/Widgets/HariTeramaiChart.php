<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HariTeramaiChart extends BarChartWidget
{
    protected static ?string $heading = 'Analisis Hari Teramai';
    protected static ?int $sort = 4;

    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'month';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    protected function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        return cache()->remember('chart_hari_teramai_' . $filter, 300, function () use ($filter) {
            $now = Carbon::now('Asia/Makassar');

            $query = Attendance::select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('COUNT(*) as total')
            );

            if ($filter === 'month') {
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ]);
                $label = 'Bulan ' . $now->translatedFormat('F Y');
            } elseif ($filter === 'year') {
                $query->whereYear('created_at', $now->year);
                $label = 'Tahun ' . $now->year;
            } else {
                $label = 'Semua Waktu';
            }

            // DAYOFWEEK: 1=Minggu, 2=Senin, ..., 7=Sabtu
            $data = $query->groupBy('day_of_week')
                ->orderBy('day_of_week')
                ->pluck('total', 'day_of_week')
                ->all();

            $hariLabels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $values = [];
            $colors = [];

            $maxVal = !empty($data) ? max($data) : 0;

            foreach ($hariLabels as $index => $hari) {
                $dayKey = $index + 1; // DAYOFWEEK dimulai dari 1
                $val = $data[$dayKey] ?? 0;
                $values[] = $val;
                // Warna lebih terang untuk hari teramai
                $colors[] = ($val === $maxVal && $maxVal > 0)
                    ? 'rgba(245, 158, 11, 0.9)'
                    : 'rgba(245, 158, 11, 0.3)';
            }

            return [
                'datasets' => [
                    [
                        'type' => 'bar',
                        'label' => 'Total Kunjungan (' . $label . ')',
                        'data' => $values,
                        'backgroundColor' => $colors,
                        'borderColor' => '#d97706',
                        'borderWidth' => 1,
                        'borderRadius' => 6,
                        'order' => 2,
                    ],
                    [
                        'type' => 'line',
                        'label' => 'Tren Kunjungan',
                        'data' => $values,
                        'borderColor' => '#d97706',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'borderWidth' => 2,
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                        'pointHoverRadius' => 5,
                        'order' => 1,
                    ],
                ],
                'labels' => $hariLabels,
            ];
        });
    }

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Bulan Ini',
            'year'  => 'Tahun Ini',
            'all'   => 'Semua Waktu',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'ticks' => ['stepSize' => 1],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
