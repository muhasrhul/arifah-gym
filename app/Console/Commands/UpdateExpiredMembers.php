<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateExpiredMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status member yang sudah melewati tanggal expired menjadi non-aktif';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Memulai pengecekan member expired...');
        
        // Ambil tanggal hari ini (timezone Makassar)
        $today = Carbon::now('Asia/Makassar')->startOfDay();
        
        // Cari member yang:
        // 1. Masih aktif (is_active = true)
        // 2. Punya expiry_date
        // 3. Expiry_date sudah lewat dari hari ini
        $expiredMembers = Member::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->get();
        
        $count = $expiredMembers->count();
        
        if ($count === 0) {
            $this->info('✅ Tidak ada member yang expired hari ini.');
            Log::info('[Scheduler] Tidak ada member expired', [
                'checked_at' => $today->format('Y-m-d H:i:s')
            ]);
    
            // Kirim notifikasi WhatsApp ke owner (meskipun tidak ada member expired)
            $this->sendWhatsAppNotification(collect([]), $today);
            $this->info('📱 Notifikasi WhatsApp terkirim.');
            
            return Command::SUCCESS;
        }
        
        // Update status menjadi non-aktif
        $this->info("📋 Ditemukan {$count} member yang sudah expired:");
        
        $message = "🚨 *MEMBER EXPIRED* - " . $today->format('d M Y') . "\n\n";
        $message .= "Ada *{$count} member* yang masa aktifnya habis:\n\n";
        
        $no = 1;
        foreach ($expiredMembers as $member) {
            $this->line("   - {$member->name} (Expired: {$member->expiry_date})");
            
            // Update status menjadi non-aktif
            $member->update(['is_active' => false]);
            
            // Format untuk Telegram
            $expiredDate = Carbon::parse($member->expiry_date)->format('d M Y');
            $message .= "{$no}. *{$member->name}*\n";
            $message .= "   📞 {$member->phone}\n";
            $message .= "   📅 Expired: {$expiredDate}\n\n";
            
            Log::info('[Scheduler] Member expired diupdate', [
                'member_id' => $member->id,
                'name' => $member->name,
                'expiry_date' => $member->expiry_date,
                'updated_at' => now()->format('Y-m-d H:i:s')
            ]);
            
            $no++;
        }
        
        $message .= "Status sudah diupdate menjadi non-aktif.\n\n";
        $message .= "---\n";
        $message .= "_ARIFAH Gym Management System_";
        
        // Kirim notifikasi ke Telegram
        $this->sendTelegramNotification($message);
        
        // Kirim notifikasi ke WhatsApp Owner
        $this->sendWhatsAppNotification($expiredMembers, $today);
        
        $this->info("✅ Berhasil update {$count} member menjadi non-aktif.");
        
        // Clear cache widget
        cache()->forget('expired_members_ids');
        cache()->forget('pendaftar_baru_count');
        cache()->forget('pendaftar_baru_exists');
        
        $this->info('🗑️  Cache widget dibersihkan.');
        $this->info('📱 Notifikasi Telegram terkirim.');
        $this->info('📱 Notifikasi WhatsApp terkirim.');
        
        return Command::SUCCESS;
    }
    
    /**
     * Kirim notifikasi ke WhatsApp Owner
     */
    private function sendWhatsAppNotification($expiredMembers, $today)
    {
        $ownerPhone = config('services.whatsapp.owner');
        
        if (!$ownerPhone) {
            $this->warn('⚠️  OWNER_WHATSAPP belum dikonfigurasi. Notifikasi WhatsApp tidak terkirim.');
            Log::warning('[WhatsApp] OWNER_WHATSAPP belum diset di config');
            return;
        }
        
        try {
            // Format pesan WhatsApp
            $message = "🚨 *MEMBER EXPIRED - ARIFAH GYM*\n\n";
            $message .= "Tanggal: " . $today->translatedFormat('d F Y') . "\n\n";
            
            if ($expiredMembers->count() > 0) {
                $message .= "⚠️ *MEMBER YANG SUDAH EXPIRED:*\n\n";
                $message .= "Total: *{$expiredMembers->count()} member*\n\n";
                
                foreach ($expiredMembers as $member) {
                    $expiryDate = Carbon::parse($member->expiry_date)->translatedFormat('d F Y');
                    $message .= "• *{$member->name}*\n";
                    $message .= "  Paket: {$member->type}\n";
                    $message .= "  Expired: {$expiryDate}\n";
                    $message .= "  HP: {$member->phone}\n\n";
                }
                
                $message .= "Status sudah diupdate menjadi non-aktif.\n\n";
                $message .= "Segera hubungi member untuk perpanjangan!\n\n";
            } else {
                $message .= "✅ *TIDAK ADA MEMBER YANG EXPIRED*\n\n";
                $message .= "Semua member masih aktif!\n\n";
            }
            
            $message .= "ARIFAH Gym System";
            
            // Kirim via WhatsAppHelper
            $result = \App\Helpers\WhatsAppHelper::sendMessage($ownerPhone, $message);
            
            if ($result['success']) {
                Log::info('[WhatsApp] Notifikasi member expired berhasil dikirim ke owner', [
                    'phone' => $ownerPhone,
                    'count' => $expiredMembers->count()
                ]);
            } else {
                Log::error('[WhatsApp] Gagal kirim notifikasi member expired', [
                    'phone' => $ownerPhone,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
                $this->error('❌ Gagal kirim WhatsApp: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('[WhatsApp] Error kirim notifikasi member expired', [
                'error' => $e->getMessage()
            ]);
            $this->error('❌ Error WhatsApp: ' . $e->getMessage());
        }
    }
    
    /**
     * Kirim notifikasi ke Telegram
     */
    private function sendTelegramNotification($message)
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');
        
        // Jika tidak ada konfigurasi Telegram, skip
        if (!$botToken || !$chatId) {
            $this->warn('⚠️  Telegram belum dikonfigurasi. Notifikasi tidak terkirim.');
            Log::warning('[Telegram] Bot token atau chat ID belum diset di config');
            return;
        }
        
        try {
            $response = Http::timeout(30)
                ->retry(3, 1000) // Retry 3x dengan delay 1 detik
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ]);
            
            // Cek status HTTP response
            if (!$response->successful()) {
                Log::error('[Telegram] HTTP Error', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                $this->error('❌ Gagal kirim notifikasi Telegram (HTTP ' . $response->status() . ')');
                return;
            }

            $result = $response->json();
            
            // Validasi response structure
            if (!is_array($result) || !isset($result['ok'])) {
                Log::error('[Telegram] Invalid Response Format', [
                    'response' => $response->body()
                ]);
                $this->error('❌ Format response Telegram tidak valid');
                return;
            }

            // Cek status dari API Telegram
            if ($result['ok'] === true) {
                Log::info('[Telegram] Notifikasi berhasil dikirim', [
                    'chat_id' => $chatId,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'message_length' => strlen($message)
                ]);
            } else {
                Log::error('[Telegram] API returned failure', [
                    'error_code' => $result['error_code'] ?? null,
                    'description' => $result['description'] ?? 'Unknown error'
                ]);
                $this->error('❌ Telegram API Error: ' . ($result['description'] ?? 'Unknown error'));
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[Telegram] Connection Error', [
                'error' => $e->getMessage()
            ]);
            $this->error('❌ Koneksi ke Telegram gagal: ' . $e->getMessage());

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('[Telegram] Request Error', [
                'error' => $e->getMessage(),
                'response' => $e->response ? $e->response->body() : null
            ]);
            $this->error('❌ Request ke Telegram gagal: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('[Telegram] Unexpected Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('❌ Error tidak terduga: ' . $e->getMessage());
        }
    }
}
