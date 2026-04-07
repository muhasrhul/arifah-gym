<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Resources\CashFlowResource;
use App\Models\CashFlow;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class ListCashFlows extends ListRecords
{
    protected static string $resource = CashFlowResource::class;

    public function getBreadcrumb(): ?string
    {
        return null; // Hilangkan breadcrumb
    }

    protected function getTitle(): string
    {
        return ''; // Hilangkan title
    }

    protected function getHeading(): string
    {
        return ' '; // Space kosong agar tombol tetap muncul
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('export_pdf')
                ->label('Unduh Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('period')
                        ->label('Pilih Periode Laporan')
                        ->options(function () {
                            $options = [
                                'today' => 'Hari Ini',
                            ];
                            
                            $now = \Carbon\Carbon::now('Asia/Makassar');
                            
                            // Generate 12 bulan terakhir
                            for ($i = 0; $i < 12; $i++) {
                                $date = $now->copy()->subMonths($i);
                                $key = $date->format('Y-m'); // 2026-04
                                $value = $date->translatedFormat('F Y'); // April 2026
                                $options[$key] = $value;
                            }
                            
                            return $options;
                        })
                        ->searchable()
                        ->default('today')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('export.pembukuan', ['period' => $data['period']]);
                    
                    // Redirect ke URL export
                    return redirect()->to($url);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\CashFlowResource\Widgets\HeaderWidget::class,
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');
    }
}
