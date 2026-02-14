<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form reset password
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Proses reset password
     */
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ]);

        // Cek token di database
        $updatePassword = DB::table('password_resets')
                            ->where([
                                'email' => $request->email,
                                'token' => $request->token
                            ])
                            ->first();

        if (!$updatePassword) {
            return back()->withErrors(['email' => 'Token reset password tidak valid!']);
        }

        // Cek apakah token sudah expired (24 jam)
        $tokenCreated = Carbon::parse($updatePassword->created_at);
        if (Carbon::now()->diffInHours($tokenCreated) > 24) {
            return back()->withErrors(['email' => 'Token reset password sudah kadaluarsa!']);
        }

        // Update password user
        $user = User::where('email', $request->email)
                    ->whereIn('role', ['super_admin', 'admin'])
                    ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan atau bukan akun admin.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token setelah berhasil reset
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/admin/login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
