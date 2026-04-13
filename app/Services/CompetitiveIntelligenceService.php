<?php

namespace App\Services;

use App\Models\CompetitorAnalysis;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class CompetitiveIntelligenceService
{
    protected OpenRouterService $ai;
    protected GoogleSearchService $googleSearch;
    protected SearxngSearchService $searxng;

    public function __construct(OpenRouterService $ai, GoogleSearchService $googleSearch, SearxngSearchService $searxng)
    {
        $this->ai = $ai;
        $this->googleSearch = $googleSearch;
        $this->searxng = $searxng;
    }

    /**
     * Analyze competitors for a keyword.
     * Priority:
     *  1. SearXNG (self-hosted, gratis, unlimited)
     *  2. Google Custom Search API (gratis 100/hari)
     *  3. AI-only estimasi (fallback terakhir)
     */
    public function analyzeKeyword(string $keyword): ?CompetitorAnalysis
    {
        try {
            // Find our matching article
            $ourArticle = Article::published()
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('meta_keywords', 'LIKE', "%{$keyword}%")
                      ->orWhere('meta_title', 'LIKE', "%{$keyword}%");
                })
                ->orderByDesc('views_count')
                ->first();

            $ourUrl = $ourArticle ? url($ourArticle->getUrl()) : null;

            // Priority 1: SearXNG (open-source, self-hosted, unlimited)
            if ($this->searxng->isConfigured()) {
                $serp = $this->searxng->search($keyword);
                if ($serp['success'] && !empty($serp['results'])) {
                    return $this->analyzeWithRealSerp($keyword, $ourUrl, $serp, 'searxng');
                }
                Log::info("SearXNG returned no results for '{$keyword}', trying Google...");
            }

            // Priority 2: Google Custom Search API (100 free/day)
            if ($this->googleSearch->isConfigured()) {
                $serp = $this->googleSearch->search($keyword);
                if ($serp['success'] && !empty($serp['results'])) {
                    return $this->analyzeWithRealSerp($keyword, $ourUrl, $serp, 'google_serp');
                }
                Log::info("Google Search returned no results for '{$keyword}', falling back to AI...");
            }

            // Priority 3: AI-only (fallback terakhir)
            return $this->analyzeWithAiOnly($keyword, $ourUrl);

        } catch (\Throwable $e) {
            Log::error("CompetitiveIntelligence: Failed for '{$keyword}'", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * REAL DATA: Real SERP results (from SearXNG or Google) + AI enrichment.
     * Competitors, positions, URLs are real from search engines.
     * AI only adds: strengths analysis, content gaps, recommendations.
     */
    protected function analyzeWithRealSerp(string $keyword, ?string $ourUrl, array $serp, string $dataSource): ?CompetitorAnalysis
    {
        $ourPosition = $serp['our_position']; // null if not in top 10

        // Step 2: Build real competitor list (exclude our site)
        $competitors = [];
        foreach ($serp['results'] as $result) {
            if (!empty($result['is_ours'])) {
                continue;
            }
            $competitors[] = [
                'position' => $result['position'],
                'domain' => $result['domain'],
                'url' => $result['url'],
                'title' => $result['title'],
                'snippet' => $result['snippet'],
                'strengths' => '', // Will be filled by AI
            ];
        }

        // Step 3: AI enrichment — analyze real competitors for strengths, gaps, and recommendations
        $serpDataForPrompt = collect($competitors)->take(5)->map(fn($c) =>
            "#{$c['position']} {$c['domain']} — \"{$c['title']}\" — {$c['snippet']}"
        )->implode("\n");

        $prompt = <<<PROMPT
Kamu ahli SEO competitive analysis untuk industri perizinan dan lingkungan hidup di Indonesia.

Keyword target: "{$keyword}"
Website kami: bizmark.id (jasa konsultan perizinan lingkungan: AMDAL, UKL-UPL, izin limbah B3, OSS, SLF, izin K3)

Berikut HASIL PENCARIAN GOOGLE yang REAL untuk keyword ini:
{$serpDataForPrompt}

Posisi kita di Google: {$this->formatPosition($ourPosition)}

Berdasarkan DATA REAL di atas, berikan analisis dalam format JSON:
{
    "search_volume_estimate": <estimasi volume pencarian bulanan>,
    "difficulty": "<easy|medium|hard>",
    "competitor_strengths": [
        {"domain": "<domain dari data di atas>", "strengths": "<analisis kekuatan dari title & snippet>"}
    ],
    "content_gaps": ["topik yang kompetitor cover tapi kita belum, BERDASARKAN snippet mereka"],
    "recommendations": [
        "saran actionable spesifik untuk menang di keyword ini, berdasarkan analisis kompetitor di atas"
    ]
}

PENTING: Gunakan HANYA domain yang ada di data pencarian di atas. Jangan mengarang domain.
Response HANYA JSON valid, tanpa tambahan.
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.3,
            'max_tokens' => 2000,
        ]);

        $aiData = null;
        if ($response['success']) {
            $aiData = json_decode(
                preg_replace('/```json\s*|\s*```/', '', trim($response['content'])),
                true
            );
        }

        // Merge AI strengths into real competitor data
        if ($aiData && !empty($aiData['competitor_strengths'])) {
            $strengthsMap = collect($aiData['competitor_strengths'])->keyBy('domain');
            foreach ($competitors as &$comp) {
                if ($strengthsMap->has($comp['domain'])) {
                    $comp['strengths'] = $strengthsMap[$comp['domain']]['strengths'] ?? '';
                }
            }
            unset($comp);
        }

        return CompetitorAnalysis::create([
            'keyword' => $keyword,
            'our_url' => $ourUrl,
            'our_position' => $ourPosition,
            'top_competitors' => array_slice($competitors, 0, 5),
            'content_gaps' => $aiData['content_gaps'] ?? [],
            'recommendations' => $aiData['recommendations'] ?? [],
            'search_volume' => is_numeric($aiData['search_volume_estimate'] ?? null) ? (int) $aiData['search_volume_estimate'] : null,
            'difficulty' => $aiData['difficulty'] ?? 'medium',
            'data_source' => $dataSource,
            'analyzed_at' => now(),
        ]);
    }

    /**
     * FALLBACK: AI-only mode — all data is AI-estimated (may contain hallucinations).
     * Used when Google Custom Search API is not configured.
     */
    protected function analyzeWithAiOnly(string $keyword, ?string $ourUrl): ?CompetitorAnalysis
    {
        $prompt = <<<PROMPT
Kamu ahli SEO competitive analysis untuk industri perizinan dan lingkungan hidup di Indonesia.

Keyword target: "{$keyword}"
Website kami: bizmark.id (jasa konsultan perizinan lingkungan: AMDAL, UKL-UPL, izin limbah B3, OSS, SLF, izin K3)

Analisis kompetitor untuk keyword ini. Berikan response JSON:
{
    "search_volume_estimate": <angka estimasi volume pencarian bulanan>,
    "difficulty": "<easy|medium|hard>",
    "our_estimated_position": <1-100 atau null jika tidak ranking>,
    "top_competitors": [
        {"domain": "...", "title": "...", "strengths": "..."}
    ],
    "content_gaps": ["topik yang kompetitor cover tapi kita belum"],
    "recommendations": [
        "saran actionable untuk menang di keyword ini"
    ]
}

Response HANYA JSON valid, tanpa tambahan.
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.3,
            'max_tokens' => 2000,
        ]);

        if (!$response['success']) {
            return null;
        }

        $data = json_decode(
            preg_replace('/```json\s*|\s*```/', '', trim($response['content'])),
            true
        );

        if (!$data) {
            return null;
        }

        return CompetitorAnalysis::create([
            'keyword' => $keyword,
            'our_url' => $ourUrl,
            'our_position' => $data['our_estimated_position'] ?? null,
            'top_competitors' => $data['top_competitors'] ?? [],
            'content_gaps' => $data['content_gaps'] ?? [],
            'recommendations' => $data['recommendations'] ?? [],
            'search_volume' => is_numeric($data['search_volume_estimate'] ?? null) ? (int) $data['search_volume_estimate'] : null,
            'difficulty' => $data['difficulty'] ?? 'medium',
            'data_source' => 'ai_estimated',
            'analyzed_at' => now(),
        ]);
    }

    protected function formatPosition(?int $position): string
    {
        if ($position === null) {
            return 'Tidak ditemukan di halaman 1 (posisi > 10)';
        }
        return "#{$position}";
    }

    /**
     * Batch analyze top keywords from our keyword clusters
     */
    public function analyzeTopKeywords(int $limit = 10): array
    {
        $results = [];

        $keywords = \App\Models\KeywordCluster::query()
            ->orderByDesc('estimated_volume')
            ->take($limit)
            ->pluck('seed_keyword')
            ->toArray();

        $coreKeywords = [
            'jasa pengurusan AMDAL',
            'konsultan izin limbah B3',
            'pengurusan UKL-UPL',
            'jasa perizinan lingkungan',
            'konsultan OSS NIB',
        ];

        $keywords = array_unique(array_merge($keywords, $coreKeywords));
        $keywords = array_slice($keywords, 0, $limit);

        foreach ($keywords as $keyword) {
            $existing = CompetitorAnalysis::where('keyword', $keyword)
                ->where('analyzed_at', '>=', now()->subDays(7))
                ->exists();

            if ($existing) {
                continue;
            }

            $analysis = $this->analyzeKeyword($keyword);
            if ($analysis) {
                $results[] = $analysis;
            }
        }

        return $results;
    }

    /**
     * Get dashboard summary
     */
    public function getSummary(): array
    {
        $recent = CompetitorAnalysis::where('analyzed_at', '>=', now()->subDays(30));

        return [
            'total_analyzed' => $recent->count(),
            'ranking_keywords' => (clone $recent)->whereNotNull('our_position')->where('our_position', '<=', 10)->count(),
            'opportunity_keywords' => (clone $recent)->whereNotNull('our_position')->whereBetween('our_position', [11, 30])->count(),
            'unranked_keywords' => (clone $recent)->whereNull('our_position')->count(),
            'avg_position' => round((clone $recent)->whereNotNull('our_position')->avg('our_position') ?? 0, 1),
            'total_gaps' => CompetitorAnalysis::where('analyzed_at', '>=', now()->subDays(30))
                ->whereNotNull('content_gaps')
                ->get()
                ->sum(fn($a) => count($a->content_gaps ?? [])),
        ];
    }
}
