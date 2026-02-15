<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    /**
     * Kirim pesan WhatsApp via Fonnte API
     * 
     * @param string $phone Nomor HP (format: 08xxx atau 628xxx)
     * @param string $message Isi pesan
     * @return array Response dari API
     */
    public static function sendMessage($phone, $message)
    {
        // Validasi API token
        $token = env('FONNTE_TOKEN');
        if (!$token) {
            Log::error('FONNTE_TOKEN tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'FONNTE_TOKEN tidak dikonfigurasi'
            ];
        }

        // Format nomor HP (pastikan format 628xxx)
        $phone = self::formatPhoneNumber($phone);

        try {
            // Kirim request ke Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ]);

            $result = $response->json();

            // Log response
            Log::info('WhatsApp sent', [
                'phone' => $phone,
                'status' => $result['status'] ?? 'unknown',
                'message' => substr($message, 0, 50) . '...'
            ]);

            return [
                'success' => ($result['status'] ?? false) === true,
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Format nomor HP ke format 628xxx
     * 
     * @param string $phone
     * @return string
     */
    private static function formatPhoneNumber($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak dimulai dengan 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Template: Reminder H-7 membership akan expired
     */
    public static function sendReminderH7($member)
    {
        $message = "🔔 *REMINDER MEMBERSHIP - ARIFAH GYM*\n\n";
        $message .= "Halo *{$member->name}*\n\n";
        $message .= "Membership Anda akan berakhir dalam *7 hari* lagi.\n\n";
        $message .= "📅 Tanggal Berakhir: *" . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "*\n";
        $message .= "💪 Paket: *{$member->type}*\n\n";
        $message .= "Segera perpanjang membership Anda agar tetap bisa menikmati fasilitas gym!\n\n";
        $message .= "Hubungi kasir atau datang langsung ke ARIFAH Gym.\n\n";
        $message .= "Terima kasih!";

        return self::sendMessage($member->phone, $message);
    }

    /**
     * Template: Reminder H-3 membership akan expired
     */
    public static function sendReminderH3($member)
    {
        $message = "⚠️ *REMINDER PENTING - ARIFAH GYM*\n\n";
        $message .= "Halo *{$member->name}*\n\n";
        $message .= "Membership Anda tinggal *3 hari* lagi!\n\n";
        $message .= "📅 Tanggal Berakhir: *" . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "*\n";
        $message .= "💪 Paket: *{$member->type}*\n\n";
        $message .= "Jangan sampai terputus! Perpanjang sekarang dan dapatkan promo spesial!\n\n";
        $message .= "Hubungi kasir atau datang langsung ke ARIFAH Gym.\n\n";
        $message .= "Terima kasih!";

        return self::sendMessage($member->phone, $message);
    }

    /**
     * Template: Reminder H-1 membership akan expired
     */
    public static function sendReminderH1($member)
    {
        $message = "🚨 *REMINDER - ARIFAH GYM*\n\n";
        $message .= "Halo *{$member->name}*\n\n";
        $message .= "Membership Anda akan berakhir *BESOK*!\n\n";
        $message .= "📅 Tanggal Berakhir: *" . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "*\n";
        $message .= "💪 Paket: *{$member->type}*\n\n";
        $message .= "Ini kesempatan terakhir! Perpanjang hari ini dan tetap aktif!\n\n";
        $message .= "Hubungi kasir atau datang langsung ke ARIFAH Gym.\n\n";
        $message .= "Terima kasih!";

        return self::sendMessage($member->phone, $message);
    }

    /**
     * Template: Notifikasi setelah perpanjangan membership
     */
    public static function sendPerpanjanganSuccess($member)
    {
        $message = "✅ *PERPANJANGAN BERHASIL - ARIFAH GYM*\n\n";
        $message .= "Terima kasih *{$member->name}*\n\n";
        $message .= "Membership Anda sudah berhasil diperpanjang!\n\n";
        $message .= "📅 Aktif Sampai: *" . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "*\n";
        $message .= "💪 Paket: *{$member->type}*\n\n";
        $message .= "Selamat berlatih dan raih target fitness Anda!\n\n";
        $message .= "Terima kasih sudah mempercayai ARIFAH Gym!";

        return self::sendMessage($member->phone, $message);
    }

    /**
     * Template: Ucapan ulang tahun
     */
    public static function sendBirthdayWish($member)
    {
        $message = "🎂 *SELAMAT ULANG TAHUN - ARIFAH GYM*\n\n";
        $message .= "Selamat ulang tahun *{$member->name}*\n\n";
        $message .= "Semoga panjang umur, sehat selalu, dan semakin semangat dalam mencapai target fitness!\n\n";
        $message .= "Terima kasih sudah menjadi bagian dari keluarga ARIFAH Gym!\n\n";
        $message .= "Salam sehat,\n";
        $message .= "ARIFAH Gym Team";

        return self::sendMessage($member->phone, $message);
    }

    /**
     * Template: Blast promo (custom message)
     */
    public static function sendPromo($phone, $message)
    {
        $promoMessage = "📢 *PROMO SPESIAL - ARIFAH GYM*\n\n";
        $promoMessage .= $message . "\n\n";
        $promoMessage .= "Jangan lewatkan kesempatan ini!\n\n";
        $promoMessage .= "Info lebih lanjut hubungi kasir atau datang langsung ke ARIFAH Gym.\n\n";
        $promoMessage .= "Terima kasih!";

        return self::sendMessage($phone, $promoMessage);
    }

    /**
     * Template: Laporan harian ke owner tentang member yang expired HARI INI
     */
    public static function sendDailyReportToOwner($membersExpiredToday)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "🚨 *NOTIFIKASI EXPIRED - ARIFAH GYM*\n\n";
        $message .= "Tanggal: " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y') . "\n\n";

        if ($membersExpiredToday->count() > 0) {
            $message .= "⚠️ *MEMBER YANG EXPIRED HARI INI:*\n\n";
            $message .= "Total: *{$membersExpiredToday->count()} member*\n\n";
            
            foreach ($membersExpiredToday as $member) {
                $expiryDate = \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y');
                $message .= "• *{$member->name}*\n";
                $message .= "  Paket: {$member->type}\n";
                $message .= "  Expired: {$expiryDate}\n";
                $message .= "  HP: {$member->phone}\n\n";
            }

            $message .= "Segera hubungi member untuk perpanjangan!\n\n";
        } else {
            $message .= "✅ *TIDAK ADA MEMBER YANG EXPIRED HARI INI*\n\n";
            $message .= "Semua member masih aktif!\n\n";
        }

        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Laporan ke owner tentang member yang AKAN EXPIRED BESOK (H-1)
     */
    public static function sendReminderReportToOwner($membersH1)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "🚨 *LAPORAN REMINDER H-1 - ARIFAH GYM*\n\n";
        $message .= "Tanggal: " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y') . "\n\n";

        if ($membersH1->count() > 0) {
            $message .= "⚠️ *MEMBER YANG AKAN EXPIRED BESOK:*\n\n";
            $message .= "Total: *{$membersH1->count()} member*\n\n";
            
            foreach ($membersH1 as $member) {
                $expiryDate = \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y');
                $message .= "• *{$member->name}*\n";
                $message .= "  Paket: {$member->type}\n";
                $message .= "  Expired: {$expiryDate}\n";
                $message .= "  HP: {$member->phone}\n\n";
            }

            $message .= "Pesan reminder sudah dikirim ke member.\n\n";
        } else {
            $message .= "✅ *TIDAK ADA MEMBER YANG AKAN EXPIRED BESOK*\n\n";
            $message .= "Semua member masih aman!\n\n";
        }

        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Notifikasi transaksi produk ke owner
     */
    public static function sendTransactionNotification($transaction)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "💰 *TRANSAKSI BARU - ARIFAH GYM*\n\n";
        $message .= "Tanggal: " . \Carbon\Carbon::parse($transaction->payment_date)->translatedFormat('d F Y H:i') . "\n\n";
        
        // Customer info
        if ($transaction->member) {
            $message .= "👤 *Customer:* {$transaction->member->name}\n";
        } elseif ($transaction->guest_name) {
            $message .= "👤 *Customer:* {$transaction->guest_name} (Tamu)\n";
        } else {
            $message .= "👤 *Customer:* Umum\n";
        }
        
        // Transaction details
        $message .= "📦 *Produk:* {$transaction->type}\n";
        $message .= "💵 *Harga:* Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "💳 *Metode:* {$transaction->payment_method}\n";
        
        // Status
        $statusEmoji = $transaction->status === 'completed' ? '✅' : '⏳';
        $statusText = $transaction->status === 'completed' ? 'Lunas' : 'Pending';
        $message .= "{$statusEmoji} *Status:* {$statusText}\n\n";
        
        $message .= "Terima kasih!\n\n";
        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Notifikasi pendaftaran member baru ke owner
     */
    public static function sendPendaftaranBaru($member, $paket)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "📝 *PENDAFTARAN MEMBER BARU - ARIFAH GYM*\n\n";
        $message .= "👤 *Data Member*\n";
        $message .= "Nama: {$member->name}\n";
        $message .= "HP: {$member->phone}\n";
        $message .= "Email: {$member->email}\n\n";
        $message .= "📦 *Paket:* {$paket}\n\n";
        $message .= "🕐 *Waktu:* " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . "\n\n";
        $message .= "⚠️ Status: Menunggu Aktivasi\n";
        $message .= "Silakan aktivasi member di panel admin.\n\n";
        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Notifikasi aktivasi member ke owner
     */
    public static function sendAktivasiMember($member, $transaction)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "✅ *AKTIVASI MEMBER - ARIFAH GYM*\n\n";
        $message .= "👤 *Data Member*\n";
        $message .= "Nama: {$member->name}\n";
        $message .= "HP: {$member->phone}\n";
        
        if (!empty($member->fingerprint_id)) {
            $message .= "Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "Fingerprint: -\n\n";
        }
        
        $message .= "📦 *Paket:* {$member->type}\n\n";
        $message .= "💰 *Pembayaran*\n";
        $message .= "Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 *Aktif s/d:* " . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "\n\n";
        $message .= "🕐 *Waktu:* " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . "\n\n";
        $message .= "Member aktif dan siap latihan!\n\n";
        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Notifikasi perpanjangan member ke owner
     */
    public static function sendPerpanjanganMember($member, $transaction)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "🔄 *PERPANJANGAN MEMBERSHIP - ARIFAH GYM*\n\n";
        $message .= "👤 *Data Member*\n";
        $message .= "Nama: {$member->name}\n";
        $message .= "HP: {$member->phone}\n";
        
        if (!empty($member->fingerprint_id)) {
            $message .= "Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "Fingerprint: -\n\n";
        }
        
        $message .= "📦 *Paket:* {$member->type}\n\n";
        $message .= "💰 *Pembayaran*\n";
        $message .= "Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 *Aktif s/d:* " . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "\n\n";
        $message .= "🕐 *Waktu:* " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . "\n\n";
        $message .= "Perpanjangan berhasil!\n\n";
        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }

    /**
     * Template: Notifikasi perpanjangan EARLY (H-2 sampai H-1) ke owner
     */
    public static function sendPerpanjanganEarly($member, $transaction)
    {
        $ownerPhone = env('OWNER_WHATSAPP');
        
        if (!$ownerPhone) {
            Log::warning('OWNER_WHATSAPP tidak ditemukan di .env');
            return [
                'success' => false,
                'message' => 'OWNER_WHATSAPP tidak dikonfigurasi'
            ];
        }

        $message = "⚡ *PERPANJANGAN EARLY - ARIFAH GYM*\n\n";
        $message .= "👤 *Data Member*\n";
        $message .= "Nama: {$member->name}\n";
        $message .= "HP: {$member->phone}\n";
        
        if (!empty($member->fingerprint_id)) {
            $message .= "Fingerprint: {$member->fingerprint_id}\n\n";
        } else {
            $message .= "Fingerprint: -\n\n";
        }
        
        $message .= "📦 *Paket:* {$member->type}\n\n";
        $message .= "💰 *Pembayaran*\n";
        $message .= "Total: Rp " . number_format($transaction->amount, 0, ',', '.') . "\n";
        $message .= "Metode: {$transaction->payment_method}\n\n";
        $message .= "📅 *Aktif s/d:* " . \Carbon\Carbon::parse($member->expiry_date)->translatedFormat('d F Y') . "\n\n";
        $message .= "🕐 *Waktu:* " . \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y H:i') . "\n\n";
        $message .= "✅ Perpanjangan early berhasil!\n";
        $message .= "Member tidak kehilangan sisa waktu membership.\n\n";
        $message .= "ARIFAH Gym System";

        return self::sendMessage($ownerPhone, $message);
    }
}

