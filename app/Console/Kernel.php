<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use App\Models\Member;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ========================================
        // AUTO UPDATE STATUS MEMBER EXPIRED
        // ========================================
        
        // PRODUCTION: Jalankan setiap hari jam 00:01 (tengah malam)
        $schedule->command('members:update-expired')
            ->dailyAt('00:01')
            ->timezone('Asia/Makassar')
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        // TESTING: Uncomment baris di bawah untuk test (jalankan setiap menit)
        // Setelah test berhasil, comment lagi dan gunakan yang dailyAt
        // $schedule->command('members:update-expired')
        //     ->everyMinute()
        //     ->appendOutputTo(storage_path('logs/scheduler.log'));

        // ========================================
        // AUTO KIRIM REMINDER WHATSAPP MEMBERSHIP
        // ========================================
        
        // PRODUCTION: Jalankan setiap hari jam 09:00 pagi
        $schedule->command('membership:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Asia/Makassar')
            ->appendOutputTo(storage_path('logs/whatsapp-reminders.log'));
        
        // TESTING: Uncomment baris di bawah untuk test (jalankan setiap menit)
        // Setelah test berhasil, comment lagi dan gunakan yang dailyAt
        // $schedule->command('membership:send-reminders')
        //     ->everyMinute()
        //     ->appendOutputTo(storage_path('logs/whatsapp-reminders.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}