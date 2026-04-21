<?php

namespace App\Modules\ContentSeo\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * SearXNG Search Service — Open-source, self-hosted metasearch engine.
 * Aggregates results from Google, Bing, DuckDuckGo without any API keys.
 * Zero cost, unlimited queries.
 */
class SearxngSearchService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.searxng.url', 'http://bizmark_searxng:8080'), '/');
    }

    /**
     * Check if SearXNG instance is reachable.
     */
    public function isConfigured(): bool
    {
        if (empty($this->baseUrl)) {
            return false;
        }

        $cacheKey = 'searxng:health';

        return Cache::remember($cacheKey, 300, function () {
            try {
                $response = Http::timeout(5)->get($this->baseUrl . '/healthz');
                return $response->successful();
            } catch (\Throwable $e) {
                return false;
            }
        });
    }

    /**
     * Search SearXNG for a keyword and return structured SERP results.
     *
     * @param string $keyword  Search query
     * @param int    $num      Number of results (default: 10)
     * @param string $language Language code (default: id-ID)
     * @return array{success: bool, results: array, total_results: int, search_time: float, our_position: ?int, error?: string}
     */
    public function search(string $keyword, int $num = 10, string $language = 'id-ID'): array
    {
        // Cache results for 12 hours (SearXNG is free, so shorter cache = fresher data)
        $cacheKey = 'searxng_search:' . md5($keyword . $language . $num);

        return Cache::remember($cacheKey, 43200, function () use ($keyword, $num, $language) {
            try {
                $response = Http::timeout(20)->get($this->baseUrl . '/search', [
                    'q' => $keyword,
                    'format' => 'json',
                    'categories' => 'general',
                    'language' => $language,
                    'pageno' => 1,
                ]);

                if (!$response->successful()) {
                    Log::warning('SearXNG search error', [
                        'status' => $response->status(),
                        'keyword' => $keyword,
                    ]);

                    return [
                        'success' => false,
                        'results' => [],
                        'total_results' => 0,
                        'search_time' => 0,
                        'our_position' => null,
                        'error' => "SearXNG error: HTTP {$response->status()}",
                    ];
                }

                $data = $response->json();
                $results = [];
                $ourPosition = null;

                foreach (array_slice($data['results'] ?? [], 0, $num) as $i => $item) {
                    $position = $i + 1;
                    $url = $item['url'] ?? '';
                    $domain = parse_url($url, PHP_URL_HOST) ?: 'unknown';
                    $domain = preg_replace('/^www\./', '', $domain);

                    $result = [
                        'position' => $position,
                        'url' => $url,
                        'domain' => $domain,
                        'title' => $item['title'] ?? '',
                        'snippet' => $item['content'] ?? '',
                        'engines' => $item['engines'] ?? [],
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
                    'total_results' => (int) ($data['number_of_results'] ?? count($results)),
                    'search_time' => round((float) ($data['search_time'] ?? 0), 3),
                    'our_position' => $ourPosition,
                ];

            } catch (\Throwable $e) {
                Log::error('SearXNG search failed', [
                    'keyword' => $keyword,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'results' => [],
                    'total_results' => 0,
                    'search_time' => 0,
                    'our_position' => null,
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
        Cache::forget('searxng_search:' . md5($keyword . 'id-ID' . '10'));
        Cache::forget('searxng:health');
    }
}
