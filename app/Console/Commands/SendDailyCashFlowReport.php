<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashFlow;
use App\Helpers\WhatsAppHelper;
use Carbon\Carbon;

class SendDailyCashFlowReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cashflow:daily-report {date?}';

    /**
     * The console command description.
     */
    protected $description = 'Send daily cash flow report to owner via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil tanggal dari parameter atau gunakan hari ini
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now('Asia/Makassar');
        
        $this->info("Generating cash flow report for: " . $date->format('d/m/Y'));
        
        // Ambil data cash flow hari ini
        $cashFlows = CashFlow::whereDate('date', $date->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();
        
        // Hitung total pemasukan dan pengeluaran
        $totalIncome = $cashFlows->where('type', 'income')->sum('amount');
        $totalExpense = $cashFlows->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        
        // Kirim notifikasi ke owner
        $result = $this->sendReportToOwner($date, $cashFlows, $totalIncome, $totalExpense, $netBalance);
        
        if ($result['success']) {
            $this->info("✅ Daily cash flow report sent successfully!");
        } else {
            $this->error("❌ Failed to send report: " . $result['message']);
        }
        
        return $result['success'] ? 0 : 1;
    }
    
    /**
     * Kirim laporan ke owner via WhatsApp
     */
    private function sendReportToOwner($date, $cashFlows, $totalIncome, $totalExpense, $netBalance)
    {
        return WhatsAppHelper::sendDailyCashFlowReport($date, $cashFlows, $totalIncome, $totalExpense, $netBalance);
    }
}