<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // 1. Izinkan kolom-kolom ini diisi (Mass Assignment)
    protected $fillable = [
        'member_id',
        'guest_name',
        'order_id',
        'amount',
        'type',
        'payment_method',
        'payment_date',
    ];

    // 2. Beritahu Laravel bahwa payment_date adalah format tanggal/waktu
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    /**
     * Relasi ke tabel Member
     * Ditambahkan withTrashed() agar riwayat keuangan tetap menampilkan nama 
     * meskipun member sudah dihapus (Soft Delete).
     */
    public function member()
    {
        // Penambahan withTrashed() adalah kunci agar nama member tetap muncul
        return $this->belongsTo(Member::class, 'member_id')->withTrashed();
    }
}