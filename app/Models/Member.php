<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * Logika Perpanjangan Dinamis (Renewal via Tombol Khusus)
     */
    public function perpanjangSatuBulan()
    {
        $paket = \App\Models\Paket::where('nama_paket', $this->type)->first();
        
        if (!$paket) {
            Notification::make()->title('Paket Tidak Ditemukan')->danger()->send();
            return;
        }

        $durasi = $paket->durasi_hari;
        $harga  = $paket->harga;

        $tanggalLama = $this->expiry_date ? Carbon::parse($this->expiry_date) : now();
        
        if ($durasi > 1) {
            // Paket bulanan: hitung bulan dari durasi_hari
            $bulan = round($durasi / 30);
            $tanggalBaru = $tanggalLama->isPast() ? now()->addMonths($bulan) : $tanggalLama->addMonths($bulan);
        } else {
            // Paket harian
            $tanggalBaru = $tanggalLama->isPast() ? now()->addDays($durasi) : $tanggalLama->addDays($durasi);
        }

        $this->update([
            'expiry_date' => $tanggalBaru->format('Y-m-d'),
            'is_active' => true,
        ]);

        \App\Models\Transaction::create([
            'member_id'      => $this->id,
            'order_id'       => 'REN-' . strtoupper(uniqid()), 
            'amount'         => $harga, 
            'type'           => 'Perpanjang Member: ' . $this->type,
            'payment_method' => 'Tunai (Kasir)',
            'payment_date'   => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            /** * LOGIKA AKTIVASI PENDAFTAR BARU
             */
            if ($model->isDirty('is_active') && $model->is_active == true && empty($model->getOriginal('expiry_date'))) {
                
                $now = now();
                $paket = \App\Models\Paket::where('nama_paket', $model->type)->first();
                
                if ($paket) {
                    $durasi = $paket->durasi_hari;
                    $harga  = $paket->harga;

                    // 1. Set Tanggal Join & Expiry (Berlaku untuk QRIS maupun Kasir)
                    $model->join_date = $now->format('Y-m-d');
                    if ($durasi <= 1) {
                        $model->expiry_date = $model->join_date;
                    } else {
                        // Paket bulanan: hitung bulan dari durasi_hari
                        $bulan = round($durasi / 30);
                        $model->expiry_date = $now->copy()->addMonths($bulan)->format('Y-m-d');
                    }

                    /**
                     * 2. LOGIKA ANTI-DOUBLE KRUSIAL:
                     * Kita cek apakah perubahan ini dilakukan oleh ADMIN (via Kasir/Dashboard).
                     * Jika dilakukan oleh sistem otomatis (QRIS/Web), Auth::check() biasanya false 
                     * atau kita pastikan lewat pengecekan transaksi yang sudah ada.
                     */
                    $transaksiAda = \App\Models\Transaction::where('member_id', $model->id)
                        ->whereDate('payment_date', $now->format('Y-m-d'))
                        ->exists();

                    // HANYA buat transaksi jika:
                    // - Admin Sedang Login (Artinya diubah via dashboard kasir)
                    // - Belum ada transaksi masuk untuk hari ini (Mencegah double QRIS)
                    if (Auth::check() && !$transaksiAda) {
                        // Tentukan metode pembayaran berdasarkan pilihan member
                        $paymentMethodLabel = 'Tunai (Kasir)'; // Default jika tidak ada pilihan
                        
                        if ($model->payment_method) {
                            switch ($model->payment_method) {
                                case 'transfer_bank':
                                    $paymentMethodLabel = 'Transfer Bank';
                                    break;
                                case 'cash':
                                    $paymentMethodLabel = 'Cash';
                                    break;
                                default:
                                    $paymentMethodLabel = 'Tunai (Kasir)';
                            }
                        }
                        
                        \App\Models\Transaction::create([
                            'member_id'      => $model->id,
                            'order_id'       => 'REG-' . strtoupper(uniqid()), 
                            'amount'         => $harga, 
                            'type'           => 'Pendaftaran Baru: ' . $model->type,
                            'payment_method' => $paymentMethodLabel,
                            'payment_date'   => $now,
                        ]);
                    }
                }
                
                // CLEAR CACHE notifikasi pendaftar baru
                cache()->forget('pendaftar_baru_count');
                cache()->forget('pendaftar_baru_exists');
            }
        });
    }
}