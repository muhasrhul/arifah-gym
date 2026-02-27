<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran - ARIFAH Gym</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #030712;
            background-image: radial-gradient(circle at top right, rgba(9, 146, 194, 0.05), transparent);
            transition: all 0.3s ease;
        }
        
        /* Light Mode Styles */
        body.light {
            background-color: #ffffff;
            background-image: radial-gradient(circle at top right, rgba(9, 146, 194, 0.08), transparent);
            color: #0f172a;
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
            transition: all 0.3s ease;
        }
        
        /* Light mode glass card */
        body.light .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .btn-premium {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px rgba(9, 146, 194, 0.5);
        }

        /* Modal QRIS & Transfer */
        .payment-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(3, 7, 18, 0.95);
            backdrop-filter: blur(15px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        /* Light mode modal */
        body.light .payment-modal {
            background: rgba(255, 255, 255, 0.95);
        }

        .payment-modal.active {
            display: flex;
        }

        /* Animasi sukses */
        #success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(3, 7, 18, 0.95);
            backdrop-filter: blur(15px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        /* Light mode success overlay */
        body.light #success-overlay {
            background: rgba(255, 255, 255, 0.95);
        }

        .svg-container { width: 120px; height: 120px; }

        .circle {
            stroke-dasharray: 410;
            stroke-dashoffset: 410;
            stroke-width: 4;
            stroke-miterlimit: 10;
            stroke: #0992C2;
            fill: none;
            animation: draw-circle 0.8s ease-out forwards;
        }

        .check {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            stroke-width: 6;
            stroke: #0992C2;
            fill: none;
            animation: draw-check 0.5s 0.8s ease-out forwards;
        }

        @keyframes draw-circle { to { stroke-dashoffset: 0; } }
        @keyframes draw-check { to { stroke-dashoffset: 0; } }

        .fade-in-up {
            animation: fadeInUp 0.5s 1.2s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Copy button animation */
        .copy-btn {
            transition: all 0.2s;
        }
        .copy-btn:active {
            transform: scale(0.95);
        }

        /* Custom scrollbar untuk modal */
        .glass-card::-webkit-scrollbar {
            width: 8px;
        }
        .glass-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .glass-card::-webkit-scrollbar-thumb {
            background: rgba(9, 146, 194, 0.5);
            border-radius: 10px;
        }
        .glass-card::-webkit-scrollbar-thumb:hover {
            background: rgba(9, 146, 194, 0.7);
        }
        
        /* Light mode scrollbar */
        body.light .glass-card::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        
        /* Text colors */
        .text-white {
            color: white;
            transition: color 0.3s ease;
        }
        
        body.light .text-white {
            color: #0f172a;
        }
        
        .text-zinc-400 {
            color: rgb(161 161 170);
            transition: color 0.3s ease;
        }
        
        body.light .text-zinc-400 {
            color: rgb(100 116 139);
        }
        
        .text-zinc-500 {
            color: rgb(113 113 122);
            transition: color 0.3s ease;
        }
        
        body.light .text-zinc-500 {
            color: rgb(71 85 105);
        }
        
        /* Background colors */
        .bg-white\/5 {
            background-color: rgba(255, 255, 255, 0.05);
            transition: background-color 0.3s ease;
        }
        
        body.light .bg-white\/5 {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .bg-white\/10 {
            background-color: rgba(255, 255, 255, 0.1);
            transition: background-color 0.3s ease;
        }
        
        body.light .bg-white\/10 {
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        /* Border colors */
        .border-white\/10 {
            border-color: rgba(255, 255, 255, 0.1);
            transition: border-color 0.3s ease;
        }
        
        body.light .border-white\/10 {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        /* Button text colors for light mode */
        .whatsapp-btn-text {
            color: white;
            transition: color 0.3s ease;
        }
        
        body.light .whatsapp-btn-text {
            color: white; /* Keep white for contrast against green background */
        }
        
        .transfer-btn-text {
            color: black;
            transition: color 0.3s ease;
        }
        
        body.light .transfer-btn-text {
            color: white; /* Change to white for better contrast in light mode */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-white">

    <!-- Theme Toggle Button -->
    <div class="fixed top-6 right-6 md:top-8 md:right-8 z-50">
        <button id="theme-toggle" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all" title="Toggle Light/Dark Mode">
            <i class="fas fa-sun" id="theme-icon"></i>
        </button>
    </div>

    <!-- Success Overlay -->
    <div id="success-overlay">
        <div class="svg-container">
            <svg viewBox="0 0 140 140">
                <circle class="circle" cx="70" cy="70" r="65" />
                <polyline class="check" points="40,75 60,95 100,50" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="text-center fade-in-up mt-6 px-6">
            <h2 id="success-title" class="text-3xl font-hero text-[#0992C2] italic mb-2 tracking-tighter">SUKSES!</h2>
            <p id="success-msg" class="text-zinc-400 text-[10px] uppercase tracking-[0.4em] font-black leading-relaxed">Pembayaran Berhasil</p>
        </div>
    </div>

    <!-- Warning Popup - Custom Design -->
    <div id="warning-popup" class="payment-modal" style="display: none;">
        <div class="glass-card rounded-3xl p-8 max-w-md w-full relative">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-[#0992C2]/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-exclamation-triangle text-[#0992C2] text-4xl"></i>
                </div>
                <h3 class="text-2xl font-hero text-[#0992C2] italic mb-3 tracking-tighter">PERINGATAN!</h3>
                <p class="text-white font-bold text-base mb-2">Pembayaran Belum Selesai</p>
                <p class="text-zinc-400 text-sm leading-relaxed">
                    Anda belum menyelesaikan proses pembayaran. Silakan pilih metode pembayaran dan selesaikan transaksi untuk aktivasi akun.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="stayOnPage()" class="w-full bg-gradient-to-r from-[#0992C2] to-[#0992C2] hover:from-[#0992C2] hover:to-[#0992C2] text-black font-black py-4 rounded-xl uppercase tracking-wider shadow-lg hover:shadow-[#0992C2]/50 transition-all duration-300">
                    <i class="fa-solid fa-credit-card mr-2"></i>
                    Lanjutkan Pembayaran
                </button>
                <button onclick="forceLeave()" class="w-full bg-white/5 hover:bg-white/10 border-2 border-white/10 text-white font-bold py-4 rounded-xl uppercase tracking-wider transition-all duration-300">
                    <i class="fa-solid fa-clock mr-2"></i>
                    Bayar Nanti
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Transfer -->
    <div id="transfer-modal" class="payment-modal">
        <div class="glass-card rounded-3xl p-8 max-w-md w-full relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeModal('transfer-modal')" class="absolute top-6 right-6 text-white/50 hover:text-white text-2xl z-10">
                <i class="fa-solid fa-times"></i>
            </button>
            
            <div class="text-center mb-6">
                <h3 class="text-2xl font-hero text-[#0992C2] italic mb-2">TRANSFER BANK</h3>
                <p class="text-zinc-400 text-xs uppercase tracking-widest font-bold">Transfer ke rekening berikut</p>
            </div>

            <div class="space-y-4 mb-6">
                <!-- Bank BCA -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center font-black text-white">
                            BCA
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Bank BCA</p>
                            <p class="text-zinc-500 text-xs">a.n. MUSDHALIFAH</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between bg-black/30 rounded-lg p-3">
                        <p class="text-white font-black text-lg tracking-wider">7686540064</p>
                        <button onclick="copyText('7686540064')" class="copy-btn text-[#0992C2] hover:text-[#0992C2]">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center mb-6 p-4 bg-[#0992C2]/10 border border-[#0992C2]/20 rounded-xl">
                <p class="text-[#0992C2] font-black text-2xl mb-1">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                <p class="text-zinc-400 text-xs uppercase tracking-widest">Total Transfer</p>
            </div>

            <!-- Informasi Kirim Bukti -->
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
                <div class="flex items-start gap-3">
                    <i class="fa-brands fa-whatsapp text-green-500 text-2xl mt-1"></i>
                    <div class="flex-1">
                        <p class="text-white font-bold text-sm mb-1">Kirim Bukti Transfer</p>
                        <p class="text-zinc-400 text-xs leading-relaxed">
                            Setelah transfer, kirim bukti (screenshot) ke WhatsApp kasir untuk aktivasi akun lebih cepat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="sendToWhatsApp('transfer')" class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 font-black py-5 rounded-2xl text-sm uppercase tracking-wider shadow-lg hover:shadow-green-500/50 transition-all duration-300 transform hover:scale-[1.02] relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <div class="relative flex items-center justify-center gap-3 whatsapp-btn-text">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                        <span class="font-hero italic">Kirim ke WhatsApp</span>
                    </div>
                </button>
                <button onclick="confirmPayment('transfer')" class="w-full bg-gradient-to-r from-[#0992C2] to-[#0992C2] hover:from-[#0992C2] hover:to-[#0992C2] font-black py-5 rounded-2xl text-sm uppercase tracking-wider shadow-lg hover:shadow-[#0992C2]/50 transition-all duration-300 transform hover:scale-[1.02] relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <div class="relative flex items-center justify-center gap-3 transfer-btn-text">
                        <i class="fa-solid fa-check-circle text-2xl"></i>
                        <span class="font-hero italic">Sudah Transfer</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-hero text-white italic orange-glow">
                ARIFAH <span class="text-[#0992C2]">GYM</span>
            </h1>
            <p class="text-zinc-500 text-[10px] uppercase tracking-[0.5em] font-black italic mt-2">Secure Checkout</p>
        </div>

        <div class="glass-card rounded-[3rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#0992C2]/10 rounded-full blur-[80px]"></div>

            <div class="text-center mb-10 border-b border-white/5 pb-8">
                <p class="text-zinc-500 text-[10px] uppercase font-black tracking-widest mb-3 italic">Total Tagihan</p>
                
                @if($registrationFee > 0)
                    <!-- Breakdown Biaya -->
                    <div class="mb-4 space-y-2">
                        <div class="flex justify-between items-center text-zinc-400 text-sm px-4">
                            <span>{{ $paket->nama_paket }}</span>
                            <span class="font-bold">Rp {{ number_format($hargaPaket, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-zinc-400 text-sm px-4">
                            <span>Biaya Registrasi</span>
                            <span class="font-bold">Rp {{ number_format($registrationFee, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-white/10 my-2"></div>
                    </div>
                @endif
                
                <h2 class="text-3xl md:text-5xl font-hero text-white mb-4 tracking-tighter">
                    Rp {{ number_format($amount, 0, ',', '.') }}
                </h2>
                <div class="inline-block px-5 py-2 bg-[#0992C2]/10 rounded-2xl border border-[#0992C2]/20">
                    <p class="text-[#0992C2] text-xs font-black uppercase tracking-widest italic">
                        <i class="fa-solid fa-user-check mr-2"></i> {{ $member->name }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Metode 1: Transfer Bank -->
                <button onclick="openModal('transfer-modal')" class="w-full bg-white/5 hover:bg-[#0992C2]/10 border-2 border-white/10 hover:border-[#0992C2]/30 text-white font-bold py-5 rounded-xl text-xs uppercase tracking-wider shadow-md hover:shadow-[#0992C2]/30 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-center gap-3">
                        <i class="fa-solid fa-building-columns text-[#0992C2] text-xl"></i> 
                        <span>TRANSFER BANK</span>
                    </div>
                </button>

                <!-- Metode 2: Cash -->
                <button onclick="savePaymentMethod('cash'); confirmPayment('cash');" class="w-full bg-white/5 hover:bg-[#0992C2]/10 border-2 border-white/10 hover:border-[#0992C2]/30 text-white font-bold py-5 rounded-xl text-xs uppercase tracking-wider shadow-md hover:shadow-[#0992C2]/30 transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-center gap-3">
                        <i class="fa-solid fa-money-bill-wave text-[#0992C2] text-xl"></i> 
                        <span>CASH</span>
                    </div>
                </button>
            </div>

            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <div class="flex items-center justify-center gap-3 opacity-40">
                    <i class="fa-solid fa-shield-halved text-[#0992C2]"></i>
                    <p class="text-[9px] uppercase font-bold tracking-[0.2em]">Encrypted Secure Payment</p>
                </div>
            </div>
        </div>

        <div class="mt-10 text-center">
            <p class="text-zinc-700 text-[8px] uppercase tracking-[0.5em] font-black">ARIFAH Gym Makassar • Payment Gateway</p>
        </div>
    </div>

    <script type="text/javascript">
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
        
        // Open modal
        function openModal(modalId) {
            // Jika membuka modal transfer, langsung simpan metode pembayaran
            if (modalId === 'transfer-modal') {
                savePaymentMethod('transfer');
            }
            document.getElementById(modalId).classList.add('active');
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Copy text to clipboard
        function copyText(text) {
            // Method 1: Modern Clipboard API (untuk HTTPS)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopyToast();
                }).catch(function(err) {
                    // Fallback jika gagal
                    copyTextFallback(text);
                });
            } else {
                // Method 2: Fallback untuk HTTP atau browser lama
                copyTextFallback(text);
            }
        }

        // Fallback method untuk copy text
        function copyTextFallback(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopyToast();
                } else {
                    alert('Gagal menyalin nomor rekening. Silakan salin manual: ' + text);
                }
            } catch (err) {
                alert('Gagal menyalin nomor rekening. Silakan salin manual: ' + text);
            }
            
            document.body.removeChild(textArea);
        }

        // Show toast notification
        function showCopyToast() {
            const toast = document.createElement('div');
            toast.className = 'fixed top-6 right-6 bg-[#0992C2] text-black px-6 py-3 rounded-xl font-bold text-sm z-[10000] shadow-2xl';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            toast.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Nomor rekening disalin!';
            document.body.appendChild(toast);
            
            // Animasi masuk
            setTimeout(() => {
                toast.style.opacity = '1';
            }, 10);
            
            // Hapus setelah 2 detik
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Confirm payment (untuk Transfer dan Cash)
        function confirmPayment(method) {
            // Mark payment as confirmed to allow page exit
            markPaymentConfirmed();
            
            // Tutup modal terlebih dahulu
            if (method === 'transfer') {
                closeModal('transfer-modal');
            }
            
            let title, message;
            
            switch(method) {
                case 'transfer':
                    title = "TRANSFER DITERIMA!";
                    message = "Terima kasih sudah melakukan transfer! <br><br> <span class='text-[#0992C2] font-bold'>Silakan tunjukkan bukti transfer ke kasir untuk aktivasi akun Anda.</span>";
                    break;
                case 'cash':
                    title = "PERMINTAAN DITERIMA!";
                    message = "Permintaan pembayaran cash diterima. <br><br> <span class='text-[#0992C2] font-bold'>Silakan menuju kasir untuk melakukan pembayaran dan aktivasi akun.</span>";
                    break;
            }
            
            // Tampilkan animasi sukses
            showSuccessAnimation(title, message);
        }

        // Simpan metode pembayaran ke database
        function savePaymentMethod(method) {
            const memberId = {{ $member->id }};
            let paymentMethod = '';
            
            // Map method ke format database
            switch(method) {
                case 'transfer':
                    paymentMethod = 'transfer_bank';
                    break;
                case 'cash':
                    paymentMethod = 'cash';
                    break;
                default:
                    paymentMethod = method;
            }
            
            // Kirim ke backend via AJAX
            fetch('{{ route("member.updatePaymentMethod") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    member_id: memberId,
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Payment method saved:', data);
            })
            .catch(error => {
                console.error('Error saving payment method:', error);
            });
        }

        // Send to WhatsApp
        function sendToWhatsApp(method) {
            // GANTI NOMOR INI DENGAN NOMOR WHATSAPP KASIR ANDA
            const waNumber = '6281943622015'; // Format: 62xxx (tanpa +, tanpa spasi)
            
            let message = '';
            const memberName = '{{ $member->name }}';
            const amount = 'Rp {{ number_format($amount, 0, ',', '.') }}';
            
            if (method === 'transfer') {
                message = `Halo Admin ARIFAH GYM,%0A%0A` +
                         `Saya *${memberName}* sudah melakukan *Transfer Bank* sebesar *${amount}*.%0A%0A` +
                         `Mohon untuk aktivasi akun saya.%0A%0A` +
                         `Bukti transfer akan saya kirim via chat ini.%0A%0A` +
                         `Terima kasih! 🙏`;
            }
            
            // Open WhatsApp
            const waUrl = `https://wa.me/${waNumber}?text=${message}`;
            window.open(waUrl, '_blank');
            
            // Show toast
            const toast = document.createElement('div');
            toast.className = 'fixed top-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm z-[10000] shadow-2xl';
            toast.innerHTML = '<i class="fa-brands fa-whatsapp mr-2"></i> Membuka WhatsApp...';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Fungsi animasi sukses
        function showSuccessAnimation(title, message) {
            const overlay = document.getElementById('success-overlay');
            const titleEl = document.getElementById('success-title');
            const msgEl = document.getElementById('success-msg');

            titleEl.innerText = title;
            msgEl.innerHTML = message;

            overlay.style.display = 'flex';
            
            setTimeout(function() {
                window.location.href = "/";
            }, 4000);
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const transferModal = document.getElementById('transfer-modal');
            
            if (event.target === transferModal) {
                closeModal('transfer-modal');
            }
        });

        // Prevent user from leaving page before payment confirmation
        let paymentConfirmed = false;
        let pendingNavigation = null;

        // Show custom warning popup
        function showWarningPopup() {
            document.getElementById('warning-popup').style.display = 'flex';
        }

        // Hide warning popup and stay on page
        function stayOnPage() {
            document.getElementById('warning-popup').style.display = 'none';
            pendingNavigation = null;
        }

        // Force leave page
        function forceLeave() {
            paymentConfirmed = true;
            document.getElementById('warning-popup').style.display = 'none';
            
            if (pendingNavigation === 'back') {
                window.history.back();
            } else if (pendingNavigation === 'close') {
                window.close();
            }
        }

        // Warning before leaving page (refresh, close tab, new URL)
        window.addEventListener('beforeunload', function (e) {
            if (!paymentConfirmed) {
                e.preventDefault();
                e.returnValue = 'Anda belum menyelesaikan pembayaran. Yakin ingin keluar?';
                return e.returnValue;
            }
        });

        // Disable back button with custom popup
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', function () {
            if (!paymentConfirmed) {
                history.pushState(null, null, location.href);
                pendingNavigation = 'back';
                showWarningPopup();
            }
        });

        // Mark payment as confirmed when user clicks "Sudah Bayar"
        function markPaymentConfirmed() {
            paymentConfirmed = true;
        }
    </script>
</body>
</html>