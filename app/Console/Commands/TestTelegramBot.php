<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TelegramHelper;

class TestTelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test koneksi bot Telegram dan kirim pesan test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Testing Telegram Bot...');
        $this->newLine();
        
        // Cek konfigurasi
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        
        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN tidak ditemukan di .env');
            $this->info('💡 Tambahkan: TELEGRAM_BOT_TOKEN=your_token_here');
            return 1;
        }
        
        if (!$chatId) {
            $this->error('❌ TELEGRAM_CHAT_ID tidak ditemukan di .env');
            $this->info('💡 Tambahkan: TELEGRAM_CHAT_ID=your_chat_id_here');
            return 1;
        }
        
        $this->info("✅ Bot Token: " . substr($botToken, 0, 20) . "...");
        $this->info("✅ Chat ID: {$chatId}");
        $this->newLine();
        
        // Kirim pesan test
        $this->info('📤 Mengirim pesan test...');
        
        $message = "🤖 *TEST BOT TELEGRAM*\n\n";
        $message .= "✅ Bot berhasil terhubung!\n\n";
        $message .= "📅 Waktu: " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "ARIFAH Gym Management System";
        
        $result = TelegramHelper::send($message);
        
        if ($result) {
            $this->newLine();
            $this->info('✅ BERHASIL! Pesan test telah dikirim ke Telegram');
            $this->info('📱 Cek aplikasi Telegram Anda');
            return 0;
        } else {
            $this->newLine();
            $this->error('❌ GAGAL mengirim pesan');
            $this->info('💡 Cek log di storage/logs/laravel.log untuk detail error');
            return 1;
        }
    }
}
