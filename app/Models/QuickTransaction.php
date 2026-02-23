<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickTransaction extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'quick_transactions';

    // Kolom yang bisa diisi (Mass Assignment)
    protected $fillable = [
        'guest_name',
        'product_name',
        'order_id',
        'amount',
        'payment_method',
        'type',
        'payment_date',
    ];

    // Cast kolom ke tipe data yang sesuai
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Scope untuk filter berdasarkan tanggal
    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
    }

    // Accessor untuk format rupiah
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}