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
        'customer_phone',
        'notes',
        'product_name',
        'order_id',
        'amount',
        'payment_method',
        'type',
        'payment_date',
        'status',
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

    // Accessor untuk status dalam bahasa Indonesia
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'paid' => 'Lunas',
            'pending' => 'Belum Bayar',
            default => 'Lunas'
        };
    }

    // Accessor untuk warna status
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            default => 'success'
        };
    }

    // Scope untuk hutang yang belum dibayar
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope untuk transaksi yang sudah dibayar
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}