<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between py-2">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-primary-600">
                    Selamat Datang kembali, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">
                    Semangat bertugas mengelola <span class="font-bold text-gray-800 dark:text-gray-200">ARIFAH Gym Makassar</span> hari ini.
                </p>
            </div>

            <div class="hidden md:flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="bg-[#0992C2]/10 p-2 rounded-lg shadow-sm border border-[#0992C2]/20">
                    <svg class="w-6 h-6 text-[#0992C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-[10px] font-bold text-[#0992C2] uppercase tracking-widest">Hari Ini</p>
                    <p class="text-sm font-black text-gray-800 dark:text-white tracking-tight">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>

        </div>
    </x-filament::card>
</x-filament::widget>