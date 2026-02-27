<nav class="fixed w-full z-[100] bg-black/60 backdrop-blur-lg border-b border-white/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl font-black text-[#0992C2] italic uppercase tracking-tighter orange-glow mr-12">
                ARIFAH <span class="navbar-text">GYM</span>
            </h1>

            <div class="hidden md:flex items-center space-x-6 font-bold text-[11px] uppercase tracking-[0.2em]">
                <a href="#home" class="navbar-link hover:text-[#0992C2] transition-colors">Beranda</a>
                <a href="#tentang" class="navbar-link hover:text-[#0992C2] transition-colors">Tentang</a>
                <a href="#harga" class="navbar-link hover:text-[#0992C2] transition-colors">Membership</a>
                <a href="#fasilitas" class="navbar-link hover:text-[#0992C2] transition-colors">Fasilitas</a>
                <a href="#testimoni" class="navbar-link hover:text-[#0992C2] transition-colors">Testimoni</a>
                <a href="#lokasi" class="navbar-link hover:text-[#0992C2] transition-colors">Lokasi</a>
                <a href="#cek-status" class="navbar-link hover:text-[#0992C2] transition-colors">Cek Member</a>
                <a href="/daftar" class="bg-[#0992C2] px-6 py-2.5 rounded-full btn-hover shadow-lg shadow-[#0992C2]/40 text-white text-[10px]">Daftar Sekarang</a>
                
                <!-- Desktop Theme Toggle Button -->
                <div id="theme-toggle" class="navbar-theme-toggle" title="Toggle Light/Dark Mode">
                    <i class="fas fa-sun theme-icon sun-icon"></i>
                    <i class="fas fa-moon theme-icon moon-icon"></i>
                </div>
            </div>

            <button id="menu-btn" class="md:hidden navbar-text focus:outline-none p-2 relative w-10 h-10 z-[101]">
                <i class="fa-solid fa-bars-staggered text-2xl absolute inset-0 flex items-center justify-center transition-all duration-300" id="bars-icon"></i>
                <i class="fa-solid fa-xmark text-2xl absolute inset-0 flex items-center justify-center transition-all duration-300 opacity-0 scale-50" id="x-icon"></i>
            </button>
        </div>

        <div id="mobile-menu" class="md:hidden absolute top-full left-0 w-full glass-card bg-black/98 border-b border-white/10 shadow-2xl dark transition-all duration-300">
            <div class="flex flex-col p-8 space-y-6 font-bold text-center uppercase tracking-[0.2em] text-xs">
                <!-- Mobile Theme Toggle di posisi paling atas -->
                <div class="flex justify-center mb-4">
                    <div id="mobile-theme-toggle" class="navbar-theme-toggle mobile-theme-toggle" title="Toggle Light/Dark Mode">
                        <i class="fas fa-sun theme-icon sun-icon"></i>
                        <i class="fas fa-moon theme-icon moon-icon"></i>
                    </div>
                </div>
                
                <a href="#home" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Beranda</a>
                <a href="#tentang" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Tentang</a>
                <a href="#harga" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Membership</a>
                <a href="#fasilitas" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Fasilitas</a>
                <a href="#testimoni" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Testimoni</a>
                <a href="#lokasi" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Lokasi</a>
                <a href="#cek-status" class="mobile-link py-3 hover:text-[#0992C2] transition-all border-b mobile-border">Cek Member</a>
                <a href="/daftar" class="bg-[#0992C2] py-4 rounded-2xl text-white font-black italic shadow-xl shadow-[#0992C2]/20 mt-4 block">Daftar Sekarang</a>
            </div>
        </div>
    </nav>