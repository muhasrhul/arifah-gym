<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\Product; // TAMBAHAN: Untuk ambil data produk kantin
use App\Models\User;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class KasirCepat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';
    protected static ?string $title = 'Kasir Cepat & Kantin';
    protected static string $view = 'filament.pages.kasir-cepat';

    // PERMISSION: Semua role bisa akses (ini untuk kasir)
    public static function canAccess(): bool
    {
        return true; // Super Admin, Admin, dan Staff bisa akses
    }

    // TAMBAHAN: Fungsi untuk mengirim data produk ke file Blade (tampilan)
    protected function getViewData(): array
    {
        // Ambil produk langsung tanpa cache (real-time)
        $products = Product::where('is_active', true)->get();
        
        return [
            'products' => $products,
        ];
    }

    /**
     * Logika Pembayaran Dinamis
     * Sekarang menerima ID Produk, bukan lagi input manual
     */
    public function bayarHarian($productId)
    {
        // 1. Ambil data produk dari database berdasarkan ID yang diklik
        $product = Product::find($productId);

        if (!$product) {
            Notification::make()
                ->title('Produk tidak ditemukan!')
                ->danger()
                ->send();
            return;
        }

        // 2. CEK STOCK - Jika habis, tolak transaksi
        if ($product->stock <= 0) {
            Notification::make()
                ->title('Stock Habis!')
                ->body("Produk **{$product->name}** sudah habis. Silakan isi stock terlebih dahulu.")
                ->danger()
                ->send();
            return;
        }

        $amount = $product->price;
        $itemName = $product->name;

        // 3. Cari member "Tamu Harian" sebagai penampung transaksi
        $member = Member::where('name', 'Tamu Harian')->first();

        if (!$member) {
            Notification::make()
                ->title('Member "Tamu Harian" Belum Ada!')
                ->body('Silakan buat member dengan nama "Tamu Harian" terlebih dahulu di menu Member.')
                ->danger()
                ->send();
            return;
        }

        $nominal = "Rp " . number_format($amount, 0, ',', '.');

        // 4. LOGIKA ABSENSI: Otomatis jika nama produk mengandung kata 'Latihan' atau 'Harian'
        // Bapak bisa sesuaikan logika ini jika ingin lebih spesifik
        if (str_contains(strtolower($itemName), 'latihan') || str_contains(strtolower($itemName), 'harian')) {
            Attendance::create([
                'member_id' => $member->id,
                'created_at' => now(),
            ]);
        }

        // 5. KURANGI STOCK PRODUK
        $product->decrement('stock', 1);
        
        // Clear cache dashboard agar pendapatan update langsung
        cache()->forget('stats_omset_hari_ini');
        cache()->forget('stats_total_omzet');

        // 6. KIRIM NOTIFIKASI KE DATABASE ADMIN
        $admins = User::all();
        foreach ($admins as $admin) {
            Notification::make()
                ->title("Pembayaran {$itemName}")
                ->body("Kasir baru saja mencatat penjualan **{$itemName}** seharga **{$nominal}**. Stock tersisa: **{$product->stock}**")
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->sendToDatabase($admin);
        }

        // 6.1 KIRIM NOTIFIKASI TELEGRAM
        \App\Helpers\TelegramHelper::sendTransaksiKasir($itemName, $amount, $product->stock);

        // 7. LOGIKA KEUANGAN: Catat ke Tabel Transaction
        $transaction = Transaction::create([
            'member_id'      => $member->id,
            'guest_name'     => 'Tamu Kantin',
            'order_id'       => 'REG-' . date('YmdHis'),
            'amount'         => $amount,
            'type'           => $itemName, 
            'payment_method' => 'Cash',
            'status'         => 'paid', // Langsung lunas karena offline
            'payment_date'   => now(),
        ]);

        // 7.1 KIRIM NOTIFIKASI WHATSAPP KE OWNER
        \App\Helpers\WhatsAppHelper::sendTransactionNotification($transaction);

        // 8. Notifikasi melayang (Toast) di layar kasir
        Notification::make()
            ->title('Transaksi Berhasil!')
            ->body("Pembayaran **{$itemName}** sebesar **{$nominal}** telah dicatat. Stock tersisa: **{$product->stock}**")
            ->success()
            ->send();
    }
}