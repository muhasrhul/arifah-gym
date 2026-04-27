<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--local-only : Simpan hanya di local tanpa upload ke Google Drive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database secara otomatis dan upload ke Google Drive';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Set unlimited execution time untuk backup
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $this->info('Memulai backup database...');

        try {
            // Ambil konfigurasi database
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST', '127.0.0.1');
            
            // Buat nama file backup dengan timestamp
            $filename = 'backup-' . Carbon::now()->format('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups');
            
            // Buat folder backups jika belum ada
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $fullPath = $backupPath . '/' . $filename;
            
            // Command mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );
            
            // Jalankan backup
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->info('✓ Backup database berhasil: ' . $filename);
                
                // Compress file untuk menghemat space dan waktu upload
                $compressedFile = $this->compressBackup($fullPath);
                
                // Upload ke Google Drive jika tidak pakai flag --local-only
                if (!$this->option('local-only') && config('filesystems.disks.google')) {
                    if ($compressedFile) {
                        $this->uploadToGoogleDrive($compressedFile, basename($compressedFile));
                        // Hapus file compressed lokal setelah upload
                        @unlink($compressedFile);
                    } else {
                        $this->uploadToGoogleDrive($fullPath, $filename);
                    }
                }
                
                // Hapus backup lama (lebih dari 30 hari)
                $this->cleanOldBackups($backupPath);
                
                return Command::SUCCESS;
            } else {
                $this->error('✗ Backup gagal!');
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Upload backup ke Google Drive
     */
    private function uploadToGoogleDrive($filePath, $filename)
    {
        $maxRetries = 3;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $this->info('Mengupload ke Google Drive...' . ($retryCount > 0 ? " (Percobaan ke-" . ($retryCount + 1) . ")" : ""));
                
                // Upload ke Google Drive menggunakan writeStream
                $stream = fopen($filePath, 'r');
                
                if (!$stream) {
                    throw new \Exception('Tidak bisa membuka file backup');
                }
                
                try {
                    Storage::disk('google')->writeStream('backups/' . $filename, $stream);
                    $this->info('✓ Berhasil upload ke Google Drive');
                    
                    // Hapus backup lama di Google Drive (lebih dari 30 hari)
                    $this->cleanOldGoogleDriveBackups();
                    
                    return; // Sukses, keluar dari function
                    
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
                
            } catch (\Exception $e) {
                $retryCount++;
                
                if ($retryCount >= $maxRetries) {
                    $this->warn('⚠ Error upload ke Google Drive setelah ' . $maxRetries . ' percobaan: ' . $e->getMessage());
                    $this->warn('   Backup tetap tersimpan di lokal: storage/app/backups/' . $filename);
                } else {
                    $this->warn('⚠ Upload gagal, mencoba lagi dalam 5 detik...');
                    sleep(5); // Tunggu 5 detik sebelum retry
                }
            }
        }
    }
    
    /**
     * Compress backup file menggunakan gzip
     */
    private function compressBackup($filePath)
    {
        try {
            $this->info('Mengkompress backup...');
            
            $compressedPath = $filePath . '.gz';
            
            // Baca file dan compress
            $fileContent = file_get_contents($filePath);
            $compressed = gzencode($fileContent, 9); // Level 9 = kompresi maksimal
            
            file_put_contents($compressedPath, $compressed);
            
            $originalSize = filesize($filePath);
            $compressedSize = filesize($compressedPath);
            $savedPercent = round((1 - $compressedSize / $originalSize) * 100, 1);
            
            $this->info("✓ Kompresi berhasil: " . $this->formatBytes($originalSize) . " → " . $this->formatBytes($compressedSize) . " (hemat {$savedPercent}%)");
            
            return $compressedPath;
            
        } catch (\Exception $e) {
            $this->warn('⚠ Kompresi gagal: ' . $e->getMessage());
            $this->warn('   Upload file tanpa kompresi...');
            return null;
        }
    }
    
    /**
     * Format bytes ke KB/MB/GB
     */
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Hapus backup lokal yang lebih dari 30 hari
     */
    private function cleanOldBackups($path)
    {
        $files = glob($path . '/backup-*.{sql,gz}', GLOB_BRACE);
        $now = time();
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                // Hapus file yang lebih dari 30 hari
                if ($now - filemtime($file) >= 30 * 24 * 60 * 60) {
                    unlink($file);
                    $deleted++;
                }
            }
        }
        
        if ($deleted > 0) {
            $this->info("✓ Dihapus {$deleted} backup lokal lama");
        }
    }
    
    /**
     * Hapus backup di Google Drive yang lebih dari 30 hari
     */
    private function cleanOldGoogleDriveBackups()
    {
        try {
            $files = Storage::disk('google')->listContents('backups', false);
            $now = time();
            $deleted = 0;
            
            foreach ($files as $file) {
                if ($file->isFile() && isset($file['lastModified'])) {
                    $lastModified = $file['lastModified'];
                    
                    // Hapus file yang lebih dari 30 hari
                    if ($now - $lastModified >= 30 * 24 * 60 * 60) {
                        Storage::disk('google')->delete($file['path']);
                        $deleted++;
                    }
                }
            }
            
            if ($deleted > 0) {
                $this->info("✓ Dihapus {$deleted} backup Google Drive lama");
            }
            
        } catch (\Exception $e) {
            // Skip error jika folder belum ada atau error lainnya
            $this->warn('⚠ Info: ' . $e->getMessage());
        }
    }
}
