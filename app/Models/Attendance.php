<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan input data

    /**
     * Relasi ke tabel Member
     * Relasi normal tanpa withTrashed karena soft delete sudah dihapus dari Member
     */
    public function member()
    {
        // Relasi normal ke Member
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    /**
     * Get member name dengan fallback jika member dihapus
     */
    public function getMemberNameAttribute()
    {
        return $this->member->name ?? 'Member Dihapus';
    }
}