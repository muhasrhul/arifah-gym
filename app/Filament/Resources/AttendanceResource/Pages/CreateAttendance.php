<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    // FUNGSI PENGALIHAN OTOMATIS KE DAFTAR LOG ABSENSI
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}