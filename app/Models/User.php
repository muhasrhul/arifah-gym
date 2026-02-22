<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser; // <--- TAMBAHAN: Biar Lonceng Filament Kenal User Ini

class User extends Authenticatable implements FilamentUser // <--- TAMBAHAN: Implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * SYARAT FILAMENT: Menentukan siapa saja yang boleh masuk panel admin.
     * Kita set true agar user yang terdaftar bisa akses lonceng & dashboard.
     */
    public function canAccessFilament(): bool
    {
        // Hanya super_admin, admin, dan user yang bisa akses panel
        return in_array($this->role, ['super_admin', 'admin', 'user']); 
    }
    
    /**
     * Helper method untuk cek apakah user adalah super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    
    /**
     * Helper method untuk cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Helper method untuk cek apakah user adalah staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'user';
    }

    /**
     * LANGKAH 2: Relasi manual ke tabel notifications.
     * Tetap dipertahankan sesuai keinginan Bapak agar sinkron dengan BIGINT di phpMyAdmin.
     */
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }
}