<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:clear {--days=1 : Number of days old notifications to keep}';

    /**
     * The console command description.
     */
    protected $description = 'Clear old notifications from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        $this->info("Clearing notifications older than {$daysToKeep} day(s)...");
        $this->info("Cutoff date: " . $cutoffDate->format('Y-m-d H:i:s'));
        
        // Hapus notifikasi lama dari tabel notifications
        $deletedCount = DB::table('notifications')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
        
        $this->info("✅ Deleted {$deletedCount} old notifications");
        
        // Hapus juga dari tabel database_notifications jika ada
        $deletedDbNotifications = 0;
        if (DB::getSchemaBuilder()->hasTable('database_notifications')) {
            $deletedDbNotifications = DB::table('database_notifications')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            
            $this->info("✅ Deleted {$deletedDbNotifications} old database notifications");
        }
        
        // Hapus juga notifikasi Filament jika ada
        $deletedFilamentNotifications = 0;
        if (DB::getSchemaBuilder()->hasTable('filament_notifications')) {
            $deletedFilamentNotifications = DB::table('filament_notifications')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            
            $this->info("✅ Deleted {$deletedFilamentNotifications} old filament notifications");
        }
        
        $totalDeleted = $deletedCount + $deletedDbNotifications + $deletedFilamentNotifications;
        
        if ($totalDeleted > 0) {
            $this->info("🎉 Total {$totalDeleted} notifications cleared successfully!");
        } else {
            $this->info("ℹ️ No old notifications found to clear");
        }
        
        return 0;
    }
}