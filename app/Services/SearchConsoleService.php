<?php

namespace App\Services;

use App\Models\SearchConsoleData;
use App\Models\KeywordCluster;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SearchConsoleService
{
    /**
     * Import data from Google Search Console API.
     *
     * Authentication: OAuth2 refresh token flow.
     * Set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REFRESH_TOKEN, GSC_SITE_URL in .env
     * to activate real data. Falls back to simulation when credentials are missing.
     */
    public function importFromGSC(int $days = 7): array
    {
        if ($this->hasRealCredentials()) {
            try {
                return $this->fetchRealGSCData($days);
            } catch (\Throwable $e) {
                Log::warning('GSC real API failed, falling back to simulation', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->simulateGSCData($days);
    }

    /**
     * Check whether real OAuth2 credentials are configured.
     */
    public function hasRealCredentials(): bool
    {
        return !empty(config('services.google.client_id'))
            && !empty(config('services.google.client_secret'))
            && !empty(config('services.google.refresh_token'))
            && !empty(config('services.google.gsc_site_url'));
    }

    /**
     * Obtain a short-lived OAuth2 access token via the refresh token grant.
     * Cached for 55 minutes (tokens expire in 60 minutes).
     *
     * @throws \RuntimeException when the token exchange fails
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('gsc_access_token', 55 * 60, function () {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => config('services.google.refresh_token'),
                'grant_type'    => 'refresh_token',
            ]);

            if (!$response->ok() || empty($response->json('access_token'))) {
                throw new \RuntimeException(
                    'Failed to obtain GSC access token: ' . $response->body()
                );
            }

            return $response->json('access_token');
        });
    }

    /**
     * Fetch real Search Analytics data from GSC API v3.
     *
     * Endpoint: POST /webmasters/v3/sites/{siteUrl}/searchAnalytics/query
     * Dimensions: query + page
     * Dates: last $days days
     *
     * @throws \RuntimeException
     */
    protected function fetchRealGSCData(int $days): array
    {
        $accessToken   = $this->getAccessToken();
        $configuredUrl = (string) config('services.google.gsc_site_url');
        $siteCandidates = $this->buildSiteCandidates($configuredUrl);

        $endDate   = now()->subDay()->toDateString();
        $startDate = now()->subDays($days + 1)->toDateString();
        $lastError = null;

        foreach ($siteCandidates as $siteUrl) {
            $encodedSite = rawurlencode($siteUrl);
            $imported = 0;
            $startRow = 0;
            $rowLimit = 1000;

            try {
                do {
                    $response = Http::withToken($accessToken)
                        ->timeout(30)
                        ->post(
                            "https://searchconsole.googleapis.com/webmasters/v3/sites/{$encodedSite}/searchAnalytics/query",
                            [
                                'startDate'  => $startDate,
                                'endDate'    => $endDate,
                                'dimensions' => ['query', 'page'],
                                'rowLimit'   => $rowLimit,
                                'startRow'   => $startRow,
                                'dataState'  => 'all',
                            ]
                        );

                    if (!$response->ok()) {
                        throw new \RuntimeException('GSC API error: ' . $response->status() . ' ' . $response->body());
                    }

                    $rows = $response->json('rows', []);

                    foreach ($rows as $row) {
                        $query   = $row['keys'][0] ?? null;
                        $pageUrl = $row['keys'][1] ?? null;

                        if (!$query || !$pageUrl) {
                            continue;
                        }

                        SearchConsoleData::updateOrCreate(
                            [
                                'page_url' => $pageUrl,
                                'query'    => $query,
                                'date'     => $endDate,
                            ],
                            [
                                'clicks'      => (int) ($row['clicks'] ?? 0),
                                'impressions' => (int) ($row['impressions'] ?? 0),
                                'ctr'         => round(($row['ctr'] ?? 0) * 100, 2),
                                'position'    => round($row['position'] ?? 0, 1),
                            ]
                        );
                        $imported++;
                    }

                    $startRow += $rowLimit;
                } while (count($rows) === $rowLimit);

                return [
                    'imported' => $imported,
                    'source' => 'google_search_console',
                    'site' => $siteUrl,
                ];
            } catch (\Throwable $e) {
                $lastError = $e;
                Log::warning('GSC site variant failed, trying next variant', [
                    'site' => $siteUrl,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        throw new \RuntimeException($lastError?->getMessage() ?? 'GSC API failed for all site variants');
    }

    /**
     * Build candidate property identifiers for GSC API.
     * Supports URL-prefix property and domain property formats.
     */
    protected function buildSiteCandidates(string $configuredUrl): array
    {
        $configuredUrl = trim($configuredUrl);
        if ($configuredUrl === '') {
            return [];
        }

        $variants = [$configuredUrl];

        if (str_starts_with($configuredUrl, 'sc-domain:')) {
            $domain = str_replace('sc-domain:', '', $configuredUrl);
            $variants[] = 'https://' . rtrim($domain, '/') . '/';
            $variants[] = 'http://' . rtrim($domain, '/') . '/';
        } else {
            $normalized = rtrim($configuredUrl, '/') . '/';
            $variants[] = $normalized;
            $variants[] = rtrim($normalized, '/');

            $host = parse_url($normalized, PHP_URL_HOST) ?: $normalized;
            $host = preg_replace('/^www\./', '', $host);
            $variants[] = 'sc-domain:' . $host;

            if (str_starts_with($normalized, 'https://')) {
                $variants[] = 'http://' . substr($normalized, 8);
            } elseif (str_starts_with($normalized, 'http://')) {
                $variants[] = 'https://' . substr($normalized, 7);
            }
        }

        return array_values(array_unique(array_filter($variants)));
    }

    /**
     * Simulate GSC data from actual article view counts.
     * Provides realistic-looking data until real OAuth2 credentials are configured.
     */
    protected function simulateGSCData(int $days = 7): array
    {
        $imported = 0;
        $articles = Article::published()->orderByDesc('views_count')->take(50)->get();

        foreach ($articles as $article) {
            $url      = url($article->getUrl());
            $keywords = array_filter(explode(',', $article->meta_keywords ?? ''));

            if (empty($keywords)) {
                $keywords = [strtolower($article->title)];
            }

            foreach (array_slice($keywords, 0, 3) as $keyword) {
                $keyword = trim($keyword);
                if (empty($keyword)) {
                    continue;
                }

                $date             = now()->subDays(rand(0, $days - 1))->toDateString();
                $baseImpressions  = max(10, (int) ($article->views_count * 0.3));
                $dailyImpressions = max(1, (int) ($baseImpressions / 30));
                $clicks           = max(0, (int) ($dailyImpressions * (rand(2, 15) / 100)));
                $ctr              = $dailyImpressions > 0
                    ? round(($clicks / $dailyImpressions) * 100, 2)
                    : 0;

                SearchConsoleData::updateOrCreate(
                    ['page_url' => $url, 'query' => $keyword, 'date' => $date],
                    [
                        'clicks'      => $clicks,
                        'impressions' => $dailyImpressions,
                        'ctr'         => $ctr,
                        'position'    => rand(1, 50),
                    ]
                );
                $imported++;
            }
        }

        return ['imported' => $imported, 'source' => 'simulated'];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cross-Reference GSC data against AI-estimated keyword_clusters
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cross-reference search_console_data with keyword_clusters.
     *
     * For each keyword cluster, aggregate real GSC clicks/impressions/position
     * from search_console_data and update the cluster's gsc_* columns.
     * Also returns a discrepancy report comparing AI estimates vs real data.
     *
     * @param  int  $days  Look-back window for GSC data
     * @return array{updated: int, discrepancies: array, report: array}
     */
    public function crossReferenceWithKeywordClusters(int $days = 28): array
    {
        $updated       = 0;
        $discrepancies = [];
        $report        = [];

        $clusters = KeywordCluster::where('status', 'active')->get();

        foreach ($clusters as $cluster) {
            // Build a list of all keyword terms for this cluster
            $terms   = array_merge(
                (array) ($cluster->keywords ?? []),
                (array) ($cluster->long_tail_keywords ?? []),
                [$cluster->seed_keyword]
            );
            $terms   = array_unique(array_filter(array_map('trim', $terms)));

            if (empty($terms)) {
                continue;
            }

            // Aggregate GSC data for all terms in this cluster
            $agg = SearchConsoleData::where('date', '>=', now()->subDays($days))
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('query', 'ILIKE', "%{$term}%");
                    }
                })
                ->selectRaw('
                    SUM(clicks)       AS total_clicks,
                    SUM(impressions)  AS total_impressions,
                    AVG(position)     AS avg_position,
                    AVG(ctr)          AS avg_ctr,
                    COUNT(*)          AS row_count
                ')
                ->first();

            $realImpressions = (int) ($agg->total_impressions ?? 0);
            $realClicks      = (int) ($agg->total_clicks      ?? 0);
            $realPosition    = round((float) ($agg->avg_position ?? 0), 1);
            $realCtr         = round((float) ($agg->avg_ctr     ?? 0), 2);

            if ($realImpressions === 0) {
                continue;   // no GSC data for this cluster yet
            }

            // Detect discrepancy between AI estimate and real impressions
            $aiEstimate   = $cluster->estimated_volume ?? 0;
            $discrepancy  = $aiEstimate > 0
                ? round((($realImpressions - $aiEstimate) / $aiEstimate) * 100, 1)
                : null;

            // Update keyword_cluster with real GSC figures
            $cluster->update([
                'gsc_clicks'      => $realClicks,
                'gsc_impressions' => $realImpressions,
                'gsc_avg_position'=> $realPosition,
                'gsc_ctr'         => $realCtr,
                'gsc_synced_at'   => now(),
            ]);
            $updated++;

            $report[] = [
                'cluster'          => $cluster->cluster_name,
                'seed_keyword'     => $cluster->seed_keyword,
                'ai_est_volume'    => $aiEstimate,
                'real_impressions' => $realImpressions,
                'real_clicks'      => $realClicks,
                'real_position'    => $realPosition,
                'real_ctr'         => $realCtr . '%',
                'discrepancy_pct'  => $discrepancy !== null ? $discrepancy . '%' : 'n/a',
            ];

            // Flag large discrepancies (>200% or <-50%) for review
            if ($discrepancy !== null && (abs($discrepancy) > 200 || $discrepancy < -50)) {
                $discrepancies[] = [
                    'cluster'    => $cluster->cluster_name,
                    'ai_est'     => $aiEstimate,
                    'real'       => $realImpressions,
                    'diff_pct'   => $discrepancy . '%',
                    'action'     => $aiEstimate > $realImpressions
                        ? 'AI over-estimated — lower priority'
                        : 'AI under-estimated — consider increasing priority',
                ];
            }
        }

        return compact('updated', 'discrepancies', 'report');
    }

    /**
     * Get top performing queries
     */
    public function getTopQueries(int $days = 30, int $limit = 20): \Illuminate\Support\Collection
    {
        return SearchConsoleData::where('date', '>=', now()->subDays($days))
            ->selectRaw('query, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position, AVG(ctr) as avg_ctr')
            ->groupBy('query')
            ->orderByDesc('total_clicks')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top performing pages
     */
    public function getTopPages(int $days = 30, int $limit = 20): \Illuminate\Support\Collection
    {
        return SearchConsoleData::where('date', '>=', now()->subDays($days))
            ->selectRaw('page_url, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->groupBy('page_url')
            ->orderByDesc('total_clicks')
            ->limit($limit)
            ->get();
    }

    /**
     * Find opportunities: high impressions but low CTR (position 1-20)
     */
    public function getOpportunities(int $days = 30): \Illuminate\Support\Collection
    {
        return SearchConsoleData::where('date', '>=', now()->subDays($days))
            ->selectRaw('query, page_url, SUM(impressions) as total_impressions, SUM(clicks) as total_clicks, AVG(position) as avg_position, AVG(ctr) as avg_ctr')
            ->groupBy('query', 'page_url')
            ->havingRaw('AVG(position) BETWEEN 5 AND 20')
            ->havingRaw('AVG(ctr) < 5')
            ->havingRaw('SUM(impressions) > 10')
            ->orderByDesc('total_impressions')
            ->limit(20)
            ->get();
    }

    /**
     * Summary stats for dashboard
     */
    public function getSummary(int $days = 30): array
    {
        $data = SearchConsoleData::where('date', '>=', now()->subDays($days));

        return [
            'total_clicks' => (clone $data)->sum('clicks'),
            'total_impressions' => (clone $data)->sum('impressions'),
            'avg_ctr' => round((clone $data)->avg('ctr') ?? 0, 2),
            'avg_position' => round((clone $data)->avg('position') ?? 0, 1),
            'unique_queries' => (clone $data)->distinct('query')->count(),
            'unique_pages' => (clone $data)->distinct('page_url')->count(),
        ];
    }
}
