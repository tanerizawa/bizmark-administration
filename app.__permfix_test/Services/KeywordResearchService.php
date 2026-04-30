<?php

namespace App\Services;

use App\Models\Article;
use App\Models\KeywordCluster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KeywordResearchService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Research keywords for a specific service/topic and store as KeywordCluster
     */
    public function researchForService(string $serviceSlug, string $language = 'id'): ?KeywordCluster
    {
        $services = config('services_data', []);
        $service = collect($services)->firstWhere('slug', $serviceSlug);

        if (! $service) {
            Log::warning("KeywordResearch: Unknown service slug: {$serviceSlug}");

            return null;
        }

        // Check if cluster already exists for this service
        $existing = KeywordCluster::where('service_slug', $serviceSlug)
            ->where('language', $language)
            ->where('status', 'active')
            ->first();

        if ($existing && $existing->last_researched_at && $existing->last_researched_at->diffInDays(now()) < 30) {
            Log::info("KeywordResearch: Skipping {$serviceSlug}, researched recently");

            return $existing;
        }

        return $this->generateKeywordCluster($service, $language, $existing);
    }

    /**
     * Research keywords for a custom topic
     */
    public function researchForTopic(string $topic, string $category = 'general', string $language = 'id'): ?KeywordCluster
    {
        $existing = KeywordCluster::where('seed_keyword', $topic)
            ->where('language', $language)
            ->where('status', 'active')
            ->first();

        if ($existing && $existing->last_researched_at && $existing->last_researched_at->diffInDays(now()) < 30) {
            return $existing;
        }

        $serviceData = [
            'title' => $topic,
            'slug' => Str::slug($topic),
            'short_description' => $topic,
            'meta_keywords' => $topic,
            'category' => $category,
        ];

        return $this->generateKeywordCluster($serviceData, $language, $existing);
    }

    /**
     * Research all services in batch
     */
    public function researchAllServices(string $language = 'id'): array
    {
        $services = config('services_data', []);
        $results = [];

        foreach ($services as $service) {
            $cluster = $this->researchForService($service['slug'], $language);
            if ($cluster) {
                $results[] = [
                    'service' => $service['slug'],
                    'cluster_id' => $cluster->id,
                    'keywords_count' => count($cluster->keywords ?? []),
                    'long_tail_count' => count($cluster->long_tail_keywords ?? []),
                ];
            }
        }

        return $results;
    }

    /**
     * Use AI to generate keyword cluster
     */
    protected function generateKeywordCluster(array $serviceData, string $language, ?KeywordCluster $existing = null): ?KeywordCluster
    {
        $existingKeywords = [];
        if ($existing) {
            $existingKeywords = array_merge($existing->keywords ?? [], $existing->long_tail_keywords ?? []);
        }

        // Get existing article titles to avoid duplicates
        $existingTitles = Article::published()
            ->where('language', $language)
            ->pluck('title')
            ->take(50)
            ->toArray();

        $prompt = $this->buildKeywordPrompt($serviceData, $language, $existingKeywords, $existingTitles);

        $messages = [
            ['role' => 'system', 'content' => 'You are an SEO keyword research expert specializing in Indonesian environmental consulting and business permits. You understand search intent classification and long-tail keyword strategy. Always respond in pure JSON format.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->ai->chat($messages, [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.6,
            'max_tokens' => 3000,
        ]);

        if (! $response['success']) {
            Log::error('KeywordResearch: AI call failed', ['error' => $response['error'] ?? 'unknown']);

            return null;
        }

        $data = $this->parseAiResponse($response['content']);
        if (empty($data)) {
            return null;
        }

        // Upsert keyword cluster
        $cluster = $existing ?? new KeywordCluster;
        $cluster->fill([
            'seed_keyword' => $serviceData['title'],
            'cluster_name' => $data['cluster_name'] ?? $serviceData['title'],
            'search_intent' => $data['primary_intent'] ?? 'informational',
            'keywords' => $data['keywords'] ?? [],
            'long_tail_keywords' => $data['long_tail_keywords'] ?? [],
            'language' => $language,
            'category' => $serviceData['category'] ?? 'general',
            'service_slug' => $serviceData['slug'] ?? null,
            'estimated_volume' => $data['estimated_volume'] ?? 0,
            'difficulty_score' => $data['difficulty_score'] ?? 50,
            'priority' => $data['priority'] ?? 50,
            'last_researched_at' => now(),
        ]);
        $cluster->save();

        // Update articles_count
        $this->updateArticlesCount($cluster);

        Log::info("KeywordResearch: Generated cluster for '{$serviceData['title']}'", [
            'cluster_id' => $cluster->id,
            'keywords' => count($cluster->keywords ?? []),
            'long_tail' => count($cluster->long_tail_keywords ?? []),
        ]);

        return $cluster;
    }

    /**
     * Build the AI prompt for keyword research
     */
    protected function buildKeywordPrompt(array $service, string $language, array $existingKeywords, array $existingTitles): string
    {
        $lang = $language === 'id' ? 'Indonesia' : 'English';
        $existingStr = ! empty($existingKeywords) ? "\nKeyword yang sudah ada (jangan duplikasi): ".implode(', ', array_slice($existingKeywords, 0, 20)) : '';
        $titlesStr = ! empty($existingTitles) ? "\nJudul artikel yang sudah ada: ".implode(', ', array_slice($existingTitles, 0, 15)) : '';

        return <<<PROMPT
Lakukan riset keyword untuk topik: "{$service['title']}"
Deskripsi: {$service['short_description']}
Meta keywords referensi: {$service['meta_keywords']}
Bahasa target: {$lang}
{$existingStr}
{$titlesStr}

Hasilkan JSON dengan struktur:
{
  "cluster_name": "Nama cluster keyword",
  "primary_intent": "informational|transactional|commercial|navigational",
  "keywords": ["keyword utama 1", "keyword utama 2", ...max 15],
  "long_tail_keywords": ["long tail 3-5 kata 1", "long tail 2", ...max 20],
  "estimated_volume": 500,
  "difficulty_score": 40,
  "priority": 75,
  "content_suggestions": [
    {"title": "Judul artikel baru 1", "intent": "informational", "target_keyword": "keyword target"},
    {"title": "Judul artikel baru 2", "intent": "transactional", "target_keyword": "keyword target"}
  ]
}

Fokus pada:
1. Keyword Bahasa {$lang} yang natural (bukan terjemahan kaku)
2. Long-tail keywords 3-5 kata yang spesifik
3. Variasi search intent (informational, transactional, commercial)
4. Keyword lokal Indonesia (nama kota, regulasi spesifik)
5. Question keywords (apa, bagaimana, berapa, dimana)
6. Competitor keywords (vs, perbandingan, alternatif)
7. Year-based keywords (2026, terbaru, terkini)

Jangan duplikasi judul artikel yang sudah ada. Prioritaskan keyword gap.
Respond ONLY with the JSON object, no markdown fences.
PROMPT;
    }

    /**
     * Parse AI response JSON
     */
    protected function parseAiResponse(string $content): array
    {
        $content = trim($content);

        // Strip markdown code fences if present
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('KeywordResearch: Failed to parse AI response', [
                'error' => json_last_error_msg(),
                'content_preview' => substr($content, 0, 200),
            ]);

            return [];
        }

        return $data;
    }

    /**
     * Count how many existing articles cover this cluster's keywords
     */
    protected function updateArticlesCount(KeywordCluster $cluster): void
    {
        $allKeywords = array_merge($cluster->keywords ?? [], $cluster->long_tail_keywords ?? []);
        if (empty($allKeywords)) {
            return;
        }

        $count = 0;
        foreach (array_slice($allKeywords, 0, 10) as $keyword) {
            $count += Article::published()
                ->where('language', $cluster->language)
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'ILIKE', "%{$keyword}%")
                        ->orWhere('meta_keywords', 'ILIKE', "%{$keyword}%");
                })
                ->count();
        }

        // Deduplicate rough count
        $cluster->articles_count = min($count, Article::published()->where('language', $cluster->language)->count());
        $cluster->save();
    }
}
