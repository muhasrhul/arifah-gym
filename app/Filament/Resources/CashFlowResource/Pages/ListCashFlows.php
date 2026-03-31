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
                        ->options([
                            'today' => 'Hari Ini',
                            'week' => 'Minggu Ini (7 hari terakhir)',
                            'month' => 'Bulan Ini',
                        ])
                        ->default('today')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('export.pembukuan', ['period' => $data['period']]);
                    
                    // Buka di tab baru
                    return redirect()->away($url);
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
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');
    }
}
