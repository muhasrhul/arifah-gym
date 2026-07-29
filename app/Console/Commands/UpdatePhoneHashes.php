<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;

class UpdatePhoneHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:update-phone-hashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update phone_hash untuk semua member yang belum punya';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Mulai update phone hash...');
        
        // Ambil semua member yang phone_hash-nya masih kosong
        $members = Member::whereNull('phone_hash')->get();
        
        $this->info("Ditemukan {$members->count()} member yang perlu di-update");
        
        $bar = $this->output->createProgressBar($members->count());
        $bar->start();
        
        foreach ($members as $member) {
            // Set ulang phone (otomatis trigger setPhoneAttribute)
            $member->phone = $member->phone;
            $member->save();
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('✓ Selesai! Semua member sudah punya phone_hash');
        
        return Command::SUCCESS;
    }
}
