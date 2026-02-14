<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Helpers\WhatsAppHelper;
use Carbon\Carbon;

class SendMembershipReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WhatsApp ke member yang membership-nya akan expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Memulai pengiriman reminder membership...');
        
        $today = Carbon::now('Asia/Makassar')->startOfDay();
        
        // Counter
        $sentH1 = 0;
        $failed = 0;

        // ========================================
        // REMINDER H-1 (1 hari sebelum expired / BESOK EXPIRED)
        // ========================================
        $h1Date = $today->copy()->addDays(1);
        $membersH1 = Member::where('is_active', true)
                          ->whereDate('expiry_date', $h1Date)
                          ->whereNotNull('phone')
                          ->get();

        $this->info("📅 H-1 (Besok Expired): Ditemukan {$membersH1->count()} member");

        foreach ($membersH1 as $member) {
            $this->line("   Mengirim ke {$member->name} ({$member->phone})...");
            
            $result = WhatsAppHelper::sendReminderH1($member);
            
            if ($result['success']) {
                $sentH1++;
                $this->info("   ✅ Berhasil");
            } else {
                $failed++;
                $this->error("   ❌ Gagal: " . ($result['message'] ?? 'Unknown error'));
            }
            
            sleep(2);
        }

        // ========================================
        // SUMMARY
        // ========================================
        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->table(
            ['Kategori', 'Jumlah'],
            [
                ['H-1 (Besok)', $sentH1],
                ['Gagal', $failed],
                ['TOTAL TERKIRIM', $sentH1],
            ]
        );

        // ========================================
        // KIRIM LAPORAN KE OWNER (MEMBER YANG AKAN EXPIRED BESOK)
        // ========================================
        $this->newLine();
        $this->info('📱 Mengirim laporan ke Owner...');
        
        $this->info("📋 Member yang akan expired besok: {$membersH1->count()} member");
        
        $ownerResult = WhatsAppHelper::sendReminderReportToOwner($membersH1);
        
        if ($ownerResult['success']) {
            $this->info('✅ Laporan berhasil dikirim ke Owner');
        } else {
            $this->error('❌ Gagal kirim laporan ke Owner: ' . ($ownerResult['message'] ?? 'Unknown error'));
            $this->warn('💡 Pastikan OWNER_WHATSAPP sudah diset di .env');
        }

        $this->info('✅ Selesai!');
        
        return Command::SUCCESS;
    }
}

