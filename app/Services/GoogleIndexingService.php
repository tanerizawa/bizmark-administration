<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Indexing as GoogleIndexing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GoogleIndexingService
{
    protected ?GoogleClient $client = null;
    protected bool $enabled = false;
    
    public function __construct()
    {
        $this->initializeClient();
    }
    
    /**
     * Initialize Google API Client
     */
    protected function initializeClient(): void
    {
        try {
            $serviceAccountPath = storage_path('app/google-service-account.json');
            
            if (!file_exists($serviceAccountPath)) {
                Log::warning('Google service account file not found. Indexing API disabled.');
                return;
            }
            
            $this->client = new GoogleClient();
            $this->client->setAuthConfig($serviceAccountPath);
            $this->client->addScope(GoogleIndexing::INDEXING);
            
            $this->enabled = true;
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Indexing API', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Request indexing for a URL via Google Indexing API
     */
    public function requestIndexing(string $url, string $type = 'URL_UPDATED'): bool
    {
        if (!$this->enabled) {
            Log::info('Google Indexing API not enabled, skipping', ['url' => $url]);
            return false;
        }
        
        try {
            $service = new GoogleIndexing($this->client);
            
            $urlNotification = new \Google\Service\Indexing\UrlNotification();
            $urlNotification->setUrl($url);
            $urlNotification->setType($type); // URL_UPDATED or URL_DELETED
            
            $response = $service->urlNotifications->publish($urlNotification);
            
            Log::info('✅ Google Indexing API request sent', [
                'url' => $url,
                'response' => $response
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('❌ Google Indexing API request failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Ping search engines (Google, Bing, etc)
     */
    public function pingSearchEngines(string $sitemapUrl): array
    {
        $results = [];
        
        // Google
        $results['google'] = $this->pingGoogle($sitemapUrl);
        
        // Bing
        $results['bing'] = $this->pingBing($sitemapUrl);
        
        return $results;
    }
    
    /**
     * Ping Google about sitemap update
     */
    protected function pingGoogle(string $sitemapUrl): bool
    {
        try {
            $pingUrl = 'https://www.google.com/ping?sitemap=' . urlencode($sitemapUrl);
            
            $response = Http::timeout(10)->get($pingUrl);
            
            $success = $response->successful();
            
            Log::info($success ? '✅ Google pinged successfully' : '❌ Google ping failed', [
                'sitemap' => $sitemapUrl,
                'status' => $response->status()
            ]);
            
            return $success;
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to ping Google', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Ping Bing about sitemap update
     */
    protected function pingBing(string $sitemapUrl): bool
    {
        try {
            $pingUrl = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemapUrl);
            
            $response = Http::timeout(10)->get($pingUrl);
            
            $success = $response->successful();
            
            Log::info($success ? '✅ Bing pinged successfully' : '❌ Bing ping failed', [
                'sitemap' => $sitemapUrl,
                'status' => $response->status()
            ]);
            
            return $success;
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to ping Bing', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Check if indexing API is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    
    /**
     * Batch request indexing for multiple URLs
     */
    public function batchRequestIndexing(array $urls): array
    {
        $results = [];
        
        foreach ($urls as $url) {
            $results[$url] = $this->requestIndexing($url);
            
            // Rate limiting: wait 200ms between requests
            usleep(200000);
        }
        
        return $results;
    }
}
