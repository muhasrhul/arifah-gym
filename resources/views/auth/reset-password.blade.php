<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ARIFAH Gym</title>
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
                    <i class="fa-solid fa-lock text-[#0992C2] text-2xl"></i>
                </div>
                <h2 class="text-2xl font-black italic uppercase tracking-tight mb-2">Reset Password</h2>
                <p class="text-zinc-400 text-sm">Masukkan password baru Anda</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-bold text-zinc-400 mb-2 uppercase tracking-wider">Email Admin</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white"
                           placeholder="admin@arifahgym.com">
                </div>

                <div>
                    <label class="block text-sm font-bold text-zinc-400 mb-2 uppercase tracking-wider">Password Baru</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white"
                           placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label class="block text-sm font-bold text-zinc-400 mb-2 uppercase tracking-wider">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:ring-2 focus:ring-[#0992C2]/20 outline-none transition-all text-white"
                           placeholder="Ketik ulang password">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-[#0992C2] to-[#0992C2] hover:from-[#0992C2] hover:to-[#0992C2] text-black font-black py-4 rounded-xl uppercase tracking-wider transition-all shadow-lg hover:shadow-[#0992C2]/50 transform hover:scale-[1.02]">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    Reset Password
                </button>
            </form>

            <div class="mt-8 text-center">
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
</body>
</html>
