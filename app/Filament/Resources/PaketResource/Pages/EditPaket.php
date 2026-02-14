<?php

namespace App\Filament\Resources\PaketResource\Pages;

use App\Filament\Resources\PaketResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPaket extends EditRecord
{
    protected static string $resource = PaketResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Clear cache setelah paket diupdate
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        
        // Clear cache paket aktif agar landing page langsung update
        cache()->forget('pakets_aktif');
        cache()->forget('registration_fee_display');
        
        return $record;
    }

    // FUNGSI INI YANG MEMBUAT SETELAH SAVE LANGSUNG KE DEPAN
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}