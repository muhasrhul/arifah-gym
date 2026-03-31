<x-filament::widget>
    <div class="text-center py-6 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-orange-200 dark:border-gray-600 mb-6">
        <h1 class="text-2xl font-bold text-gray-600 dark:text-gray-300 tracking-wide">
            LAPORAN PEMBUKUAN
        </h1>
        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            {{ \Carbon\Carbon::now('Asia/Makassar')->format('d F Y') }}
        </div>
    </div>
</x-filament::widget>