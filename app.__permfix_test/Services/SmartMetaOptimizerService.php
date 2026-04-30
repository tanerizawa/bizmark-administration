<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmartMetaOptimizerService
{
    protected OpenRouterService $ai;

    protected IndexNowService $indexNow;

    public function __construct(OpenRouterService $ai, IndexNowService $indexNow)
    {
        $this->ai = $ai;
        $this->indexNow = $indexNow;
    }

    /**
     * Optimize meta tags for a batch of articles
     */
    public function optimizeBatch(int $limit = 5, string $language = 'id'): array
    {
        // Find articles with weak meta — short meta_description, missing meta_keywords, generic titles
        $articles = Article::published()
            ->where('language', $language)
            ->where(function ($q) {
                $q->whereNull('meta_description')
                    ->orWhere('meta_description', '')
                    ->orWhereRaw('LENGTH(meta_description) < 80')
                    ->orWhereNull('meta_keywords')
                    ->orWhere('meta_keywords', '')
                    ->orWhereNull('meta_title')
                    ->orWhere('meta_title', '');
            })
            ->orderBy('views_count', 'desc') // Prioritize high-traffic articles
            ->take($limit)
            ->get();

        if ($articles->isEmpty()) {
            // If no weak-meta articles, optimize oldest-optimized ones
            $articles = Article::published()
                ->where('language', $language)
                ->orderBy('updated_at', 'asc')
                ->take($limit)
                ->get();
        }

        $results = [];
        foreach ($articles as $article) {
            $result = $this->optimizeArticle($article);
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Optimize meta tags for a single article
     */
    public function optimizeArticle(Article $article): array
    {
        $result = [
            'article_id' => $article->id,
            'title' => $article->title,
            'status' => 'skipped',
            'changes' => [],
        ];

        try {
            $optimized = $this->generateOptimizedMeta($article);
            if (empty($optimized)) {
                return $result;
            }

            $changes = [];

            // Optimize meta_title (max 60 chars, keyword-rich, click-worthy)
            if (! empty($optimized['meta_title']) && $optimized['meta_title'] !== $article->meta_title) {
                $article->meta_title = Str::limit($optimized['meta_title'], 60, '');
                $changes[] = 'meta_title';
            }

            // Optimize meta_description (120-155 chars, CTA, keyword-rich)
            if (! empty($optimized['meta_description']) && $optimized['meta_description'] !== $article->meta_description) {
                $article->meta_description = Str::limit($optimized['meta_description'], 155, '');
                $changes[] = 'meta_description';
            }

            // Optimize meta_keywords (comma-separated, relevant)
            if (! empty($optimized['meta_keywords']) && $optimized['meta_keywords'] !== $article->meta_keywords) {
                $article->meta_keywords = $optimized['meta_keywords'];
                $changes[] = 'meta_keywords';
            }

            // Optimize title for CTR (only if significantly better)
            if (! empty($optimized['improved_title']) && $optimized['improved_title'] !== $article->title) {
                $article->title = $optimized['improved_title'];
                $changes[] = 'title';
            }

            // Optimize excerpt
            if (! empty($optimized['excerpt']) && $optimized['excerpt'] !== $article->excerpt) {
                $article->excerpt = $optimized['excerpt'];
                $changes[] = 'excerpt';
            }

            if (! empty($changes)) {
                $article->save();
                $this->indexNow->submitUrl($article->getUrl());
                $result['status'] = 'optimized';
                $result['changes'] = $changes;

                Log::info("MetaOptimizer: Article #{$article->id} optimized", [
                    'title' => $article->title,
                    'changes' => $changes,
                ]);
            }

        } catch (\Throwable $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            Log::error("MetaOptimizer: Failed for article #{$article->id}", ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Generate optimized meta using AI
     */
    protected function generateOptimizedMeta(Article $article): array
    {
        $year = date('Y');
        $contentPreview = strip_tags(substr($article->content, 0, 500));

        $prompt = <<<PROMPT
Optimasi SEO meta tags untuk artikel ini:

Judul: {$article->title}
Meta Title saat ini: {$article->meta_title}
Meta Description saat ini: {$article->meta_description}
Meta Keywords saat ini: {$article->meta_keywords}
Excerpt saat ini: {$article->excerpt}
Kategori: {$article->category}
Bahasa: {$article->language}
Preview konten: {$contentPreview}

Hasilkan JSON:
{
  "meta_title": "Judul SEO optimal (max 60 karakter, include keyword utama + tahun {$year} + brand 'Bizmark')",
  "meta_description": "Deskripsi meta yang menarik klik (120-155 karakter, include CTA, keyword utama, benefit)",
  "meta_keywords": "keyword1, keyword2, keyword3, long tail keyword, keyword {$year}",
  "improved_title": "Judul artikel yang lebih menarik (hanya jika bisa ditingkatkan signifikan, null jika sudah baik)",
  "excerpt": "Ringkasan menarik 1-2 kalimat yang membuat orang ingin baca"
}

Rules:
- meta_title: HARUS include keyword utama di awal, tahun {$year}, dan "Bizmark" di akhir. Max 60 char.
- meta_description: Mulai dengan benefit/hook, include CTA (Pelajari lebih lanjut, Baca panduan, dll). 120-155 char.
- meta_keywords: 5-8 keywords, prioritaskan long-tail. Include variasi Bahasa Indonesia natural.
- improved_title: Set null jika judul saat ini sudah baik. Jangan ubah hanya untuk kosmetik.
- excerpt: Informatif dan engaging, max 2 kalimat.
- Semua dalam Bahasa Indonesia yang natural, bukan terjemahan kaku.

Respond ONLY with the JSON object, no markdown fences.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => 'You are an SEO copywriter expert specializing in optimizing meta tags for maximum CTR and search ranking. Always respond in pure JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->ai->chat($messages, [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.4,
            'max_tokens' => 1500,
        ]);

        if (! $response['success']) {
            return [];
        }

        $content = trim($response['content']);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $data = json_decode($content, true);
        if (! is_array($data)) {
            return [];
        }

        // Don't change title if AI returned null
        if (isset($data['improved_title']) && $data['improved_title'] === null) {
            unset($data['improved_title']);
        }

        return $data;
    }
}
