<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Paksa kembali ke daftar produk setelah simpan untuk menghindari 404
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}