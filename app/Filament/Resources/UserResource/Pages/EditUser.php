<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Helpers\WhatsAppHelper;

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
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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
        // Jika password diubah
        if ($this->passwordChanged) {
            $user = $this->record;
            
            // Kirim notifikasi WhatsApp jika user punya nomor HP
            if (!empty($user->phone)) {
                $this->sendPasswordChangeNotification($user);
            }
            
            // Tampilkan notifikasi sukses
            Notification::make()
                ->success()
                ->title('Password berhasil diubah!')
                ->body('Silakan login kembali dengan password baru Anda.' . 
                       (!empty($user->phone) ? ' Notifikasi telah dikirim ke WhatsApp.' : ''))
                ->persistent()
                ->send();
            
            // Logout user
            Auth::guard('web')->logout();
            
            // Redirect ke halaman login Filament
            $this->redirect('/admin/login');
        }
    }
    
    /**
     * Kirim notifikasi WhatsApp saat password diubah
     */
    private function sendPasswordChangeNotification($user)
    {
        try {
            $adminUser = Auth::user();
            $adminName = $adminUser->name ?? 'Admin';
            $targetUserName = $user->name;
            $targetUserRole = match($user->role) {
                'super_admin' => 'Super Admin',
                'admin' => 'Admin', 
                'user' => 'Staff',
                default => ucfirst($user->role)
            };
            
            // Pesan untuk user yang passwordnya diubah
            $message = "🔐 *PASSWORD AKUN DIUBAH - ARIFAH GYM*\n\n";
            $message .= "Halo *{$targetUserName}*\n\n";
            $message .= "Password akun Anda telah diubah oleh *{$adminName}*.\n\n";
            $message .= "📋 *Detail Akun:*\n";
            $message .= "├ Nama: {$targetUserName}\n";
            $message .= "├ Role: {$targetUserRole}\n";
            $message .= "├ Email: {$user->email}\n";
            $message .= "└ Diubah oleh: {$adminName}\n\n";
            $message .= "🕐 *Waktu:* " . now()->format('d M Y, H:i') . " WITA\n\n";
            $message .= "⚠️ *PENTING:*\n";
            $message .= "• Silakan login dengan password baru\n";
            $message .= "• Jika Anda tidak meminta perubahan ini, segera hubungi admin\n";
            $message .= "• Jangan bagikan password kepada siapapun\n\n";
            $message .= "🔗 *Link Login:* " . url('/admin/login') . "\n\n";
            $message .= "ARIFAH Gym Management System";
            
            // Kirim ke user yang passwordnya diubah
            $result = WhatsAppHelper::sendMessage($user->phone, $message);
            
            if ($result['success']) {
                \Log::info('[WhatsApp] Notifikasi password change berhasil dikirim ke target user', [
                    'target_user' => $user->name,
                    'target_phone' => $user->phone,
                    'changed_by' => $adminName
                ]);
            } else {
                \Log::error('[WhatsApp] Gagal kirim notifikasi password change ke target user', [
                    'target_user' => $user->name,
                    'target_phone' => $user->phone,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }
            
            // Jika admin yang mengubah berbeda dengan user yang diubah, kirim notifikasi ke admin juga
            if ($adminUser->id !== $user->id && !empty($adminUser->phone)) {
                $adminMessage = "✅ *KONFIRMASI UBAH PASSWORD - ARIFAH GYM*\n\n";
                $adminMessage .= "Halo *{$adminName}*\n\n";
                $adminMessage .= "Anda telah berhasil mengubah password user:\n\n";
                $adminMessage .= "👤 *User yang diubah:*\n";
                $adminMessage .= "├ Nama: {$targetUserName}\n";
                $adminMessage .= "├ Role: {$targetUserRole}\n";
                $adminMessage .= "├ Email: {$user->email}\n";
                $adminMessage .= "└ WhatsApp: {$user->phone}\n\n";
                $adminMessage .= "🕐 *Waktu:* " . now()->format('d M Y, H:i') . " WITA\n\n";
                $adminMessage .= "📱 Notifikasi telah dikirim ke user yang bersangkutan.\n\n";
                $adminMessage .= "ARIFAH Gym Management System";
                
                $adminResult = WhatsAppHelper::sendMessage($adminUser->phone, $adminMessage);
                
                if ($adminResult['success']) {
                    \Log::info('[WhatsApp] Konfirmasi password change berhasil dikirim ke admin', [
                        'admin_user' => $adminName,
                        'admin_phone' => $adminUser->phone,
                        'target_user' => $user->name
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('[WhatsApp] Error kirim notifikasi password change', [
                'target_user' => $user->name ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
}