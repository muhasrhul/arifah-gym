<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    
    protected $originalStock = null;

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
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan stock original sebelum save
        $this->originalStock = $this->record->stock ?? 0;
        
        \Log::info('Before save - Original stock stored', [
            'original_stock' => $this->originalStock,
            'new_stock' => $data['stock'] ?? 0
        ]);
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Debug: Log untuk cek apakah method ini dipanggil
        \Log::info('afterSave called');
        
        // Gunakan stock yang sudah disimpan sebelumnya
        $originalStock = $this->originalStock ?? 0;
        $newStock = $this->record->stock ?? 0;
        
        \Log::info('Stock comparison in afterSave', [
            'original' => $originalStock,
            'new' => $newStock,
            'increased' => $newStock > $originalStock
        ]);
        
        if ($newStock > $originalStock) {
            $stockIncrease = $newStock - $originalStock;
            
            \Log::info('Sending notification for stock increase', ['increase' => $stockIncrease]);
            
            // Tampilkan notifikasi peringatan
            Notification::make()
                ->title('Stock Bertambah!')
                ->body("Stock {$this->record->name} bertambah {$stockIncrease} pcs. Jangan lupa catat pengeluaran pembelian di menu \"Catatan Pengeluaran\" untuk menjaga akurasi pembukuan.")
                ->warning()
                ->duration(20000) // 20 detik
                ->actions([
                    \Filament\Notifications\Actions\Action::make('buka_catatan_pengeluaran')
                        ->label('Buka Catatan Pengeluaran')
                        ->url(\App\Filament\Resources\ExpenseResource::getUrl('create'))
                        ->button(),
                ])
                ->send();
                
            \Log::info('Notification sent successfully');
        }
    }
}