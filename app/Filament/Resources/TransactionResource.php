<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?int $navigationSort = 1; // Urutan pertama
    protected static ?string $navigationLabel = 'Keuangan Member';
    protected static ?string $pluralLabel = 'Keuangan Member';
    protected static ?string $navigationGroup = 'Laporan Transaksi';
    
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
    
    // Filter otomatis: Hanya tampilkan transaksi member reguler (bukan kasir cepat)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('member')
            ->whereHas('member', function (Builder $query) {
                $query->where('name', '!=', 'Tamu Harian')
                      ->where('name', '!=', 'Tamu Latihan Harian');
            })
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                // 1. Pilih Member (Bisa dikosongkan jika tamu harian)
                Forms\Components\Select::make('member_id')
                    ->label('Pilih Member (Jika terdaftar)')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->placeholder('Cari nama member...'),

                // 2. Input Nama Tamu (Jika tidak mau daftar member)
                Forms\Components\TextInput::make('guest_name')
                    ->label('Nama Tamu / Harian')
                    ->placeholder('Ketik nama jika bukan member terdaftar')
                    ->helperText('Isi ini jika Anda tidak memilih nama di kolom atas.'),

                Forms\Components\TextInput::make('order_id')
                    ->label('ID Transaksi / Order ID')
                    ->default('MANUAL-' . uniqid())
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Kategori')
                    ->options(function () {
                        $options = [];
                        
                        // Ambil semua paket aktif untuk membuat opsi pendaftaran dan perpanjangan
                        $pakets = \App\Models\Paket::where('is_active', true)->get();
                        
                        foreach ($pakets as $paket) {
                            $options["Pendaftaran Baru: {$paket->nama_paket}"] = "Pendaftaran Baru: {$paket->nama_paket}";
                            $options["Perpanjang Member: {$paket->nama_paket}"] = "Perpanjang Member: {$paket->nama_paket}";
                        }
                        
                        // Tambahkan kategori lainnya
                        $options['Harian (Insidentil)'] = 'Harian (Insidentil)';
                        $options['Minuman/Kantin'] = 'Minuman/Kantin';
                        
                        return $options;
                    })
                    ->default('Harian (Insidentil)')
                    ->required()
                    ->searchable(),

                Forms\Components\Select::make('payment_method')
                    ->label('Metode Bayar')
                    ->options([
                        'Cash' => 'Cash',
                        'Transfer Bank' => 'Transfer Bank',
                    ])
                    ->default('Cash')
                    ->required(),

                Forms\Components\DateTimePicker::make('payment_date')
                    ->label('Tanggal & Jam Bayar')
                    ->default(now())
                    ->required(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // KOLOM PERTAMA: Sumber transaksi (selalu Member Reguler karena sudah difilter)
                Tables\Columns\BadgeColumn::make('source')
                    ->label('Sumber')
                    ->getStateUsing(fn () => 'Member Reguler')
                    ->color('primary')
                    ->icon('heroicon-o-user-group')
                    ->toggleable(isToggledHiddenByDefault: false),

                // KOLOM KEDUA: Nama customer
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Customer')
                    ->getStateUsing(fn ($record) => $record->member ? $record->member->name : ($record->guest_name ?? 'Umum'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('guest_name', 'like', "%{$search}%")
                            ->orWhereHas('member', function (Builder $query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            });
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Kategori')
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'pending',
                        'danger' => 'failed',
                        'secondary' => 'refund',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-x-circle' => 'failed',
                        'heroicon-o-arrow-left' => 'refund',
                    ])
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->toggleable(isToggledHiddenByDefault: false),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                // Filter 1: Tipe Paket (berdasarkan member)
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Paket')
                    ->options(function () {
                        return \App\Models\Paket::where('is_active', true)
                            ->pluck('nama_paket', 'nama_paket')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('member', function (Builder $query) use ($value) {
                                $query->where('type', $value);
                            })
                        );
                    })
                    ->placeholder('Semua Paket'),

                // Filter 2: Transaksi Bulan Ini
                Tables\Filters\Filter::make('payment_this_month')
                    ->label('Transaksi Bulan Ini')
                    ->query(fn ($query) => $query->whereMonth('payment_date', \Carbon\Carbon::now()->month)
                        ->whereYear('payment_date', \Carbon\Carbon::now()->year))
                    ->toggle(),

                // Filter 3: Data yang dihapus (seperti di MemberResource)
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                // ForceDeleteAction tidak digunakan untuk keamanan data
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-download')
                    ->url(fn () => route('cetak-laporan', ['format' => 'excel']))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => route('cetak-laporan', ['format' => 'pdf']))
                    ->openUrlInNewTab(),
            ]);;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}