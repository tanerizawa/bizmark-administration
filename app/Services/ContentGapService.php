<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\ContentGap;
use App\Models\KeywordCluster;
use App\Models\TopicCluster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentGapService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Analyze content gaps across all keyword clusters
     */
    public function analyzeAll(string $language = 'id'): array
    {
        $clusters = KeywordCluster::where('language', $language)->active()->get();
        $results = [];

        foreach ($clusters as $cluster) {
            $gaps = $this->analyzeCluster($cluster);
            if (! empty($gaps)) {
                $results[$cluster->cluster_name] = $gaps;
            }
        }

        return $results;
    }

    /**
     * Analyze content gaps for a specific keyword cluster
     */
    public function analyzeCluster(KeywordCluster $cluster): array
    {
        // 1. Get all keywords from this cluster
        $allKeywords = array_merge($cluster->keywords ?? [], $cluster->long_tail_keywords ?? []);

        // 2. Find which keywords are already covered by articles
        $coveredKeywords = [];
        $articles = Article::published()
            ->where('language', $cluster->language)
            ->get(['id', 'title', 'meta_keywords', 'meta_title']);

        foreach ($allKeywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            foreach ($articles as $article) {
                $haystack = strtolower($article->title.' '.$article->meta_keywords.' '.$article->meta_title);
                if (str_contains($haystack, $keyword)) {
                    $coveredKeywords[] = $keyword;
                    break;
                }
            }
        }

        // 3. Find uncovered keywords
        $uncoveredKeywords = array_diff(
            array_map('strtolower', array_map('trim', $allKeywords)),
            $coveredKeywords
        );

        if (empty($uncoveredKeywords)) {
            return [];
        }

        // 4. Use AI to suggest content for uncovered keywords
        return $this->generateGapSuggestions($cluster, array_values($uncoveredKeywords));
    }

    /**
     * Generate content suggestions for uncovered keywords
     */
    protected function generateGapSuggestions(KeywordCluster $cluster, array $uncoveredKeywords): array
    {
        $keywordsStr = implode(', ', array_slice($uncoveredKeywords, 0, 15));

        $prompt = <<<PROMPT
Kamu adalah content strategist. Berikut keyword yang BELUM dicakup oleh artikel kami:
Cluster: {$cluster->cluster_name}
Service: {$cluster->service_slug}
Keyword belum tercakup: {$keywordsStr}

Buat saran artikel untuk menutup gap konten ini.

Hasilkan JSON array:
[
  {
    "title": "Judul Artikel yang Disarankan",
    "description": "Deskripsi singkat apa yang harus dicakup",
    "target_keyword": "keyword utama yang ditarget",
    "intent": "informational|transactional|commercial",
    "category": "general|tips|regulation|case-study|news",
    "priority": 80
  }
]

Hasilkan 3-5 saran artikel. Pastikan judul natural dan menarik klik.
Respond ONLY with the JSON array, no markdown fences.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => 'You are a content gap analysis expert. Always respond in pure JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->ai->chat($messages, [
            'model' => config('services.openrouter.default_model', 'openrouter/free'),
            'temperature' => 0.5,
            'max_tokens' => 2000,
        ]);

        if (! $response['success']) {
            return [];
        }

        $content = trim($response['content']);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $suggestions = json_decode($content, true);
        if (! is_array($suggestions)) {
            return [];
        }

        // Store as ContentGap records
        $gaps = [];
        $topicCluster = TopicCluster::where('service_slug', $cluster->service_slug)
            ->where('language', $cluster->language)
            ->first();

        foreach ($suggestions as $suggestion) {
            $title = $suggestion['title'] ?? '';
            if (empty($title)) {
                continue;
            }

            // Skip if similar gap already exists
            $existingGap = ContentGap::where('target_keyword', $suggestion['target_keyword'] ?? '')
                ->where('status', '!=', 'dismissed')
                ->first();
            if ($existingGap) {
                continue;
            }

            $gap = ContentGap::create([
                'suggested_title' => $title,
                'suggested_slug' => Str::slug($title),
                'description' => $suggestion['description'] ?? '',
                'target_keyword' => $suggestion['target_keyword'] ?? '',
                'search_intent' => $suggestion['intent'] ?? 'informational',
                'category' => $suggestion['category'] ?? 'general',
                'service_slug' => $cluster->service_slug,
                'language' => $cluster->language,
                'priority' => $suggestion['priority'] ?? 50,
                'topic_cluster_id' => $topicCluster?->id,
            ]);

            $gaps[] = [
                'id' => $gap->id,
                'title' => $gap->suggested_title,
                'target_keyword' => $gap->target_keyword,
                'priority' => $gap->priority,
            ];
        }

        Log::info('ContentGap: Found '.count($gaps)." gaps for cluster '{$cluster->cluster_name}'");

        return $gaps;
    }

    /**
     * Convert pending content gaps to article topics for auto-generation
     */
    public function queueTopGaps(int $limit = 5): array
    {
        $gaps = ContentGap::where('status', 'pending')
            ->orderByDesc('priority')
            ->take($limit)
            ->get();

        $queued = [];
        foreach ($gaps as $gap) {
            // Check if article topic already exists for this
            $existingTopic = ArticleTopic::where('title', $gap->suggested_title)
                ->orWhere('slug', $gap->suggested_slug)
                ->first();

            if ($existingTopic) {
                $gap->update(['status' => 'dismissed', 'article_topic_id' => $existingTopic->id]);

                continue;
            }

            // Create article topic
            $topic = ArticleTopic::create([
                'title' => $gap->suggested_title,
                'slug' => $gap->suggested_slug,
                'description' => $gap->description,
                'category' => $gap->category,
                'language' => $gap->language,
                'keywords' => [$gap->target_keyword],
                'status' => 'pending',
                'priority' => $gap->priority,
            ]);

            $gap->update(['status' => 'queued', 'article_topic_id' => $topic->id]);

            $queued[] = [
                'gap_id' => $gap->id,
                'topic_id' => $topic->id,
                'title' => $gap->suggested_title,
            ];
        }

        return $queued;
    }
}
