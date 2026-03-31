<?php

namespace App\Filament\Resources\CashFlowResource\Widgets;

use Filament\Widgets\Widget;

class HeaderWidget extends Widget
{
    protected static string $view = 'filament.resources.cash-flow-resource.widgets.header-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1; // Tampil paling atas
}