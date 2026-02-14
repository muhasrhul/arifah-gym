<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?string $title = 'Dashboard';
    
    protected function getHeading(): string
    {
        return 'Dashboard';
    }
}
