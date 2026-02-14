<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim link reset password via email
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validasi email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cek apakah email adalah email admin/owner
        $user = User::where('email', $request->email)
                    ->whereIn('role', ['super_admin', 'admin'])
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan atau bukan akun admin.'
            ]);
        }

        // Generate token
        $token = Str::random(64);

        // Hapus token lama jika ada
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Simpan token baru
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Kirim email
        Mail::send('emails.reset-password', ['token' => $token, 'email' => $request->email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password - ARIFAH Gym');
        });

        return back()->with('success', 'Link reset password telah dikirim ke email Anda!');
    }
}
