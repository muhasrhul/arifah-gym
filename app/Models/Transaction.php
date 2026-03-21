<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

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
     * Relasi normal tanpa withTrashed karena soft delete sudah dihapus
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    /**
     * Get member name dengan fallback jika member dihapus
     */
    public function getMemberNameAttribute()
    {
        return $this->member->name ?? $this->guest_name ?? 'Member Dihapus';
    }
}