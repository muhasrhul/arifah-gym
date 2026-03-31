<?php

namespace App\Filament\Resources\QuickTransactionResource\Pages;

use App\Filament\Resources\QuickTransactionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\CashFlow;

class EditQuickTransaction extends EditRecord
{
    protected static string $resource = QuickTransactionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        // Handle CashFlow integration saat update (seperti di EditMember.php)
        $record = $this->record;
        $originalData = $this->record->getOriginal();
        
        // Jika status berubah dari pending ke paid
        if ($originalData['status'] === 'pending' && $record->status === 'paid') {
            CashFlow::createEntry(
                'income',
                'kasir',
                'Penjualan - ' . $record->product_name . ' (' . $record->guest_name . ')',
                $record->amount,
                $record->id,
                $record->payment_date
            );
        }
        
        // Jika status berubah dari paid ke pending, hapus entry cash flow
        if ($originalData['status'] === 'paid' && $record->status === 'pending') {
            CashFlow::where('reference_id', $record->id)
                ->where('source', 'kasir')
                ->delete();
        }
    }
}