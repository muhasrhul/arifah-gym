<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARIFAH Gym Makassar - Power & Progress</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Poppins:wght@400;600;800&display=swap');
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .hero-bg { background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(17,24,39,1)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center; }
        .orange-glow { text-shadow: 0 0 20px rgba(9, 146, 194, 0.5); }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        html { scroll-behavior: smooth; }
        .btn-hover { transition: all 0.3s ease; }
        .btn-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px -10px rgba(9, 146, 194, 1); }
        @keyframes wa-pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
        .animate-wa { animation: wa-pulse 2s infinite; }
        .stamp-expired { border: 0.2rem solid #ef4444; color: #ef4444; font-size: 1.5rem; font-weight: 900; text-transform: uppercase; padding: 0.2rem 1rem; transform: rotate(-15deg); border-radius: 0.4rem; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(1px); box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); letter-spacing: 0.1em; z-index: 50; opacity: 0.9; }
        #mobile-menu { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform: translateY(-20px); opacity: 0; pointer-events: none; visibility: hidden; }
        #mobile-menu.active { transform: translateY(0); opacity: 1; pointer-events: auto; visibility: visible; }
        @keyframes pulse-orange { 0% { box-shadow: 0 0 0 0 rgba(9, 146, 194, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(9, 146, 194, 0); } 100% { box-shadow: 0 0 0 0 rgba(9, 146, 194, 0); } }
        .promo-glow { animation: pulse-orange 2s infinite; }
    </style>
</head>
<body class="bg-gray-950 text-white selection:bg-[#0992C2] selection:text-white">

    @include('components.landing.navbar')

    <main>
        @include('components.landing.hero')
        @include('components.landing.stats')
        @include('components.landing.pricing')
        @include('components.landing.facilities')
        @include('components.landing.check-status')
        @include('components.landing.gallery')
        @include('components.landing.testimonials')
        @include('components.landing.faq')
        @include('components.landing.location')
    </main>

    @include('components.landing.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script async src="https://www.tiktok.com/embed.js"></script>
    <script>
        AOS.init({ once: true, duration: 800 });

        // WhatsApp Tooltip Animation (setiap 15 detik)
        const waTooltip = document.getElementById('wa-tooltip');
        if (waTooltip) {
            setInterval(() => {
                waTooltip.classList.add('tooltip-show');
                setTimeout(() => {
                    waTooltip.classList.remove('tooltip-show');
                }, 3000); // Tampil selama 3 detik
            }, 15000); // Setiap 15 detik
            
            // Tampilkan pertama kali setelah 5 detik
            setTimeout(() => {
                waTooltip.classList.add('tooltip-show');
                setTimeout(() => {
                    waTooltip.classList.remove('tooltip-show');
                }, 3000);
            }, 5000);
        }

        // Logic Mobile Menu
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const barsIcon = document.getElementById('bars-icon');
        const xIcon = document.getElementById('x-icon');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            const isActive = mobileMenu.classList.toggle('active');
            if(isActive) {
                barsIcon.classList.add('opacity-0', 'scale-50');
                xIcon.classList.remove('opacity-0', 'scale-50');
            } else {
                barsIcon.classList.remove('opacity-0', 'scale-50');
                xIcon.classList.add('opacity-0', 'scale-50');
            }
        }
        menuBtn?.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));

        // Logic Download Kartu
        function downloadCard() {
            const card = document.getElementById('memberCard');
            const btn = document.getElementById('btnDownload');
            if(!card) return;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;
            html2canvas(card, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Kartu-Member-ArifahGym.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Simpan Kartu ke HP';
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>