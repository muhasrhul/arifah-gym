<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Tangan Digital - {{ $member->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Tanda Tangan Digital</h1>
            <p class="text-gray-600">{{ $member->name }}</p>
            <p class="text-sm text-gray-500">Ditandatangani pada: {{ $member->signature_timestamp->setTimezone('Asia/Makassar')->format('d/m/Y') }}</p>
        </div>

        @if($member->digital_signature)
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-6">
                <img src="{{ $member->digital_signature }}" alt="Tanda Tangan {{ $member->name }}" class="max-w-full h-auto mx-auto">
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                <p>Tanda tangan tidak tersedia</p>
            </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">Informasi Persetujuan:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Nama: {{ $member->name }}</li>
                <li>• Email: {{ $member->email }}</li>
                <li>• Telepon: {{ $member->phone }}</li>
                <li>• Paket: {{ $member->type }}</li>
                <li>• Ditandatangani: {{ $member->signature_timestamp->setTimezone('Asia/Makassar')->format('d/m/Y') }}</li>
            </ul>
        </div>

        <div class="text-center mt-6">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Cetak Dokumen
            </button>
        </div>
    </div>
</body>
</html>