<?php

namespace App\Filament\Resources\PaketResource\Pages;

use App\Filament\Resources\PaketResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePaket extends CreateRecord
{
    protected static string $resource = PaketResource::class;

    // Clear cache setelah paket dibuat
    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);
        
        // Clear cache paket aktif agar landing page langsung update
        cache()->forget('pakets_aktif');
        cache()->forget('registration_fee_display');
        
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}