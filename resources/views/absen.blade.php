<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARIFAH Gym - Absensi Member</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000; overflow-x: hidden; }
        .font-hero { font-family: 'Poppins'; font-weight: 900; font-style: italic; text-transform: uppercase; }
        
        .bg-gym {
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.85), rgba(0,0,0,0.95)), 
                              url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1000&auto=format&fit=crop');
            background-size: cover; background-position: center;
        }

        .premium-card {
            background: rgba(15, 15, 15, 0.75);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8);
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s ease;
        }

        .input-premium:focus {
            border-color: #ea580c;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 30px rgba(234, 88, 12, 0.15);
        }

        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        #finalCanvas { display: none; }
        .font-loader { position: absolute; visibility: hidden; height: 0; width: 0; font-family: 'Poppins'; }
    </style>
</head>
<body class="bg-gym text-white flex items-center justify-center min-h-screen p-4">

    <div class="font-loader" style="font-weight: 400;">Poppins 400</div>
    <div class="font-loader" style="font-weight: 900; font-style: italic;">Poppins 900i</div>

    <audio id="successSound" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3"></audio>

    <div class="w-full max-w-md">
        
        <div class="text-center mb-10">
            <h1 class="text-5xl font-hero tracking-tighter italic">ARIFAH <span class="text-[#0992C2]">GYM</span></h1>
            <div class="flex items-center justify-center gap-3 mt-2">
                <div class="h-[1px] w-10 bg-zinc-800"></div>
                <p class="text-zinc-500 text-[10px] uppercase tracking-[0.5em] font-black italic">MAKASSAR</p>
                <div class="h-[1px] w-10 bg-zinc-800"></div>
            </div>
        </div>

        @if(session('success'))
            <script>document.getElementById('successSound').play();</script>

            <div class="card-pop flex flex-col items-center premium-card p-8 rounded-[3.5rem] text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4 shadow-xl">
                    <i class="fa-solid fa-check text-2xl text-black"></i>
                </div>
                
                <h1 class="text-3xl font-hero text-green-500 mb-1 italic">ABSEN BERHASIL</h1>
                <p class="text-zinc-500 text-[10px] uppercase tracking-widest mb-8 font-bold">{{ now()->format('H:i') }} WITA</p>

                <div class="grid grid-cols-2 gap-4 w-full mb-8 text-center">
                    <div class="bg-white/[0.03] p-5 rounded-3xl border border-white/5">
                        <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">TOTAL SESI</p>
                        <h3 class="text-3xl font-black italic">{{ session('total_latihan') }}<span class="text-sm text-green-500 ml-1">X</span></h3>
                    </div>
                    <div class="bg-white/[0.03] p-5 rounded-3xl border border-white/5 flex flex-col justify-center items-center">
                        <i class="fa-solid {{ session('badge') == 'ARIFAH WARRIOR' ? 'fa-fire text-[#0992C2]' : 'fa-medal text-blue-400' }} text-2xl mb-1"></i>
                        <p class="text-[10px] font-black uppercase tracking-tighter">{{ session('badge') }}</p>
                    </div>
                </div>

                <!-- Modern Member Card dengan Glassmorphism -->
                <div class="relative w-[310px] h-[185px] rounded-[2.5rem] overflow-hidden shadow-2xl mb-8 mx-auto transition-all duration-500 hover:scale-105 hover:shadow-[0_20px_60px_-15px_rgba(9,146,194,0.5)] group" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%); backdrop-filter: blur(20px);">
                    
                    <!-- Animated Background Pattern -->
                    <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
                        <div class="absolute top-0 left-0 w-full h-full" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);"></div>
                    </div>

                    <!-- Dumbbell Watermark dengan Animasi -->
                    <div class="absolute -bottom-4 -left-4 opacity-[0.06] transform -rotate-12 pointer-events-none transition-all duration-700 group-hover:opacity-[0.12] group-hover:scale-110">
                        <i class="fa-solid fa-dumbbell text-[120px]"></i>
                    </div>

                    <!-- Glowing Orb Effect -->
                    <div class="absolute top-0 right-0 w-32 h-32 opacity-30 transition-all duration-700 group-hover:opacity-50 group-hover:scale-125" style="background: radial-gradient(circle, #0992C2 0%, transparent 70%); filter: blur(30px);"></div>
                    
                    <!-- Shimmer Effect on Hover -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none" style="background: linear-gradient(90deg, transparent 0%, rgba(9, 146, 194, 0.1) 50%, transparent 100%); animation: shimmer 2s infinite;"></div>

                    <div class="p-5 text-left h-full flex flex-col justify-between relative z-10">
                        <!-- Header Section -->
                        <div class="flex justify-between items-start">
                            <h2 class="text-sm font-black italic transition-all duration-300 group-hover:text-[#0992C2]" style="font-family: 'Poppins'; color: #0992C2; text-shadow: 0 0 20px rgba(9, 146, 194, 0.5);">ARIFAH GYM</h2>
                            <span class="text-[6px] border px-2 py-0.5 rounded-full uppercase font-bold transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg" style="border-color: #0992C2; color: #0992C2; background: rgba(9, 146, 194, 0.15); box-shadow: 0 0 15px rgba(9, 146, 194, 0.3);">{{ session('paket_nama') }}</span>
                        </div>
                        
                        <!-- Member Info Section -->
                        <div class="transform transition-all duration-300 group-hover:translate-x-1">
                            <h3 class="text-lg font-bold uppercase truncate tracking-tight text-white transition-all duration-300 group-hover:text-[#0992C2]">{{ session('member_name') }}</h3>
                            <p class="text-[8px] opacity-50 font-mono tracking-widest transition-all duration-300 group-hover:opacity-70">{{ session('order_id') }}</p>
                        </div>
                        
                        <!-- Footer Section -->
                        <div class="flex justify-between items-end border-t border-white/20 pt-2 transition-all duration-300 group-hover:border-[#0992C2]/30">
                            <div class="transform transition-all duration-300 group-hover:translate-y-[-2px]">
                                <p class="text-[7px] opacity-50 uppercase transition-all duration-300 group-hover:opacity-70">Berlaku Hingga</p>
                                <p class="text-[10px] font-bold opacity-60 text-white uppercase tracking-wider transition-all duration-300 group-hover:text-[#0992C2]">{{ session('expiry_date') }}</p>
                            </div>
                            <div class="bg-white p-1 rounded-md transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:shadow-2xl" style="box-shadow: 0 4px 20px rgba(9, 146, 194, 0.3);">
                                <img id="qrSrc" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ session('order_id') }}" class="w-8 h-8" crossorigin="anonymous">
                            </div>
                        </div>
                    </div>

                    <!-- Subtle Border Glow -->
                    <div class="absolute inset-0 rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none" style="box-shadow: inset 0 0 30px rgba(9, 146, 194, 0.2);"></div>
                </div>

                <style>
                    @keyframes shimmer {
                        0% { transform: translateX(-100%); }
                        100% { transform: translateX(100%); }
                    }
                </style>

                <canvas id="finalCanvas" width="1050" height="630"></canvas>

                <div class="grid grid-cols-2 gap-4 w-full">
                    <button id="btnDownload" onclick="drawAndDownload()" class="group/btn bg-gradient-to-r from-gray-800 to-gray-900 hover:from-[#0992C2] hover:to-[#0992C2] py-5 rounded-[2rem] text-[11px] font-black uppercase tracking-widest transition-all duration-300 border border-white/10 hover:border-[#0992C2] shadow-lg hover:shadow-[0_10px_30px_-10px_rgba(9,146,194,0.5)] transform hover:scale-[1.02] active:scale-[0.98] relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-200%] group-hover/btn:translate-x-[200%] transition-transform duration-700"></div>
                        <span class="relative flex items-center justify-center gap-2">
                            <i class="fa-solid fa-download text-[#0992C2] group-hover/btn:text-black transition-colors duration-300"></i> 
                            <span class="group-hover/btn:text-black transition-colors duration-300">SIMPAN</span>
                        </span>
                    </button>
                    <a href="/absen" class="bg-gradient-to-r from-[#0992C2] to-[#0992C2] hover:from-[#0992C2] hover:to-[#0992C2] py-5 rounded-[2rem] text-[11px] font-black text-black uppercase italic tracking-widest text-center flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-[0_10px_30px_-10px_rgba(9,146,194,0.5)] transform hover:scale-[1.02] active:scale-[0.98]">SELESAI</a>
                </div>
            </div>
        @else
            <div class="premium-card p-12 rounded-[4rem] relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-56 h-56 bg-[#0992C2]/10 rounded-full blur-[80px]"></div>
                
                <div class="relative z-10 text-left">
                    <div class="mb-12">
                        <h2 class="text-2xl font-hero italic text-white tracking-widest">ABSEN</h2>
                        <div class="h-1 w-12 bg-[#0992C2] mt-2"></div>
                        <p class="text-[11px] text-zinc-500 uppercase font-bold tracking-[0.2em] mt-4 italic">Welcome back, athlete.</p>
                    </div>

                    @if(session('error'))
                        <div class="shake mb-8 p-5 bg-red-600/10 border border-red-600/20 rounded-[2rem] flex items-center gap-4">
                            <div class="w-10 h-10 bg-red-600/20 rounded-full flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-600"></i>
                            </div>
                            <p class="text-[11px] font-black text-red-500 uppercase tracking-wider leading-relaxed">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="/absen" method="POST" class="space-y-10">
                        @csrf
                        <div class="relative group">
                            <div class="absolute left-8 top-1/2 -translate-y-1/2 text-zinc-700 group-focus-within:text-[#0992C2] transition-all text-xl">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <input type="tel" name="phone" id="phone" required placeholder="NOMOR WHATSAPP" autocomplete="tel" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                   class="input-premium w-full pl-20 pr-10 py-7 rounded-[2.5rem] outline-none text-2xl font-black text-[#0992C2] placeholder:text-zinc-800 placeholder:text-sm placeholder:tracking-[0.4em]">
                        </div>

                        <button type="submit" class="w-full bg-[#0992C2] hover:bg-[#0992C2] text-black font-black py-7 rounded-[2.5rem] text-sm uppercase italic tracking-[0.3em] shadow-[0_20px_40px_-10px_rgba(9,146,194,0.4)] active:scale-95 transition-all">
                            TAP-IN NOW <i class="fa-solid fa-arrow-right-long ml-3"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        <p class="mt-12 text-zinc-900 text-[10px] uppercase tracking-[0.8em] font-black italic text-center">ARIFAH GYM &copy; 2026</p>
    </div>

    <script>
        WebFont.load({ google: { families: ['Poppins:400,700,900,900i'] } });

        async function drawAndDownload() {
            const btn = document.getElementById('btnDownload');
            const canvas = document.getElementById('finalCanvas');
            const ctx = canvas.getContext('2d');
            btn.innerHTML = "RENDERING...";
            btn.disabled = true;

            await document.fonts.ready;

            // 1. Background
            const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
            grad.addColorStop(0, '#1e293b'); grad.addColorStop(1, '#0f172a');
            ctx.fillStyle = grad;
            ctx.beginPath(); ctx.roundRect(0, 0, 1050, 630, 60); ctx.fill();

            // 2. Flare
            const flare = ctx.createRadialGradient(850, 150, 0, 850, 150, 400);
            flare.addColorStop(0, 'rgba(9, 146, 194, 0.2)'); flare.addColorStop(1, 'transparent');
            ctx.fillStyle = flare;
            ctx.beginPath(); ctx.roundRect(0, 0, 1050, 630, 60); ctx.fill();

            ctx.textBaseline = "top";
            
            // --- NAMA GYM LEBIH KECIL (48PX) ---
            ctx.fillStyle = "#0992C2";
            ctx.font = "italic 900 48px Poppins";
            ctx.fillText("ARIFAH GYM", 60, 65);

            ctx.fillStyle = "rgba(255,255,255,0.6)";
            ctx.font = "700 25px Poppins";
            ctx.fillText("OFFICIAL MEMBER", 60, 135);

            // Paket Label (Ukuran Font 22px agar lebih kecil)
            const paket = "{{ session('paket_nama') }}".toUpperCase();
            ctx.font = "700 22px Poppins";
            const pWidth = ctx.measureText(paket).width;
            ctx.strokeStyle = "#0992C2";
            ctx.lineWidth = 3;
            // Geser koordinat X ke 960 agar tidak mepet kanan
            const rectX = 960 - pWidth - 30;
            ctx.beginPath();
            ctx.roundRect(rectX, 65, pWidth + 30, 55, 27.5);
            ctx.stroke();
            ctx.fillStyle = "#0992C2";
            ctx.fillText(paket, rectX + 15, 80);

            // Nama Member
            ctx.fillStyle = "#ffffff";
            ctx.font = "900 65px Poppins";
            ctx.fillText("{{ session('member_name') }}".toUpperCase(), 60, 275);

            ctx.fillStyle = "rgba(255,255,255,0.5)"; ctx.font = "400 32px monospace";
            ctx.fillText("{{ session('order_id') }}", 60, 360);
            
            ctx.strokeStyle = "rgba(255,255,255,0.1)";
            ctx.beginPath(); ctx.moveTo(60, 460); ctx.lineTo(960, 460); ctx.stroke();
            
            ctx.fillStyle = "rgba(255,255,255,0.5)"; ctx.font = "400 25px Poppins"; ctx.fillText("BERLAKU HINGGA", 60, 490);
            ctx.fillStyle = "rgba(255,255,255,0.5)"; ctx.font = "900 45px Poppins"; ctx.fillText("{{ session('expiry_date') }}".toUpperCase(), 60, 530);

            const img = new Image();
            img.crossOrigin = "anonymous";
            img.src = document.getElementById('qrSrc').src;
            img.onload = function() {
                ctx.fillStyle = "#ffffff";
                ctx.beginPath(); ctx.roundRect(830, 470, 130, 130, 15); ctx.fill();
                ctx.drawImage(img, 845, 485, 100, 100);

                const link = document.createElement('a');
                link.download = 'ArifahGym-{{ session("member_name") }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                btn.innerHTML = '<i class="fa-solid fa-download"></i> SIMPAN';
                btn.disabled = false;
            };
        }
    </script>
</body>
</html>