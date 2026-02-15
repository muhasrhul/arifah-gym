<style>
    @keyframes wa-shadow-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
        }
        70% {
            box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }
    .animate-wa-shadow {
        animation: wa-shadow-pulse 2s infinite;
    }
</style>

<footer class="py-16 md:py-20 bg-black text-center border-t border-white/5 relative overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-[#0992C2]/10 rounded-full blur-[120px] pointer-events-none"></div>
    
    <h1 class="text-3xl md:text-4xl font-black text-[#0992C2] italic mb-4 md:mb-6">ARIFAH <span class="text-white">GYM</span></h1>
    
    <div class="flex justify-center space-x-6 md:space-x-8 mb-8 md:mb-10 text-gray-400 relative z-20">
        <a href="https://www.instagram.com/arifah_gym?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" 
           class="hover:text-[#0992C2] transition-all duration-300 hover:-translate-y-1 text-xl md:text-2xl cursor-pointer">
            <i class="fa-brands fa-instagram"></i>
        </a>
        
        <a href="https://wa.me/6281943622015" target="_blank" 
           class="hover:text-[#0992C2] transition-all duration-300 hover:-translate-y-1 text-xl md:text-2xl cursor-pointer">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
        
        <a href="https://www.tiktok.com/@arifah_gym" target="_blank" 
           class="hover:text-[#0992C2] transition-all duration-300 hover:-translate-y-1 text-xl md:text-2xl cursor-pointer">
            <i class="fa-brands fa-tiktok"></i>
        </a>
    </div>

    <div class="relative z-20 space-y-2 px-6">
        <p class="text-gray-500 text-[10px] md:text-[11px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] italic">No Pain No Gain.</p>
        <p class="text-gray-600 text-[8px] md:text-[9px] font-bold uppercase tracking-[0.3em] md:tracking-[0.4em]">ARIFAH Gym Makassar © 2026</p>
    </div>
</footer>

<a href="https://wa.me/6281943622015" target="_blank" 
   class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-[9999] bg-[#25D366] w-12 h-12 md:w-16 md:h-16 rounded-full flex items-center justify-center text-white text-xl md:text-3xl animate-wa-shadow hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer group">
    <i class="fa-brands fa-whatsapp"></i>
    
    <!-- Tooltip Popup -->
    <div id="wa-tooltip" class="absolute right-full mr-3 md:mr-4 bg-white text-gray-900 px-3 py-1.5 md:px-4 md:py-2 rounded-lg shadow-xl whitespace-nowrap font-bold text-xs md:text-sm opacity-0 pointer-events-none transition-all duration-300 group-hover:opacity-100">
        Hubungi Kami Sekarang! 💬
        <div class="absolute top-1/2 -right-2 -translate-y-1/2 w-0 h-0 border-t-8 border-t-transparent border-b-8 border-b-transparent border-l-8 border-l-white"></div>
    </div>
</a>

<style>
    @keyframes tooltip-bounce {
        0%, 100% { transform: translateX(0); opacity: 1; }
        50% { transform: translateX(-10px); opacity: 1; }
    }
    
    .tooltip-show {
        opacity: 1 !important;
        animation: tooltip-bounce 0.6s ease-in-out 3;
    }
</style>