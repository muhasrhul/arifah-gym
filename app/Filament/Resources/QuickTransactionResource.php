<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuickTransactionResource\Pages;
use App\Models\QuickTransaction;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class QuickTransactionResource extends Resource
{
    protected static ?string $model = QuickTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?int $navigationSort = 2; // Urutan kedua
    protected static ?string $navigationLabel = 'Keuangan Kasir Cepat';
    protected static ?string $pluralLabel = 'Keuangan Kasir Cepat';
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\TextInput::make('guest_name')
                    ->label('Nama Tamu')
                    ->required(),

                Forms\Components\TextInput::make('product_name')
                    ->label('Nama Produk')
                    ->required(),

                Forms\Components\TextInput::make('order_id')
                    ->label('ID Transaksi')
                    ->default('KASIR-' . uniqid())
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Kategori')
                    ->options([
                        'Latihan Harian' => 'Latihan Harian',
                        'Minuman/Kantin' => 'Minuman/Kantin',
                        'Snack' => 'Snack',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required(),

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
                Tables\Columns\BadgeColumn::make('source')
                    ->label('Sumber')
                    ->getStateUsing(fn () => 'Kasir Cepat')
                    ->color('warning')
                    ->icon('heroicon-o-lightning-bolt'),

                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Nama Tamu')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Produk')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Kategori')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode'),
                
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuickTransactions::route('/'),
            'create' => Pages\CreateQuickTransaction::route('/create'),
            'edit' => Pages\EditQuickTransaction::route('/{record}/edit'),
        ];
    }
}