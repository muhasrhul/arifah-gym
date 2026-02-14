<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    
    // Ganti Icon jadi Clipboard (untuk Absensi)
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    
    // Nama Menu di Samping
    protected static ?string $navigationLabel = 'Log Absensi';

    protected static ?string $slug = 'log-absensi';
    protected static ?string $pluralLabel = 'Log Absensi';
    protected static ?string $modelLabel = 'Absensi';
    
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
    
    // Eager loading untuk relasi member
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('member');
    } 

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()->schema([
                Forms\Components\Select::make('member_id')
                    ->label('Nama Member')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->required()
                    // LOGIKA ANTI-DOUBLE ABSEN
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $sudahAbsen = Attendance::where('member_id', $value)
                                    ->whereDate('created_at', Carbon::today())
                                    ->exists();

                                if ($sudahAbsen) {
                                    $fail('Member ini sudah melakukan absensi hari ini.');
                                }
                            };
                        },
                    ]),

                // Hanya kolom waktu check-in sesuai database baru
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Waktu Check-in')
                    ->default(now())
                    ->disabled() // Otomatis dari sistem
                    ->dehydrated(), // Agar tetap tersimpan meski disabled
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Nama Member')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Jam Latihan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-download')
                    ->url(fn () => route('export-attendance', ['format' => 'excel']))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->url(fn () => route('export-attendance', ['format' => 'pdf']))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}