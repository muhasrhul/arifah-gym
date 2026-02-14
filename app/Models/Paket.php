<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'harga',
        'registration_fee',
        'durasi_hari',
        'fasilitas',
        'is_active',
        'label_promo',
    ];
}