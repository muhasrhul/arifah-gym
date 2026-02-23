<?php

namespace App\Filament\Resources\QuickTransactionResource\Pages;

use App\Filament\Resources\QuickTransactionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuickTransaction extends EditRecord
{
    protected static string $resource = QuickTransactionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}