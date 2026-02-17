<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Paket;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FrontMemberController extends Controller
{
    // 1. HALAMAN UTAMA (LANDING PAGE)
    public function index(Request $request)
    {
        // A. Ambil Daftar Paket Aktif (dengan cache 10 menit)
        $pakets = cache()->remember('pakets_aktif', 600, function () {
            return Paket::where('is_active', true)->orderBy('harga', 'asc')->get();
        });

        // B. Ambil Registration Fee (dari paket pertama yang aktif)
        $registrationFee = cache()->remember('registration_fee_display', 600, function () use ($pakets) {
            $firstPaket = $pakets->first();
            return $firstPaket ? (int)$firstPaket->registration_fee : 100000;
        });

        // C. Persiapan Variabel Cek Status
        $member = null;
        $totalAbsenBulanIni = 0;
        $riwayatAbsen = [];

        $search = $request->get('search');
        
        if ($search) {
            // --- LOGIKA PENCARIAN SUPER FLEKSIBEL (UPDATE) ---
            
            // 1. Bersihkan input: Hanya ambil ANGKA saja (hapus +, -, spasi, titik, kurung)
            // Contoh input: "+62 853-4176-9151" -> Menjadi: "6285341769151"
            // 1. Ambil input dari user
            $search = $request->input('search');

            // 2. Buang semua karakter kecuali angka (Spasi, plus, strip hilang semua)
            $cleanDigits = preg_replace('/[^0-9]/', '', $search);

            // 3. Normalisasi format 62 menjadi 0 agar cocok dengan database
            $localFormat = $cleanDigits;
            if (str_starts_with($cleanDigits, '62')) {
                $localFormat = '0' . substr($cleanDigits, 2);
            }

            // 4. Jalankan Query (Hanya cari di kolom telepon)
            $member = Member::where(function($query) use ($cleanDigits, $localFormat) {
                if (!empty($cleanDigits)) {
                    // Cari nomor asli, nomor bersih, atau nomor format 08...
                    $query->where('phone', 'like', "%{$cleanDigits}%")
                        ->orWhere('phone', 'like', "%{$localFormat}%")
                        ->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$localFormat}%"]);
                } else {
                    // Jika input tidak ada angka sama sekali, paksa query jadi kosong
                    $query->whereRaw('1 = 0');
                }
            })->first();

            // --- JIKA MEMBER DITEMUKAN, HITUNG STATISTIK ---
            if ($member) {
                // Set Waktu Makassar
                $now = Carbon::now('Asia/Makassar');

                // Hitung Absen Bulan Ini
                $totalAbsenBulanIni = Attendance::where('member_id', $member->id)
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count();

                // Ambil 5 Riwayat Terakhir
                $riwayatAbsen = Attendance::where('member_id', $member->id)
                    ->latest()
                    ->take(5)
                    ->get();
            }
        }

        // C. Kirim Data ke View
        return view('welcome', compact('pakets', 'registrationFee', 'member', 'search', 'totalAbsenBulanIni', 'riwayatAbsen'));
    }

    // 2. PROSES PENDAFTARAN MEMBER BARU (TANPA MIDTRANS)
    public function store(Request $request)
    {
        try {
            // A. Validasi Input
            $validated = $request->validate([
                'name'      => 'required|string|min:3', 
                'email'     => 'required|email|unique:members,email', 
                'phone'     => 'required|unique:members,phone',
                'paket_id'  => 'required|exists:pakets,id',
                'nik'       => 'nullable|digits:16|unique:members,nik', 
            ]);

            // B. Ambil Data Paket
            $paket = Paket::findOrFail($request->paket_id);
            $hargaPaket = $paket->harga;
            
            // Hanya paket bulanan (durasi >= 30 hari) yang kena registration fee
            // Paket harian (durasi < 30 hari) tidak kena fee
            $registrationFee = ($paket->durasi_hari >= 30) ? ($paket->registration_fee ?? 0) : 0;
            
            $amount = $hargaPaket + $registrationFee; // Total = Harga Paket + Fee
            $typeName = $paket->nama_paket;

            // C. Simpan Member ke Database
            $orderId = 'REG-' . uniqid(); // ID Unik Order
            
            try {
                $member = Member::create([
                    'name'           => $request->name,
                    'phone'          => $request->phone,
                    'email'          => $request->email,
                    'nik'            => $request->nik, // Tambahkan NIK (optional)
                    'type'           => $paket->nama_paket, 
                    'join_date'      => now(),
                    'expiry_date'    => null, // Belum aktif sampai bayar
                    'is_active'      => false,
                    'order_id'       => $orderId,
                    'payment_method' => 'Pending', // Menunggu pembayaran manual
                ]);
                
                Log::info("Member created successfully", [
                    'member_id' => $member->id,
                    'name' => $member->name,
                    'order_id' => $orderId
                ]);
                
            } catch (\Exception $e) {
                Log::error("Error creating member", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $request->all()
                ]);
                return back()->withInput()->with('error', 'Gagal menyimpan data member: ' . $e->getMessage());
            }

            // D. Kirim Notifikasi ke Panel Admin (Filament)
            try {
                $admins = User::all();
                foreach ($admins as $admin) {
                    Notification::make()
                        ->title('Pendaftaran Member Baru!')
                        ->body("**{$member->name}** baru saja mendaftar paket **{$typeName}** via Website. Silakan aktivasi manual di panel admin.")
                        ->icon('heroicon-o-globe-alt')
                        ->iconColor('warning') 
                        ->sendToDatabase($admin);
                }
                
                // Kirim notifikasi ke Telegram & WhatsApp
                \App\Helpers\TelegramHelper::sendPendaftaranBaru($member, $typeName);
                \App\Helpers\WhatsAppHelper::sendPendaftaranBaru($member, $typeName);
                
            } catch (\Exception $e) {
                // Catat error di log jika notifikasi gagal, tapi jangan stop proses
                Log::error("Gagal notif admin: " . $e->getMessage());
            }

            // E. Redirect ke halaman pembayaran (tanpa Midtrans)
            return view('pembayaran', compact('member', 'amount', 'paket', 'hargaPaket', 'registrationFee'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error
            Log::warning("Validation failed", [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            // General error
            Log::error("Unexpected error in store method", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi admin. Error: ' . $e->getMessage());
        }
    }

    // 3. UPDATE METODE PEMBAYARAN (MANUAL)
    public function updatePaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'payment_method' => 'required|in:transfer_bank,cash'
            ]);

            $member = Member::findOrFail($request->member_id);
            $member->payment_method = $request->payment_method;
            $member->save();

            Log::info("Payment method updated", [
                'member_id' => $member->id,
                'payment_method' => $request->payment_method
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Metode pembayaran berhasil disimpan. Silakan hubungi admin untuk aktivasi.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating payment method: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan metode pembayaran'
            ], 500);
        }
    }
}