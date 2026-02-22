# Implementasi 2FA OTP via WhatsApp untuk Reset Password

## Ringkasan
Sistem reset password sekarang menggunakan 2FA (Two-Factor Authentication) dengan OTP yang dikirim via WhatsApp, menggantikan sistem email sebelumnya.

## Fitur Keamanan

### 1. OTP (One-Time Password)
- Kode 6 digit random
- Berlaku selama 10 menit
- Sekali pakai (dihapus setelah verifikasi berhasil)

### 2. Rate Limiting
- Maksimal 3 percobaan input OTP
- Setelah 3x gagal, OTP dihapus dan user harus request ulang

### 3. Validasi Ketat
- Hanya admin/super_admin yang bisa reset password
- Nomor WhatsApp harus terdaftar di database
- OTP expired otomatis setelah 10 menit

### 4. Notifikasi WhatsApp
- OTP dikirim langsung ke WhatsApp admin
- Notifikasi konfirmasi setelah password berhasil direset

## File yang Dibuat/Diubah

### 1. Database Migrations
- `database/migrations/2026_02_23_120000_create_password_reset_otps_table.php`
  - Tabel untuk menyimpan OTP sementara
  - Kolom: phone, otp, attempts, expires_at

- `database/migrations/2026_02_23_120100_add_phone_to_users_table.php`
  - Menambahkan kolom phone ke tabel users

### 2. Controller
- `app/Http/Controllers/ForgotPasswordController.php`
  - `showOtpRequestForm()` - Form input nomor WhatsApp
  - `sendOtp()` - Generate dan kirim OTP via WhatsApp
  - `showVerifyOtpForm()` - Form input OTP
  - `verifyOtp()` - Validasi OTP
  - `showResetForm()` - Form input password baru
  - `resetPassword()` - Update password di database

### 3. Views
- `resources/views/auth/forgot-password-otp.blade.php` - Form request OTP
- `resources/views/auth/verify-otp.blade.php` - Form verifikasi OTP
- `resources/views/auth/reset-password-otp.blade.php` - Form reset password

### 4. Model
- `app/Models/User.php` - Tambah 'phone' ke fillable array

### 5. Routes
- `routes/web.php` - Update routes untuk OTP flow

## Alur Kerja (Flow)

```
1. Admin buka /forgot-password
   ↓
2. Input nomor WhatsApp
   ↓
3. Sistem validasi nomor (harus terdaftar sebagai admin)
   ↓
4. Generate OTP 6 digit
   ↓
5. Simpan OTP ke database (expired 10 menit)
   ↓
6. Kirim OTP via WhatsApp
   ↓
7. Admin input OTP di form verifikasi
   ↓
8. Sistem validasi OTP:
   - Cek expired
   - Cek jumlah percobaan (max 3x)
   - Cek kecocokan OTP
   ↓
9. Jika valid, redirect ke form reset password
   ↓
10. Admin input password baru
    ↓
11. Update password di database
    ↓
12. Kirim notifikasi konfirmasi via WhatsApp
    ↓
13. Redirect ke halaman login
```

## Cara Deploy

### 1. Di Laptop (Local)
```bash
# Jalankan migrations
php artisan migrate

# Clear cache
php artisan config:cache
php artisan route:cache
```

### 2. Push ke GitHub
```bash
git add .
git commit -m "Implementasi 2FA OTP via WhatsApp untuk reset password"
git push origin main
```

### 3. Di VPS M (Production - 203.175.11.99)
```bash
# Masuk ke direktori project
cd /var/www/irongym

# Pull dari GitHub
git pull origin main

# Jalankan migrations
php artisan migrate

# Clear cache
php artisan config:cache
php artisan route:cache

# Restart services
sudo systemctl restart laravel-worker
sudo systemctl restart nginx
```

### 4. Di VPS S (Testing - 202.10.48.65)
```bash
# Masuk ke direktori project
cd /var/www/irongym

# Pull dari GitHub
git pull origin main

# Jalankan migrations
php artisan migrate

# Clear cache
php artisan config:cache
php artisan route:cache

# Restart services
sudo systemctl restart laravel-worker
sudo systemctl restart nginx
```

## Konfigurasi yang Diperlukan

### 1. Pastikan di .env sudah ada:
```env
# WhatsApp (Fonnte)
FONNTE_TOKEN=9XK7CvKB6AMfiDwMFmgp

# Owner WhatsApp
OWNER_WHATSAPP=082260580399
```

### 2. Pastikan di config/services.php sudah ada:
```php
'fonnte' => [
    'token' => env('FONNTE_TOKEN'),
],

'whatsapp' => [
    'owner' => env('OWNER_WHATSAPP'),
],
```

## Testing

### 1. Test Request OTP
- Buka: http://your-domain/forgot-password
- Input nomor WhatsApp admin yang terdaftar
- Cek WhatsApp, harus menerima kode OTP 6 digit

### 2. Test Verifikasi OTP
- Input kode OTP yang diterima
- Jika benar, akan redirect ke form reset password
- Jika salah, akan muncul error dan sisa percobaan

### 3. Test Reset Password
- Input password baru (minimal 8 karakter)
- Konfirmasi password
- Jika berhasil, akan redirect ke login
- Cek WhatsApp, harus menerima notifikasi konfirmasi

### 4. Test Security
- Coba input OTP salah 3x → harus minta OTP baru
- Tunggu 10 menit → OTP harus expired
- Coba dengan nomor yang tidak terdaftar → harus error

## Keunggulan Sistem Baru

### Dibanding Email:
1. ✅ Lebih cepat (WhatsApp instant, email bisa delay)
2. ✅ Lebih aman (OTP 6 digit + expired 10 menit)
3. ✅ Tidak perlu konfigurasi SMTP
4. ✅ Tidak masuk spam folder
5. ✅ User lebih familiar dengan WhatsApp

### Fitur Keamanan:
1. ✅ Rate limiting (max 3 percobaan)
2. ✅ OTP expired otomatis
3. ✅ Sekali pakai (one-time use)
4. ✅ Validasi nomor terdaftar
5. ✅ Notifikasi konfirmasi

## Troubleshooting

### OTP tidak terkirim
- Cek FONNTE_TOKEN di .env
- Cek saldo Fonnte
- Cek format nomor WhatsApp (harus 08xxx atau 628xxx)

### OTP expired terus
- Cek timezone server (harus Asia/Makassar)
- Cek waktu server dengan `date`

### Error "Nomor tidak terdaftar"
- Pastikan user sudah punya kolom phone di database
- Jalankan migration: `php artisan migrate`
- Update data user di panel admin, tambahkan nomor WhatsApp

## Catatan Penting

1. **Kolom Phone di Users Table**
   - Setelah migration, admin harus update nomor WhatsApp mereka di panel admin
   - Tanpa nomor WhatsApp, admin tidak bisa reset password

2. **Session Management**
   - Sistem menggunakan session untuk menyimpan nomor HP sementara
   - Session otomatis dihapus setelah password berhasil direset

3. **Database Cleanup**
   - OTP otomatis dihapus setelah:
     - Verifikasi berhasil
     - Expired (10 menit)
     - Percobaan gagal 3x

4. **Backward Compatibility**
   - Sistem email lama sudah diganti total
   - Route lama sudah diupdate ke OTP flow
   - File `ResetPasswordController.php` tidak digunakan lagi

## Maintenance

### Cleanup OTP Expired (Optional)
Buat scheduled task untuk cleanup OTP yang expired:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Cleanup OTP expired setiap jam
    $schedule->call(function () {
        DB::table('password_reset_otps')
          ->where('expires_at', '<', now())
          ->delete();
    })->hourly();
}
```

---

**Dibuat oleh:** Kiro AI Assistant  
**Tanggal:** 23 Februari 2026  
**Versi:** 1.0
