<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashFlow;
use App\Models\Transaction;
use App\Models\QuickTransaction;
use App\Models\Expense;

class CashFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data cash flow yang sudah ada
        CashFlow::truncate();

        // 1. Import dari Transaction (Member)
        $transactions = Transaction::all();
        foreach ($transactions as $transaction) {
            $description = '';
            if (str_contains($transaction->type, 'Pendaftaran')) {
                $description = 'Pendaftaran - ' . $transaction->guest_name;
            } elseif (str_contains($transaction->type, 'Perpanjangan')) {
                $description = 'Perpanjangan - ' . $transaction->guest_name;
            } else {
                $description = $transaction->type . ' - ' . $transaction->guest_name;
            }

            CashFlow::create([
                'date' => $transaction->payment_date,
                'type' => 'income',
                'source' => 'member',
                'reference_id' => $transaction->id,
                'description' => $description,
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ]);
        }

        // 2. Import dari QuickTransaction (Kasir Cepat)
        $quickTransactions = QuickTransaction::where('status', 'paid')->get();
        foreach ($quickTransactions as $quickTransaction) {
            CashFlow::create([
                'date' => $quickTransaction->payment_date,
                'type' => 'income',
                'source' => 'kasir',
                'reference_id' => $quickTransaction->id,
                'description' => 'Penjualan - ' . $quickTransaction->product_name . ' (' . $quickTransaction->guest_name . ')',
                'amount' => $quickTransaction->amount,
                'created_at' => $quickTransaction->created_at,
                'updated_at' => $quickTransaction->updated_at,
            ]);
        }

        // 3. Import dari Expense (Pengeluaran)
        $expenses = Expense::all();
        foreach ($expenses as $expense) {
            CashFlow::create([
                'date' => $expense->expense_date,
                'type' => 'expense',
                'source' => 'pengeluaran',
                'reference_id' => $expense->id,
                'description' => $expense->category . ' - ' . $expense->item . ($expense->notes ? ' (' . $expense->notes . ')' : ''),
                'amount' => $expense->amount,
                'created_at' => $expense->created_at,
                'updated_at' => $expense->updated_at,
            ]);
        }

        $this->command->info('Cash Flow data seeded successfully!');
        $this->command->info('Total records: ' . CashFlow::count());
    }
}