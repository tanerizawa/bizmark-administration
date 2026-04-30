<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IndexNowService
{
    protected string $host = 'bizmark.id';

    protected string $keyLocation;

    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.indexnow.key', Str::uuid()->toString());
        $this->keyLocation = "https://{$this->host}/{$this->apiKey}.txt";
    }

    /**
     * Submit a single URL to IndexNow (Bing, Yandex, Seznam, Naver)
     */
    public function submitUrl(string $url): bool
    {
        $engines = [
            'https://api.indexnow.org/indexnow',
        ];

        $success = false;

        foreach ($engines as $endpoint) {
            try {
                $response = Http::timeout(10)->get($endpoint, [
                    'url' => $url,
                    'key' => $this->apiKey,
                ]);

                if ($response->status() === 200 || $response->status() === 202) {
                    Log::info('IndexNow: URL submitted', ['url' => $url, 'engine' => $endpoint]);
                    $success = true;
                } else {
                    Log::warning('IndexNow: Non-success response', [
                        'url' => $url,
                        'engine' => $endpoint,
                        'status' => $response->status(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('IndexNow: Failed', [
                    'url' => $url,
                    'engine' => $endpoint,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $success;
    }

    /**
     * Submit multiple URLs in batch (up to 10,000 per request)
     */
    public function submitBatch(array $urls): bool
    {
        if (empty($urls)) {
            return false;
        }

        // IndexNow batch endpoint
        $endpoint = 'https://api.indexnow.org/indexnow';

        try {
            $response = Http::timeout(30)->post($endpoint, [
                'host' => $this->host,
                'key' => $this->apiKey,
                'keyLocation' => $this->keyLocation,
                'urlList' => array_values(array_slice($urls, 0, 10000)),
            ]);

            if ($response->status() === 200 || $response->status() === 202) {
                Log::info('IndexNow: Batch submitted', ['count' => count($urls)]);

                return true;
            }

            Log::warning('IndexNow: Batch non-success', [
                'status' => $response->status(),
                'count' => count($urls),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('IndexNow: Batch failed', [
                'error' => $e->getMessage(),
                'count' => count($urls),
            ]);

            return false;
        }
    }

    /**
     * Get the API key (used to create the key verification file)
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
