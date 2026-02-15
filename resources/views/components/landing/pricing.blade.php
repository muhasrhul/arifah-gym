<section id="harga" class="py-24 bg-gray-950 relative overflow-hidden">
    <style>
        @keyframes float-smooth {
            0%, 100% { transform: translateY(0) rotate(-6deg); }
            50% { transform: translateY(-8px) rotate(-3deg); }
        }
        .animate-float-icon {
            animation: float-smooth 4s infinite ease-in-out;
        }
    </style>

    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-[#0992C2]/10 rounded-full blur-[120px]"></div>

    <div class="max-w-6xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16" data-aos="fade-up">
            <h3 class="text-3xl md:text-5xl font-black mb-3 md:mb-4 italic uppercase tracking-tighter">
                <span class="text-[#0992C2] text-glow">Membership</span> Plans
            </h3>
            <p class="text-gray-500 uppercase tracking-[0.15em] md:tracking-[0.2em] text-[10px] md:text-sm font-bold mb-10 md:mb-12">Investasi terbaik untuk tubuh impianmu</p>

            <div class="max-w-3xl mx-auto mb-16">
                <div class="relative p-[1px] rounded-2xl md:rounded-[2rem] bg-gradient-to-r from-[#0992C2]/50 via-transparent to-[#0992C2]/50">
                    <div class="bg-gray-950/90 backdrop-blur-xl rounded-2xl md:rounded-[2rem] p-4 md:p-8 flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6">
                        
                        <div class="flex items-center gap-4 md:gap-6">
                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-[#0992C2] flex items-center justify-center shadow-[0_0_30px_rgba(9,146,194,0.4)] animate-float-icon">
                                <i class="fa-solid fa-address-card text-black text-xl md:text-2xl"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="text-white text-base md:text-xl font-black uppercase italic tracking-tighter leading-none mb-1 md:mb-2">Registration Fee</h4>
                                <p class="text-gray-500 text-[9px] md:text-xs uppercase tracking-[0.15em] md:tracking-[0.2em] font-bold">Biaya keanggotaan awal & starter kit</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-center md:items-end">
                            <div class="text-2xl md:text-4xl font-black text-[#0992C2] italic tracking-tighter">
                                @php
                                    // Ambil registration fee dari paket pertama yang aktif
                                    $regFee = \App\Models\Paket::where('is_active', true)->orderBy('harga', 'asc')->first()->registration_fee ?? 100000;
                                @endphp
                                Rp {{ number_format($regFee, 0, ',', '.') }}
                            </div>
                            <div class="px-2 py-0.5 md:px-3 md:py-1 bg-white/5 rounded-full mt-1 md:mt-2">
                                <span class="text-[8px] md:text-[9px] text-gray-400 uppercase font-black tracking-widest leading-none italic">One-Time Payment Only</span>
                            </div>
                        </div>

                    </div>
                </div>
                <p class="mt-3 md:mt-4 text-gray-600 text-[9px] md:text-[10px] uppercase font-bold tracking-[0.2em] md:tracking-[0.3em]">
                    <i class="fa-solid fa-circle-check text-[#0992C2] mr-2"></i> Berlaku selamanya selama membership aktif
                </p>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
            @foreach($pakets as $paket)
            @php
                $badgeText = $paket->label_promo; 
                $isHighlight = !empty($badgeText);
            @endphp

            <div class="glass-card p-6 md:p-12 rounded-2xl md:rounded-[2.5rem] relative hover:-translate-y-2 transition-all duration-500 flex flex-col h-full group {{ $isHighlight ? 'border-[#0992C2]/50 shadow-2xl shadow-[#0992C2]/20' : 'border-white/5' }}" data-aos="fade-up">
                
                @if($badgeText)
                    <div class="absolute -top-3 md:-top-4 left-1/2 -translate-x-1/2 bg-[#0992C2] text-white text-[9px] md:text-[10px] font-black px-4 md:px-6 py-1.5 md:py-2 rounded-full uppercase tracking-tighter shadow-lg shadow-[#0992C2]/50 promo-glow z-20 whitespace-nowrap">
                        <i class="fa-solid fa-fire mr-1"></i> {{ $badgeText }}
                    </div>
                @endif

                <h4 class="text-[10px] md:text-sm font-bold mb-3 md:mb-4 uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-500 italic">
                    {{ $paket->nama_paket }}
                </h4>
                
                <p class="text-4xl md:text-6xl font-black text-white mb-6 md:mb-8 italic tracking-tighter">
                    {{ number_format($paket->harga / 1000, 0) }}K<span class="text-base md:text-xl text-gray-600">/{{ $paket->durasi_hari }} Hari</span>
                </p>
                
                <ul class="text-gray-400 font-medium space-y-3 md:space-y-4 mb-8 md:mb-12 text-xs md:text-sm leading-relaxed">
                    @if($paket->fasilitas)
                        @foreach(explode(',', $paket->fasilitas) as $fasil)
                            <li><i class="fa-solid fa-check {{ $isHighlight ? 'text-[#0992C2]' : 'text-gray-600' }} mr-2 text-xs"></i> {{ trim($fasil) }}</li>
                        @endforeach
                    @else
                        <li><i class="fa-solid fa-check mr-2"></i> Akses Semua Alat</li>
                    @endif
                </ul>
                
                <a href="/daftar?paket_id={{ $paket->id }}" class="block w-full bg-[#0992C2] text-black hover:bg-[#0992C2] py-3.5 md:py-5 rounded-xl md:rounded-2xl font-black transition-all uppercase tracking-widest text-center shadow-xl mt-auto text-[11px] md:text-xs group-hover:scale-105">
                    Daftar Sekarang
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>