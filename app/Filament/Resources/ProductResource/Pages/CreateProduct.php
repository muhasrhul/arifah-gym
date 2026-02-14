<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // Tambahkan fungsi ini agar setelah buat produk baru langsung ke Kasir Cepat
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); 
        // Atau jika ingin langsung ke halaman Kasir Cepat spesifik:
        // return url('/admin/kasir-cepat');
    }
}