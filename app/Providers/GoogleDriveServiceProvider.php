<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Storage::extend('google', function ($app, $config) {
                // Validasi credentials
                if (empty($config['clientId']) || empty($config['clientSecret']) || empty($config['refreshToken'])) {
                    \Log::warning('Google Drive credentials incomplete in config');
                    throw new \Exception('Google Drive credentials are incomplete. Please check your .env file.');
                }
                
                $options = [];
                
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }
                
                $client = new Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                
                // Setup untuk auto-refresh access token
                $client->setAccessType('offline');
                $client->setApprovalPrompt('force');
                
                // Set refresh token
                $client->refreshToken($config['refreshToken']);
                
                // Pastikan token di-refresh jika expired
                if ($client->isAccessTokenExpired()) {
                    try {
                        $newToken = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
                        
                        if (isset($newToken['error'])) {
                            \Log::error('Google Drive token refresh failed: ' . ($newToken['error_description'] ?? $newToken['error']));
                            throw new \Exception('Failed to refresh Google Drive token: ' . ($newToken['error_description'] ?? $newToken['error']));
                        }
                        
                        $client->setAccessToken($newToken);
                    } catch (\Exception $e) {
                        \Log::error('Google Drive authentication error: ' . $e->getMessage());
                        throw $e;
                    }
                }
                
                $service = new \Google_Service_Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new Filesystem($adapter, ['case_sensitive' => false]);
                
                return $driver;
            });
        } catch (\Exception $e) {
            // Log error but don't break the app
            \Log::error('Google Drive setup failed: ' . $e->getMessage());
        }
    }
}
