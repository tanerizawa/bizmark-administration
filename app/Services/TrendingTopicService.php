<?php

namespace App\Services;

use App\Models\ArticleTopic;
use App\Models\TrendingTopic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Trending Topic Service
 *
 * Discovers trending topics using SearXNG and stores them for content generation.
 * Focuses on Indonesian business, UMKM, perizinan, and legal topics.
 */
class TrendingTopicService
{
    protected string $baseUrl;

    /**
     * Categories and their associated search seeds
     */
    protected array $categorySeeds = [
        'umkm' => [
            'UMKM terbaru 2025',
            'bisnis kecil Indonesia',
            'bantuan usaha kecil',
            'modal usaha UMKM',
            'digitalisasi UMKM',
        ],
        'perizinan' => [
            'perizinan usaha terbaru',
            'NIB OSS',
            'peraturan izin usaha',
            'izin usaha online',
            'kemudahan berusaha',
        ],
        'legal' => [
            'hukum bisnis Indonesia',
            'peraturan perusahaan',
            'PT perseroan perorangan',
            'akta notaris usaha',
            'legalitas usaha',
        ],
        'marketing' => [
            'digital marketing Indonesia',
            'strategi pemasaran UMKM',
            'social media marketing',
            'e-commerce Indonesia',
            'marketplace seller',
        ],
        'technology' => [
            'teknologi bisnis',
            'aplikasi usaha',
            'AI untuk bisnis',
            'software usaha kecil',
            'transformasi digital',
        ],
        'finance' => [
            'pajak UMKM 2025',
            'kredit usaha rakyat',
            'fintech Indonesia',
            'akuntansi usaha kecil',
            'pinjaman modal usaha',
        ],
    ];

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.searxng.url', 'http://bizmark_searxng:8080'), '/');
    }

    /**
     * Discover trending topics from all categories
     *
     * @return array Summary of discovered topics
     */
    public function discoverTrendingTopics(): array
    {
        $summary = [
            'discovered' => 0,
            'categories' => [],
            'errors' => [],
        ];

        foreach ($this->categorySeeds as $category => $seeds) {
            try {
                $topics = $this->discoverForCategory($category, $seeds);
                $summary['discovered'] += count($topics);
                $summary['categories'][$category] = count($topics);

                Log::info("Trending topic discovery for {$category}", [
                    'count' => count($topics),
                    'topics' => array_slice(array_column($topics, 'topic'), 0, 3),
                ]);
            } catch (\Throwable $e) {
                $summary['errors'][] = "{$category}: {$e->getMessage()}";
                Log::warning("Failed to discover trending topics for {$category}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $summary;
    }

    /**
     * Discover trending topics for a specific category
     */
    public function discoverForCategory(string $category, array $seeds): array
    {
        $discovered = [];

        foreach ($seeds as $seed) {
            $results = $this->searchNews($seed);

            if (! $results['success'] || empty($results['results'])) {
                continue;
            }

            $topics = $this->extractTopicsFromResults($results['results'], $category);

            foreach ($topics as $topic) {
                // Skip if already exists recently (within 7 days)
                $exists = TrendingTopic::where('topic', $topic['topic'])
                    ->where('discovered_at', '>=', now()->subDays(7))
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Create new trending topic
                $trendingTopic = TrendingTopic::create([
                    'topic' => $topic['topic'],
                    'category' => $category,
                    'language' => 'id',
                    'data_source' => 'searxng',
                    'trend_score' => $topic['score'],
                    'related_keywords' => $topic['related_keywords'] ?? [],
                    'top_sources' => $topic['sources'] ?? [],
                    'sample_headline' => $topic['headline'] ?? null,
                    'discovered_at' => now(),
                    'expires_at' => now()->addDays(14), // Topics expire after 14 days
                ]);

                $discovered[] = $trendingTopic->toArray();
            }
        }

        return $discovered;
    }

    /**
     * Search news on SearXNG
     */
    protected function searchNews(string $query): array
    {
        $cacheKey = 'trending_news_search:'.md5($query);

        return Cache::remember($cacheKey, 3600, function () use ($query) { // Cache 1 hour
            try {
                $response = Http::timeout(20)->get($this->baseUrl.'/search', [
                    'q' => $query,
                    'format' => 'json',
                    'categories' => 'news',
                    'language' => 'id-ID',
                    'time_range' => 'week', // Only last week
                    'pageno' => 1,
                ]);

                if (! $response->successful()) {
                    return [
                        'success' => false,
                        'results' => [],
                        'error' => "HTTP {$response->status()}",
                    ];
                }

                return [
                    'success' => true,
                    'results' => $response->json('results', []),
                ];
            } catch (\Throwable $e) {
                Log::warning('SearXNG news search failed', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'success' => false,
                    'results' => [],
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Extract trending topics from search results
     */
    protected function extractTopicsFromResults(array $results, string $category): array
    {
        $topics = [];
        $keywordCounts = [];
        $sources = [];

        foreach (array_slice($results, 0, 20) as $result) {
            $title = $result['title'] ?? '';
            $content = $result['content'] ?? '';
            $url = $result['url'] ?? '';
            $domain = parse_url($url, PHP_URL_HOST) ?: '';

            // Extract meaningful phrases from title
            $phrases = $this->extractPhrases($title);

            foreach ($phrases as $phrase) {
                if (! isset($keywordCounts[$phrase])) {
                    $keywordCounts[$phrase] = [
                        'count' => 0,
                        'headline' => $title,
                        'sources' => [],
                    ];
                }
                $keywordCounts[$phrase]['count']++;
                if (! in_array($domain, $keywordCounts[$phrase]['sources'])) {
                    $keywordCounts[$phrase]['sources'][] = $domain;
                }
            }
        }

        // Filter and score topics
        foreach ($keywordCounts as $phrase => $data) {
            // Score based on: frequency, number of sources, phrase quality
            $freqScore = min(40, $data['count'] * 10);
            $sourceScore = min(30, count($data['sources']) * 10);
            $qualityScore = $this->scorePhraseQuality($phrase, $category);

            $totalScore = $freqScore + $sourceScore + $qualityScore;

            // Only include if score >= 40 and phrase is relevant
            if ($totalScore >= 40 && $this->isPhraseRelevant($phrase, $category)) {
                $topics[] = [
                    'topic' => $phrase,
                    'score' => min(100, $totalScore),
                    'headline' => $data['headline'],
                    'sources' => array_slice($data['sources'], 0, 5),
                    'related_keywords' => $this->generateRelatedKeywords($phrase),
                ];
            }
        }

        // Sort by score descending
        usort($topics, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Return top 5 topics per category-seed
        return array_slice($topics, 0, 5);
    }

    /**
     * Extract meaningful phrases from text
     */
    protected function extractPhrases(string $text): array
    {
        $phrases = [];

        // Clean and normalize
        $text = Str::lower($text);
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        $words = preg_split('/\s+/', trim($text));

        // Extract 2-4 word phrases
        for ($len = 2; $len <= 4; $len++) {
            for ($i = 0; $i <= count($words) - $len; $i++) {
                $phrase = implode(' ', array_slice($words, $i, $len));
                $phrase = trim($phrase);

                // Skip if too short or contains only stopwords
                if (strlen($phrase) >= 8 && ! $this->isStopwordPhrase($phrase)) {
                    $phrases[] = $phrase;
                }
            }
        }

        return array_unique($phrases);
    }

    /**
     * Check if phrase contains only stopwords
     */
    protected function isStopwordPhrase(string $phrase): bool
    {
        $stopwords = [
            'dan', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada',
            'adalah', 'ini', 'itu', 'akan', 'sudah', 'telah', 'atau', 'juga',
            'sebagai', 'bisa', 'dapat', 'tidak', 'harus', 'oleh', 'karena',
            'the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been',
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will',
        ];

        $words = explode(' ', $phrase);
        $nonStopwords = array_filter($words, fn ($w) => ! in_array($w, $stopwords));

        return count($nonStopwords) === 0;
    }

    /**
     * Score phrase quality (0-30)
     */
    protected function scorePhraseQuality(string $phrase, string $category): int
    {
        $score = 15; // Base score

        // Category relevance keywords
        $categoryKeywords = [
            'umkm' => ['umkm', 'usaha', 'bisnis', 'modal', 'bantuan', 'kecil', 'mikro', 'menengah'],
            'perizinan' => ['izin', 'nib', 'oss', 'perizinan', 'peraturan', 'aturan', 'regulasi'],
            'legal' => ['hukum', 'akta', 'notaris', 'legal', 'pt', 'cv', 'firma', 'perjanjian'],
            'marketing' => ['marketing', 'pemasaran', 'promosi', 'digital', 'sosial', 'media', 'iklan'],
            'technology' => ['teknologi', 'digital', 'aplikasi', 'software', 'ai', 'otomasi', 'sistem'],
            'finance' => ['pajak', 'keuangan', 'kredit', 'modal', 'pinjaman', 'fintech', 'akuntansi'],
        ];

        $keywords = $categoryKeywords[$category] ?? [];
        foreach ($keywords as $kw) {
            if (Str::contains($phrase, $kw)) {
                $score += 5;
            }
        }

        // Year in phrase indicates current events
        if (preg_match('/202[4-9]/', $phrase)) {
            $score += 5;
        }

        // Proper length (sweet spot is 15-40 chars)
        $len = strlen($phrase);
        if ($len >= 15 && $len <= 40) {
            $score += 5;
        }

        return min(30, $score);
    }

    /**
     * Check if phrase is relevant to category
     */
    protected function isPhraseRelevant(string $phrase, string $category): bool
    {
        // Generic business keywords that apply to all categories
        $genericRelevant = [
            'indonesia', 'usaha', 'bisnis', 'perusahaan', 'pengusaha',
            'ekonomi', 'pemerintah', 'kementerian', 'regulasi',
        ];

        // Category-specific must-have keywords
        $mustHave = [
            'umkm' => ['umkm', 'usaha', 'bisnis', 'modal', 'bantuan'],
            'perizinan' => ['izin', 'perizinan', 'nib', 'oss', 'peraturan', 'aturan'],
            'legal' => ['hukum', 'legal', 'akta', 'notaris', 'kontrak', 'perjanjian'],
            'marketing' => ['marketing', 'pemasaran', 'promosi', 'iklan', 'brand'],
            'technology' => ['teknologi', 'digital', 'aplikasi', 'ai', 'software'],
            'finance' => ['pajak', 'keuangan', 'kredit', 'modal', 'fintech'],
        ];

        // Check category-specific keywords
        foreach ($mustHave[$category] ?? [] as $kw) {
            if (Str::contains($phrase, $kw)) {
                return true;
            }
        }

        // Check generic keywords
        foreach ($genericRelevant as $kw) {
            if (Str::contains($phrase, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate related keywords for a topic
     */
    protected function generateRelatedKeywords(string $topic): array
    {
        $related = [];
        $baseWords = explode(' ', $topic);

        // Add variations
        $related[] = $topic.' 2025';
        $related[] = $topic.' terbaru';
        $related[] = 'cara '.$topic;
        $related[] = $topic.' indonesia';

        // Add individual significant words
        foreach ($baseWords as $word) {
            if (strlen($word) > 4) {
                $related[] = $word;
            }
        }

        return array_slice(array_unique($related), 0, 8);
    }

    /**
     * Get unprocessed topics for content generation
     */
    public function getTopicsForContent(int $limit = 5, ?string $category = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = TrendingTopic::unprocessed()
            ->active()
            ->orderByDesc('trend_score')
            ->orderByDesc('discovered_at');

        if ($category) {
            $query->inCategory($category);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get trending topics summary for dashboard
     */
    public function getTrendingSummary(): array
    {
        return [
            'total_active' => TrendingTopic::active()->count(),
            'unprocessed' => TrendingTopic::unprocessed()->active()->count(),
            'high_priority' => TrendingTopic::highPriority()->unprocessed()->active()->count(),
            'by_category' => TrendingTopic::active()
                ->selectRaw('category, COUNT(*) as count, AVG(trend_score) as avg_score')
                ->groupBy('category')
                ->get()
                ->keyBy('category')
                ->toArray(),
            'recent_7d' => TrendingTopic::recent(7)->count(),
            'top_topics' => TrendingTopic::active()
                ->unprocessed()
                ->orderByDesc('trend_score')
                ->limit(5)
                ->get(['topic', 'category', 'trend_score', 'discovered_at'])
                ->toArray(),
        ];
    }

    /**
     * Clean up expired topics
     */
    public function cleanupExpired(): int
    {
        return TrendingTopic::where('expires_at', '<', now())
            ->where('is_processed', false)
            ->delete();
    }

    /**
     * Convert high-priority trending topics to ArticleTopics for auto-posting.
     *
     * @param  int  $limit  Max topics to convert
     * @param  int  $minScore  Minimum trend_score threshold
     * @return array{converted: int, skipped: int, topics: array}
     */
    public function convertToArticleTopics(int $limit = 5, int $minScore = 60): array
    {
        $trendingTopics = TrendingTopic::unprocessed()
            ->active()
            ->where('trend_score', '>=', $minScore)
            ->orderByDesc('trend_score')
            ->limit($limit)
            ->get();

        $converted = 0;
        $skipped = 0;
        $topics = [];

        foreach ($trendingTopics as $trending) {
            // Check if a similar ArticleTopic already exists
            $exists = ArticleTopic::where('title', 'LIKE', '%'.Str::limit($trending->topic, 30, '').'%')
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            // Map trending category to ArticleTopic category
            $articleCategory = $this->mapCategory($trending->category);

            // Build a descriptive title from the trending topic + sample headline
            $title = $this->buildArticleTitle($trending);

            // Create ArticleTopic
            $articleTopic = ArticleTopic::create([
                'title' => $title,
                'description' => "Artikel trending tentang: {$trending->topic}. ".
                    ($trending->sample_headline ? "Konteks: {$trending->sample_headline}" : ''),
                'category' => $articleCategory,
                'language' => $trending->language,
                'target_market' => 'local',
                'keywords' => $trending->related_keywords ?? [$trending->topic],
                'tags' => $this->buildTags($trending),
                'priority' => $this->calculatePriority($trending),
                'status' => 'pending',
            ]);

            // Mark trending topic as processed (article not yet generated, will come from auto-post pipeline)
            $trending->update(['is_processed' => true]);

            $converted++;
            $topics[] = [
                'trending_id' => $trending->id,
                'article_topic_id' => $articleTopic->id,
                'title' => $title,
                'score' => $trending->trend_score,
                'priority' => $articleTopic->priority,
            ];
        }

        return [
            'converted' => $converted,
            'skipped' => $skipped,
            'topics' => $topics,
        ];
    }

    /**
     * Map trending category to ArticleTopic category
     */
    protected function mapCategory(string $trendingCategory): string
    {
        return match ($trendingCategory) {
            'umkm', 'marketing', 'finance' => 'tips',
            'perizinan', 'legal' => 'regulation',
            'technology' => 'general',
            default => 'news',
        };
    }

    /**
     * Build article title from trending topic
     */
    protected function buildArticleTitle(TrendingTopic $trending): string
    {
        $topic = Str::title($trending->topic);
        $year = date('Y');

        $templates = [
            "Panduan Lengkap {$topic}: Yang Perlu Anda Ketahui di {$year}",
            "{$topic}: Dampak dan Peluang untuk Bisnis Indonesia",
            "Memahami {$topic}: Tips Praktis untuk Pelaku Usaha",
            "Update {$topic} Terbaru: Apa yang Berubah di {$year}?",
            "{$topic}: Strategi dan Langkah Konkret untuk UMKM",
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Build tags from trending topic data
     */
    protected function buildTags(TrendingTopic $trending): array
    {
        $tags = [$trending->category, 'trending'];
        $words = explode(' ', $trending->topic);

        foreach ($words as $word) {
            if (strlen($word) > 3 && count($tags) < 6) {
                $tags[] = Str::lower($word);
            }
        }

        return array_unique($tags);
    }

    /**
     * Calculate article priority from trend score (higher trend = higher priority)
     */
    protected function calculatePriority(TrendingTopic $trending): int
    {
        // Trending topics get high priority (70-95) to be picked up quickly
        $basePriority = 70;
        $bonus = (int) (($trending->trend_score / 100) * 25);

        return min(95, $basePriority + $bonus);
    }
}
