<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ARIFAH Gym - Absensi Member</title>
    
    @vite(['resources/css/app.css'])
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
            <script>
                document.getElementById('successSound').play();
            </script>

            <div class="card-pop flex flex-col items-center premium-card p-8 rounded-[3.5rem] text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4 shadow-xl">
                    <i class="fa-solid fa-check text-2xl text-black"></i>
                </div>
                
                <h1 class="text-3xl font-hero text-green-500 mb-1 italic">ABSEN BERHASIL</h1>
                <p class="text-zinc-500 text-[10px] uppercase tracking-widest mb-8 font-bold">{{ now()->format('H:i') }} WITA</p>

                <div class="grid grid-cols-2 gap-4 w-full mb-8 text-center">
                    <div class="bg-white/[0.03] p-5 rounded-3xl border border-white/5">
                        <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">SESI BULAN INI</p>
                        <h3 class="text-3xl font-black italic">{{ session('total_latihan') }}<span class="text-sm text-green-500 ml-1">X</span></h3>
                    </div>
                    <div class="bg-white/[0.03] p-5 rounded-3xl border border-white/5 flex flex-col justify-center items-center">
                        <i class="fa-solid {{ session('badge') == 'ARIFAH WARRIOR' ? 'fa-fire text-[#0992C2]' : 'fa-medal text-blue-400' }} text-2xl mb-1"></i>
                        <p class="text-[10px] font-black uppercase tracking-tighter">{{ session('badge') }}</p>
                    </div>
                </div>

                <!-- Digital Member Card - Design dari check-status.blade.php -->
                <div class="flex flex-col items-center gap-6 py-4 w-full px-2">
                    <!-- Modern Member Card dengan Glassmorphism -->
                    <div id="memberCard" class="relative text-white font-sans rounded-2xl md:rounded-3xl overflow-hidden shadow-2xl text-left w-full max-w-[320px] md:max-w-[350px] transition-all duration-500 hover:shadow-[0_20px_60px_-15px_rgba(9,146,194,0.5)] group" 
                         style="aspect-ratio: 5/3; background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%); backdrop-filter: blur(20px);">
                        
                        <!-- Animated Background Pattern -->
                        <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
                            <div class="absolute top-0 left-0 w-full h-full" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 20px);"></div>
                        </div>

                        <!-- Dumbbell Watermark dengan Animasi -->
                        <div class="absolute -bottom-6 -left-6 opacity-[0.06] transform -rotate-12 pointer-events-none transition-all duration-700 group-hover:opacity-[0.12] group-hover:scale-110">
                            <i class="fa-solid fa-dumbbell text-[180px]"></i>
                        </div>

                        <!-- Glowing Orb Effect -->
                        <div class="absolute top-0 right-0 w-40 h-40 opacity-30 transition-all duration-700 group-hover:opacity-50 group-hover:scale-125" style="background: radial-gradient(circle, #0992C2 0%, transparent 70%); filter: blur(30px);"></div>
                        
                        <!-- Shimmer Effect on Hover -->
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none" style="background: linear-gradient(90deg, transparent 0%, rgba(9, 146, 194, 0.1) 50%, transparent 100%); animation: shimmer 2s infinite;"></div>

                        <div class="p-4 md:p-6 h-full flex flex-col justify-between relative z-10">
                            <!-- Header Section -->
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-xs md:text-[14px] font-extrabold italic leading-tight tracking-tighter transition-all duration-300 group-hover:text-[#0992C2]" style="color: #0992C2; font-family: 'Poppins', sans-serif; text-shadow: 0 0 20px rgba(9, 146, 194, 0.5);">ARIFAH GYM</h2>
                                    <p class="text-[7px] md:text-[8px] uppercase tracking-[0.15em] md:tracking-[0.2em] opacity-70 font-bold transition-all duration-300 group-hover:opacity-100" style="font-family: 'Poppins', sans-serif;">Official Member</p>
                                </div>
                                <span class="px-2 py-0.5 md:px-3 md:py-1 rounded-full text-[6px] md:text-[7px] font-bold uppercase border whitespace-nowrap transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg" style="border-color: #0992C2; color: #0992C2; background: rgba(9, 146, 194, 0.15); font-family: 'Poppins', sans-serif; box-shadow: 0 0 15px rgba(9, 146, 194, 0.3);">
                                    {{ session('paket_nama') }}
                                </span>
                            </div>

                            <!-- Member Info Section -->
                            <div class="mt-2 transform transition-all duration-300 group-hover:translate-x-1">
                                <h3 class="text-base md:text-xl font-bold uppercase truncate transition-all duration-300 group-hover:text-[#0992C2]" style="font-family: 'Poppins', sans-serif; letter-spacing: 0.05em;">{{ session('member_name') }}</h3>
                                <p class="text-[8px] md:text-[10px] opacity-50 font-mono tracking-widest transition-all duration-300 group-hover:opacity-70">ID: {{ session('member_id') }}</p>
                            </div>

                            <!-- Footer Section -->
                            <div class="flex justify-between items-end border-t border-white/20 pt-2 md:pt-3 transition-all duration-300 group-hover:border-[#0992C2]/30">
                                <div class="transform transition-all duration-300 group-hover:translate-y-[-2px]">
                                    <p class="text-[8px] md:text-[9px] uppercase opacity-50 font-medium mb-1 transition-all duration-300 group-hover:opacity-70" style="font-family: 'Poppins', sans-serif; letter-spacing: 0.1em;">Berlaku Hingga</p>
                                    <p class="text-xs md:text-sm font-bold text-white transition-all duration-300 group-hover:text-[#0992C2]" style="font-family: 'Poppins', sans-serif;">
                                        {{ session('expiry_date') }}
                                    </p>
                                </div>
                                <div class="bg-white p-1 md:p-1.5 rounded-lg shadow-xl transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:shadow-2xl" style="box-shadow: 0 4px 20px rgba(9, 146, 194, 0.3);">
                                    <img id="qrSource" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ session('member_id') }}" crossorigin="anonymous" style="width: 35px; height: 35px;" class="md:w-[40px] md:h-[40px]">
                                </div>
                            </div>
                        </div>

                        <!-- Subtle Border Glow -->
                        <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none" style="box-shadow: inset 0 0 30px rgba(9, 146, 194, 0.2);"></div>
                    </div>
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
                        <script>
                            // Auto-redirect ke halaman absen setelah 5 detik untuk error
                            setTimeout(function() {
                                window.location.href = '/absen';
                            }, 5000); // 5000ms = 5 detik
                        </script>
                        <div class="shake mb-8 p-5 bg-red-600/10 border border-red-600/20 rounded-[2rem] flex items-center gap-4">
                            <div class="w-10 h-10 bg-red-600/20 rounded-full flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-xmark text-red-600"></i>
                            </div>
                            <p class="text-[11px] font-black text-red-500 uppercase tracking-wider leading-relaxed">{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="/absen" method="POST" class="space-y-10" id="absenForm">
                        @csrf
                        <div class="relative group">
                            <div class="absolute left-8 top-1/2 -translate-y-1/2 text-zinc-700 group-focus-within:text-[#0992C2] transition-all text-xl">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <input type="tel" name="phone" id="phone" required placeholder="NOMOR WHATSAPP" autocomplete="tel" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                   class="input-premium w-full pl-20 pr-10 py-7 rounded-[2.5rem] outline-none text-2xl font-black text-[#0992C2] placeholder:text-zinc-800 placeholder:text-sm placeholder:tracking-[0.4em]">
                        </div>

                        <button type="submit" id="submitBtn" class="w-full bg-[#0992C2] hover:bg-[#0992C2] text-black font-black py-7 rounded-[2.5rem] text-sm uppercase italic tracking-[0.3em] shadow-[0_20px_40px_-10px_rgba(9,146,194,0.4)] active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btnText">TAP-IN NOW <i class="fa-solid fa-arrow-right-long ml-3"></i></span>
                            <span id="btnLoading" class="hidden">
                                <i class="fa-solid fa-spinner fa-spin"></i> PROCESSING...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        <p class="mt-12 text-zinc-900 text-[10px] uppercase tracking-[0.8em] font-black italic text-center">ARIFAH GYM &copy; 2026</p>
    </div>

    <script>
        WebFont.load({ google: { families: ['Poppins:400,700,900,900i'] } });

        // Auto-refresh halaman setiap 90 menit untuk mencegah CSRF token expired
        // Session Laravel default: 120 menit, refresh sebelum expired
        setTimeout(function() {
            console.log('Auto-refreshing page to prevent CSRF token expiration...');
            window.location.reload();
        }, 90 * 60 * 1000); // 90 menit = 5,400,000 ms

        // Prevent double submit dan tampilkan loading state
        const absenForm = document.getElementById('absenForm');
        if (absenForm) {
            let formSubmitted = false;
            
            absenForm.addEventListener('submit', function(e) {
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                
                formSubmitted = true;
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnLoading = document.getElementById('btnLoading');
                
                // Disable button dan tampilkan loading
                submitBtn.disabled = true;
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
                
                // Set timeout untuk re-enable button jika terlalu lama (10 detik)
                // Jika lebih dari 10 detik, kemungkinan ada masalah
                setTimeout(function() {
                    if (submitBtn.disabled && formSubmitted) {
                        // Reload halaman jika stuck
                        console.warn('Form submission timeout, reloading page...');
                        window.location.reload();
                    }
                }, 10000);
            });
        }

        async function drawAndDownload() {
            const btn = document.getElementById('btnDownload');
            const canvas = document.getElementById('finalCanvas');
            const ctx = canvas.getContext('2d');
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            
            await document.fonts.load('900 italic 48px Poppins');
            await document.fonts.load('700 25px Poppins');
            await document.fonts.load('700 50px Poppins');
            await document.fonts.ready;

            // 1. Background
            const grad = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
            grad.addColorStop(0, '#1e293b');
            grad.addColorStop(1, '#0f172a');
            ctx.fillStyle = grad;
            ctx.beginPath(); ctx.roundRect(0, 0, 1050, 630, 60); ctx.fill();

            // 2. Dumbbell Watermark
            ctx.save();
            ctx.translate(100, 600);
            ctx.rotate(-15 * Math.PI / 180);
            ctx.fillStyle = "rgba(255, 255, 255, 0.04)";
            ctx.font = "900 450px 'Font Awesome 6 Free'";
            ctx.fillText("\uf44b", 0, 0); 
            ctx.restore();

            // 3. Flare Kuning
            const flare = ctx.createRadialGradient(850, 150, 0, 850, 150, 400);
            flare.addColorStop(0, 'rgba(9, 146, 194, 0.15)');
            flare.addColorStop(1, 'transparent');
            ctx.fillStyle = flare;
            ctx.beginPath(); ctx.roundRect(0, 0, 1050, 630, 60); ctx.fill();

            ctx.textBaseline = "top";

            // 4. Nama Gym
            ctx.fillStyle = "#0992C2";
            ctx.font = "italic 900 48px Poppins";
            ctx.fillText("ARIFAH GYM", 60, 65);
            
            ctx.fillStyle = "rgba(255,255,255,0.6)";
            ctx.font = "700 25px Poppins";
            ctx.fillText("OFFICIAL MEMBER", 60, 135);

            // 5. Type Member (Pill Box)
            const typeText = "{{ session('paket_nama') ?? 'MEMBER' }}".toUpperCase();
            ctx.font = "700 22px Poppins";
            const pWidth = ctx.measureText(typeText).width;
            const rectX = 960 - pWidth - 30;
            ctx.strokeStyle = "#0992C2";
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.roundRect(rectX, 65, pWidth + 30, 55, 27.5);
            ctx.stroke();
            ctx.fillStyle = "#0992C2";
            ctx.textBaseline = "middle";
            ctx.fillText(typeText, rectX + 15, 65 + (55/2));
            ctx.textBaseline = "top";

            // 6. Nama & ID
            ctx.fillStyle = "#ffffff";
            ctx.font = "900 70px Poppins";
            ctx.fillText("{{ session('member_name') ?? 'NAME' }}".toUpperCase(), 60, 270);
            ctx.fillStyle = "rgba(255,255,255,0.5)";
            ctx.font = "400 32px monospace";
            ctx.fillText("ID: {{ session('member_id') ?? '' }}", 60, 360);

            // 7. Garis Pemisah & Footer
            ctx.strokeStyle = "rgba(255,255,255,0.1)";
            ctx.beginPath(); ctx.moveTo(60, 460); ctx.lineTo(960, 460); ctx.stroke();
            ctx.fillStyle = "rgba(255,255,255,0.5)";
            ctx.font = "400 25px Poppins";
            ctx.fillText("BERLAKU HINGGA", 60, 490);
            ctx.fillStyle = "rgba(255,255,255,0.6)";
            ctx.font = "700 50px Poppins"; 
            
            const expDate = "{{ session('expiry_date') ?? '' }}";
            ctx.fillText(expDate.toUpperCase(), 60, 530);

            // 8. QR Code
            const qrImg = new Image();
            qrImg.crossOrigin = "anonymous";
            qrImg.src = document.getElementById('qrSource').src;
            qrImg.onload = function() {
                const qrBoxSize = 115;
                const qrSize = 95;
                const qrX = 960 - qrBoxSize;
                const qrY = 485;
                ctx.fillStyle = "#ffffff";
                ctx.beginPath(); 
                ctx.roundRect(qrX, qrY, qrBoxSize, qrBoxSize, 15); 
                ctx.fill();
                ctx.drawImage(qrImg, qrX + (qrBoxSize - qrSize) / 2, qrY + (qrBoxSize - qrSize) / 2, qrSize, qrSize);

                const link = document.createElement('a');
                link.download = 'Member-{{ session("member_name") ?? "Gym" }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                btn.innerHTML = '<span class="relative flex items-center justify-center gap-2"><i class="fa-solid fa-download text-[#0992C2] group-hover/btn:text-black transition-colors duration-300"></i><span class="group-hover/btn:text-black transition-colors duration-300">SIMPAN</span></span>';
            };
        }
    </script>
</body>
</html>