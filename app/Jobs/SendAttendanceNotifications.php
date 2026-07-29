<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Member;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendAttendanceNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $memberId;
    public $totalLatihan;
    public $badge;

    /**
     * Create a new job instance.
     */
    public function __construct($memberId, $totalLatihan, $badge)
    {
        $this->memberId = $memberId;
        $this->totalLatihan = $totalLatihan;
        $this->badge = $badge;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Load member data
            $member = Member::find($this->memberId);
            
            if (!$member) {
                Log::warning("Member #{$this->memberId} not found for attendance notification");
                return;
            }

            // 1. Notifikasi ke Admin Filament
            $allAdmins = User::all(); 
            foreach ($allAdmins as $admin) {
                Notification::make()
                    ->title('Member Absen Baru!')
                    ->body("**{$member->name}** baru saja melakukan absensi. (Total: {$this->totalLatihan}x bulan ini)")
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->sendToDatabase($admin);
            }
            
            // 2. WhatsApp Notification
            if (env('FONNTE_TOKEN')) {
                try {
                    \App\Helpers\WhatsAppHelper::sendAbsenNotification($member, $this->totalLatihan, $this->badge);
                } catch (\Exception $e) {
                    Log::warning('WhatsApp notification failed: ' . $e->getMessage());
                }
            }
            
            // 3. Telegram Notification
            if (env('TELEGRAM_BOT_TOKEN')) {
                try {
                    \App\Helpers\TelegramHelper::sendAbsenNotification($member, $this->totalLatihan, $this->badge);
                } catch (\Exception $e) {
                    Log::warning('Telegram notification failed: ' . $e->getMessage());
                }
            }

            Log::info("Attendance notifications sent for member: {$member->name}");
            
        } catch (\Exception $e) {
            Log::error('Failed to send attendance notifications: ' . $e->getMessage());
            // Re-throw untuk trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Attendance notification job failed for member #{$this->memberId}: " . $exception->getMessage());
    }
}
