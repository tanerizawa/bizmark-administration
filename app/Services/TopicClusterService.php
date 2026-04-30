<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\KeywordCluster;
use App\Models\TopicCluster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopicClusterService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate topic clusters from services data + existing keywords
     */
    public function generateClusters(string $language = 'id'): array
    {
        $services = config('services_data', []);
        $results = [];

        foreach ($services as $service) {
            $cluster = $this->generateForService($service, $language);
            if ($cluster) {
                $results[] = [
                    'service' => $service['slug'],
                    'pillar' => $cluster->pillar_title,
                    'subtopics' => count($cluster->subtopics ?? []),
                    'articles_mapped' => count($cluster->article_ids ?? []),
                ];
            }
        }

        return $results;
    }

    /**
     * Generate or update a topic cluster for a service
     */
    public function generateForService(array $service, string $language = 'id'): ?TopicCluster
    {
        $existing = TopicCluster::where('service_slug', $service['slug'])
            ->where('language', $language)
            ->first();

        // Get keyword clusters for context
        $keywordClusters = KeywordCluster::where('service_slug', $service['slug'])
            ->where('language', $language)
            ->active()
            ->get();

        // Get existing articles in this service area
        $articles = Article::published()
            ->where('language', $language)
            ->where(function ($q) use ($service) {
                $q->where('title', 'LIKE', "%{$service['title']}%")
                    ->orWhere('meta_keywords', 'LIKE', "%{$service['slug']}%");
                foreach (explode(',', $service['meta_keywords'] ?? '') as $kw) {
                    $kw = trim($kw);
                    if (strlen($kw) > 3) {
                        $q->orWhere('title', 'LIKE', "%{$kw}%");
                    }
                }
            })
            ->get(['id', 'title', 'slug', 'category', 'tags']);

        $subtopics = $this->generateSubtopics($service, $keywordClusters, $articles, $language);

        if (empty($subtopics)) {
            return $existing;
        }

        $year = date('Y');
        $cluster = $existing ?? new TopicCluster;
        $cluster->fill([
            'pillar_title' => "Panduan Lengkap {$service['title']} {$year}",
            'pillar_slug' => "panduan-{$service['slug']}-{$year}",
            'pillar_description' => "Panduan komprehensif tentang {$service['title']}: persyaratan, prosedur, biaya, tips, dan regulasi terbaru {$year}.",
            'service_slug' => $service['slug'],
            'language' => $language,
            'subtopics' => $subtopics,
            'article_ids' => $articles->pluck('id')->toArray(),
            'keyword_cluster_ids' => $keywordClusters->pluck('id')->toArray(),
        ]);
        $cluster->save();

        Log::info("TopicCluster: Generated for '{$service['title']}'", [
            'cluster_id' => $cluster->id,
            'subtopics' => count($subtopics),
            'articles_mapped' => $articles->count(),
        ]);

        return $cluster;
    }

    /**
     * Generate subtopics using AI
     */
    protected function generateSubtopics(array $service, $keywordClusters, $articles, string $language): array
    {
        $keywordContext = $keywordClusters->map(function ($kc) {
            return implode(', ', array_slice($kc->long_tail_keywords ?? $kc->keywords ?? [], 0, 5));
        })->implode('; ');

        $articleTitles = $articles->pluck('title')->implode(', ');

        $prompt = <<<PROMPT
Buat topic cluster (pilar + subtopics) untuk layanan: "{$service['title']}"
Deskripsi: {$service['short_description']}

Keyword clusters yang sudah diriset: {$keywordContext}
Artikel yang sudah ada: {$articleTitles}

Hasilkan JSON array subtopics untuk membangun topical authority. Setiap subtopic harus:
1. Spesifik dan actionable
2. Target search intent berbeda (info, transactional, how-to)
3. Bisa jadi artikel terpisah yang saling interlink
4. Mencakup semua aspek layanan ini

Format JSON:
[
  {"title": "Apa Itu [Service]? Pengertian dan Dasar Hukum", "type": "definisi", "priority": "high"},
  {"title": "Persyaratan [Service]: Dokumen yang Diperlukan", "type": "persyaratan", "priority": "high"},
  {"title": "Prosedur Pengurusan [Service] Step-by-Step", "type": "how-to", "priority": "high"},
  {"title": "Biaya [Service] Terbaru 2026", "type": "transactional", "priority": "high"},
  {"title": "Berapa Lama Proses [Service]?", "type": "faq", "priority": "medium"},
  {"title": "Tips Mempercepat Proses [Service]", "type": "tips", "priority": "medium"},
  {"title": "Sanksi Jika Tidak Memiliki [Service]", "type": "regulasi", "priority": "medium"},
  {"title": "Studi Kasus: Perusahaan yang Berhasil Mengurus [Service]", "type": "case-study", "priority": "low"}
]

Hasilkan 8-12 subtopics. Jangan duplikat judul artikel yang sudah ada.
Respond ONLY with the JSON array, no markdown fences.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => 'You are a content strategist specializing in topic cluster architecture for SEO. Always respond in pure JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->ai->chat($messages, [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.5,
            'max_tokens' => 2000,
        ]);

        if (! $response['success']) {
            Log::error('TopicCluster: AI call failed', ['service' => $service['slug']]);

            return [];
        }

        $content = trim($response['content']);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Build internal links between articles in the same cluster
     */
    public function buildInternalLinks(TopicCluster $cluster): int
    {
        $articles = $cluster->getArticles();
        if ($articles->count() < 2) {
            return 0;
        }

        $linksBuilt = 0;

        foreach ($articles as $article) {
            $siblings = $articles->where('id', '!=', $article->id)->take(3);
            $content = $article->content;
            $modified = false;

            foreach ($siblings as $sibling) {
                $linkHtml = '<a href="'.e($sibling->getUrl()).'">'.e($sibling->title).'</a>';

                // Don't add if link already exists
                if (str_contains($content, $sibling->getUrl())) {
                    continue;
                }

                // Find a paragraph to inject the link context
                $anchor = $this->findAnchorPoint($content, $sibling->title);
                if ($anchor) {
                    $relatedText = "\n<p class=\"related-link\">Baca juga: {$linkHtml}</p>\n";
                    $content = substr_replace($content, $relatedText, $anchor['position'], 0);
                    $modified = true;
                    $linksBuilt++;
                }
            }

            if ($modified) {
                $article->content = $content;
                $article->save();
            }
        }

        $cluster->internal_links_built = $linksBuilt;
        $cluster->save();

        return $linksBuilt;
    }

    /**
     * Convert TopicCluster subtopics into ArticleTopic records for auto-posting.
     * This bridges the gap between cluster generation and the content pipeline.
     */
    public function convertSubtopicsToArticleTopics(TopicCluster $cluster, int $limit = 0): array
    {
        $subtopics = $cluster->subtopics ?? [];
        if (empty($subtopics)) {
            return [];
        }

        $service = collect(config('services_data', []))
            ->firstWhere('slug', $cluster->service_slug);

        $category = $service['category'] ?? 'perizinan';
        $created = [];
        $skipped = 0;

        foreach ($subtopics as $subtopic) {
            if ($limit > 0 && count($created) >= $limit) {
                break;
            }

            $title = $subtopic['title'] ?? '';
            if (empty($title)) {
                continue;
            }

            $slug = Str::slug($title);

            // Skip if article or topic already exists
            $existsAsArticle = Article::where('slug', $slug)->exists();
            $existsAsTopic = ArticleTopic::where('slug', $slug)
                ->whereIn('status', ['pending', 'processing', 'published'])
                ->exists();

            if ($existsAsArticle || $existsAsTopic) {
                $skipped++;

                continue;
            }

            $priority = match ($subtopic['priority'] ?? 'medium') {
                'high' => 80,
                'medium' => 50,
                'low' => 30,
                default => 50,
            };

            $keywords = [];
            if ($service) {
                $keywords = array_filter(
                    array_map('trim', explode(',', $service['meta_keywords'] ?? ''))
                );
            }

            $topic = ArticleTopic::create([
                'title' => $title,
                'slug' => $slug,
                'description' => "Artikel dari topic cluster: {$cluster->pillar_title}",
                'category' => $category,
                'language' => $cluster->language,
                'target_market' => 'id',
                'keywords' => $keywords,
                'tags' => [$cluster->service_slug, $subtopic['type'] ?? 'article'],
                'status' => 'pending',
                'priority' => $priority,
                'topic_cluster_id' => $cluster->id,
            ]);

            $created[] = [
                'topic_id' => $topic->id,
                'title' => $title,
                'priority' => $priority,
            ];
        }

        Log::info('TopicCluster: Converted subtopics to ArticleTopics', [
            'cluster_id' => $cluster->id,
            'pillar' => $cluster->pillar_title,
            'created' => count($created),
            'skipped' => $skipped,
        ]);

        return $created;
    }

    /**
     * Convert subtopics from ALL clusters into ArticleTopics.
     */
    public function convertAllClustersToTopics(int $limitPerCluster = 0): array
    {
        $clusters = TopicCluster::all();
        $results = [];

        foreach ($clusters as $cluster) {
            $converted = $this->convertSubtopicsToArticleTopics($cluster, $limitPerCluster);
            if (! empty($converted)) {
                $results[$cluster->service_slug] = [
                    'pillar' => $cluster->pillar_title,
                    'topics_created' => count($converted),
                    'topics' => $converted,
                ];
            }
        }

        return $results;
    }

    /**
     * Find a suitable anchor point in content for inserting a "Baca juga" link
     */
    protected function findAnchorPoint(string $content, string $targetTitle): ?array
    {
        // Find paragraph endings after first 20% of content
        $minPosition = (int) (strlen($content) * 0.2);
        $maxPosition = (int) (strlen($content) * 0.8);

        // Find </p> tags within the target zone
        preg_match_all('/<\/p>/i', $content, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $match) {
            $pos = $match[1] + strlen($match[0]);
            if ($pos >= $minPosition && $pos <= $maxPosition) {
                return ['position' => $pos];
            }
        }

        return null;
    }
}
