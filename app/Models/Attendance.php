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
     * Ditambahkan withTrashed() agar riwayat absen tetap menampilkan nama 
     * meskipun member sudah dihapus (Soft Delete).
     */
    public function member()
    {
        // Penambahan withTrashed() memastikan nama orang yang sudah berhenti 
        // tetap muncul di laporan absensi.
        return $this->belongsTo(Member::class, 'member_id')->withTrashed();
    }
}