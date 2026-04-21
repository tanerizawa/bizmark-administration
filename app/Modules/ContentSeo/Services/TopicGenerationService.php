<?php

namespace App\Modules\ContentSeo\Services;

use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Models\AutoPostLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TopicGenerationService
{
    protected OpenRouterService $openRouter;
    protected TopicSimilarityService $similarityService;

    public function __construct(
        OpenRouterService $openRouter,
        TopicSimilarityService $similarityService
    ) {
        $this->openRouter = $openRouter;
        $this->similarityService = $similarityService;
    }

    /**
     * Auto-replenish topic pool if running low.
     * Returns number of topics created.
     */
    public function replenishIfNeeded(int $minThreshold = 5): int
    {
        $available = ArticleTopic::available()->count();

        if ($available >= $minThreshold) {
            return 0;
        }

        $needed = max($minThreshold * 2, 10) - $available;

        Log::info('🔄 Topic pool low, auto-generating', [
            'available' => $available,
            'threshold' => $minThreshold,
            'generating' => $needed,
        ]);

        return $this->generateTopics($needed);
    }

    /**
     * Generate N new topics via AI and persist them.
     */
    public function generateTopics(int $count = 10): int
    {
        $config = AutoPostConfig::current();

        // Gather existing titles to prevent duplicates
        $existingTitles = ArticleTopic::pluck('title')->toArray();

        // Determine language distribution
        $languages = $this->getLanguageDistribution($count, $config);

        $created = 0;

        foreach ($languages as $language => $langCount) {
            try {
                $topics = $this->generateTopicBatch($langCount, $language, $config, $existingTitles);

                foreach ($topics as $topicData) {
                    // Skip if title already exists (exact or very similar)
                    if ($this->titleExists($topicData['title'], $existingTitles)) {
                        Log::debug('⏭️ Skipping duplicate topic', ['title' => $topicData['title']]);
                        continue;
                    }

                    ArticleTopic::create([
                        'title' => $topicData['title'],
                        'description' => $topicData['description'] ?? '',
                        'category' => $topicData['category'] ?? 'general',
                        'language' => $language,
                        'target_market' => $topicData['target_market'] ?? ($language === 'en' ? 'pma' : 'local'),
                        'keywords' => $topicData['keywords'] ?? [],
                        'tags' => $topicData['tags'] ?? [],
                        'priority' => $topicData['priority'] ?? rand(50, 90),
                        'status' => 'pending',
                    ]);

                    $existingTitles[] = $topicData['title'];
                    $created++;
                }
            } catch (\Exception $e) {
                Log::error('❌ Topic generation failed for language', [
                    'language' => $language,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($created > 0) {
            Log::info("✅ Generated {$created} new topics");

            AutoPostLog::create([
                'level' => 'success',
                'event' => 'topics_auto_generated',
                'message' => "Auto-generated {$created} new topics for the pool",
                'context' => ['count' => $created],
            ]);
        }

        return $created;
    }

    /**
     * Call AI to generate a batch of topic ideas.
     */
    protected function generateTopicBatch(int $count, string $language, AutoPostConfig $config, array $existingTitles): array
    {
        $categoryWeights = $config->category_weights ?? [
            'tips' => 40,
            'regulation' => 25,
            'general' => 20,
            'case-study' => 10,
            'news' => 5,
        ];

        // Build a list of recent titles for context
        $recentTitles = array_slice($existingTitles, -20);
        $recentTitlesList = implode("\n- ", $recentTitles);

        $prompt = $this->buildPrompt($count, $language, $categoryWeights, $recentTitlesList);

        $messages = [
            [
                'role' => 'system',
                'content' => $this->getSystemPrompt($language),
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        $response = $this->openRouter->chat($messages, [
            'model' => $config->ai_model ?? 'anthropic/claude-3.5-sonnet',
            'temperature' => 0.85,
            'max_tokens' => 3000,
        ]);

        if (!$response['success']) {
            throw new \Exception('AI topic generation failed: ' . ($response['error'] ?? 'Unknown'));
        }

        return $this->parseTopicResponse($response['content']);
    }

    /**
     * System prompt for topic generation.
     */
    protected function getSystemPrompt(string $language): string
    {
        if ($language === 'en') {
            return <<<PROMPT
You are a content strategist for bizmark.id, a business licensing and environmental permit consultancy in Indonesia.
Your job is to generate unique, engaging blog topic ideas that:
- Are relevant to foreign investors (PMA), expat entrepreneurs, and international businesses
- Cover business licensing, permits, regulations, compliance, and investment in Indonesia
- Are SEO-friendly with clear, descriptive titles
- Vary in category (tips, regulation, general, case-study, news)
- Are current and timely for 2026

Respond ONLY with valid JSON. No markdown, no code blocks, no extra text.
PROMPT;
        }

        return <<<PROMPT
Kamu adalah content strategist untuk bizmark.id, konsultan perizinan usaha dan dokumen lingkungan di Indonesia.
Tugasmu membuat ide topik blog yang unik dan menarik yang:
- Relevan untuk pemilik usaha lokal, developer, dan industri yang butuh perizinan
- Mencakup perizinan usaha, dokumen lingkungan, regulasi, compliance, dan tips bisnis
- SEO-friendly dengan judul yang jelas dan deskriptif
- Bervariasi dalam kategori (tips, regulation, general, case-study, news)
- Aktual dan timely untuk tahun 2026

Respond HANYA dengan JSON valid. Tanpa markdown, tanpa code block, tanpa teks tambahan.
PROMPT;
    }

    /**
     * Build the user prompt for topic generation.
     */
    protected function buildPrompt(int $count, string $language, array $categoryWeights, string $recentTitles): string
    {
        $categoryList = collect($categoryWeights)
            ->map(fn($weight, $cat) => "- {$cat}: ~{$weight}% of topics")
            ->implode("\n");

        $lang = $language === 'en' ? 'English' : 'Bahasa Indonesia';

        return <<<PROMPT
Generate exactly {$count} unique blog topic ideas in {$lang}.

**Category distribution (approximate):**
{$categoryList}

**Already existing topics (DO NOT duplicate or create very similar ones):**
- {$recentTitles}

**Required JSON format:**
[
  {
    "title": "Clear SEO-friendly title",
    "description": "1-2 sentence description of what the article covers",
    "category": "tips|regulation|general|case-study|news",
    "keywords": ["keyword1", "keyword2", "keyword3"],
    "tags": ["Tag1", "Tag2"],
    "target_market": "local|pma|both",
    "priority": 50-95
  }
]

Generate exactly {$count} topics. Output ONLY the JSON array, nothing else.
PROMPT;
    }

    /**
     * Parse AI response into array of topic data.
     */
    protected function parseTopicResponse(string $content): array
    {
        // Strip markdown code fences if present
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*/m', '', $content);
        $content = preg_replace('/```\s*$/m', '', $content);
        $content = trim($content);

        $topics = json_decode($content, true);

        if (!is_array($topics)) {
            Log::warning('⚠️ Failed to parse AI topic response', [
                'raw' => substr($content, 0, 500),
            ]);
            return [];
        }

        // Validate and clean each topic
        $valid = [];
        $allowedCategories = ['tips', 'regulation', 'general', 'case-study', 'news'];
        $allowedMarkets = ['local', 'pma', 'both'];

        foreach ($topics as $topic) {
            if (empty($topic['title'])) {
                continue;
            }

            $valid[] = [
                'title' => Str::limit(trim($topic['title']), 200),
                'description' => Str::limit(trim($topic['description'] ?? ''), 500),
                'category' => in_array($topic['category'] ?? '', $allowedCategories) ? $topic['category'] : 'general',
                'keywords' => is_array($topic['keywords'] ?? null) ? array_slice($topic['keywords'], 0, 6) : [],
                'tags' => is_array($topic['tags'] ?? null) ? array_slice($topic['tags'], 0, 5) : [],
                'target_market' => in_array($topic['target_market'] ?? '', $allowedMarkets) ? $topic['target_market'] : 'local',
                'priority' => max(10, min(95, (int) ($topic['priority'] ?? rand(50, 80)))),
            ];
        }

        return $valid;
    }

    /**
     * Check if a title already exists (exact or normalized match).
     */
    protected function titleExists(string $title, array $existingTitles): bool
    {
        $normalized = Str::lower(Str::ascii($title));

        foreach ($existingTitles as $existing) {
            $existingNorm = Str::lower(Str::ascii($existing));
            // Exact or near-exact match
            if ($normalized === $existingNorm) {
                return true;
            }
            // Simple similarity check (> 85% similar)
            similar_text($normalized, $existingNorm, $percent);
            if ($percent > 85) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine how many topics per language based on config.
     */
    protected function getLanguageDistribution(int $total, AutoPostConfig $config): array
    {
        $dist = $config->language_distribution;

        if (!$dist || empty($dist)) {
            return ['id' => $total]; // Default all Indonesian
        }

        $sum = array_sum($dist);
        if ($sum === 0) {
            return ['id' => $total];
        }

        $result = [];
        $allocated = 0;

        foreach ($dist as $lang => $weight) {
            $count = (int) round(($weight / $sum) * $total);
            if ($count > 0) {
                $result[$lang] = $count;
                $allocated += $count;
            }
        }

        // Distribute any remainder to first language
        if ($allocated < $total && !empty($result)) {
            $firstLang = array_key_first($result);
            $result[$firstLang] += ($total - $allocated);
        }

        return $result ?: ['id' => $total];
    }
}
