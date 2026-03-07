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
        
        if ($durasi >= 30) {
            // Paket bulanan: hitung bulan dari durasi_hari
            $bulan = round($durasi / 30);
            $tanggalBaru = $tanggalLama->isPast() ? now()->addMonths($bulan) : $tanggalLama->addMonths($bulan);
        } else {
            // Paket harian: expired di hari yang sama untuk durasi = 1
            if ($durasi == 1) {
                // Member harian expired di hari yang sama
                $tanggalBaru = $tanggalLama->isPast() ? now() : $tanggalLama->addDays(1);
            } else {
                // Paket beberapa hari (misal 3 hari, 7 hari)
                $tanggalBaru = $tanggalLama->isPast() ? now()->addDays($durasi - 1) : $tanggalLama->addDays($durasi);
            }
        }

        $this->update([
            'expiry_date' => $tanggalBaru->format('Y-m-d'),
            'is_active' => true,
        ]);

        \App\Models\Transaction::create([
            'member_id'      => $this->id,
            'order_id'       => 'RNW-' . strtoupper(uniqid()), 
            'amount'         => $harga, 
            'type'           => 'Perpanjangan: ' . $this->type,
            'payment_method' => 'Tunai (Kasir)',
            'payment_date'   => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        // OBSERVER DINONAKTIFKAN - Transaksi sekarang dihandle di CreateMember.php dan EditMember.php
        // untuk menghindari duplikasi dan memastikan konsistensi data
        
        /*
        static::updating(function ($model) {
            // OBSERVER INI SUDAH TIDAK DIGUNAKAN LAGI
            // Pembuatan transaksi sekarang dilakukan di:
            // - CreateMember.php (untuk member baru)
            // - EditMember.php (untuk aktivasi dan perpanjangan)
            // 
            // Alasan dinonaktifkan:
            // 1. Mencegah double transaction
            // 2. Memastikan konsistensi payment_method dari form
            // 3. CreateMember dan EditMember sudah menggunakan withoutEvents()
        });
        */
    }
}