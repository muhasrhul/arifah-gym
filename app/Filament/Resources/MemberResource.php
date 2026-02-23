<?php

namespace App\Filament\Resources;

use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use App\Models\Paket;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Carbon\Carbon;
// Import untuk fitur Soft Deletes & Actions
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Filters\TrashedFilter;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Daftar Member';
    protected static ?string $pluralLabel = 'Daftar Member';
    protected static ?string $modelLabel = 'Member';

    // PERMISSION: Staff hanya bisa lihat, tidak bisa create/edit/delete
    public static function canCreate(): bool
    {
        return !auth()->user()->isStaff(); // Super Admin & Admin bisa
    }

    public static function canEdit($record): bool
    {
        return !auth()->user()->isStaff(); // Super Admin & Admin bisa
    }

    public static function canDelete($record): bool
    {
        return !auth()->user()->isStaff(); // Super Admin & Admin bisa
    }

    public static function canDeleteAny(): bool
    {
        return !auth()->user()->isStaff(); // Super Admin & Admin bisa
    }

    // Mendukung pembacaan data yang sudah di-soft delete
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function getNavigationBadge(): ?string
    {
        // Menghitung member yang tidak aktif DAN belum punya tanggal expired (Pendaftar Baru)
        $count = static::getModel()::where('is_active', false)
            ->whereNull('expiry_date')
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required(),

                    Forms\Components\TextInput::make('nik')
                        ->label('NIK KTP')
                        ->maxLength(16)
                        ->minLength(16)
                        ->numeric()
                        ->placeholder('Masukkan 16 digit NIK KTP')
                        ->helperText('NIK KTP harus 16 digit angka')
                        ->unique(ignorable: fn ($record) => $record),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email')
                            ->helperText('Email bersifat opsional. Jika diisi, akan digunakan untuk notifikasi.')
                            ->unique(ignorable: fn ($record) => $record),

                        Forms\Components\TextInput::make('phone')
                            ->label('WhatsApp')
                            ->tel()
                            ->required()
                            ->unique(ignorable: fn ($record) => $record),
                    ]),

                    Forms\Components\TextInput::make('fingerprint_id')
                        ->label('Fingerprint ID')
                        ->numeric()
                        ->unique(ignorable: fn ($record) => $record)
                        ->placeholder('Masukkan ID fingerprint'),
                    
                    Forms\Components\Section::make('Informasi Membership')->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipe Member')
                            ->options(Paket::all()->pluck('nama_paket', 'nama_paket'))
                            ->reactive() 
                            ->required()
                            ->disabled(fn ($record) => $record && $record->is_active)
                            ->helperText(fn ($record) => $record && $record->is_active 
                                ? '⚠️ Tipe member tidak bisa diubah untuk member yang sudah aktif' 
                                : 'Pilih tipe membership')
                            ->afterStateUpdated(function ($state, $set, $get, $record) {
                                // 1. Cari Paket di Database
                                $paket = Paket::where('nama_paket', $state)->first();
                                $harga = $paket ? (int)$paket->harga : 0;
                                $registrationFee = $paket ? (int)$paket->registration_fee : 0;

                                // 2. TIDAK ada update expiry_date otomatis!
                                // Admin harus input manual

                                // 3. Update Breakdown Biaya
                                // Jika member sudah pernah punya expiry_date (perpanjangan), fee = 0
                                // Jika belum pernah punya expiry_date (pendaftar baru), tampilkan fee
                                // TAMBAHAN: Jika paket harian (durasi < 30), fee = 0
                                // TAMBAHAN: Jika member sudah aktif, fee = 0 (tidak boleh charge lagi)
                                $isPerpanjangan = $record && $record->expiry_date;
                                $isMemberAktif = $record && $record->is_active;
                                
                                // Cek apakah paket harian
                                $isPaketHarian = $paket && $paket->durasi_hari < 30;
                                
                                $set('biaya_paket_info', $harga);
                                
                                // Set biaya registrasi: 0 jika perpanjangan ATAU paket harian ATAU member sudah aktif
                                if ($isPerpanjangan || $isPaketHarian || $isMemberAktif) {
                                    $set('biaya_registrasi_info', 0);
                                    $set('harga_paket_info', $harga);
                                } else {
                                    $set('biaya_registrasi_info', $registrationFee);
                                    $set('harga_paket_info', $harga + $registrationFee);
                                }
                            }),

                        // --- FIELD METODE PEMBAYARAN ---
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Cash',
                                'transfer_bank' => 'Transfer Bank',
                            ])
                            ->default('cash')
                            ->required()
                            ->helperText('Pilih metode pembayaran yang digunakan member'),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('join_date')
                                ->label('Tanggal Mulai')
                                ->placeholder('Pilih tanggal mulai membership')
                                ->helperText('Input manual tanggal mulai membership (untuk data lama atau baru)')
                                ->required()
                                ->reactive()
                                ->rule('required', 'Tanggal mulai membership wajib diisi'),

                            Forms\Components\DatePicker::make('expiry_date')
                                ->label('Tanggal Berakhir')
                                ->reactive()
                                ->required(fn ($get) => $get('is_active') === true)
                                ->placeholder(function ($get) {
                                    $joinDate = $get('join_date');
                                    $paketType = $get('type');
                                    
                                    if ($joinDate && $paketType) {
                                        $paket = \App\Models\Paket::where('nama_paket', $paketType)->first();
                                        if ($paket) {
                                            $durasi = $paket->durasi_hari;
                                            $tanggalMulai = \Carbon\Carbon::parse($joinDate);
                                            
                                            if ($durasi >= 30) {
                                                // Paket bulanan: hitung bulan dari durasi_hari
                                                $bulan = round($durasi / 30);
                                                $rekomendasiExpiry = $tanggalMulai->copy()->addMonths($bulan);
                                            } else {
                                                // Paket harian: expired di hari yang sama (durasi = 1) atau sesuai durasi
                                                if ($durasi == 1) {
                                                    // Member harian expired di hari yang sama
                                                    $rekomendasiExpiry = $tanggalMulai->copy();
                                                } else {
                                                    // Paket beberapa hari (misal 3 hari, 7 hari)
                                                    $rekomendasiExpiry = $tanggalMulai->copy()->addDays($durasi - 1);
                                                }
                                            }
                                            
                                            return 'Rekomendasi: ' . $rekomendasiExpiry->format('d/m/Y');
                                        }
                                    }
                                    
                                    return 'Pilih tanggal mulai dan paket dulu';
                                })
                                ->helperText(function ($record, $get) {
                                    if (!$record) {
                                        $joinDate = $get('join_date');
                                        $paketType = $get('type');
                                        
                                        if ($joinDate && $paketType) {
                                            $paket = \App\Models\Paket::where('nama_paket', $paketType)->first();
                                            if ($paket) {
                                                $durasi = $paket->durasi_hari;
                                                $tanggalMulai = \Carbon\Carbon::parse($joinDate);
                                                
                                                if ($durasi >= 30) {
                                                    $bulan = round($durasi / 30);
                                                    $rekomendasiExpiry = $tanggalMulai->copy()->addMonths($bulan);
                                                    return "💡 Rekomendasi otomatis: {$rekomendasiExpiry->format('d/m/Y')} (dari tanggal mulai + {$bulan} bulan). WAJIB diisi jika toggle Status Aktif dinyalakan.";
                                                } else {
                                                    if ($durasi == 1) {
                                                        $rekomendasiExpiry = $tanggalMulai->copy();
                                                        return "💡 Rekomendasi otomatis: {$rekomendasiExpiry->format('d/m/Y')} (member harian expired di hari yang sama). WAJIB diisi jika toggle Status Aktif dinyalakan.";
                                                    } else {
                                                        $rekomendasiExpiry = $tanggalMulai->copy()->addDays($durasi - 1);
                                                        return "💡 Rekomendasi otomatis: {$rekomendasiExpiry->format('d/m/Y')} (dari tanggal mulai + {$durasi} hari). WAJIB diisi jika toggle Status Aktif dinyalakan.";
                                                    }
                                                }
                                            }
                                        }
                                        
                                        return 'WAJIB diisi jika toggle Status Aktif dinyalakan. Pilih tanggal mulai dan paket untuk melihat rekomendasi.';
                                    }
                                    
                                    if ($record->expiry_date) {
                                        $expiredDate = \Carbon\Carbon::parse($record->expiry_date)->format('d/m/Y');
                                        
                                        // Jika member expired (tidak aktif tapi punya expiry_date)
                                        if (!$record->is_active) {
                                            return "⚠️ Member expired pada: {$expiredDate}. WAJIB ubah tanggal berakhir yang baru untuk perpanjangan.";
                                        }
                                        
                                        return "Tanggal berakhir saat ini: {$expiredDate}";
                                    }
                                    
                                    return 'WAJIB diisi jika toggle Status Aktif dinyalakan.';
                                }),
                        ]),

                        // --- KOLOM TAGIHAN KASIR (BREAKDOWN) ---
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('biaya_paket_info')
                                ->label('Biaya Paket')
                                ->placeholder('Otomatis...')
                                ->numeric()
                                ->prefix('Rp')
                                ->reactive()
                                ->dehydrated(false)
                                ->disabled(fn ($record) => $record && $record->is_active) // Disable jika sudah aktif
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    // Update total saat biaya paket diubah manual
                                    $biayaPaket = (int)($state ?? 0);
                                    $biayaRegistrasi = (int)($get('biaya_registrasi_info') ?? 0);
                                    $set('harga_paket_info', $biayaPaket + $biayaRegistrasi);
                                })
                                ->afterStateHydrated(function ($set, $get, $record) {
                                    if ($record && $record->type) {
                                        $paket = Paket::where('nama_paket', $record->type)->first();
                                        $hargaPaket = $paket ? (int)$paket->harga : 0;
                                        
                                        // LOGIKA BARU:
                                        // 1. Jika member SUDAH AKTIF: Tampilkan harga paket sebagai referensi historis
                                        // 2. Jika member EXPIRED (perpanjangan): Set 0 saat pertama buka (akan terisi otomatis saat ganti paket)
                                        // 3. Jika member PENDAFTAR BARU: Tampilkan harga paket
                                        
                                        if ($record->is_active) {
                                            // Member sudah aktif: tampilkan harga sebagai referensi historis
                                            $set('biaya_paket_info', $hargaPaket);
                                        } elseif ($record->expiry_date) {
                                            // Member expired: set 0 (akan terisi otomatis saat ganti paket via afterStateUpdated)
                                            $set('biaya_paket_info', 0);
                                        } else {
                                            // Pendaftar baru: tampilkan harga paket
                                            $set('biaya_paket_info', $hargaPaket);
                                        }
                                    }
                                })
                                ->helperText(fn ($record) => $record && $record->is_active ? 'Tidak bisa diedit (member sudah aktif)' : 'Bisa diedit manual')
                                ->extraInputAttributes(['style' => 'font-weight: 700; color: #059669; background-color: #f0fdf4;']),

                            Forms\Components\TextInput::make('biaya_registrasi_info')
                                ->label('Biaya Admin')
                                ->placeholder('Otomatis...')
                                ->numeric()
                                ->prefix('Rp')
                                ->reactive()
                                ->dehydrated(false)
                                ->disabled(function ($record, $get) {
                                    // Disable jika sudah aktif ATAU sudah pernah punya expiry_date
                                    if ($record && ($record->is_active || $record->expiry_date)) {
                                        return true;
                                    }
                                    
                                    // Disable jika paket harian (durasi < 30 hari)
                                    $paketName = $get('type');
                                    if ($paketName) {
                                        $paket = Paket::where('nama_paket', $paketName)->first();
                                        if ($paket && $paket->durasi_hari < 30) {
                                            return true; // Disable untuk paket harian
                                        }
                                    }
                                    
                                    return false;
                                })
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    // Update total saat biaya registrasi diubah manual
                                    $biayaPaket = (int)($get('biaya_paket_info') ?? 0);
                                    $biayaRegistrasi = (int)($state ?? 0);
                                    $set('harga_paket_info', $biayaPaket + $biayaRegistrasi);
                                })
                                ->afterStateHydrated(function ($set, $get, $record) {
                                    if ($record && $record->type) {
                                        $paket = Paket::where('nama_paket', $record->type)->first();
                                        $registrationFee = $paket ? (int)$paket->registration_fee : 0;
                                        $hargaPaket = $paket ? (int)$paket->harga : 0;
                                        
                                        // Paksa set 0 untuk paket harian DAN update total
                                        if ($paket && $paket->durasi_hari < 30) {
                                            $set('biaya_registrasi_info', 0);
                                            $set('harga_paket_info', $hargaPaket); // Total = harga paket saja
                                            return;
                                        }
                                        
                                        // LOGIKA PINTAR:
                                        // Cek apakah member ini sudah pernah perpanjangan (berarti sudah pernah expired)
                                        $sudahPernahPerpanjangan = \App\Models\Transaction::where('member_id', $record->id)
                                            ->where('type', 'like', 'Perpanjangan%')
                                            ->exists();
                                        
                                        // 1. Jika member AKTIF dan BELUM pernah perpanjangan → Tampilkan fee sebagai referensi (member baru pertama kali aktif)
                                        // 2. Jika member AKTIF dan SUDAH pernah perpanjangan → Fee = 0 (member lama, tidak perlu referensi lagi)
                                        // 3. Jika member EXPIRED → Fee = 0 (perpanjangan tidak kena fee)
                                        // 4. Jika member PENDAFTAR BARU → Tampilkan fee
                                        
                                        if ($record->is_active && !$sudahPernahPerpanjangan) {
                                            // Member aktif dan belum pernah perpanjangan: tampilkan fee sebagai referensi (member baru)
                                            $set('biaya_registrasi_info', $registrationFee);
                                        } elseif ($record->is_active && $sudahPernahPerpanjangan) {
                                            // Member aktif dan sudah pernah perpanjangan: fee = 0 (member lama)
                                            $set('biaya_registrasi_info', 0);
                                        } elseif ($record->expiry_date) {
                                            // Member expired (perpanjangan): fee = 0
                                            $set('biaya_registrasi_info', 0);
                                        } else {
                                            // Pendaftar baru: tampilkan fee
                                            $set('biaya_registrasi_info', $registrationFee);
                                        }
                                    }
                                })
                                ->helperText(function ($record, $get) {
                                    if (!$record) {
                                        // Untuk create member baru
                                        $paketName = $get('type');
                                        if ($paketName) {
                                            $paket = Paket::where('nama_paket', $paketName)->first();
                                            if ($paket && $paket->durasi_hari < 30) {
                                                return '⚠️ Tamu harian tidak dikenakan biaya admin';
                                            }
                                        }
                                        return 'Hanya untuk pendaftar baru (bisa diedit)';
                                    }
                                    
                                    // Cek apakah paket yang dipilih adalah paket harian
                                    $paketName = $get('type') ?? $record->type;
                                    if ($paketName) {
                                        $paket = Paket::where('nama_paket', $paketName)->first();
                                        if ($paket && $paket->durasi_hari < 30) {
                                            return '⚠️ Tamu harian tidak dikenakan biaya admin';
                                        }
                                    }
                                    
                                    // Cek apakah member sudah pernah perpanjangan
                                    $sudahPernahPerpanjangan = \App\Models\Transaction::where('member_id', $record->id)
                                        ->where('type', 'like', 'Perpanjangan%')
                                        ->exists();
                                    
                                    if ($record->is_active && !$sudahPernahPerpanjangan) {
                                        return 'Biaya admin yang sudah dibayar saat pendaftaran pertama kali';
                                    } elseif ($record->is_active && $sudahPernahPerpanjangan) {
                                        return 'Member lama tidak dikenakan biaya admin';
                                    } elseif ($record->expiry_date) {
                                        return 'Tidak dikenakan biaya admin (perpanjangan membership)';
                                    }
                                    
                                    return 'Hanya untuk pendaftar baru (bisa diedit)';
                                })
                                ->extraInputAttributes(['style' => 'font-weight: 700; color: #ea580c; background-color: #fff7ed;']),

                            Forms\Components\TextInput::make('harga_paket_info')
                                ->label('TOTAL TAGIHAN')
                                ->placeholder('Otomatis...')
                                ->numeric()
                                ->prefix('Rp')
                                ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '0')
                                ->reactive()
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($set, $get, $record) {
                                    if ($record && $record->type) {
                                        $paket = Paket::where('nama_paket', $record->type)->first();
                                        $harga = $paket ? (int)$paket->harga : 0;
                                        $registrationFee = $paket ? (int)$paket->registration_fee : 0;
                                        
                                        // PENTING: Jika member EXPIRED (tidak aktif tapi punya expiry_date), set 0
                                        if (!$record->is_active && $record->expiry_date) {
                                            $set('harga_paket_info', 0);
                                            return;
                                        }
                                        
                                        // PENTING: Paksa set 0 untuk paket harian (durasi < 30 hari)
                                        if ($paket && $paket->durasi_hari < 30) {
                                            $set('harga_paket_info', $harga); // Total = harga paket saja, tanpa fee
                                            return;
                                        }
                                        
                                        // LOGIKA PINTAR:
                                        // Cek apakah member ini sudah pernah perpanjangan
                                        $sudahPernahPerpanjangan = \App\Models\Transaction::where('member_id', $record->id)
                                            ->where('type', 'like', 'Perpanjangan%')
                                            ->exists();
                                        
                                        // 1. Jika member AKTIF dan BELUM pernah perpanjangan → Total = harga + fee (member baru)
                                        // 2. Jika member AKTIF dan SUDAH pernah perpanjangan → Total = harga saja (member lama)
                                        // 3. Jika member PENDAFTAR BARU → Total = harga + fee
                                        
                                        if ($record->is_active && !$sudahPernahPerpanjangan) {
                                            // Member aktif dan belum pernah perpanjangan: total dengan fee (member baru)
                                            $totalTagihan = $harga + $registrationFee;
                                        } elseif ($record->is_active && $sudahPernahPerpanjangan) {
                                            // Member aktif dan sudah pernah perpanjangan: total tanpa fee (member lama)
                                            $totalTagihan = $harga;
                                        } else {
                                            // Pendaftar baru: harga paket + fee
                                            $totalTagihan = $harga + $registrationFee;
                                        }
                                        
                                        $set('harga_paket_info', $totalTagihan);
                                    }
                                })
                                ->helperText(function ($record) {
                                    if (!$record) return null;
                                    if ($record->is_active) return 'Total yang sudah dibayar';
                                    if ($record->expiry_date) return 'Total untuk perpanjangan (tanpa biaya registrasi)';
                                    return null;
                                })
                                ->extraInputAttributes(['style' => 'font-weight: 900; color: #000000; font-size: 1.5rem; background-color: #fef3c7;']),
                        ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->reactive()
                            ->helperText(function ($record) {
                                if (!$record) return 'Nyalakan ini hanya jika member sudah membayar lunas. PENTING: Anda harus mengisi Tanggal Berakhir secara manual sebelum mengaktifkan.';
                                
                                // Jika member sedang aktif dan sudah pernah punya transaksi
                                if ($record->is_active) {
                                    $sudahPernahAktif = \App\Models\Transaction::where('member_id', $record->id)
                                        ->where('type', 'like', 'Pendaftaran Baru%')
                                        ->exists();
                                    
                                    if ($sudahPernahAktif) {
                                        return 'Member sedang aktif. Toggle tidak bisa dimatikan untuk mencegah kesalahan perhitungan.';
                                    }
                                }
                                
                                // Jika member expired (tidak aktif tapi punya expiry_date)
                                if (!$record->is_active && $record->expiry_date) {
                                    return 'Member expired. Nyalakan toggle untuk perpanjangan membership.';
                                }
                                
                                return 'Nyalakan ini hanya jika member sudah membayar lunas. PENTING: Anda harus mengisi Tanggal Berakhir secara manual sebelum mengaktifkan.';
                            })
                            ->disabled(function ($record) {
                                if (!$record) return false;
                                
                                // Hanya disable jika member SEDANG AKTIF dan sudah pernah punya transaksi
                                // Jika member expired (is_active = false), toggle TIDAK disabled agar bisa diperpanjang
                                if ($record->is_active) {
                                    $sudahPernahAktif = \App\Models\Transaction::where('member_id', $record->id)
                                        ->where('type', 'like', 'Pendaftaran Baru%')
                                        ->exists();
                                    
                                    return $sudahPernahAktif;
                                }
                                
                                return false; // Jika tidak aktif, toggle bisa dinyalakan
                            })
                            ->default(false),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama ⇅')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Member $record): string => $record->trashed() ? 'DATA DIHAPUS' : '')
                    ->color(fn (Member $record): string => $record->trashed() ? 'danger' : 'default')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK KTP')
                    ->searchable()
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('fingerprint_id')
                    ->label('Fingerprint ID ⇅')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                    ->color('primary')
                    ->weight('medium')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('join_date')
                    ->label('Masuk ⇅')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Berakhir ⇅')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '-')
                    ->color(function ($record) {
                        if (!$record->expiry_date) return null;
                        return Carbon::parse($record->expiry_date)->startOfDay()->isPast() && !Carbon::parse($record->expiry_date)->isToday() ? 'danger' : 'success';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('phone')
                    ->label('WA')
                    ->searchable()
                    ->icon('heroicon-o-chat-alt')
                    ->color('success')
                    ->url(function ($record) {
                        $nomor = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($nomor, '0')) { $nomor = '62' . substr($nomor, 1); }
                        return "https://wa.me/{$nomor}";
                    })
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                // --- KOLOM STATUS YANG SUDAH DIPERBARUI LOGIKANYA ---
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $today = Carbon::now('Asia/Makassar')->startOfDay();
                        
                        // 1. Jika BELUM BAYAR (is_active mati & tgl expired kosong)
                        if (!$record->is_active && !$record->expiry_date) {
                            return 'Pendaftar Baru';
                        }

                        // 2. Jika SUDAH EXPIRED (is_active mati & tgl expired sudah lewat)
                        if (!$record->is_active && $record->expiry_date) {
                            $expiry = Carbon::parse($record->expiry_date)->startOfDay();
                            if ($today->gt($expiry)) {
                                return 'Masa Aktif Habis'; 
                            }
                        }

                        // 3. Jika AKTIF (is_active menyala)
                        if ($record->is_active) {
                            return 'Aktif';
                        }

                        return 'Non-Aktif';
                    })
                    ->colors([
                        'warning' => 'Pendaftar Baru',   // Warna Kuning
                        'danger' => 'Masa Aktif Habis',  // Warna Merah
                        'success' => 'Aktif',            // Warna Hijau
                        'secondary' => 'Non-Aktif',      // Warna Abu-abu
                    ])
                    ->icons([
                        'heroicon-o-user-add' => 'Pendaftar Baru',
                        'heroicon-o-clock' => 'Masa Aktif Habis',
                        'heroicon-o-check-circle' => 'Aktif',
                        'heroicon-o-minus-circle' => 'Non-Aktif',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter 1: Status Member
                Tables\Filters\Filter::make('status_member')
                    ->label('Status Member')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'aktif' => 'Aktif',
                                'expired' => 'Masa Aktif Habis',
                                'pendaftar_baru' => 'Pendaftar Baru',
                                'non_aktif' => 'Non-Aktif',
                            ])
                            ->placeholder('Semua Status'),
                    ])
                    ->query(function ($query, array $data) {
                        $hariIni = Carbon::now('Asia/Makassar')->startOfDay();
                        return $query->when($data['status'], function ($query, $status) use ($hariIni) {
                            if ($status === 'aktif') {
                                return $query->where('is_active', true);
                            }
                            if ($status === 'expired') {
                                return $query->where('is_active', false)
                                    ->whereDate('expiry_date', '<', $hariIni);
                            }
                            if ($status === 'pendaftar_baru') {
                                return $query->where('is_active', false)
                                    ->whereNull('expiry_date');
                            }
                            if ($status === 'non_aktif') {
                                return $query->where('is_active', false)
                                    ->whereDate('expiry_date', '>=', $hariIni);
                            }
                        });
                    }),

                // Filter 2: Tipe Paket
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Paket')
                    ->options(function () {
                        return \App\Models\Paket::where('is_active', true)
                            ->pluck('nama_paket', 'nama_paket')
                            ->toArray();
                    })
                    ->placeholder('Semua Paket'),

                // Filter 3: Berakhir Bulan Ini
                Tables\Filters\Filter::make('expiry_this_month')
                    ->label('Berakhir Bulan Ini')
                    ->query(fn ($query) => $query->whereMonth('expiry_date', Carbon::now()->month)
                        ->whereYear('expiry_date', Carbon::now()->year))
                    ->toggle(),

                // Filter 4: Punya Fingerprint
                Tables\Filters\Filter::make('has_fingerprint')
                    ->label('Punya Fingerprint')
                    ->query(fn ($query) => $query->whereNotNull('fingerprint_id'))
                    ->toggle(),

                // Filter 5: Data yang dihapus
                TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('sort_options')
                    ->label('Urutkan')
                    ->icon('heroicon-o-sort-ascending')
                    ->color('primary')
                    ->button()
                    ->form([
                        Forms\Components\Select::make('sort')
                            ->label('Urutkan Berdasarkan')
                            ->options([
                                'name-asc' => 'Nama (A-Z)',
                                'name-desc' => 'Nama (Z-A)',
                                'join_date-asc' => 'Tanggal Masuk (Terlama)',
                                'join_date-desc' => 'Tanggal Masuk (Terbaru)',
                                'expiry_date-asc' => 'Tanggal Berakhir (Terlama)',
                                'expiry_date-desc' => 'Tanggal Berakhir (Terbaru)',
                                'created_at-desc' => 'Default (Terbaru Daftar)',
                            ])
                            ->default('created_at-desc')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $sort = explode('-', $data['sort']);
                        $url = url()->current() . '?tableSort=' . $sort[0] . '&tableSortDirection=' . $sort[1];
                        return redirect($url);
                    }),
                    
                Tables\Actions\Action::make('reset_sort')
                    ->label('Reset Urutan')
                    ->icon('heroicon-o-refresh')
                    ->color('secondary')
                    ->button()
                    ->action(function () {
                        return redirect(url()->current());
                    })
                    ->visible(fn () => request()->has('tableSort') || request()->has('tableSortDirection')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(function ($record) {
                        // 1. Pendaftar Baru (belum pernah aktif)
                        if (!$record->is_active && !$record->expiry_date) {
                            return 'Aktivasi Sekarang';
                        }
                        
                        // 2. Masa Aktif Habis (expired)
                        if (!$record->is_active && $record->expiry_date) {
                            $today = Carbon::now('Asia/Makassar')->startOfDay();
                            $expiry = Carbon::parse($record->expiry_date)->startOfDay();
                            if ($today->gt($expiry)) {
                                return 'Perpanjang';
                            }
                        }
                        
                        // 3. Aktif atau status lainnya
                        return 'Ubah';
                    })
                    ->icon(function ($record) {
                        // 1. Pendaftar Baru
                        if (!$record->is_active && !$record->expiry_date) {
                            return 'heroicon-o-check-circle';
                        }
                        
                        // 2. Masa Aktif Habis
                        if (!$record->is_active && $record->expiry_date) {
                            $today = Carbon::now('Asia/Makassar')->startOfDay();
                            $expiry = Carbon::parse($record->expiry_date)->startOfDay();
                            if ($today->gt($expiry)) {
                                return 'heroicon-o-refresh';
                            }
                        }
                        
                        // 3. Aktif atau status lainnya
                        return 'heroicon-o-pencil';
                    })
                    ->color(function ($record) {
                        // 1. Pendaftar Baru - Hijau
                        if (!$record->is_active && !$record->expiry_date) {
                            return 'success';
                        }
                        
                        // 2. Masa Aktif Habis - Hijau
                        if (!$record->is_active && $record->expiry_date) {
                            $today = Carbon::now('Asia/Makassar')->startOfDay();
                            $expiry = Carbon::parse($record->expiry_date)->startOfDay();
                            if ($today->gt($expiry)) {
                                return 'success';
                            }
                        }
                        
                        // 3. Aktif atau status lainnya - Biru (default)
                        return 'primary';
                    }),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-download')
                    ->url(fn () => route('export-members', ['format' => 'excel']))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => route('export-members', ['format' => 'pdf']))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('pendaftar_baru')
                    ->label(fn () => "Ada " . cache()->remember('pendaftar_baru_count', 300, fn () => \App\Models\Member::where('is_active', false)->whereNull('expiry_date')->count()) . " Pendaftar Baru")
                    ->color('danger')
                    ->icon('heroicon-o-user-add')
                    ->url(fn () => static::getUrl('index', ['tableFilters[is_active][value]' => '0']))
                    ->visible(fn () => cache()->remember('pendaftar_baru_exists', 300, fn () => \App\Models\Member::where('is_active', false)->whereNull('expiry_date')->exists())),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }    
}