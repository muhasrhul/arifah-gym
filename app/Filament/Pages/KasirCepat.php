<?php

namespace App\Filament\Pages;

use App\Models\QuickTransaction; // Tabel terpisah untuk kasir cepat
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
     * SISTEM BARU: Transaksi Langsung 100% Tanpa Member Bayangan
     * Menggunakan tabel quick_transactions yang terpisah
     * TIDAK ADA absensi untuk kasir cepat (tamu harian tidak perlu tracking detail)
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

        // 3. KURANGI STOCK PRODUK
        $product->decrement('stock', 1);
        
        // Clear cache dashboard agar pendapatan update langsung
        cache()->forget('stats_omset_hari_ini');
        cache()->forget('stats_total_omzet');

        // 4. SIMPAN KE TABEL QUICK_TRANSACTIONS (TANPA MEMBER!)
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

        // 5. KIRIM NOTIFIKASI KE DATABASE ADMIN
        $admins = User::all();
        foreach ($admins as $admin) {
            Notification::make()
                ->title("Pembayaran {$itemName}")
                ->body("Kasir baru saja mencatat penjualan **{$itemName}** seharga **{$nominal}**. Stock tersisa: **{$product->stock}**")
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->sendToDatabase($admin);
        }

        // 6. KIRIM NOTIFIKASI TELEGRAM
        \App\Helpers\TelegramHelper::sendTransaksiKasir($itemName, $amount, $product->stock);

        // 7. KIRIM NOTIFIKASI WHATSAPP KE OWNER (Gunakan data quick transaction)
        \App\Helpers\WhatsAppHelper::sendQuickTransactionNotification($quickTransaction);

        // 8. Notifikasi melayang (Toast) di layar kasir
        Notification::make()
            ->title('Transaksi Berhasil!')
            ->body("Pembayaran **{$itemName}** sebesar **{$nominal}** telah dicatat. Stock tersisa: **{$product->stock}**")
            ->success()
            ->send();
    }
}