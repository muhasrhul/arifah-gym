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
                    ->form([
                        Forms\Components\Card::make()->schema([
                            Forms\Components\Select::make('filter_type')
                                ->label('Jenis Filter Tanggal')
                                ->options([
                                    'all' => 'Semua Data (Tanpa Filter)',
                                    'single' => 'Tanggal Tunggal',
                                    'range' => 'Rentang Tanggal',
                                ])
                                ->default('all')
                                ->reactive()
                                ->required(),
                            
                            Forms\Components\DatePicker::make('single_date')
                                ->label('Pilih Tanggal')
                                ->visible(fn ($get) => $get('filter_type') === 'single')
                                ->required(fn ($get) => $get('filter_type') === 'single')
                                ->closeOnDateSelection(),
                            
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->visible(fn ($get) => $get('filter_type') === 'range')
                                ->required(fn ($get) => $get('filter_type') === 'range')
                                ->closeOnDateSelection(),
                            
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Tanggal Akhir')
                                ->visible(fn ($get) => $get('filter_type') === 'range')
                                ->required(fn ($get) => $get('filter_type') === 'range')
                                ->afterOrEqual('start_date')
                                ->closeOnDateSelection(),
                        ])
                    ])
                    ->action(function (array $data) {
                        $params = ['format' => 'excel'];
                        
                        if ($data['filter_type'] === 'single' && !empty($data['single_date'])) {
                            $params['filter_type'] = 'single';
                            $params['single_date'] = $data['single_date'];
                        } elseif ($data['filter_type'] === 'range' && !empty($data['start_date']) && !empty($data['end_date'])) {
                            $params['filter_type'] = 'range';
                            $params['start_date'] = $data['start_date'];
                            $params['end_date'] = $data['end_date'];
                        }
                        
                        $url = route('export-attendance', $params);
                        
                        // Show notification
                        \Filament\Notifications\Notification::make()
                            ->title('Excel berhasil dibuat')
                            ->success()
                            ->send();
                            
                        // Redirect to download URL
                        return redirect()->away($url);
                    })
                    ->modalHeading('Filter Export Excel')
                    ->modalSubheading('Pilih filter tanggal untuk data absensi yang akan di-export ke Excel')
                    ->modalButton('Export Excel'),

                Tables\Actions\Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->color('warning')
                    ->icon('heroicon-o-printer')
                    ->form([
                        Forms\Components\Card::make()->schema([
                            Forms\Components\Select::make('filter_type')
                                ->label('Jenis Filter Tanggal')
                                ->options([
                                    'all' => 'Semua Data (Tanpa Filter)',
                                    'single' => 'Tanggal Tunggal',
                                    'range' => 'Rentang Tanggal',
                                ])
                                ->default('all')
                                ->reactive()
                                ->required(),
                            
                            Forms\Components\DatePicker::make('single_date')
                                ->label('Pilih Tanggal')
                                ->visible(fn ($get) => $get('filter_type') === 'single')
                                ->required(fn ($get) => $get('filter_type') === 'single')
                                ->closeOnDateSelection(),
                            
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->visible(fn ($get) => $get('filter_type') === 'range')
                                ->required(fn ($get) => $get('filter_type') === 'range')
                                ->closeOnDateSelection(),
                            
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Tanggal Akhir')
                                ->visible(fn ($get) => $get('filter_type') === 'range')
                                ->required(fn ($get) => $get('filter_type') === 'range')
                                ->afterOrEqual('start_date')
                                ->closeOnDateSelection(),
                        ])
                    ])
                    ->action(function (array $data) {
                        $params = ['format' => 'pdf'];
                        
                        if ($data['filter_type'] === 'single' && !empty($data['single_date'])) {
                            $params['filter_type'] = 'single';
                            $params['single_date'] = $data['single_date'];
                        } elseif ($data['filter_type'] === 'range' && !empty($data['start_date']) && !empty($data['end_date'])) {
                            $params['filter_type'] = 'range';
                            $params['start_date'] = $data['start_date'];
                            $params['end_date'] = $data['end_date'];
                        }
                        
                        $url = route('export-attendance', $params);
                        
                        // Show notification
                        \Filament\Notifications\Notification::make()
                            ->title('PDF berhasil dibuat')
                            ->success()
                            ->send();
                            
                        // Redirect to PDF URL
                        return redirect()->away($url);
                    })
                    ->modalHeading('Filter Cetak PDF')
                    ->modalSubheading('Pilih filter tanggal untuk data absensi yang akan dicetak ke PDF')
                    ->modalButton('Cetak PDF'),
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