<?php

namespace App\Filament\Resources\CashFlowResource\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;

class ExportWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.cash-flow-resource.widgets.export-widget';
    
    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'period' => 'today'
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period')
                    ->label('Periode Laporan')
                    ->options([
                        'today' => 'Hari Ini',
                        'week' => 'Minggu Ini (7 hari terakhir)',
                        'month' => 'Bulan Ini',
                    ])
                    ->default('today')
                    ->required()
                    ->selectablePlaceholder(false),
            ])
            ->statePath('data');
    }

    public function exportPDF(): void
    {
        $data = $this->form->getState();
        $url = route('export.pembukuan', ['period' => $data['period']]);
        
        // Redirect ke URL export
        $this->redirect($url, navigate: false);
    }
}