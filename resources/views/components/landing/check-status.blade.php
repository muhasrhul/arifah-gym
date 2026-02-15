<section id="cek-status" class="py-16 md:py-24 bg-gray-950">
    <div class="max-w-2xl mx-auto px-6" data-aos="zoom-in">
        <div class="glass-card p-6 md:p-12 rounded-2xl md:rounded-[3rem] border-white/10 shadow-3xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#0992C2]/10 rounded-full blur-3xl"></div>

            <div class="text-center mb-8 md:mb-10 relative z-10">
                <h3 class="text-2xl md:text-4xl font-black italic mb-3 md:mb-4 uppercase tracking-tighter text-white leading-none">
                    CHECK <span class="text-[#0992C2]">MEMBERSHIP</span>
                </h3>
                <div class="h-1 md:h-1.5 w-12 md:w-16 bg-[#0992C2] mx-auto rounded-full mb-4 md:mb-6"></div>
                
                <p class="text-gray-500 text-[9px] md:text-xs uppercase tracking-[0.2em] md:tracking-[0.3em] font-bold">
                    Akses Kartu Digital & Pantau Masa Aktif Anda
                </p>
            </div>

            <form action="{{ url()->current() }}#cek-status" method="GET" class="relative mb-6 md:mb-8 z-10">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 md:pl-5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-phone text-gray-500 group-focus-within:text-[#0992C2] transition-colors text-sm md:text-base"></i>
                    </div>
                    <input type="tel" 
                           name="search" 
                           id="phoneInput"
                           value="{{ request('search') }}" 
                           placeholder="Masukan nomor telpon anda" 
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                           class="w-full pl-10 md:pl-12 pr-20 md:pr-32 py-3.5 md:py-5 rounded-xl md:rounded-2xl bg-white/5 border border-white/10 focus:border-[#0992C2] focus:bg-white/10 focus:ring-4 focus:ring-[#0992C2]/20 outline-none transition-all font-bold text-white placeholder:text-gray-600 uppercase tracking-widest text-[11px] md:text-sm">
                    
                    <button type="submit" class="absolute right-1.5 md:right-2 top-1.5 md:top-2 bottom-1.5 md:bottom-2 px-3 md:px-8 bg-[#0992C2] hover:bg-[#0992C2] rounded-lg md:rounded-xl font-black uppercase text-[9px] md:text-xs transition-all shadow-lg active:scale-95">
                        Cari
                    </button>
                </div>
            </form>

            @if(request('search'))
            <div class="mt-8 flex justify-center overflow-x-auto pb-4 relative z-10">
                @if(isset($member) && $member)
                    @php
                        $now = \Carbon\Carbon::now('Asia/Makassar')->startOfDay();
                        $expiredDate = \Carbon\Carbon::parse($member->expiry_date)->startOfDay();
                        $isExpired = $expiredDate->lt($now); 
                    @endphp

                    <div class="flex flex-col items-center gap-6 py-4 w-full">
                        <!-- Modern Member Card dengan Glassmorphism -->
                        <div id="memberCard" class="relative text-white font-sans rounded-3xl overflow-hidden shadow-2xl text-left scale-[0.8] md:scale-100 origin-center transition-all duration-500 hover:scale-[0.85] md:hover:scale-105 hover:shadow-[0_20px_60px_-15px_rgba(9,146,194,0.5)] group" 
                             style="width: 350px; height: 210px; background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%); backdrop-filter: blur(20px);">
                            
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

                            @if($isExpired || !$member->is_active)
                                <div class="absolute inset-0 z-50 flex items-center justify-center pointer-events-none bg-black/50 backdrop-blur-sm">
                                    <div class="stamp-expired animate-pulse" style="border: 0.3rem solid #ef4444; color: #ef4444; font-size: 2rem; font-weight: 900; text-transform: uppercase; padding: 0.6rem 2rem; transform: rotate(-15deg); border-radius: 0.75rem; box-shadow: 0 0 30px rgba(239, 68, 68, 0.6); background: rgba(0, 0, 0, 0.3);">EXPIRED</div>
                                </div>
                            @endif

                            <div class="p-6 h-full flex flex-col justify-between relative z-10">
                                <!-- Header Section -->
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-[14px] font-extrabold italic leading-tight tracking-tighter transition-all duration-300 group-hover:text-[#0992C2]" style="color: #0992C2; font-family: 'Poppins', sans-serif; text-shadow: 0 0 20px rgba(9, 146, 194, 0.5);">ARIFAH GYM</h2>
                                        <p class="text-[8px] uppercase tracking-[0.2em] opacity-70 font-bold transition-all duration-300 group-hover:opacity-100" style="font-family: 'Poppins', sans-serif;">Official Member</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[7px] font-bold uppercase border whitespace-nowrap transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg" style="border-color: #0992C2; color: #0992C2; background: rgba(9, 146, 194, 0.15); font-family: 'Poppins', sans-serif; box-shadow: 0 0 15px rgba(9, 146, 194, 0.3);">
                                        {{ $member->type }}
                                    </span>
                                </div>

                                <!-- Member Info Section -->
                                <div class="mt-2 transform transition-all duration-300 group-hover:translate-x-1">
                                    <h3 class="text-xl font-bold uppercase truncate transition-all duration-300 group-hover:text-[#0992C2]" style="font-family: 'Poppins', sans-serif; letter-spacing: 0.05em;">{{ $member->name }}</h3>
                                    <p class="text-[10px] opacity-50 font-mono tracking-widest transition-all duration-300 group-hover:opacity-70">{{ $member->order_id }}</p>
                                </div>

                                <!-- Footer Section -->
                                <div class="flex justify-between items-end border-t border-white/20 pt-3 transition-all duration-300 group-hover:border-[#0992C2]/30">
                                    <div class="transform transition-all duration-300 group-hover:translate-y-[-2px]">
                                        <p class="text-[9px] uppercase opacity-50 font-medium mb-1 transition-all duration-300 group-hover:opacity-70" style="font-family: 'Poppins', sans-serif; letter-spacing: 0.1em;">Berlaku Hingga</p>
                                        <p class="text-sm font-bold {{ $isExpired ? 'text-red-400' : 'text-white' }} transition-all duration-300 group-hover:text-[#0992C2]" style="font-family: 'Poppins', sans-serif;">
                                            {{ \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="bg-white p-1.5 rounded-lg shadow-xl transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 group-hover:shadow-2xl" style="box-shadow: 0 4px 20px rgba(9, 146, 194, 0.3);">
                                        <img id="qrSource" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $member->order_id }}" crossorigin="anonymous" style="width: 40px; height: 40px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Subtle Border Glow -->
                            <div class="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none" style="box-shadow: inset 0 0 30px rgba(9, 146, 194, 0.2);"></div>
                        </div>

                        <!-- Action Buttons dengan Modern Design -->
                        <div class="flex flex-col w-full gap-3 px-4 max-w-[350px]">
                            <button onclick="generateAndDownload()" id="dlBtn" class="group/btn w-full py-4 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-[#0992C2] hover:to-[#0992C2] text-white font-bold rounded-2xl transition-all duration-300 border border-white/10 hover:border-[#0992C2] uppercase text-[10px] tracking-widest shadow-lg hover:shadow-[0_10px_30px_-10px_rgba(9,146,194,0.5)] transform hover:scale-[1.02] active:scale-[0.98] relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent translate-x-[-200%] group-hover/btn:translate-x-[200%] transition-transform duration-700"></div>
                                <span class="relative flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-download transition-transform duration-300 group-hover/btn:scale-110"></i> 
                                    <span>Simpan Kartu ke HP</span>
                                </span>
                            </button>

                            @if($isExpired || !$member->is_active)
                                <div class="w-full px-6 py-4 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/30 text-amber-400 font-bold rounded-2xl text-center shadow-lg backdrop-blur-sm transform transition-all duration-300 hover:scale-[1.02]">
                                    <i class="fa-solid fa-info-circle animate-pulse"></i>
                                    <p class="text-xs uppercase tracking-wide mt-2 leading-relaxed">
                                        Silakan menuju kasir untuk<br>perpanjangan dan aktivasi ulang
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <canvas id="hiddenCanvas" width="1050" height="630" style="display:none;"></canvas>
                    </div>

                    <style>
                        @keyframes shimmer {
                            0% { transform: translateX(-100%); }
                            100% { transform: translateX(100%); }
                        }
                    </style>

                    <script>
                    async function generateAndDownload() {
                        const btn = document.getElementById('dlBtn');
                        const canvas = document.getElementById('hiddenCanvas');
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
                        const typeText = "{{ $member->type ?? 'MEMBER' }}".toUpperCase();
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
                        ctx.fillText("{{ $member->name ?? 'NAME' }}".toUpperCase(), 60, 270);
                        ctx.fillStyle = "rgba(255,255,255,0.5)";
                        ctx.font = "400 32px monospace";
                        ctx.fillText("{{ $member->order_id ?? '' }}", 60, 360);

                        // 7. Garis Pemisah & Footer
                        ctx.strokeStyle = "rgba(255,255,255,0.1)";
                        ctx.beginPath(); ctx.moveTo(60, 460); ctx.lineTo(960, 460); ctx.stroke();
                        ctx.fillStyle = "rgba(255,255,255,0.5)";
                        ctx.font = "400 25px Poppins";
                        ctx.fillText("BERLAKU HINGGA", 60, 490);
                        ctx.fillStyle = "rgba(255,255,255,0.6)";
                        ctx.font = "700 50px Poppins"; 
                        
                        const expDate = "{{ $member ? \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') : '' }}";
                        ctx.fillText(expDate.toUpperCase(), 60, 530);

                        // LOGIKA STEMPEL EXPIRED PADA CANVAS
                        if ("{{ ($member && ($isExpired || !$member->is_active)) ? '1' : '0' }}" == "1") {
                            ctx.save();
                            ctx.translate(525, 315);
                            ctx.rotate(-15 * Math.PI / 180);
                            ctx.strokeStyle = "#ef4444";
                            ctx.lineWidth = 10;
                            ctx.beginPath();
                            ctx.roundRect(-200, -60, 400, 120, 15);
                            ctx.stroke();
                            ctx.fillStyle = "rgba(239, 68, 68, 0.15)";
                            ctx.fill();
                            ctx.fillStyle = "#ef4444";
                            ctx.font = "900 80px Poppins";
                            ctx.textAlign = "center";
                            ctx.textBaseline = "middle";
                            ctx.fillText("EXPIRED", 0, 0);
                            ctx.restore();
                        }

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
                            link.download = 'Member-{{ $member->name ?? "Gym" }}.png';
                            link.href = canvas.toDataURL('image/png', 1.0);
                            link.click();
                            btn.innerHTML = '<i class="fa-solid fa-download"></i> Simpan Kartu ke HP';
                        };
                    }
                    </script>
                @else
                    <div class="text-center py-10 w-full border border-dashed border-white/10 rounded-3xl bg-white/5">
                        <i class="fa-solid fa-user-slash text-gray-600 text-3xl mb-4"></i>
                        <p class="text-gray-400 font-bold uppercase text-xs tracking-widest px-4">Member tidak ditemukan atau belum aktif.</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>