<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetTelegramChatId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:get-chat-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dapatkan Chat ID dari bot Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Mencari Chat ID dari Telegram Bot...');
        $this->newLine();
        
        // Cek token
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN tidak ditemukan di .env');
            $this->newLine();
            $this->info('📝 Langkah-langkah:');
            $this->info('1. Buka Telegram, cari @BotFather');
            $this->info('2. Kirim: /newbot');
            $this->info('3. Ikuti instruksi untuk buat bot baru');
            $this->info('4. Copy token yang diberikan');
            $this->info('5. Tambahkan ke .env: TELEGRAM_BOT_TOKEN=your_token');
            return 1;
        }
        
        $this->info("✅ Bot Token ditemukan: " . substr($botToken, 0, 20) . "...");
        $this->newLine();
        
        try {
            // Get updates dari Telegram
            $response = Http::get("https://api.telegram.org/bot{$botToken}/getUpdates");
            
            if (!$response->successful()) {
                $this->error('❌ Gagal mengakses Telegram API');
                $this->error('Response: ' . $response->body());
                return 1;
            }
            
            $data = $response->json();
            
            if (!isset($data['ok']) || !$data['ok']) {
                $this->error('❌ Token tidak valid atau bot bermasalah');
                $this->error('Response: ' . json_encode($data));
                return 1;
            }
            
            $updates = $data['result'] ?? [];
            
            if (empty($updates)) {
                $this->warn('⚠️  Tidak ada pesan ditemukan');
                $this->newLine();
                $this->info('📝 Cara mendapatkan Chat ID:');
                $this->info('1. Buka Telegram, cari bot Anda (username bot)');
                $this->info('2. Klik START atau kirim pesan apa saja');
                $this->info('3. Jalankan command ini lagi: php artisan telegram:get-chat-id');
                $this->newLine();
                $this->info('💡 Untuk group:');
                $this->info('1. Buat group baru');
                $this->info('2. Tambahkan bot ke group');
                $this->info('3. Kirim pesan di group');
                $this->info('4. Jalankan command ini lagi');
                return 1;
            }
            
            // Tampilkan semua chat yang ditemukan
            $this->info('✅ Ditemukan ' . count($updates) . ' pesan');
            $this->newLine();
            
            $chats = [];
            foreach ($updates as $update) {
                $chat = $update['message']['chat'] ?? null;
                if ($chat) {
                    $chatId = $chat['id'];
                    $chatType = $chat['type'];
                    $chatTitle = $chat['title'] ?? $chat['first_name'] ?? 'Unknown';
                    
                    if (!isset($chats[$chatId])) {
                        $chats[$chatId] = [
                            'id' => $chatId,
                            'type' => $chatType,
                            'title' => $chatTitle
                        ];
                    }
                }
            }
            
            if (empty($chats)) {
                $this->warn('⚠️  Tidak ada chat ditemukan dalam pesan');
                return 1;
            }
            
            $this->info('📋 DAFTAR CHAT:');
            $this->newLine();
            
            foreach ($chats as $chat) {
                $type = $chat['type'] === 'private' ? '👤 Personal' : '👥 Group';
                $this->line("$type: {$chat['title']}");
                $this->line("   Chat ID: {$chat['id']}");
                $this->newLine();
            }
            
            // Ambil chat ID pertama sebagai rekomendasi
            $recommendedChat = reset($chats);
            $recommendedId = $recommendedChat['id'];
            
            $this->info('💡 REKOMENDASI:');
            $this->info("Tambahkan ke .env:");
            $this->line("TELEGRAM_CHAT_ID={$recommendedId}");
            $this->newLine();
            
            $this->info('✅ Setelah update .env, test dengan: php artisan telegram:test');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
