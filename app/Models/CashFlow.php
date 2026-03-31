<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'source',
        'reference_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Scope untuk filter berdasarkan tanggal
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan type
    public function scopeFilterByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope untuk pencarian description
    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', '%' . $search . '%');
    }

    // Method untuk mendapatkan saldo running
    public static function getRunningBalance($upToDate = null)
    {
        $query = self::orderBy('date', 'asc')->orderBy('id', 'asc');
        
        if ($upToDate) {
            $query->where('date', '<=', $upToDate);
        }

        $balance = 0;
        $records = $query->get();

        foreach ($records as $record) {
            if ($record->type === 'income') {
                $balance += $record->amount;
            } else {
                $balance -= $record->amount;
            }
        }

        return $balance;
    }

    // Method untuk mendapatkan total pemasukan
    public static function getTotalIncome($startDate = null, $endDate = null)
    {
        $query = self::where('type', 'income');
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->sum('amount');
    }

    // Method untuk mendapatkan total pengeluaran
    public static function getTotalExpense($startDate = null, $endDate = null)
    {
        $query = self::where('type', 'expense');
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        return $query->sum('amount');
    }

    // Method untuk membuat entry cash flow otomatis
    public static function createEntry($type, $source, $description, $amount, $referenceId = null, $date = null)
    {
        return self::create([
            'date' => $date ?? now(),
            'type' => $type,
            'source' => $source,
            'reference_id' => $referenceId,
            'description' => $description,
            'amount' => $amount,
        ]);
    }

    // Method untuk menghitung running balance berdasarkan chronological order
    public static function calculateRunningBalanceByDate($targetDate, $targetId)
    {
        $balance = 0;
        
        // Ambil semua record sampai tanggal dan ID tertentu
        $records = self::where(function($query) use ($targetDate, $targetId) {
                $query->where('date', '<', $targetDate)
                      ->orWhere(function($q) use ($targetDate, $targetId) {
                          $q->where('date', '=', $targetDate)
                            ->where('id', '<=', $targetId);
                      });
            })
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get(['type', 'amount']);
            
        // Hitung saldo kumulatif berdasarkan urutan chronological
        foreach ($records as $record) {
            if ($record->type === 'income') {
                $balance += $record->amount;
            } else {
                $balance -= $record->amount;
            }
        }
        
        return $balance;
    }

    // Method untuk menghitung running balance sampai ID tertentu (legacy)
    public static function calculateRunningBalance($recordId)
    {
        $balance = 0;
        
        // Ambil semua record sampai ID tertentu, urutkan berdasarkan ID
        $records = self::where('id', '<=', $recordId)
            ->orderBy('id', 'asc')
            ->get(['type', 'amount']);
            
        // Hitung saldo kumulatif
        foreach ($records as $record) {
            if ($record->type === 'income') {
                $balance += $record->amount;
            } else {
                $balance -= $record->amount;
            }
        }
        
        return $balance;
    }
}