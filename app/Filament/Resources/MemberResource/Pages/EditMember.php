<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Paket;
use App\Models\Transaction;
use App\Models\User; 
use Filament\Notifications\Notification; 
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;
    
    // Property untuk menyimpan data form
    protected $formBiayaPaket = 0;
    protected $formBiayaRegistrasi = 0;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('perpanjang_sekarang')
                ->label('Perpanjang Sekarang')
                ->icon('heroicon-o-refresh')
                ->color('success')
                ->visible(fn (): bool => $this->isEligibleForEarlyRenewal())
                ->modalHeading('Perpanjangan Early')
                ->modalWidth('3xl')
                ->form([
                    Forms\Components\Section::make('Informasi Membership')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipe Member')
                                ->options(Paket::all()->pluck('nama_paket', 'nama_paket'))
                                ->default(fn () => $this->record->type)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $paket = Paket::where('nama_paket', $state)->first();
                                    if ($paket) {
                                        $set('biaya_paket_info', $paket->harga);
                                        $set('harga_paket_info', $paket->harga);
                                        
                                        // Update tanggal berakhir
                                        $record = $this->record;
                                        $durasi = $paket->durasi_hari;
                                        $tanggalLama = Carbon::parse($record->expiry_date);
                                        
                                        if ($durasi > 1) {
                                            $tanggalBaru = $tanggalLama->copy()->addDays(30);
                                        } else {
                                            $tanggalBaru = $tanggalLama->copy();
                                        }
                                        
                                        $set('expiry_date_preview', $tanggalBaru->format('Y-m-d'));
                                    }
                                }),
                            
                            Forms\Components\Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Cash',
                                    'transfer_bank' => 'Transfer Bank',
                                ])
                                ->default(fn () => $this->record->payment_method ?? 'cash')
                                ->required()
                                ->helperText('Pilih metode pembayaran yang digunakan member'),
                            
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\DatePicker::make('join_date_preview')
                                    ->label('Tanggal Mulai')
                                    ->default(fn () => $this->record->expiry_date)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Update tanggal berakhir ketika tanggal mulai diubah
                                        $selectedType = $get('type') ?? $this->record->type;
                                        $paket = Paket::where('nama_paket', $selectedType)->first();
                                        
                                        if ($paket && $state) {
                                            $durasi = $paket->durasi_hari;
                                            $tanggalMulai = Carbon::parse($state);
                                            
                                            if ($durasi > 1) {
                                                $tanggalBaru = $tanggalMulai->copy()->addDays(30);
                                            } else {
                                                $tanggalBaru = $tanggalMulai->copy();
                                            }
                                            
                                            $set('expiry_date_preview', $tanggalBaru->format('Y-m-d'));
                                        }
                                    }),
                                
                                Forms\Components\DatePicker::make('expiry_date_preview')
                                    ->label('Tanggal Berakhir')
                                    ->default(function () {
                                        $record = $this->record;
                                        $paket = Paket::where('nama_paket', $record->type)->first();
                                        
                                        if (!$paket) {
                                            return $record->expiry_date;
                                        }
                                        
                                        $durasi = $paket->durasi_hari;
                                        $tanggalLama = Carbon::parse($record->expiry_date);
                                        
                                        if ($durasi > 1) {
                                            $tanggalBaru = $tanggalLama->copy()->addDays(30);
                                        } else {
                                            $tanggalBaru = $tanggalLama->copy();
                                        }
                                        
                                        return $tanggalBaru->format('Y-m-d');
                                    })
                                    ->required()
                                    ->helperText('Perpanjangan dihitung dari hari ini'),
                            ]),
                            
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('biaya_paket_info')
                                    ->label('Biaya Paket')
                                    ->default(function () {
                                        $paket = Paket::where('nama_paket', $this->record->type)->first();
                                        return $paket ? $paket->harga : 0;
                                    })
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        // Update total ketika biaya paket diubah
                                        $set('harga_paket_info', $state);
                                    })
                                    ->helperText('Bisa diedit manual')
                                    ->extraInputAttributes(['style' => 'font-weight: 700; color: #059669; background-color: #f0fdf4;']),
                                
                                Forms\Components\TextInput::make('harga_paket_info')
                                    ->label('TOTAL TAGIHAN')
                                    ->default(function () {
                                        $paket = Paket::where('nama_paket', $this->record->type)->first();
                                        return $paket ? $paket->harga : 0;
                                    })
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Total untuk perpanjangan')
                                    ->extraInputAttributes(['style' => 'font-weight: 900; color: #000000; font-size: 1.5rem; background-color: #fef3c7;']),
                            ]),
                        ])
                ])
                ->action(fn (array $data) => $this->handleEarlyRenewal($data)),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $now = Carbon::now('Asia/Makassar');
        
        // Simpan nilai dari form untuk digunakan dalam transaksi
        $this->formBiayaPaket = isset($data['biaya_paket_info']) ? (int)$data['biaya_paket_info'] : 0;
        $this->formBiayaRegistrasi = isset($data['biaya_registrasi_info']) ? (int)$data['biaya_registrasi_info'] : 0;
        
        // PENTING: Jika member sudah punya expiry_date (bukan pendaftar baru), set fee ke 0
        if ($record->expiry_date) {
            $this->formBiayaRegistrasi = 0;
        }
        
        // 1. Cek apakah status diubah dari Mati ke Aktif
        $sedangDiaktifkan = !empty($data['is_active']) && !$record->is_active;

        // 2. Jika TIDAK diaktifkan DAN expiry_date tidak diubah manual, kembalikan ke nilai lama
        // Tapi jika user mengubah expiry_date manual, biarkan tersimpan
        if (!$sedangDiaktifkan && $record->expiry_date) {
            // Cek apakah expiry_date diubah manual oleh user
            $expiryDiubah = isset($data['expiry_date']) && $data['expiry_date'] != $record->expiry_date;
            $joinDiubah = isset($data['join_date']) && $data['join_date'] != $record->join_date;
            
            // Jika tidak diubah, kembalikan ke nilai lama
            if (!$expiryDiubah) {
                $data['expiry_date'] = $record->expiry_date;
            }
            if (!$joinDiubah) {
                $data['join_date'] = $record->join_date;
            }
        }

        if ($sedangDiaktifkan) {
            // Gunakan nilai dari form jika ada, jika tidak ambil dari database paket
            $hargaPaket = $this->formBiayaPaket;
            $registrationFee = $this->formBiayaRegistrasi;
            
            // Jika form kosong, ambil dari database paket (fallback)
            if ($hargaPaket == 0 && $registrationFee == 0) {
                $paket = Paket::where('nama_paket', $data['type'])->first();
                $hargaPaket = $paket ? (int)$paket->harga : 0;
                
                // Hanya tambahkan fee jika member belum punya expiry_date (pendaftar baru)
                $registrationFee = (!$record->expiry_date && $paket) ? (int)$paket->registration_fee : 0;
            }
            
            $totalHarga = $hargaPaket + $registrationFee;

            // 3. Update Tanggal Expired untuk Perpanjangan
            // Jika member sudah punya expiry_date (perpanjangan), perpanjang dari tanggal sekarang
            if ($record->expiry_date) {
                $paket = Paket::where('nama_paket', $data['type'])->first();
                $durasi = $paket ? (int)$paket->durasi_hari : 1;
                
                // Perpanjang dari hari ini (Asia/Makassar timezone)
                if ($durasi > 1) {
                    // Paket bulanan: hitung bulan dari durasi_hari
                    $bulan = round($durasi / 30);
                    $data['expiry_date'] = $now->copy()->addMonths($bulan)->format('Y-m-d');
                } else {
                    $data['expiry_date'] = $now->format('Y-m-d');
                }
                $data['join_date'] = $now->format('Y-m-d');
            }

            // 4. CEK DATABASE: Apakah sudah ada uang masuk (Pendaftaran Baru) untuk member ini?
            $sudahAdaUang = Transaction::where('member_id', $record->id)
                ->where('type', 'like', 'Pendaftaran Baru%')
                ->exists();

            if (!$sudahAdaUang) {
                // --- URUTAN: TEMBAK NOTIFIKASI DULU (SESUAI REQUEST BAPAK) ---
                $admins = User::all();
                foreach ($admins as $admin) {
                    Notification::make()
                        ->title('Pembayaran Member Baru!')
                        // Keterangan sudah seragam dengan menu Create
                        ->body("Pendaftaran baru **Rp " . number_format($totalHarga, 0, ',', '.') . "** dari **{$record->name}** via kasir.")
                        ->status('success')
                        ->icon('heroicon-o-user-add')
                        ->sendToDatabase($admin);
                }

                // Notifikasi Pop-up di layar
                Notification::make()
                    ->title('Aktivasi & Pembayaran Sukses')
                    ->status('success')
                    ->send();

                // --- BARU CATAT TRANSAKSI ---
                // Tentukan metode pembayaran berdasarkan pilihan member
                $paymentMethodLabel = 'Cash'; // Default jika tidak ada pilihan
                
                if ($record->payment_method) {
                    switch ($record->payment_method) {
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
                    'member_id'      => $record->id,
                    'order_id'       => $record->order_id ?? 'ADM-' . strtoupper(uniqid()),
                    'amount'         => $totalHarga,
                    'status'         => 'paid',
                    'payment_method' => $paymentMethodLabel,
                    'type'           => 'Pendaftaran Baru: ' . $data['type'],
                    'payment_date'   => $now,
                    'guest_name'     => $record->name,
                ]);
                
                // Ambil transaksi yang baru dibuat untuk notifikasi Telegram
                $transaction = Transaction::where('member_id', $record->id)
                    ->latest()
                    ->first();
                
                // Kirim notifikasi Telegram SETELAH semua data commit
                if ($transaction) {
                    // Gunakan dispatch untuk kirim notifikasi setelah response
                    dispatch(function () use ($record, $transaction) {
                        // Ambil data fresh dari database
                        $memberFresh = \App\Models\Member::find($record->id);
                        
                        if ($memberFresh) {
                            \App\Helpers\TelegramHelper::sendAktivasiMember($memberFresh, $transaction);
                            \App\Helpers\WhatsAppHelper::sendAktivasiMember($memberFresh, $transaction);
                        }
                    })->afterResponse();
                }
                
                // Clear cache dashboard agar pendapatan update langsung
                cache()->forget('stats_omset_hari_ini');
                cache()->forget('stats_total_omzet');
                cache()->forget('stats_total_member');

                // Update Tanggal jika pendaftaran baru (expiry masih kosong)
                if (empty($record->expiry_date)) {
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
            } else {
                // Jika sudah ada transaksi pendaftaran (perpanjangan), catat transaksi perpanjangan
                if ($record->expiry_date) {
                    // Tentukan metode pembayaran
                    $paymentMethodLabel = 'Cash';
                    if ($record->payment_method) {
                        switch ($record->payment_method) {
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
                    
                    // Catat transaksi perpanjangan
                    Transaction::create([
                        'member_id'      => $record->id,
                        'order_id'       => 'RNW-' . strtoupper(uniqid()),
                        'amount'         => $totalHarga, // Hanya harga paket, tanpa fee
                        'status'         => 'paid',
                        'payment_method' => $paymentMethodLabel,
                        'type'           => 'Perpanjangan: ' . $data['type'],
                        'payment_date'   => $now,
                        'guest_name'     => $record->name,
                    ]);
                    
                    // Ambil transaksi yang baru dibuat untuk notifikasi Telegram
                    $transaction = Transaction::where('member_id', $record->id)
                        ->latest()
                        ->first();
                    
                    // Kirim notifikasi Telegram SETELAH semua data commit
                    if ($transaction) {
                        // Gunakan dispatch untuk kirim notifikasi setelah response
                        dispatch(function () use ($record, $transaction) {
                            // Ambil data fresh dari database
                            $memberFresh = \App\Models\Member::find($record->id);
                            
                            if ($memberFresh) {
                                // Panggil fungsi perpanjangan (bukan aktivasi)
                                \App\Helpers\TelegramHelper::sendPerpanjanganMember($memberFresh, $transaction);
                                \App\Helpers\WhatsAppHelper::sendPerpanjanganMember($memberFresh, $transaction);
                            }
                        })->afterResponse();
                    }
                    
                    // Notifikasi perpanjangan
                    $admins = User::all();
                    foreach ($admins as $admin) {
                        Notification::make()
                            ->title('Perpanjangan Membership!')
                            ->body("Perpanjangan **Rp " . number_format($totalHarga, 0, ',', '.') . "** dari **{$record->name}**.")
                            ->status('success')
                            ->icon('heroicon-o-refresh')
                            ->sendToDatabase($admin);
                    }
                    
                    Notification::make()
                        ->title('Perpanjangan Membership Sukses')
                        ->status('success')
                        ->send();
                    
                    // Clear cache
                    cache()->forget('stats_omset_hari_ini');
                    cache()->forget('stats_total_omzet');
                    cache()->forget('stats_total_member');
                }
            }
        }

        // Simpan semua perubahan data ke database
        $record->update($data);
        
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Check if member is eligible for early renewal
     * Requirements: 1.1, 1.2, 6.1, 6.2, 6.3
     * Tombol muncul H-2 sampai hari expired (H), hilang setelah lewat tengah malam
     */
    protected function isEligibleForEarlyRenewal(): bool
    {
        $record = $this->record;
        
        // Check is_active = true
        if (!$record->is_active) {
            return false;
        }
        
        // Check expiry_date exists
        if (!$record->expiry_date) {
            return false;
        }
        
        $expiryDate = Carbon::parse($record->expiry_date)->startOfDay();
        $today = Carbon::now('Asia/Makassar')->startOfDay();
        
        // Hitung selisih hari antara hari ini dan expiry_date
        $daysUntilExpiry = $today->diffInDays($expiryDate, false);
        
        // Tombol muncul jika:
        // - daysUntilExpiry >= 0 (termasuk hari expired)
        // - dan daysUntilExpiry <= 2 (maksimal H-2)
        // Jadi: H-2 (2 hari lagi), H-1 (1 hari lagi), H (hari expired)
        // Setelah lewat tengah malam (H+1), daysUntilExpiry = -1, tombol TIDAK muncul
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 2;
    }

    /**
     * Get information text for early renewal confirmation dialog
     * Requirements: 5.2, 5.3, 5.4
     */
    protected function getEarlyRenewalInfo(): string
    {
        $record = $this->record;
        
        // Get package info
        $paket = Paket::where('nama_paket', $record->type)->first();
        if (!$paket) {
            return 'Paket tidak ditemukan';
        }
        
        $durasi = $paket->durasi_hari;
        $harga = number_format($paket->harga, 0, ',', '.');
        
        // Calculate new expiry date from old expiry date
        $tanggalLama = Carbon::parse($record->expiry_date);
        
        if ($durasi > 1) {
            // Paket bulanan: tambah 30 hari (bukan bulan) agar tanggal tetap sama
            // Misal: 11 Feb + 30 hari = 13 Mar (bukan 11 Mar)
            $tanggalBaru = $tanggalLama->copy()->addDays(30);
        } else {
            // Paket harian: expired date tetap hari ini (tidak ditambah)
            $tanggalBaru = $tanggalLama->copy();
        }
        
        return "Paket: {$record->type}\n" .
               "Harga: Rp {$harga}\n" .
               "Expired Lama: {$tanggalLama->format('d/m/Y')}\n" .
               "Expired Baru: {$tanggalBaru->format('d/m/Y')}\n\n" .
               "Member akan diperpanjang dari tanggal expired lama, sehingga tidak kehilangan sisa waktu membership.";
    }

    /**
     * Handle early renewal process
     * Requirements: All
     */
    protected function handleEarlyRenewal(array $data): void
    {
        $record = $this->record;
        
        try {
            // Validasi eligibility
            if (!$this->isEligibleForEarlyRenewal()) {
                Notification::make()
                    ->title('Gagal')
                    ->body('Member tidak eligible untuk perpanjangan early.')
                    ->danger()
                    ->send();
                return;
            }
            
            // Get package info from selected type
            $selectedType = $data['type'] ?? $record->type;
            $selectedPaymentMethod = $data['payment_method'] ?? 'cash';
            $customBiayaPaket = isset($data['biaya_paket_info']) ? (int)$data['biaya_paket_info'] : null;
            $customExpiryDate = $data['expiry_date_preview'] ?? null;
            
            $paket = Paket::where('nama_paket', $selectedType)->first();
            if (!$paket) {
                Notification::make()
                    ->title('Paket Tidak Ditemukan')
                    ->danger()
                    ->send();
                return;
            }
            
            // Gunakan harga custom jika ada, jika tidak gunakan harga dari paket
            $harga = $customBiayaPaket ?? $paket->harga;
            
            // Gunakan tanggal expired custom jika ada
            if ($customExpiryDate) {
                $tanggalBaru = Carbon::parse($customExpiryDate);
            } else {
                // Calculate new expiry date from old expiry date (NOT from today)
                $durasi = $paket->durasi_hari;
                $tanggalLama = Carbon::parse($record->expiry_date);
                
                if ($durasi > 1) {
                    $tanggalBaru = $tanggalLama->copy()->addDays(30);
                } else {
                    $tanggalBaru = $tanggalLama->copy();
                }
            }
            
            // Check for duplicate transaction today
            $now = Carbon::now('Asia/Makassar');
            $existingTransaction = Transaction::where('member_id', $record->id)
                ->where('type', 'like', 'Perpanjang Member%')
                ->whereDate('payment_date', $now->format('Y-m-d'))
                ->exists();
            
            if ($existingTransaction) {
                Notification::make()
                    ->title('Transaksi Sudah Ada')
                    ->body('Transaksi perpanjangan sudah ada untuk hari ini.')
                    ->warning()
                    ->send();
                return;
            }
            
            // Update member data
            $record->update([
                'type' => $selectedType,
                'expiry_date' => $tanggalBaru->format('Y-m-d'),
                'payment_method' => $selectedPaymentMethod,
                // is_active NOT changed, remains true
            ]);
            
            // Determine payment method label
            $paymentMethodLabel = 'Cash';
            switch ($selectedPaymentMethod) {
                case 'transfer_bank':
                    $paymentMethodLabel = 'Transfer Bank';
                    break;
                case 'cash':
                    $paymentMethodLabel = 'Cash';
                    break;
                default:
                    $paymentMethodLabel = 'Cash';
            }
            
            // Create transaction
            Transaction::create([
                'member_id'      => $record->id,
                'order_id'       => 'REN-' . strtoupper(uniqid()),
                'amount'         => $harga,
                'status'         => 'paid',
                'type'           => 'Perpanjang Member: ' . $selectedType,
                'payment_method' => $paymentMethodLabel,
                'payment_date'   => $now,
                'guest_name'     => $record->name,
            ]);
            
            // Ambil transaksi yang baru dibuat untuk notifikasi
            $transaction = Transaction::where('member_id', $record->id)
                ->latest()
                ->first();
            
            // Kirim notifikasi WhatsApp & Telegram SETELAH semua data commit
            if ($transaction) {
                // Gunakan dispatch untuk kirim notifikasi setelah response
                dispatch(function () use ($record, $transaction) {
                    // Ambil data fresh dari database
                    $memberFresh = \App\Models\Member::find($record->id);
                    
                    if ($memberFresh) {
                        // Panggil fungsi perpanjangan early
                        \App\Helpers\TelegramHelper::sendPerpanjanganEarly($memberFresh, $transaction);
                        \App\Helpers\WhatsAppHelper::sendPerpanjanganEarly($memberFresh, $transaction);
                    }
                })->afterResponse();
            }
            
            // Send notifications to admins
            $admins = User::all();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Perpanjangan Early!')
                    ->body("Perpanjangan early **Rp " . number_format($harga, 0, ',', '.') . "** dari **{$record->name}**.")
                    ->status('success')
                    ->icon('heroicon-o-refresh')
                    ->sendToDatabase($admin);
            }
            
            // Success notification
            Notification::make()
                ->title('Perpanjangan Berhasil')
                ->body("Member {$record->name} berhasil diperpanjang sampai {$tanggalBaru->format('d/m/Y')}")
                ->success()
                ->send();
            
            // Clear cache
            cache()->forget('stats_omset_hari_ini');
            cache()->forget('stats_total_omzet');
            
            // Refresh the page to show updated data
            redirect()->to($this->getResource()::getUrl('edit', ['record' => $record]));
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Memproses Perpanjangan')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}