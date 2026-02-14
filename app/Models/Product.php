<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // TAMBAHKAN BARIS INI UNTUK MEMBERIKAN IZIN SIMPAN DATA
    protected $fillable = [
        'name',
        'price',
        'stock',
        'icon',
        'color',
        'is_active',
    ];
}