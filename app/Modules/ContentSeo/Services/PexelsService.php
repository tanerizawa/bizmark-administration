<?php

namespace App\Modules\ContentSeo\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PexelsService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.pexels.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.pexels.api_key');
    }

    /**
     * Search for photos on Pexels
     *
     * @param string $query
     * @param int $perPage
     * @param int $page
     * @param array $filters
     * @return array|null
     */
    public function searchPhotos(string $query, int $perPage = 15, int $page = 1, array $filters = []): ?array
    {
        try {
            $params = [
                'query' => $query,
                'per_page' => min($perPage, 80), // Max 80 per Pexels API
                'page' => $page,
            ];

            // Add optional filters
            if (isset($filters['orientation'])) {
                $params['orientation'] = $filters['orientation'];
            }
            if (isset($filters['size'])) {
                $params['size'] = $filters['size'];
            }
            if (isset($filters['color'])) {
                $params['color'] = $filters['color'];
            }
            if (isset($filters['locale'])) {
                $params['locale'] = $filters['locale'];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/search", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Pexels API search error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Pexels search exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Get curated photos
     *
     * @param int $perPage
     * @param int $page
     * @return array|null
     */
    public function getCuratedPhotos(int $perPage = 15, int $page = 1): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/curated", [
                'per_page' => min($perPage, 80),
                'page' => $page,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Pexels curated photos exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get a specific photo by ID
     *
     * @param int $photoId
     * @return array|null
     */
    public function getPhoto(int $photoId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/photos/{$photoId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Pexels get photo exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download and save a photo from Pexels
     *
     * @param string $photoUrl
     * @param string $photographerName
     * @param int $photoId
     * @return string|null Path to saved image
     */
    public function downloadAndSavePhoto(string $photoUrl, string $photographerName, int $photoId): ?string
    {
        try {
            // Download image
            $imageContent = Http::get($photoUrl)->body();

            if (empty($imageContent)) {
                return null;
            }

            // Generate filename
            $extension = $this->getImageExtension($photoUrl);
            $filename = 'pexels-' . $photoId . '-' . time() . '.' . $extension;
            $path = 'articles/' . $filename;

            // Save to storage
            Storage::disk('public')->put($path, $imageContent);

            // Verify the file was actually written to disk
            if (!Storage::disk('public')->exists($path)) {
                Log::error('Pexels photo saved but file not found on disk', [
                    'path' => $path,
                    'photo_id' => $photoId,
                ]);
                return null;
            }

            // Log attribution for compliance
            Log::info('Pexels photo downloaded', [
                'photo_id' => $photoId,
                'photographer' => $photographerName,
                'url' => $photoUrl,
                'saved_path' => $path,
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('Pexels download photo exception', [
                'message' => $e->getMessage(),
                'photo_url' => $photoUrl,
            ]);
            return null;
        }
    }

    /**
     * Get image extension from URL
     *
     * @param string $url
     * @return string
     */
    protected function getImageExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Default to jpg if no extension found
        return $extension ?: 'jpg';
    }

    /**
     * Get attribution text for a photo
     *
     * @param string $photographerName
     * @param string $photographerUrl
     * @param string $photoUrl
     * @return string
     */
    public function getAttributionText(string $photographerName, string $photographerUrl, string $photoUrl): string
    {
        return sprintf(
            'Photo by <a href="%s" target="_blank">%s</a> on <a href="%s" target="_blank">Pexels</a>',
            $photographerUrl,
            $photographerName,
            $photoUrl
        );
    }
}
