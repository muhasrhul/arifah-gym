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
    public function bayarHarian($productId, $paymentMethod = 'cash', $quantity = 1)
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

        // 2. Validasi quantity
        if ($quantity < 1) $quantity = 1;
        if ($quantity > $product->stock) {
            Notification::make()
                ->title('Stock Tidak Cukup!')
                ->body("Stock **{$product->name}** hanya tersisa **{$product->stock}** pcs. Tidak bisa menjual **{$quantity}** pcs.")
                ->danger()
                ->send();
            return;
        }

        // 3. CEK STOCK - Jika habis, tolak transaksi
        if ($product->stock <= 0) {
            Notification::make()
                ->title('Stock Habis!')
                ->body("Produk **{$product->name}** sudah habis. Silakan isi stock terlebih dahulu.")
                ->danger()
                ->send();
            return;
        }

        $unitPrice = $product->price;
        $totalAmount = $unitPrice * $quantity;
        $itemName = $product->name;
        $nominal = "Rp " . number_format($totalAmount, 0, ',', '.');

        // 4. KURANGI STOCK PRODUK sesuai quantity
        $product->decrement('stock', $quantity);
        
        // Clear cache dashboard agar pendapatan update langsung
        cache()->forget('stats_omset_hari_ini');
        cache()->forget('stats_total_omzet');

        // 5. Format metode pembayaran
        $paymentMethodLabel = match($paymentMethod) {
            'cash' => 'Cash',
            'transfer' => 'Transfer Bank',
            default => 'Cash'
        };

        // 6. SIMPAN KE TABEL QUICK_TRANSACTIONS
        $quickTransaction = QuickTransaction::create([
            'guest_name'     => str_contains(strtolower($itemName), 'latihan') || str_contains(strtolower($itemName), 'harian') 
                                ? 'Tamu Latihan' 
                                : 'Tamu Kantin',
            'product_name'   => $quantity > 1 ? "{$itemName} ({$quantity}x)" : $itemName,
            'order_id'       => 'KASIR-' . date('YmdHis'),
            'amount'         => $totalAmount,
            'type'           => $itemName,
            'payment_method' => $paymentMethodLabel,
            'payment_date'   => now(),
        ]);

        // 7. KIRIM NOTIFIKASI KE DATABASE ADMIN
        $admins = User::all();
        $quantityText = $quantity > 1 ? " ({$quantity}x)" : "";
        foreach ($admins as $admin) {
            Notification::make()
                ->title("Pembayaran {$itemName}")
                ->body("Kasir baru saja mencatat penjualan **{$itemName}{$quantityText}** seharga **{$nominal}** via **{$paymentMethodLabel}**. Stock tersisa: **{$product->stock}**")
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->sendToDatabase($admin);
        }

        // 8. KIRIM NOTIFIKASI TELEGRAM
        \App\Helpers\TelegramHelper::sendTransaksiKasir($itemName, $totalAmount, $product->stock);

        // 9. KIRIM NOTIFIKASI WHATSAPP KE OWNER
        \App\Helpers\WhatsAppHelper::sendQuickTransactionNotification($quickTransaction);

        // 10. Notifikasi melayang (Toast) di layar kasir
        Notification::make()
            ->title('Transaksi Berhasil!')
            ->body("Pembayaran **{$itemName}{$quantityText}** sebesar **{$nominal}** via **{$paymentMethodLabel}** telah dicatat. Stock tersisa: **{$product->stock}**")
            ->success()
            ->send();
    }
}