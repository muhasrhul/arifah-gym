<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Models\Attendance;
use App\Models\QuickTransaction; // GANTI: Pakai tabel terpisah
use App\Models\Product;
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
     * SISTEM BARU: Transaksi Langsung Tanpa Member Bayangan
     * Menggunakan tabel quick_transactions yang terpisah
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
        $nominal = "Rp " . number_format($amount, 0, ',', '.');

        // 3. ABSENSI: Hanya untuk produk latihan/harian (perlu member sementara)
        if (str_contains(strtolower($itemName), 'latihan') || str_contains(strtolower($itemName), 'harian')) {
            // Untuk absensi, tetap perlu member (tapi cuma untuk tracking kehadiran)
            $tempMember = Member::firstOrCreate(
                ['name' => 'Tamu Latihan Harian'],
                [
                    'email' => 'tamu.latihan@arifahgym.local',
                    'phone' => '000000000001',
                    'type' => 'Temporary Attendance',
                    'is_active' => true,
                    'join_date' => now(),
                    'expiry_date' => now()->addDays(1), // Expired besok
                ]
            );

            Attendance::create([
                'member_id' => $tempMember->id,
                'created_at' => now(),
            ]);
        }

        // 4. KURANGI STOCK PRODUK
        $product->decrement('stock', 1);
        
        // Clear cache dashboard agar pendapatan update langsung
        cache()->forget('stats_omset_hari_ini');
        cache()->forget('stats_total_omzet');

        // 5. SIMPAN KE TABEL QUICK_TRANSACTIONS (TANPA MEMBER!)
        $quickTransaction = QuickTransaction::create([
            'guest_name'     => str_contains(strtolower($itemName), 'latihan') || str_contains(strtolower($itemName), 'harian') 
                                ? 'Tamu Latihan' 
                                : 'Tamu Kantin',
            'product_name'   => $itemName,
            'order_id'       => 'KASIR-' . date('YmdHis'),
            'amount'         => $amount,
            'type'           => $itemName,
            'payment_method' => 'Cash',
            'payment_date'   => now(),
        ]);

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

        // 7. KIRIM NOTIFIKASI TELEGRAM
        \App\Helpers\TelegramHelper::sendTransaksiKasir($itemName, $amount, $product->stock);

        // 8. KIRIM NOTIFIKASI WHATSAPP KE OWNER (Gunakan data quick transaction)
        \App\Helpers\WhatsAppHelper::sendQuickTransactionNotification($quickTransaction);

        // 9. Notifikasi melayang (Toast) di layar kasir
        Notification::make()
            ->title('Transaksi Berhasil!')
            ->body("Pembayaran **{$itemName}** sebesar **{$nominal}** telah dicatat. Stock tersisa: **{$product->stock}**")
            ->success()
            ->send();
    }
}