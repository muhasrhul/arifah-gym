<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Resources\CashFlowResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCashFlow extends CreateRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Paksa set type dan source untuk pengeluaran manual
        $data['type'] = 'expense';
        $data['source'] = 'pengeluaran';
        
        // Set tanggal default jika tidak diisi
        if (!isset($data['date'])) {
            $data['date'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Pengeluaran Berhasil Dicatat')
            ->body('Data pengeluaran telah ditambahkan ke buku kas.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
