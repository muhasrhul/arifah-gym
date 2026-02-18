<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Paket;
use App\Models\Transaction;
use App\Models\User; 
use Filament\Notifications\Notification; 
use Carbon\Carbon;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;
    
    // Property untuk menyimpan data form
    protected $formBiayaPaket = 0;
    protected $formBiayaRegistrasi = 0;

    // 1. CEGAT DATA SEBELUM DISIMPAN (SET ORDER ID & TANGGAL)
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $now = Carbon::now('Asia/Makassar');

        // VALIDASI BACKEND: Paksa set biaya admin = 0 untuk paket harian
        // Simpan ke property untuk digunakan di afterCreate
        if (isset($data['type'])) {
            $paket = Paket::where('nama_paket', $data['type'])->first();
            if ($paket && $paket->durasi_hari < 30) {
                // Paket harian → Paksa set 0
                $this->formBiayaRegistrasi = 0;
            }
        }

        // Simpan nilai dari form untuk digunakan di afterCreate
        $this->formBiayaPaket = isset($data['biaya_paket_info']) ? (int)$data['biaya_paket_info'] : 0;
        
        // Jika belum di-set di validasi di atas, ambil dari form
        if (!isset($this->formBiayaRegistrasi) || $this->formBiayaRegistrasi === null) {
            $this->formBiayaRegistrasi = isset($data['biaya_registrasi_info']) ? (int)$data['biaya_registrasi_info'] : 0;
        }

        // AWALAN REG: Agar serasi di semua tabel
        $data['order_id'] = 'REG-' . strtoupper(uniqid());

        // Jika Bapak TIDAK mencentang tombol "Active"
        if (empty($data['is_active'])) {
            $data['expiry_date'] = null;
        } 
        // Jika Bapak mencentang "Active"
        else {
            $paket = Paket::where('nama_paket', $data['type'])->first();
            $durasi = $paket ? (int)$paket->durasi_hari : 1;

            if ($durasi > 1) {
                // Paket bulanan: hitung bulan dari durasi_hari
                $bulan = round($durasi / 30);
                $data['expiry_date'] = $now->copy()->addMonths($bulan)->format('Y-m-d');
            } else {
                $data['expiry_date'] = $now->format('Y-m-d'); 
            }
            
            $data['join_date'] = $now->format('Y-m-d');
        }

        return $data;
    }

    // 2. JALAN SETELAH DATA TERSIMPAN (URUSAN UANG & NOTIFIKASI)
    protected function afterCreate(): void
    {
        $member = $this->record;

        // Hanya catat uang dan kirim notif kalau statusnya AKTIF
        if ($member->is_active) {
            // Gunakan nilai dari form jika ada, jika tidak ambil dari database paket
            $hargaPaket = $this->formBiayaPaket;
            $registrationFee = $this->formBiayaRegistrasi;
            
            // Jika form kosong, ambil dari database paket (fallback)
            if ($hargaPaket == 0 && $registrationFee == 0) {
                $paket = Paket::where('nama_paket', $member->type)->first();
                $hargaPaket = $paket ? (int)$paket->harga : 0;
                $registrationFee = $paket ? (int)$paket->registration_fee : 0;
            }
            
            $totalHarga = $hargaPaket + $registrationFee; // Total termasuk fee untuk pendaftar baru

            // --- A. KIRIM NOTIFIKASI KE LONCENG DULU ---
            $admins = User::all();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Pembayaran Member Baru!')
                    ->body("Pendaftaran baru **Rp " . number_format($totalHarga, 0, ',', '.') . "** dari **{$member->name}**.")
                    ->status('success') 
                    ->icon('heroicon-o-user-add')
                    ->sendToDatabase($admin);
            }

            // --- B. TAMPILKAN POP-UP DI LAYAR ---
            Notification::make()
                ->title('Member Baru & Pembayaran Sukses')
                ->status('success')
                ->send();

            // --- C. CATAT TRANSAKSI KE KEUANGAN ---
            // Tentukan metode pembayaran berdasarkan pilihan member
            $paymentMethodLabel = 'Cash'; // Default jika tidak ada pilihan
            
            if ($member->payment_method) {
                switch ($member->payment_method) {
                    case 'transfer_bank':
                        $paymentMethodLabel = 'Transfer Bank';
                        break;
                    case 'cash':
                        $paymentMethodLabel = 'Cash';
                        break;
                    default:
                        $paymentMethodLabel = 'Cash';
                }
            }
            
            Transaction::create([
                'member_id'      => $member->id,
                'order_id'       => $member->order_id, // Menggunakan REG- yang sama dengan Member
                'amount'         => $totalHarga,
                'status'         => 'paid',
                'payment_method' => $paymentMethodLabel,
                'type'           => 'Pendaftaran Baru: ' . $member->type,
                'payment_date'   => Carbon::now('Asia/Makassar'),
                'guest_name'     => $member->name,
            ]);
            
            // Ambil transaksi yang baru dibuat untuk notifikasi Telegram
            $transaction = Transaction::where('member_id', $member->id)
                ->where('order_id', $member->order_id)
                ->first();
            
            // Pastikan semua data sudah tersimpan ke database
            if ($transaction) {
                // Tunggu sebentar untuk memastikan database sudah commit
                usleep(100000); // 0.1 detik
                
                // Ambil ulang data member dari database
                $memberFresh = \App\Models\Member::find($member->id);
                
                // Kirim notifikasi Telegram & WhatsApp
                if ($memberFresh) {
                    \App\Helpers\TelegramHelper::sendAktivasiMember($memberFresh, $transaction);
                    \App\Helpers\WhatsAppHelper::sendAktivasiMember($memberFresh, $transaction);
                }
            }
            
            // Clear cache dashboard agar pendapatan update langsung
            cache()->forget('stats_omset_hari_ini');
            cache()->forget('stats_total_omzet');
            cache()->forget('stats_total_member');
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}