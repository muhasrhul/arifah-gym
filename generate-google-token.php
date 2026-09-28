<?php

/**
 * Google Drive Refresh Token Generator
 * 
 * Script ini membantu generate Refresh Token untuk Google Drive API
 */

require __DIR__.'/vendor/autoload.php';

use Google\Client;

echo "\n=================================================\n";
echo "   Google Drive Refresh Token Generator\n";
echo "=================================================\n\n";

// Ambil credentials dari .env (atau input manual)
$clientId = env('GOOGLE_DRIVE_CLIENT_ID', '');
$clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET', '');

// Jika tidak ada di .env, minta input manual
if (empty($clientId) || empty($clientSecret)) {
    echo "Credentials tidak ditemukan di .env\n";
    echo "Silakan input manual:\n\n";
    
    echo "Masukkan Client ID: ";
    $clientId = trim(fgets(STDIN));
    
    echo "Masukkan Client Secret: ";
    $clientSecret = trim(fgets(STDIN));
} else {
    echo "Menggunakan credentials dari .env\n";
    echo "Client ID: " . substr($clientId, 0, 20) . "...\n\n";
}

if (empty($clientId) || empty($clientSecret)) {
    die("Error: Client ID dan Client Secret harus diisi!\n");
}

try {
    // Setup Google Client
    $client = new Client();
    $client->setApplicationName('GYM Backup System');
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri('http://localhost:8080'); // Harus sama dengan yang di Google Console
    $client->setAccessType('offline'); // Penting untuk mendapatkan refresh token
    $client->setPrompt('consent'); // Paksa consent screen muncul
    $client->setScopes([
        'https://www.googleapis.com/auth/drive', // Full access ke Google Drive
    ]);
    
    // Generate authorization URL
    $authUrl = $client->createAuthUrl();
    
    echo "\n=================================================\n";
    echo "LANGKAH-LANGKAH:\n";
    echo "=================================================\n\n";
    
    echo "1. Buka URL berikut di browser Anda:\n\n";
    echo $authUrl . "\n\n";
    
    echo "2. Login dengan akun Google Anda\n";
    echo "3. Klik 'Allow' untuk memberikan akses\n";
    echo "4. Anda akan di-redirect ke halaman error (normal)\n";
    echo "5. Copy SELURUH URL dari address bar\n";
    echo "   Atau copy hanya KODE setelah '?code=' atau '&code='\n\n";
    
    echo "=================================================\n";
    echo "Paste URL atau kode authorization di sini:\n";
    echo "=================================================\n";
    $input = trim(fgets(STDIN));
    
    // Extract kode dari URL jika user paste full URL
    $code = $input;
    if (strpos($input, 'code=') !== false) {
        $parts = parse_url($input);
        parse_str($parts['query'] ?? $parts['path'], $query);
        $code = $query['code'] ?? $input;
    }
    
    if (empty($code)) {
        die("\nError: Kode authorization kosong!\n");
    }
    
    echo "\nMemproses authorization code...\n";
    
    // Exchange authorization code untuk access token + refresh token
    $token = $client->fetchAccessTokenWithAuthCode($code);
    
    if (isset($token['error'])) {
        echo "\n✗ Error: " . $token['error'] . "\n";
        if (isset($token['error_description'])) {
            echo "   " . $token['error_description'] . "\n";
        }
        die("\n");
    }
    
    if (!isset($token['refresh_token'])) {
        echo "\n⚠ Warning: Refresh token tidak ditemukan dalam response!\n";
        echo "   Kemungkinan penyebab:\n";
        echo "   1. Anda sudah pernah authorize sebelumnya\n";
        echo "   2. Akun sudah punya akses yang aktif\n\n";
        echo "   Solusi: Revoke akses dulu, lalu coba lagi:\n";
        echo "   https://myaccount.google.com/permissions\n\n";
        
        if (isset($token['access_token'])) {
            echo "   Access Token (sementara): " . substr($token['access_token'], 0, 30) . "...\n";
        }
        die("\n");
    }
    
    $refreshToken = $token['refresh_token'];
    $accessToken = $token['access_token'] ?? '';
    
    echo "\n=================================================\n";
    echo "✓ SUCCESS! Token berhasil di-generate!\n";
    echo "=================================================\n\n";
    
    echo "Simpan credentials berikut ke file .env Anda:\n\n";
    echo "GOOGLE_DRIVE_CLIENT_ID=" . $clientId . "\n";
    echo "GOOGLE_DRIVE_CLIENT_SECRET=" . $clientSecret . "\n";
    echo "GOOGLE_DRIVE_REFRESH_TOKEN=" . $refreshToken . "\n";
    echo "GOOGLE_DRIVE_FOLDER=\n\n";
    
    echo "=================================================\n";
    echo "Info Token:\n";
    echo "=================================================\n";
    echo "Refresh Token: " . $refreshToken . "\n";
    echo "Access Token (30 hari pertama): " . substr($accessToken, 0, 50) . "...\n";
    echo "Expires in: " . ($token['expires_in'] ?? 'N/A') . " seconds\n";
    echo "Scope: " . ($token['scope'] ?? 'N/A') . "\n\n";
    
    // Test koneksi
    echo "=================================================\n";
    echo "Test Koneksi ke Google Drive\n";
    echo "=================================================\n";
    
    $client->setAccessToken($token);
    $service = new \Google_Service_Drive($client);
    
    // List files untuk test
    $results = $service->files->listFiles([
        'pageSize' => 5,
        'fields' => 'files(id, name)',
    ]);
    
    echo "✓ Koneksi berhasil!\n";
    echo "Files di Google Drive Anda (5 terakhir):\n";
    
    if (count($results->getFiles()) == 0) {
        echo "   (Google Drive kosong)\n";
    } else {
        foreach ($results->getFiles() as $file) {
            echo "   - " . $file->getName() . " (ID: " . $file->getId() . ")\n";
        }
    }
    
    echo "\n=================================================\n";
    echo "Selesai! Silakan update .env dan test backup.\n";
    echo "=================================================\n\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
