<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi ARIFAH Gym - Forge Your Legend</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #000; 
            transition: all 0.3s ease;
        }
        
        /* Light Mode Styles */
        body.light {
            background-color: #ffffff;
            color: #0f172a;
        }
        
        .font-hero { 
            font-weight: 900; 
            font-style: italic; 
            letter-spacing: -0.05em; 
            line-height: 0.9;
            text-transform: uppercase;
        }

        .bg-gym {
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.85), rgba(0,0,0,0.95)), url('https://images.unsplash.com/photo-1593079831268-3381b0db4a77?q=80&w=2000');
            background-size: cover; background-position: center;
            background-attachment: fixed;
            transition: all 0.3s ease;
        }
        
        /* Light mode background */
        body.light .bg-gym {
            background-image: linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(248,250,252,0.98)), url('https://images.unsplash.com/photo-1593079831268-3381b0db4a77?q=80&w=2000');
        }
        
        .glass-card {
            background: rgba(24, 24, 27, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        /* Light mode glass card */
        body.light .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .orange-glow {
            text-shadow: 0 0 20px rgba(9, 146, 194, 0.6);
        }
        
        /* Form elements */
        input, select {
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transition: all 0.3s ease;
        }
        
        /* Light mode form elements */
        body.light input, body.light select {
            background: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            color: #0f172a !important;
        }
        
        body.light input::placeholder {
            color: #64748b !important;
        }
        
        .is-invalid {
            border-color: #ef4444 !important;
            background: rgba(127, 29, 29, 0.3) !important;
        }
        
        body.light .is-invalid {
            background: rgba(254, 226, 226, 0.8) !important;
        }
        
        input:focus, select:focus {
            border-color: #0992C2 !important;
            box-shadow: 0 0 15px rgba(9, 146, 194, 0.2) !important;
            background: rgba(0, 0, 0, 0.7) !important;
        }
        
        body.light input:focus, body.light select:focus {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 0 15px rgba(9, 146, 194, 0.3) !important;
        }
        
        /* Text colors */
        .text-zinc-500 {
            color: rgb(113 113 122);
            transition: color 0.3s ease;
        }
        
        body.light .text-zinc-500 {
            color: rgb(71 85 105);
        }
        
        .text-zinc-400 {
            color: rgb(161 161 170);
            transition: color 0.3s ease;
        }
        
        body.light .text-zinc-400 {
            color: rgb(100 116 139);
        }
        
        .text-zinc-300 {
            color: rgb(212 212 216);
            transition: color 0.3s ease;
        }
        
        body.light .text-zinc-300 {
            color: rgb(51 65 85);
        }
        
        .text-white {
            color: white;
            transition: color 0.3s ease;
        }
        
        body.light .text-white {
            color: #0f172a;
        }
        
        /* Modal styles */
        #rulesModal {
            background: rgba(0, 0, 0, 0.95);
            transition: all 0.3s ease;
        }
        
        body.light #rulesModal {
            background: rgba(255, 255, 255, 0.95);
        }
        
        .bg-zinc-900 {
            background-color: rgb(24 24 27);
            transition: background-color 0.3s ease;
        }
        
        body.light .bg-zinc-900 {
            background-color: rgb(255 255 255);
        }
        
        /* Back button */
        .back-btn {
            color: rgb(113 113 122);
            transition: color 0.3s ease;
        }
        
        .back-btn:hover {
            color: #0992C2;
        }
        
        body.light .back-btn {
            color: rgb(71 85 105);
        }
        
        body.light .back-btn:hover {
            color: #0992C2;
        }
        
        .success-anim { animation: successPop 0.6s cubic-bezier(0.17, 0.89, 0.32, 1.49); }
        @keyframes successPop { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        #rulesModal { display: none; }
        #rulesModal.active { display: flex; }

        /* Custom Scrollbar untuk Modal */
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .custom-scroll::-webkit-scrollbar-thumb { background: #0992C2; border-radius: 10px; }
        
        body.light .custom-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-gym min-h-screen flex flex-col items-center justify-center p-4 md:p-8 text-white">

    <div class="fixed top-6 left-6 md:top-8 md:left-8 z-50">
        <a href="/" class="flex items-center gap-2 back-btn hover:text-[#0992C2] transition-all font-black uppercase text-[9px] md:text-[10px] tracking-[0.2em] md:tracking-[0.3em]">
            <i class="fa-solid fa-arrow-left"></i> Beranda
        </a>
    </div>

    <!-- Theme Toggle Button - Hidden (theme follows welcome page) -->
    <div class="fixed top-6 right-6 md:top-8 md:right-8 z-50 hidden">
        <button id="theme-toggle" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all" title="Toggle Light/Dark Mode">
            <i class="fas fa-sun" id="theme-icon"></i>
        </button>
    </div>

    <div class="w-full max-w-xl glass-card rounded-[2rem] md:rounded-[3.5rem] p-6 md:p-14 shadow-2xl my-10 relative overflow-hidden">
        
        @if(session('success'))
            <script>
                confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 }, colors: ['#0992C2', '#ffffff', '#000000'] });
            </script>
            <div class="text-center success-anim py-6">
                <div class="w-20 h-20 bg-[#0992C2] rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <i class="fa-solid fa-check text-3xl text-black"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-hero text-white orange-glow italic mb-4">SUKSES!</h1>
                <p class="text-zinc-400 text-[10px] md:text-xs uppercase tracking-[0.3em] mb-8 font-bold leading-relaxed">Selamat Bergabung di Keluarga Besar <br> <span class="text-[#0992C2] font-black">ARIFAH GYM Makassar</span></p>
                <a href="/" class="inline-block bg-white/5 border border-white/10 px-8 py-3 rounded-full text-white text-[10px] uppercase font-black tracking-widest hover:bg-white/10 transition">Kembali</a>
            </div>
        @else
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-4xl md:text-6xl font-hero text-[#0992C2] orange-glow italic mb-2">REGISTRASI</h2>
                <div class="h-1 w-16 bg-white mx-auto rounded-full mb-4 opacity-20"></div>
                <p class="text-zinc-500 text-[9px] md:text-[10px] uppercase tracking-[0.4em] font-black italic">Build Excellence. Forge Your Legend.</p>
            </div>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-[9px] font-black uppercase tracking-widest italic text-center">
                    ⚠️ Data tidak valid. Periksa kolom merah.
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-[10px] font-bold leading-relaxed text-center">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded-xl mb-6 text-[10px] font-bold leading-relaxed text-center">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="/daftar" method="POST" id="formDaftar" class="space-y-6 md:space-y-8">
                @csrf
                
                <div class="space-y-2">
                    <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500 ml-1 italic">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            placeholder="Sesuai KTP" 
                            class="w-full p-4 md:p-5 rounded-xl md:rounded-2xl outline-none font-bold text-sm @error('name') is-invalid @enderror">
                    @error('name') <p class="text-red-500 text-[8px] mt-1 ml-1 font-black uppercase italic tracking-widest">⚠️ {{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500 ml-1 italic">NIK KTP <span class="text-zinc-600">(Opsional)</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                            placeholder="16 Digit NIK KTP (Opsional)" 
                            maxlength="16"
                            class="w-full p-4 md:p-5 rounded-xl md:rounded-2xl outline-none font-bold text-sm @error('nik') is-invalid @enderror">
                    @error('nik') <p class="text-red-500 text-[8px] mt-1 ml-1 font-black uppercase italic tracking-widest">⚠️ {{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                    <div class="space-y-2">
                        <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500 ml-1 italic">WhatsApp</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                               placeholder="0812..." 
                               class="w-full p-4 md:p-5 rounded-xl md:rounded-2xl outline-none font-bold text-sm @error('phone') is-invalid @enderror">
                        @error('phone') <p class="text-red-500 text-[8px] mt-1 ml-1 font-black uppercase italic tracking-widest">⚠️ Terdaftar</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500 ml-1 italic">Email Aktif</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               placeholder="gym@email.com" 
                               class="w-full p-4 md:p-5 rounded-xl md:rounded-2xl outline-none font-bold text-sm @error('email') is-invalid @enderror">
                        @error('email') <p class="text-red-500 text-[8px] mt-1 ml-1 font-black uppercase italic tracking-widest">⚠️ Terpakai</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-zinc-500 ml-1 italic">Pilihan Paket Membership</label>
                    <div class="relative group">
                        <select name="paket_id" required class="w-full p-4 md:p-5 rounded-xl md:rounded-2xl outline-none font-black appearance-none cursor-pointer pr-12 text-[#0992C2] text-xs md:text-sm tracking-widest @error('paket_id') is-invalid @enderror">
                            <option value="" disabled selected>Pilih Paket Membership</option>
                            @foreach($pakets as $p)
                                <option value="{{ $p->id }}" {{ (old('paket_id') == $p->id || request('paket_id') == $p->id) ? 'selected' : '' }}>
                                    {{ strtoupper($p->nama_paket) }} (RP {{ number_format($p->harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#0992C2] group-hover:translate-y-1 transition-transform">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                    @error('paket_id') <p class="text-red-500 text-[8px] mt-1 ml-1 font-black uppercase italic tracking-widest">⚠️ Pilih satu</p> @enderror
                </div>

                <div class="flex items-start space-x-3 md:space-x-4 p-4 md:p-5 bg-white/5 rounded-xl border border-white/5 hover:bg-white/10 transition-all">
                    <input type="checkbox" id="agree" required class="w-5 h-5 md:w-6 md:h-6 accent-[#0992C2] cursor-pointer mt-0.5">
                    <label for="agree" class="text-zinc-400 text-[9px] md:text-[10px] leading-relaxed cursor-pointer font-bold italic uppercase tracking-wider">
                        Saya menyetujui seluruh <button type="button" onclick="toggleModal()" class="text-[#0992C2] underline hover:text-[#0992C2] font-black">Syarat & Aturan</button> yang berlaku.
                    </label>
                </div>

                <button type="submit" id="btnSubmit" class="w-full bg-[#0992C2] hover:bg-[#0992C2] text-black font-black py-5 md:py-6 rounded-xl md:rounded-2xl text-lg md:text-xl uppercase italic transition-all shadow-2xl group tracking-widest">
                    Daftar Sekarang <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </button>
            </form>
        @endif
    </div>

    <div id="rulesModal" class="fixed inset-0 z-[100] items-center justify-center p-4 bg-black/95 backdrop-blur-xl">
        <div class="bg-zinc-900 border border-[#0992C2]/20 w-full max-w-2xl rounded-[2rem] md:rounded-[3rem] p-8 md:p-10 shadow-2xl relative text-left">
            <h2 class="text-3xl md:text-4xl font-hero text-[#0992C2] orange-glow italic mb-6 md:mb-8 text-center">Syarat & Ketentuan</h2>
            
            <!-- Tab Navigation -->
            <div class="flex gap-2 mb-6 border-b border-white/10">
                <button onclick="switchTab('larangan')" id="tab-larangan" class="tab-btn flex-1 py-3 px-4 font-black text-xs uppercase tracking-wider transition-all border-b-2 border-[#0992C2] text-[#0992C2]">
                    Larangan
                </button>
                <button onclick="switchTab('aturan')" id="tab-aturan" class="tab-btn flex-1 py-3 px-4 font-black text-xs uppercase tracking-wider transition-all border-b-2 border-transparent text-zinc-500 hover:text-zinc-300">
                    Aturan
                </button>
            </div>

            <!-- Tab Content: Larangan -->
            <div id="content-larangan" class="tab-content">
                <div class="space-y-4 text-zinc-300 text-[10px] md:text-[11px] font-bold uppercase tracking-wider mb-8 leading-relaxed px-2 h-80 overflow-y-auto custom-scroll">
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Member di larang menyimpan air di showcase (pendingin). Members are prohibited from storing water in the showcase.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Member di larang duduk di kursi kasir maupun di atas meja kasir. Members are prohibited from sitting on the cashier's chair or on the cashier's table.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Loker (penyimpanan barang) untuk umum tanpa terkecuali. Member di larang membawa pulang kunci loker (jika di dapati, di kenakan denda Rp.50.000). Lockers (storage facilities) are available to the public without exception. Members are prohibited from taking locker keys home (if found, a fine of 50,000 rupiah will be imposed).</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Di larang membuang sampah sembarangan. Tempat sampah sudah di sediakan. Do not litter. Trash bins are provided.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Di larang membawa pulang sisir, hair dryer, parfum yang telah di sediakan kecuali pembalut & jarum pentul, gunakan pada saat urgent saja dan seperlunya. It is forbidden to take home combs, hair dryers, perfumes that have been provided except sanitary napkins and safety pins, use them only in urgent situations and as needed.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Di larang main HP di tempat alat beban, bijaklah. Masih ada banyak yang antri alat. Playing with your cell phone is prohibited, be wise. There are still many people queuing.</p></div>
                </div>
            </div>

            <!-- Tab Content: Aturan -->
            <div id="content-aturan" class="tab-content hidden">
                <div class="space-y-4 text-zinc-300 text-[10px] md:text-[11px] font-bold uppercase tracking-wider mb-8 leading-relaxed px-2 h-80 overflow-y-auto custom-scroll">
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Hanya member yang bisa akses pintu masuk. Only members can access the entrance.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Telat pembayaran member, Face ID/Sidik jari tertolak (tidak bisa masuk) kecuali melakukan pembayaran member kembali. Late member payment, Face ID or fingerprint (cannot log in) except for making member payments again.</p></div>
                    <div class="flex gap-4"><span class="text-[#0992C2] font-black">•</span><p>Siapapun yang membukakan pintu yang lambat membayar member, akan di kenakan sebanyak harga pervisit (Rp.75.000). Tidak terima alasan. Anyone who opens the door a late paying member will be charged the visit fee (Rp.75,000). No excuses accepted.</p></div>
                </div>
            </div>

            <button onclick="toggleModal()" class="w-full bg-zinc-800 hover:bg-white hover:text-black text-white font-black py-4 rounded-xl uppercase transition-all tracking-widest">Saya Mengerti</button>
        </div>
    </div>

    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;
        
        // Check for saved theme preference or default to 'dark'
        const currentTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply saved theme on page load
        function applyTheme(theme) {
            if (theme === 'light') {
                body.classList.remove('dark');
                body.classList.add('light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                body.classList.remove('light');
                body.classList.add('dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        }
        
        // Apply theme on page load
        applyTheme(currentTheme);
        
        // Theme toggle click handler
        themeToggle.addEventListener('click', () => {
            const isLight = body.classList.contains('light');
            const newTheme = isLight ? 'dark' : 'light';
            
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
        
        function toggleModal() { document.getElementById('rulesModal').classList.toggle('active'); }
        
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-[#0992C2]', 'text-[#0992C2]');
                btn.classList.add('border-transparent', 'text-zinc-500');
            });
            
            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('border-[#0992C2]', 'text-[#0992C2]');
            activeTab.classList.remove('border-transparent', 'text-zinc-500');
        }
        
        const form = document.getElementById('formDaftar');
        if(form) {
            form.onsubmit = function() {
                const btn = document.getElementById('btnSubmit');
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...';
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.disabled = true;
            };
        }
    </script>
</body>
</html>