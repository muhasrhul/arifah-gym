<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - ARIFAH Gym</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }
        .glass-card {
            background: rgba(24, 24, 27, 0.8);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .otp-input {
            width: 3rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-white">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-5xl font-black italic tracking-tighter">
                ARIFAH <span class="text-[#0992C2]">GYM</span>
            </h1>
            <p class="text-zinc-500 text-xs uppercase tracking-widest mt-2 font-bold">Admin Panel</p>
        </div>

        <div class="glass-card rounded-3xl p-10 shadow-2xl">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-[#0992C2]/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-shield-halved text-[#0992C2] text-2xl"></i>
                </div>
                <h2 class="text-2xl font-black italic uppercase tracking-tight mb-2">Verifikasi OTP</h2>
                <p class="text-zinc-400 text-sm">Masukkan kode OTP yang dikirim ke WhatsApp Anda</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 text-sm">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.verify.otp') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-zinc-400 mb-4 uppercase tracking-wider text-center">Kode OTP (6 Digit)</label>
                    <div class="flex justify-center gap-2 mb-4">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp1" oninput="moveToNext(this, 'otp2')">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp2" oninput="moveToNext(this, 'otp3')">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp3" oninput="moveToNext(this, 'otp4')">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp4" oninput="moveToNext(this, 'otp5')">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp5" oninput="moveToNext(this, 'otp6')">
                        <input type="text" maxlength="1" class="otp-input rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white" id="otp6" oninput="moveToNext(this, null)">
                    </div>
                    <input type="hidden" name="otp" id="otpFull">
                    <p class="text-zinc-500 text-xs text-center">
                        <i class="fa-solid fa-clock mr-1"></i>
                        Kode OTP berlaku selama 10 menit
                    </p>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-[#0992C2] to-[#0992C2] hover:from-[#0992C2] hover:to-[#0992C2] text-black font-black py-4 rounded-xl uppercase tracking-wider transition-all shadow-lg hover:shadow-[#0992C2]/50 transform hover:scale-[1.02]">
                    <i class="fa-solid fa-check mr-2"></i>
                    Verifikasi OTP
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('password.request.otp') }}" class="text-zinc-400 hover:text-[#0992C2] text-sm font-bold uppercase tracking-wider transition-colors">
                    <i class="fa-solid fa-rotate-right mr-2"></i>
                    Kirim Ulang OTP
                </a>
            </div>

            <div class="mt-4 text-center">
                <a href="/admin/login" class="text-zinc-400 hover:text-[#0992C2] text-sm font-bold uppercase tracking-wider transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>

        <p class="mt-8 text-zinc-700 text-xs uppercase tracking-widest font-black text-center">
            ARIFAH Gym &copy; 2026
        </p>
    </div>

    <script>
        function moveToNext(current, nextFieldId) {
            if (current.value.length >= 1) {
                if (nextFieldId) {
                    document.getElementById(nextFieldId).focus();
                } else {
                    // Last input, combine all OTP values
                    combineOTP();
                }
            }
        }

        function combineOTP() {
            const otp1 = document.getElementById('otp1').value;
            const otp2 = document.getElementById('otp2').value;
            const otp3 = document.getElementById('otp3').value;
            const otp4 = document.getElementById('otp4').value;
            const otp5 = document.getElementById('otp5').value;
            const otp6 = document.getElementById('otp6').value;
            
            document.getElementById('otpFull').value = otp1 + otp2 + otp3 + otp4 + otp5 + otp6;
        }

        // Combine OTP before form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            combineOTP();
        });

        // Auto-focus first input
        document.getElementById('otp1').focus();

        // Allow backspace to move to previous input
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>
