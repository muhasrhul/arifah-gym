<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuickTransaction;
use App\Helpers\WhatsAppHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendUnpaidDebtReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debt:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WhatsApp ke owner tentang hutang yang belum lunas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memproses notifikasi hutang yang belum lunas...');
        
        try {
            // Ambil semua hutang yang belum lunas
            $unpaidDebts = QuickTransaction::pending()
                ->orderBy('payment_date', 'desc')
                ->get();
            
            if ($unpaidDebts->isEmpty()) {
                $this->info('Tidak ada hutang yang belum lunas. Notifikasi tidak dikirim.');
                Log::info('Debt Reminder: Tidak ada hutang yang belum lunas, notifikasi tidak dikirim');
                return 0;
            }
            
            // Kirim notifikasi ke owner hanya jika ada hutang
            $result = WhatsAppHelper::sendUnpaidDebtReminder($unpaidDebts);
            
            if ($result['success']) {
                $this->info("Notifikasi berhasil dikirim! Total hutang: {$unpaidDebts->count()}");
                Log::info('Debt Reminder: Notifikasi berhasil dikirim', [
                    'total_debts' => $unpaidDebts->count()
                ]);
            } else {
                $this->error("Gagal mengirim notifikasi: {$result['message']}");
                Log::error('Debt Reminder: Gagal mengirim notifikasi', [
                    'error' => $result['message']
                ]);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            Log::error('Debt Reminder Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
