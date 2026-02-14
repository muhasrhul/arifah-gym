# 🏋️ ARIFAH GYM - Sistem Manajemen Gym

Sistem manajemen gym berbasis web menggunakan Laravel + Filament untuk mengelola member, transaksi, absensi, dan produk kantin.

---

## 🚀 Fitur Utama

### 1. **Manajemen Member**
- Pendaftaran member baru (online & offline)
- Perpanjangan membership
- Biaya registrasi otomatis untuk member baru
- Status member (aktif/expired)
- Tracking expiry date

### 2. **Kasir Cepat & Kantin**
- Pembayaran harian/tamu
- Penjualan produk kantin
- Manajemen stock produk
- Notifikasi real-time ke admin

### 3. **Absensi Member**
- Check-in otomatis untuk member aktif
- Riwayat absensi
- Laporan kehadiran

### 4. **Transaksi & Keuangan**
- Pencatatan transaksi otomatis
- Laporan pendapatan
- Dashboard statistik
- Export PDF

### 5. **Notifikasi Otomatis**
- **Telegram**: Notifikasi pendaftaran, perpanjangan, transaksi
- **WhatsApp**: Reminder membership expired (H-1)
- **Email**: Reset password untuk admin

### 6. **Dashboard & Laporan**
- Statistik omzet harian/bulanan
- Grafik pendapatan
- Member expired
- Jam teramai

---

## 📋 Dokumentasi

### Setup & Konfigurasi:
- **EMAIL_SETUP.md** - Setup email Gmail SMTP
- **TELEGRAM_SETUP.md** - Setup notifikasi Telegram
- **WHATSAPP_REMINDER_SETUP.md** - Setup reminder WhatsApp (Fonnte)
- **SCHEDULER_SETUP.md** - Setup scheduler Laravel
- **CARA_TEST_SCHEDULER.md** - Cara test scheduler

### Dokumentasi Fitur:
- **FITUR_BIAYA_REGISTRASI.md** - Biaya admin untuk member baru
- **FITUR_FORGOT_PASSWORD.md** - Reset password admin
- **FITUR_STOCK_PRODUK.md** - Manajemen stock produk kantin
- **FITUR_WHATSAPP_REMINDER.md** - Reminder WhatsApp otomatis
- **FONNTE_VERIFICATION_CHECKLIST.md** - Checklist verifikasi Fonnte

### Script Helper:
- **test-email.bat** - Test kirim email
- **test-whatsapp-reminder.bat** - Test reminder WhatsApp
- **run-scheduler.bat** - Jalankan scheduler
- **fix-database.bat** - Fix database issues

---

## 🛠️ Tech Stack

- **Framework**: Laravel 9.x
- **Admin Panel**: Filament v2
- **Database**: MySQL
- **Frontend**: Blade, Tailwind CSS, Livewire
- **Notifikasi**: Telegram Bot API, Fonnte (WhatsApp), Gmail SMTP

---

## ⚙️ Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd arifah-gym
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
copy .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_DATABASE=dbku_baru
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi Database
```bash
php artisan migrate
php artisan db:seed
```

### 6. Setup Notifikasi (Opsional)
- Email: Lihat `EMAIL_SETUP.md`
- Telegram: Lihat `TELEGRAM_SETUP.md`
- WhatsApp: Lihat `WHATSAPP_REMINDER_SETUP.md`

### 7. Jalankan Aplikasi
```bash
php artisan serve
```

Akses: http://localhost:8000

---

## 👤 Default Login

**Admin:**
- Email: admin@arifah.gym
- Password: admin123

---

## 📅 Scheduler (Reminder Otomatis)

Untuk menjalankan reminder WhatsApp otomatis:

```bash
run-scheduler.bat
```

Atau manual:
```bash
php artisan schedule:work
```

---

## 🎨 Branding

- **Nama**: ARIFAH Gym Makassar
- **Warna Primary**: Cyan (#0992C2)
- **Logo**: Di folder `public/images/`

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi developer atau buat issue di repository.

---

## 📝 License

Proprietary - ARIFAH Gym Makassar

---

**Version**: 1.0
**Last Update**: 13 Februari 2026
