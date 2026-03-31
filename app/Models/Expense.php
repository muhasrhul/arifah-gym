<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'category',
        'item',
        'quantity',
        'amount',
        'receipt_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'datetime',
        'amount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relationship dengan User (yang mencatat)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor untuk format amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Accessor untuk format tanggal
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->expense_date)->translatedFormat('d F Y');
    }

    // Scope untuk filter berdasarkan bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', Carbon::now()->month)
                    ->whereYear('expense_date', Carbon::now()->year);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    protected static function boot()
    {
        parent::boot();

        // Observer untuk mencatat ke cash flow saat expense dibuat
        static::created(function ($expense) {
            \App\Models\CashFlow::createEntry(
                'expense',
                'pengeluaran',
                $expense->category . ' - ' . $expense->item . ($expense->notes ? ' (' . $expense->notes . ')' : ''),
                $expense->amount,
                $expense->id,
                $expense->expense_date // Sekarang sudah datetime, tidak perlu diubah lagi
            );
        });

        // Observer untuk update cash flow saat expense diupdate
        static::updated(function ($expense) {
            // Update entry yang sudah ada
            $cashFlow = \App\Models\CashFlow::where('reference_id', $expense->id)
                ->where('source', 'pengeluaran')
                ->first();

            if ($cashFlow) {
                $cashFlow->update([
                    'date' => $expense->expense_date, // Sekarang sudah datetime
                    'description' => $expense->category . ' - ' . $expense->item . ($expense->notes ? ' (' . $expense->notes . ')' : ''),
                    'amount' => $expense->amount,
                ]);
            }
        });

        // Observer untuk hapus cash flow saat expense dihapus
        static::deleted(function ($expense) {
            \App\Models\CashFlow::where('reference_id', $expense->id)
                ->where('source', 'pengeluaran')
                ->delete();
        });
    }
}
