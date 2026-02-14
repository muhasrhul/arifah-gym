<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramHelper
{
    /**
     * Kirim pesan ke Telegram
     * 
     * @param string $message Pesan yang akan dikirim (support Markdown)
     * @return bool Success status
     */
    public static function send($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        
        // Jika tidak ada konfigurasi Telegram, skip
        if (!$botToken || !$chatId) {
            Log::warning('[Telegram] Bot token atau chat ID belum diset di .env');
            return false;
        }
        
        try {
            $response = Http::timeout(60) // Tingkatkan timeout jadi 60 detik
                ->retry(3, 100) // Retry 3x dengan delay 100ms
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                ]);
            
            if ($response->successful()) {
                Log::info('[Telegram] Notifikasi berhasil dikirim', [
                    'chat_id' => $chatId,
                    'message_preview' => substr($message, 0, 100)
                ]);
                return true;
            } else {
                Log::error('[Telegram] Gagal kirim notifikasi', [
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('[Telegram] Error kirim notifikasi', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Format pesan transaksi kasir cepat
     */
    public static function sendTransaksiKasir($itemName, $amount, $stockTersisa)
    {
        $message = "🛒 *TRANSAKSI KASIR CEPAT*\n\n";
        $message .= "📦 Produk\n";
        $message .= "├ {$itemName}\n\n";
        $message .= "💰 Harga\n";
        $message .= "├ Rp " . number_format($amount, 0, ',', '.') . "\n\n";
        $message .= "📊 Stock Tersisa\n";
        $message .= "├ {$stockTersisa} unit\n\n";
        $message .= "💳 Pembayaran\n";
        $message .= "├ Tunai (Kasir)\n\n";
        $message .= "🕐 Waktu\n";
        $message .= "└ " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "✅ Transaksi berhasil dicatat\n\n";
        $message .= "_ARIFAH Gym Management System_";
        
        return self::send($message);
    }
    
    /**
     * Format pesan pendaftaran member baru (dari web)
     */
    public static function sendPendaftaranBaru($member, $paket)
    {
        $message = "📝 *PENDAFTARAN MEMBER BARU*\n\n";
        $message .= "👤 Data Member\n";
        $message .= "├ Nama: {$member->name}\n";
        $message .= "├ 📞 {$member->phone}\n";
        $message .= "└ 📧 {$member->email}\n\n";
        $message .= "📦 Paket Dipilih\n";
        $message .= "├ {$paket}\n\n";
        $message .= "🕐 Waktu Daftar\n";
        $message .= "└ " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "⚠️ Status: *Menunggu Aktivasi*\n";
        $message .= "💡 Silakan aktivasi member di panel admin\n\n";
        $message .= "_ARIFAH Gym Management System_";
        
        return self::send($message);
    }
    
    /**
     * Format pesan aktivasi member & transaksi
     */
    public static function sendAktivasiMember($member, $transaction)
    {
        // Log untuk debug
        \Illuminate\Support\Facades\Log::info('[Telegram] Data member untuk notifikasi', [
            'member_id' => $member->id,
            'name' => $member->name,
            'fingerprint_id' => $member->fingerprint_id,
            'fingerprint_id_type' => gettype($member->fingerprint_id),
            'all_attributes' => $member->getAttributes()
        ]);
        
        $message = "✅ *AKTIVASI MEMBER & TRANSAKSI*\n\n";
        $message .= "👤 Data Member\n";
        $message .= "├ Nama: {$member->name}\n";
        $message .= "├ 📞 {$member->phone}\n";
        
        // Tambahkan Fingerprint ID
        if (!empty($member->fingerprint_id)) {
            $message .= "└ 🔐 Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "└ 🔐 Fingerprint: -\n\n";
        }
        
        $message .= "📦 Paket\n";
        $message .= "├ {$member->type}\n\n";
        $message .= "💰 Pembayaran\n";
        $message .= "├ Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "└ Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 Masa Aktif\n";
        $message .= "├ s/d " . \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') . "\n\n";
        $message .= "🕐 Waktu Transaksi\n";
        $message .= "└ " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🎉 Member aktif dan siap latihan!\n\n";
        $message .= "_ARIFAH Gym Management System_";
        
        return self::send($message);
    }
    
    /**
     * Format pesan perpanjangan member
     */
    public static function sendPerpanjanganMember($member, $transaction)
    {
        // Log untuk debug
        \Illuminate\Support\Facades\Log::info('[Telegram] Data member untuk notifikasi perpanjangan', [
            'member_id' => $member->id,
            'name' => $member->name,
            'fingerprint_id' => $member->fingerprint_id,
        ]);
        
        $message = "🔄 *PERPANJANGAN MEMBERSHIP*\n\n";
        $message .= "👤 Data Member\n";
        $message .= "├ Nama: {$member->name}\n";
        $message .= "├ 📞 {$member->phone}\n";
        
        // Tambahkan Fingerprint ID
        if (!empty($member->fingerprint_id)) {
            $message .= "└ 🔐 Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "└ 🔐 Fingerprint: -\n\n";
        }
        
        $message .= "📦 Paket\n";
        $message .= "├ {$member->type}\n\n";
        $message .= "💰 Pembayaran\n";
        $message .= "├ Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "└ Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 Masa Aktif Baru\n";
        $message .= "├ s/d " . \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') . "\n\n";
        $message .= "🕐 Waktu Transaksi\n";
        $message .= "└ " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "✅ Perpanjangan berhasil!\n\n";
        $message .= "_ARIFAH Gym Management System_";
        
        return self::send($message);
    }

    /**
     * Format pesan perpanjangan EARLY (H-2 sampai H-1)
     */
    public static function sendPerpanjanganEarly($member, $transaction)
    {
        // Log untuk debug
        \Illuminate\Support\Facades\Log::info('[Telegram] Data member untuk notifikasi perpanjangan early', [
            'member_id' => $member->id,
            'name' => $member->name,
            'fingerprint_id' => $member->fingerprint_id,
        ]);
        
        $message = "⚡ *PERPANJANGAN EARLY*\n\n";
        $message .= "👤 Data Member\n";
        $message .= "├ Nama: {$member->name}\n";
        $message .= "├ 📞 {$member->phone}\n";
        
        // Tambahkan Fingerprint ID
        if (!empty($member->fingerprint_id)) {
            $message .= "└ 🔐 Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "└ 🔐 Fingerprint: -\n\n";
        }
        
        $message .= "📦 Paket\n";
        $message .= "├ {$member->type}\n\n";
        $message .= "💰 Pembayaran\n";
        $message .= "├ Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "└ Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 Masa Aktif Baru\n";
        $message .= "├ s/d " . \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') . "\n\n";
        $message .= "🕐 Waktu Transaksi\n";
        $message .= "└ " . now()->format('d M Y, H:i') . " WITA\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "✅ Perpanjangan early berhasil!\n";
        $message .= "💡 Member tidak kehilangan sisa waktu membership\n\n";
        $message .= "_ARIFAH Gym Management System_";
        
        return self::send($message);
    }
}

