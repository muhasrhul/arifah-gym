<?php

namespace App\Filament\Resources\CashFlowResource\Pages;

use App\Filament\Resources\CashFlowResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCashFlow extends EditRecord
{
    protected static string $resource = CashFlowResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->source === 'pengeluaran'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Hanya pengeluaran manual yang bisa diedit
        if ($this->record->source !== 'pengeluaran') {
            Notification::make()
                ->title('Tidak Dapat Diedit')
                ->body('Hanya pengeluaran manual yang dapat diedit. Data ini berasal dari sistem otomatis.')
                ->warning()
                ->send();
            
            $this->redirect($this->getResource()::getUrl('index'));
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data Berhasil Diperbarui')
            ->body('Perubahan telah disimpan ke buku kas.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
