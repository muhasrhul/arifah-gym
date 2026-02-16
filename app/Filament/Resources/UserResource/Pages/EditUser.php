<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected $passwordChanged = false;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan info apakah password diubah
        $this->passwordChanged = !empty($data['password']);
        
        // Jika password kosong, hapus dari data yang akan disimpan
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Jika password diisi, hash password
            $data['password'] = Hash::make($data['password']);
        }
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Jika password diubah, logout user dan redirect ke halaman login Filament
        if ($this->passwordChanged) {
            // Tampilkan notifikasi sukses
            Notification::make()
                ->success()
                ->title('Password berhasil diubah!')
                ->body('Silakan login kembali dengan password baru Anda.')
                ->persistent()
                ->send();
            
            // Logout user
            Auth::guard('web')->logout();
            
            // Redirect ke halaman login Filament
            $this->redirect('/admin/login');
        }
    }
}