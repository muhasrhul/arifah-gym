<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeAdminWidget extends Widget
{
    protected static ?int $sort = -10;

    // Tambahkan baris ini agar widget memakan lebar maksimal (Full Width)
    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.welcome-admin-widget';
}