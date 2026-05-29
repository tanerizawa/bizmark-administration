<?php

namespace App\Services;

use App\Models\ArticleTopic;
use App\Models\ArticleTopicSimilarity;
use App\Models\AutoPostConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TopicSimilarityService
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    /**
     * Calculate similarity between two topics
     */
    public function calculateSimilarity(ArticleTopic $topicA, ArticleTopic $topicB): float
    {
        // Check cache first
        $cached = ArticleTopicSimilarity::findSimilarity($topicA->id, $topicB->id);

        if ($cached) {
            Log::debug('📦 Using cached similarity score', [
                'topic_a' => $topicA->id,
                'topic_b' => $topicB->id,
                'score' => $cached->similarity_score,
            ]);

            return $cached->similarity_score;
        }

        // Calculate using AI
        Log::info('🔍 Calculating topic similarity with AI', [
            'topic_a' => $topicA->title,
            'topic_b' => $topicB->title,
        ]);

        $score = $this->calculateWithAI($topicA, $topicB);

        // Cache result - use updateOrCreate to handle duplicates
        ArticleTopicSimilarity::updateOrCreate(
            [
                'topic_a_id' => $topicA->id,
                'topic_b_id' => $topicB->id,
            ],
            [
                'similarity_score' => $score,
                'calculated_at' => now(),
            ]
        );

        Log::info('✅ Similarity calculated and cached', [
            'topic_a' => $topicA->title,
            'topic_b' => $topicB->title,
            'score' => $score,
        ]);

        return $score;
    }

    /**
     * Check if topic is too similar to recent articles
     */
    public function isDuplicate(ArticleTopic $topic, float $threshold = 0.75): bool
    {
        // Get recent published topics (configurable cooldown period)
        $cooldownDays = AutoPostConfig::current()->cooldown_days ?? 30;

        $recentTopics = ArticleTopic::where('status', 'published')
            ->where('published_at', '>=', now()->subDays($cooldownDays))
            ->where('id', '!=', $topic->id)
            ->get();

        if ($recentTopics->isEmpty()) {
            return false;
        }

        Log::info('🔍 Checking for duplicate topics', [
            'topic' => $topic->title,
            'recent_count' => $recentTopics->count(),
            'threshold' => $threshold,
        ]);

        foreach ($recentTopics as $recentTopic) {
            $similarity = $this->calculateSimilarity($topic, $recentTopic);

            if ($similarity >= $threshold) {
                Log::warning('⚠️  Duplicate topic detected', [
                    'topic' => $topic->title,
                    'similar_to' => $recentTopic->title,
                    'similarity' => $similarity,
                    'threshold' => $threshold,
                ]);

                return true;
            }
        }

        return false;
    }

    /**
     * Get most similar topics
     */
    public function getMostSimilarTopics(ArticleTopic $topic, int $limit = 5): Collection
    {
        $allTopics = ArticleTopic::where('id', '!=', $topic->id)->get();

        $similarities = [];
        foreach ($allTopics as $otherTopic) {
            $score = $this->calculateSimilarity($topic, $otherTopic);
            $similarities[] = [
                'topic' => $otherTopic,
                'similarity' => $score,
            ];
        }

        // Sort by similarity descending
        usort($similarities, fn ($a, $b) => $b['similarity'] <=> $a['similarity']);

        return collect($similarities)->take($limit);
    }

    /**
     * Calculate similarity using OpenRouter AI
     */
    protected function calculateWithAI(ArticleTopic $topicA, ArticleTopic $topicB): float
    {
        $prompt = "
Analisis kemiripan antara dua topik artikel berikut. Berikan skor 0.00 - 1.00:

**Kriteria Scoring:**
- **0.00 - 0.25:** Sangat berbeda (topik tidak terkait)
- **0.26 - 0.50:** Terkait tapi fokus sangat berbeda
- **0.51 - 0.75:** Terkait dengan beberapa overlap
- **0.76 - 0.90:** Sangat mirip, overlap signifikan
- **0.91 - 1.00:** Hampir identik / duplikat

**Topik A:**
Judul: {$topicA->title}
Kategori: {$topicA->category}
Deskripsi: {$topicA->description}
Keywords: ".implode(', ', $topicA->keywords ?? [])."

**Topik B:**
Judul: {$topicB->title}
Kategori: {$topicB->category}
Deskripsi: {$topicB->description}
Keywords: ".implode(', ', $topicB->keywords ?? [])."

**Pertimbangan:**
1. Apakah membahas hal yang sama dari sudut pandang berbeda? (contoh: 'Cara Mengurus IMB' vs 'Syarat IMB' → skor tinggi)
2. Apakah kategori sama namun topik berbeda? (contoh: 'Mengurus IMB' vs 'Mengurus SLF' → skor sedang)
3. Apakah hanya terkait secara tangensial? (contoh: 'IMB' vs 'AMDAL' → skor rendah)

**OUTPUT:**
Berikan HANYA angka skor (format: 0.XX), tanpa penjelasan atau teks lain.
Contoh output yang benar: 0.75
";

        try {
            $response = $this->openRouter->chat(
                [['role' => 'user', 'content' => $prompt]],
                [
                    'model' => config('services.openrouter.default_model', 'openrouter/free'), // Cost-efficient model for similarity scoring
                    'temperature' => 0.3,
                    'max_tokens' => 10,
                ]
            );

            if (! $response['success']) {
                Log::warning('⚠️  Similarity API call failed', ['error' => $response['error'] ?? 'unknown']);

                return $this->calculateSimpleKeywordSimilarity($topicA, $topicB);
            }

            $scoreText = trim($response['content'] ?? '');

            // Extract number from response
            preg_match('/\d+\.\d+/', $scoreText, $matches);

            if (empty($matches)) {
                Log::warning('⚠️  AI did not return valid score format', [
                    'response' => $scoreText,
                ]);

                return 0.5; // Default to medium similarity if parsing fails
            }

            $score = (float) $matches[0];

            // Clamp between 0 and 1
            return min(max($score, 0.0), 1.0);

        } catch (\Exception $e) {
            Log::error('❌ Similarity calculation failed', [
                'error' => $e->getMessage(),
            ]);

            // Fallback: simple keyword matching
            return $this->calculateSimpleKeywordSimilarity($topicA, $topicB);
        }
    }

    /**
     * Fallback: Simple keyword-based similarity
     */
    protected function calculateSimpleKeywordSimilarity(ArticleTopic $topicA, ArticleTopic $topicB): float
    {
        $keywordsA = array_merge(
            [$topicA->title],
            $topicA->keywords ?? [],
            $topicA->tags ?? []
        );

        $keywordsB = array_merge(
            [$topicB->title],
            $topicB->keywords ?? [],
            $topicB->tags ?? []
        );

        // Normalize to lowercase
        $keywordsA = array_map('strtolower', $keywordsA);
        $keywordsB = array_map('strtolower', $keywordsB);

        // Calculate Jaccard similarity
        $intersection = count(array_intersect($keywordsA, $keywordsB));
        $union = count(array_unique(array_merge($keywordsA, $keywordsB)));

        if ($union === 0) {
            return 0.0;
        }

        return round($intersection / $union, 2);
    }

    /**
     * Batch calculate similarities for multiple topics
     */
    public function batchCalculateSimilarities(Collection $topics): void
    {
        Log::info('🔄 Batch calculating similarities', [
            'topic_count' => $topics->count(),
        ]);

        $totalPairs = ($topics->count() * ($topics->count() - 1)) / 2;
        $calculated = 0;

        foreach ($topics as $i => $topicA) {
            foreach ($topics->slice($i + 1) as $topicB) {
                $this->calculateSimilarity($topicA, $topicB);
                $calculated++;

                if ($calculated % 10 === 0) {
                    Log::info("📊 Progress: {$calculated}/{$totalPairs} pairs calculated");
                }
            }
        }

        Log::info('✅ Batch calculation complete', [
            'pairs_calculated' => $calculated,
        ]);
    }
}
