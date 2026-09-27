<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - ARIFAH Gym</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #030712;
            background-image: radial-gradient(circle at top right, rgba(9, 146, 194, 0.05), transparent);
        }
        
        .font-hero { 
            font-weight: 900; 
            font-style: italic; 
            letter-spacing: -0.05em; 
            text-transform: uppercase;
        }

        .orange-glow {
            text-shadow: 0 0 20px rgba(9, 146, 194, 0.6);
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
            <h1 class="text-4xl font-hero text-white italic orange-glow">
                ARIFAH <span class="text-[#0992C2]">GYM</span>
            </h1>
            <p class="text-zinc-500 text-[10px] uppercase tracking-[0.5em] font-black italic mt-2">Payment Status</p>
        </div>

        <div class="glass-card rounded-[3rem] p-10 shadow-2xl text-center">
            @if($status === 'success')
                <!-- Success -->
                <div class="w-24 h-24 mx-auto mb-6 bg-green-500/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-500 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-hero text-green-500 mb-4">PEMBAYARAN BERHASIL!</h2>
                <p class="text-zinc-400 mb-6">Terima kasih, pembayaran Anda telah dikonfirmasi.</p>
                
                @if(isset($member))
                <div class="bg-white/5 rounded-xl p-6 mb-6 text-left">
                    <p class="text-zinc-500 text-xs mb-3 uppercase">Informasi Member</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Nama:</span>
                            <span class="font-bold">{{ $member->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Paket:</span>
                            <span class="font-bold">{{ $member->type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Status:</span>
                            <span class="font-bold text-green-500">{{ $member->is_active ? 'Aktif' : 'Menunggu' }}</span>
                        </div>
                    </div>
                </div>
                @endif

            @elseif($status === 'pending')
                <!-- Pending -->
                <div class="w-24 h-24 mx-auto mb-6 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-clock text-yellow-500 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-hero text-yellow-500 mb-4">PEMBAYARAN DIPROSES</h2>
                <p class="text-zinc-400 mb-6">{{ $message }}</p>

            @else
                <!-- Failed -->
                <div class="w-24 h-24 mx-auto mb-6 bg-red-500/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-times-circle text-red-500 text-5xl"></i>
                </div>
                <h2 class="text-3xl font-hero text-red-500 mb-4">PEMBAYARAN GAGAL</h2>
                <p class="text-zinc-400 mb-6">{{ $message }}</p>
            @endif

            <a href="/" class="block w-full bg-gradient-to-r from-[#0992C2] to-[#0782A9] text-black font-black py-4 rounded-xl uppercase tracking-wider hover:shadow-lg hover:shadow-[#0992C2]/50 transition-all duration-300">
                <i class="fa-solid fa-home mr-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
