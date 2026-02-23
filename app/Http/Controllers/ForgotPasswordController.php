<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\WhatsAppHelper;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form request OTP
     */
    public function showOtpRequestForm()
    {
        return view('auth.forgot-password-otp');
    }

    /**
     * Kirim OTP ke WhatsApp
     */
    public function sendOtp(Request $request)
    {
        // Validasi nomor HP
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 10 digit.',
            'phone.max' => 'Nomor WhatsApp maksimal 15 digit.',
        ]);

        // Format nomor HP
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // KEAMANAN: Cek rate limiting (maksimal 3 request per 15 menit)
        $recentRequests = DB::table('password_reset_otps')
            ->where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->subMinutes(15))
            ->count();

        if ($recentRequests >= 3) {
            return back()->withErrors([
                'phone' => 'Terlalu banyak permintaan OTP. Silakan tunggu 15 menit sebelum mencoba lagi.'
            ]);
        }

        // KEAMANAN: Cek cooldown (minimal 2 menit antar request)
        $lastRequest = DB::table('password_reset_otps')
            ->where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastRequest && Carbon::parse($lastRequest->created_at)->addMinutes(2)->isFuture()) {
            $waitTime = Carbon::parse($lastRequest->created_at)->addMinutes(2)->diffInSeconds(Carbon::now());
            return back()->withErrors([
                'phone' => "Silakan tunggu {$waitTime} detik sebelum meminta OTP baru."
            ]);
        }

        // Cek apakah nomor HP terdaftar sebagai admin/owner
        $user = User::where('phone', 'like', '%' . substr($phone, -10) . '%')
                    ->whereIn('role', ['super_admin', 'admin'])
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'phone' => 'Nomor WhatsApp tidak terdaftar atau bukan akun admin.'
            ]);
        }

        // Generate OTP 6 digit yang lebih aman
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama untuk nomor ini
        DB::table('password_reset_otps')->where('phone', $phone)->delete();

        // Simpan OTP baru (expired 10 menit)
        DB::table('password_reset_otps')->insert([
            'phone' => $phone,
            'otp' => $otp,
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
        ]);

        // Kirim OTP via WhatsApp
        $message = "🔐 *KODE OTP RESET PASSWORD - ARIFAH GYM*\n\n";
        $message .= "Kode OTP Anda: *{$otp}*\n\n";
        $message .= "Kode ini berlaku selama 10 menit.\n";
        $message .= "Jangan bagikan kode ini kepada siapapun!\n\n";
        $message .= "Jika Anda tidak merasa melakukan reset password, abaikan pesan ini.\n\n";
        $message .= "ARIFAH Gym System";

        $result = WhatsAppHelper::sendMessage($phone, $message);

        if (!$result['success']) {
            return back()->withErrors([
                'phone' => 'Gagal mengirim OTP. Silakan coba lagi.'
            ]);
        }

        // Simpan phone ke session dengan cara yang lebih persistent
        session()->put('otp_phone', $phone);
        session()->save();

        return redirect()->route('password.verify.otp.form')
                        ->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda!');
    }

    /**
     * Tampilkan form verifikasi OTP
     */
    public function showVerifyOtpForm()
    {
        if (!session('otp_phone')) {
            return redirect()->route('password.request.otp')
                           ->withErrors(['phone' => 'Sesi expired. Silakan request OTP baru.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verifikasi OTP dan tampilkan form reset password
     */
    public function verifyOtp(Request $request)
    {
        // Validasi OTP
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit.',
        ]);

        $phone = session('otp_phone');
        if (!$phone) {
            return redirect()->route('password.request.otp')
                           ->withErrors(['otp' => 'Sesi expired. Silakan request OTP baru.']);
        }

        // Cari OTP di database
        $otpRecord = DB::table('password_reset_otps')
                      ->where('phone', $phone)
                      ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah expired.']);
        }

        // Cek apakah sudah expired
        if (Carbon::parse($otpRecord->expires_at)->isPast()) {
            DB::table('password_reset_otps')->where('phone', $phone)->delete();
            return back()->withErrors(['otp' => 'Kode OTP sudah expired. Silakan request OTP baru.']);
        }

        // Cek jumlah percobaan
        if ($otpRecord->attempts >= 3) {
            DB::table('password_reset_otps')->where('phone', $phone)->delete();
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan gagal. Silakan request OTP baru.']);
        }

        // Verifikasi OTP
        if ($request->otp !== $otpRecord->otp) {
            // Increment attempts
            DB::table('password_reset_otps')
              ->where('phone', $phone)
              ->increment('attempts');

            $remainingAttempts = 3 - ($otpRecord->attempts + 1);
            return back()->withErrors(['otp' => "Kode OTP salah. Sisa percobaan: {$remainingAttempts}x"]);
        }

        // OTP benar, hapus dari database
        DB::table('password_reset_otps')->where('phone', $phone)->delete();

        // Simpan flag verifikasi berhasil
        session()->put('otp_verified', true);
        session()->save();

        // Redirect ke form reset password
        return redirect()->route('password.reset.form')
                        ->with('success', 'Verifikasi berhasil! Silakan masukkan password baru.');
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm()
    {
        if (!session('otp_phone') || !session('otp_verified')) {
            return redirect()->route('password.request.otp')
                           ->withErrors(['error' => 'Sesi expired. Silakan ulangi proses verifikasi OTP.']);
        }

        return view('auth.reset-password-otp');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        // Validasi password baru
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $phone = session('otp_phone');
        if (!$phone || !session('otp_verified')) {
            return redirect()->route('password.request.otp')
                           ->withErrors(['password' => 'Sesi expired. Silakan ulangi proses reset password.']);
        }

        // Cari user berdasarkan nomor HP
        $user = User::where('phone', 'like', '%' . substr($phone, -10) . '%')
                    ->whereIn('role', ['super_admin', 'admin'])
                    ->first();

        if (!$user) {
            return redirect()->route('password.request.otp')
                           ->withErrors(['password' => 'User tidak ditemukan.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus session
        session()->forget(['otp_phone', 'otp_verified']);
        session()->save();

        // Kirim notifikasi ke WhatsApp
        $message = "✅ *PASSWORD BERHASIL DIRESET - ARIFAH GYM*\n\n";
        $message .= "Halo *{$user->name}*\n\n";
        $message .= "Password Anda telah berhasil direset.\n\n";
        $message .= "Jika Anda tidak melakukan reset password, segera hubungi admin!\n\n";
        $message .= "ARIFAH Gym System";

        WhatsAppHelper::sendMessage($phone, $message);

        return redirect('/admin/login')
                      ->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
