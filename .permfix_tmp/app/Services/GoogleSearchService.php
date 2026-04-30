<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleSearchService
{
    protected ?string $apiKey;
    protected ?string $searchEngineId;
    protected string $baseUrl = 'https://www.googleapis.com/customsearch/v1';

    public function __construct()
    {
        $this->apiKey = config('services.google_search.api_key');
        $this->searchEngineId = config('services.google_search.engine_id');
    }

    /**
     * Check if Google Custom Search is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->searchEngineId);
    }

    /**
     * Search Google for a keyword and return structured SERP results.
     * Returns real domains, titles, URLs, snippets, and positions.
     *
     * @param string $keyword  Search query
     * @param int    $num      Number of results (max 10)
     * @param string $gl       Country code (default: id for Indonesia)
     * @param string $hl       Language (default: id for Indonesian)
     * @return array{success: bool, results: array, total_results: int, search_time: float, error?: string}
     */
    public function search(string $keyword, int $num = 10, string $gl = 'id', string $hl = 'id'): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'results' => [],
                'total_results' => 0,
                'search_time' => 0,
                'error' => 'Google Custom Search API not configured. Set GOOGLE_SEARCH_API_KEY and GOOGLE_SEARCH_ENGINE_ID.',
            ];
        }

        // Cache results for 24 hours to conserve API quota (100 free/day)
        $cacheKey = 'google_search:' . md5($keyword . $gl . $hl . $num);

        return Cache::remember($cacheKey, 86400, function () use ($keyword, $num, $gl, $hl) {
            try {
                $response = Http::timeout(15)->get($this->baseUrl, [
                    'key' => $this->apiKey,
                    'cx' => $this->searchEngineId,
                    'q' => $keyword,
                    'num' => min($num, 10),
                    'gl' => $gl,
                    'hl' => $hl,
                ]);

                if (!$response->successful()) {
                    Log::warning('Google Search API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'keyword' => $keyword,
                    ]);

                    return [
                        'success' => false,
                        'results' => [],
                        'total_results' => 0,
                        'search_time' => 0,
                        'error' => "API error: HTTP {$response->status()}",
                    ];
                }

                $data = $response->json();

                $results = [];
                $ourPosition = null;

                foreach ($data['items'] ?? [] as $i => $item) {
                    $position = $i + 1;
                    $domain = parse_url($item['link'] ?? '', PHP_URL_HOST) ?: 'unknown';
                    $domain = preg_replace('/^www\./', '', $domain);

                    $result = [
                        'position' => $position,
                        'url' => $item['link'] ?? '',
                        'domain' => $domain,
                        'title' => $item['title'] ?? '',
                        'snippet' => $item['snippet'] ?? '',
                    ];

                    // Detect our own site
                    if (str_contains($domain, 'bizmark.id')) {
                        $ourPosition = $position;
                        $result['is_ours'] = true;
                    }

                    $results[] = $result;
                }

                return [
                    'success' => true,
                    'results' => $results,
                    'total_results' => (int) ($data['searchInformation']['totalResults'] ?? 0),
                    'search_time' => (float) ($data['searchInformation']['searchTime'] ?? 0),
                    'our_position' => $ourPosition,
                ];
            } catch (\Throwable $e) {
                Log::error('Google Search failed', [
                    'keyword' => $keyword,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'results' => [],
                    'total_results' => 0,
                    'search_time' => 0,
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Clear cached search results for a keyword.
     */
    public function clearCache(string $keyword): void
    {
        Cache::forget('google_search:' . md5($keyword . 'id' . 'id' . '10'));
    }
}
