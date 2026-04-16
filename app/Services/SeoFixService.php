<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SeoScore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SeoFixService
{
    private const OPENROUTER_API_URL = 'https://openrouter.ai/api/v1/chat/completions';
    private const REQUEST_TIMEOUT = 60;
    private const CONTENT_EXPAND_TIMEOUT = 90;

    protected SeoScoringService $scorer;

    public function __construct(SeoScoringService $scorer)
    {
        $this->scorer = $scorer;
    }

    /**
     * AI-powered fix for a single article.
     * Uses OpenRouter to generate optimized meta data.
     */
    public function fixArticle(Article $article): array
    {
        // Score first if not scored yet
        $score = SeoScore::where('article_id', $article->id)->first();
        if (!$score) {
            $score = $this->scorer->scoreArticle($article);
        }
        $recommendations = $score->recommendations ?? [];
        $factors = $score->factors ?? [];
        $oldScore = $score->total_score ?? 0;

        // Gather current state
        $current = [
            'title' => $article->title,
            'meta_title' => $article->meta_title,
            'meta_description' => $article->meta_description,
            'meta_keywords' => $article->meta_keywords,
            'excerpt' => $article->excerpt,
            'category' => $article->category,
            'tags' => $article->tags,
            'slug' => $article->slug,
            'content_preview' => Str::limit(strip_tags($article->content ?? ''), 500),
            'word_count' => $article->content ? str_word_count(strip_tags($article->content)) : 0,
        ];

        $fixes = [];
        $changed = false;

        // ──────────────────────────────────────────────────────
        // Phase 1: AI meta optimization (meta_title, desc, keywords, excerpt)
        // ──────────────────────────────────────────────────────
        $needsMeta = ($factors['title']['score'] ?? 0) < ($factors['title']['max'] ?? 15)
            || ($factors['meta_description']['score'] ?? 0) < ($factors['meta_description']['max'] ?? 12)
            || ($factors['meta_keywords']['score'] ?? 0) < ($factors['meta_keywords']['max'] ?? 5)
            || ($factors['excerpt_schema']['score'] ?? 0) < ($factors['excerpt_schema']['max'] ?? 7);

        $aiResult = null;
        if ($needsMeta) {
            $aiResult = $this->callAIOptimizer($current, $recommendations, $factors);
            if ($aiResult) {
                $metaFixes = $this->applyAIMetaResult($article, $aiResult);
                $fixes = array_merge($fixes, $metaFixes['fixes']);
                $changed = $changed || $metaFixes['changed'];
            }
        }

        // ──────────────────────────────────────────────────────
        // Phase 2: Content expansion via AI (if content too short)
        // ──────────────────────────────────────────────────────
        $contentScore = $factors['content']['score'] ?? 0;
        $contentMax = $factors['content']['max'] ?? 20;
        $wordCount = $current['word_count'];

        if ($contentScore < ($contentMax - 4) && $wordCount < 800) {
            $contentResult = $this->callAIContentExpander($article, $recommendations);
            if ($contentResult) {
                $article->content = $contentResult;
                $article->reading_time = max(1, ceil(str_word_count(strip_tags($contentResult)) / 200));
                $fixes[] = '🤖 Konten diperkaya AI (heading, paragraf, list, bold ditambahkan)';
                $changed = true;

                // Content refresh: update published_at for freshness bonus
                // This is legitimate SEO practice when content is substantially updated
                $article->published_at = now();
                $fixes[] = '📅 Tanggal publish diperbarui (content refresh)';
            }
        }

        // ──────────────────────────────────────────────────────
        // Phase 3: Rule-based fallback fixes
        // ──────────────────────────────────────────────────────
        $ruleFixes = $this->applyRuleBasedFixes($article, $factors);
        if (!empty($ruleFixes['fixes'])) {
            $fixes = array_merge($fixes, $ruleFixes['fixes']);
            $changed = $changed || $ruleFixes['changed'];
        }

        // ──────────────────────────────────────────────────────
        // Phase 4: Content enhancement (internal links, images)
        // ──────────────────────────────────────────────────────
        $contentFixes = $this->enhanceContent($article, $factors);
        if (!empty($contentFixes['fixes'])) {
            $fixes = array_merge($fixes, $contentFixes['fixes']);
            $changed = $changed || $contentFixes['changed'];
        }

        // ──────────────────────────────────────────────────────
        // Phase 5: Save, refresh, re-score
        // ──────────────────────────────────────────────────────
        if ($changed) {
            $article->updated_at = now();
            $article->saveQuietly();
        }

        $article->refresh();
        $newScore = $this->scorer->scoreArticle($article);

        // Clear dashboard cache
        Cache::forget('seo_dashboard_stats_7days');
        Cache::forget('seo_dashboard_stats_30days');
        Cache::forget('seo_dashboard_stats_90days');

        return [
            'article_id' => $article->id,
            'title' => $article->title,
            'fixes_applied' => $fixes,
            'fixes_count' => count($fixes),
            'old_score' => $oldScore,
            'new_score' => $newScore->total_score,
            'score_change' => $newScore->total_score - $oldScore,
            'new_grade' => $newScore->grade,
            'remaining_issues' => count($newScore->recommendations ?? []),
            'ai_powered' => $aiResult !== null,
        ];
    }

    /**
     * Batch fix multiple articles with AI.
     */
    public function fixBatch(array $articleIds = [], int $threshold = 80): array
    {
        if (empty($articleIds)) {
            $articleIds = SeoScore::where('total_score', '<', $threshold)
                ->pluck('article_id')
                ->toArray();

            $unscoredIds = Article::where('status', 'published')
                ->whereDoesntHave('seoScore')
                ->pluck('id')
                ->toArray();

            $articleIds = array_unique(array_merge($articleIds, $unscoredIds));
        }

        $results = [
            'total_processed' => 0,
            'total_fixed' => 0,
            'total_fixes' => 0,
            'avg_new_score' => 0,
            'avg_score_change' => 0,
            'ai_powered_count' => 0,
            'details' => [],
        ];

        $scoreSum = 0;
        $changeSum = 0;

        foreach ($articleIds as $id) {
            $article = Article::find($id);
            if (!$article) continue;

            $result = $this->fixArticle($article);
            $results['total_processed']++;

            if ($result['fixes_count'] > 0) {
                $results['total_fixed']++;
                $results['total_fixes'] += $result['fixes_count'];
            }

            if ($result['ai_powered']) {
                $results['ai_powered_count']++;
            }

            $scoreSum += $result['new_score'];
            $changeSum += $result['score_change'];
            $results['details'][] = $result;
        }

        $n = $results['total_processed'];
        $results['avg_new_score'] = $n > 0 ? round($scoreSum / $n, 1) : 0;
        $results['avg_score_change'] = $n > 0 ? round($changeSum / $n, 1) : 0;

        return $results;
    }

    // ─── AI Optimizer ────────────────────────────────────────

    protected function applyAIMetaResult(Article $article, array $aiResult): array
    {
        $fixes = [];
        $changed = false;

        if (!empty($aiResult['meta_title']) && $aiResult['meta_title'] !== $article->meta_title) {
            $article->meta_title = Str::limit($aiResult['meta_title'], 65, '');
            $fixes[] = '🤖 Meta title dioptimasi AI: "' . Str::limit($article->meta_title, 40) . '"';
            $changed = true;
        }

        if (!empty($aiResult['meta_description']) && $aiResult['meta_description'] !== $article->meta_description) {
            $article->meta_description = Str::limit($aiResult['meta_description'], 160, '');
            $fixes[] = '🤖 Meta description dioptimasi AI';
            $changed = true;
        }

        if (!empty($aiResult['meta_keywords'])) {
            $newKw = is_array($aiResult['meta_keywords'])
                ? implode(', ', $aiResult['meta_keywords'])
                : $aiResult['meta_keywords'];
            if ($newKw !== $article->meta_keywords) {
                $article->meta_keywords = $newKw;
                $fixes[] = '🤖 Meta keywords dioptimasi AI';
                $changed = true;
            }
        }

        if (!empty($aiResult['excerpt']) && (empty($article->excerpt) || mb_strlen($article->excerpt) < 80)) {
            $article->excerpt = Str::limit($aiResult['excerpt'], 250, '');
            $fixes[] = '🤖 Excerpt dioptimasi AI';
            $changed = true;
        }

        // AI-generated tags
        if (!empty($aiResult['tags']) && is_array($aiResult['tags'])) {
            $currentTags = $article->tags ?? [];
            if (count($currentTags) < 2) {
                $article->tags = array_unique(array_merge($currentTags, array_slice($aiResult['tags'], 0, 5)));
                $fixes[] = '🤖 Tags ditambahkan AI';
                $changed = true;
            }
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }

    protected function callAIOptimizer(array $current, array $recommendations, array $factors): ?array
    {
        $apiKey = config('services.openrouter.api_key');
        if (empty($apiKey)) {
            Log::warning('SeoFixService: OpenRouter API key not configured, using rule-based only');
            return null;
        }

        $model = config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash');

        $systemPrompt = <<<'PROMPT'
Kamu adalah SEO expert Indonesia khusus untuk website konsultan perizinan bisnis "Bizmark" (bizmark.id).
Tugasmu: OPTIMALKAN meta data artikel agar skor SEO naik signifikan.

ATURAN KETAT:
1. meta_title: MAKSIMAL 55 chars (KETAT!), WAJIB ada tahun (2026), WAJIB ada "Bizmark", gunakan power words (Lengkap, Panduan, Tips, Terbaru). Jangan pernah lebih dari 55 karakter!
2. meta_description: 130-155 chars, WAJIB ada CTA (Konsultasi gratis!, Hubungi Bizmark!, Pelajari selengkapnya di Bizmark), keyword dari judul
3. meta_keywords: 5-8 keywords relevan dipisah koma, termasuk "Bizmark" dan variasi long-tail
4. excerpt: 100-200 chars, ringkasan menarik dengan CTA, informatif
5. tags: array 3-5 tags relevan untuk kategorisasi

Output JSON saja, tanpa markdown:
{
  "meta_title": "...",
  "meta_description": "...",
  "meta_keywords": "keyword1, keyword2, ...",
  "excerpt": "...",
  "tags": ["tag1", "tag2", "tag3"]
}
PROMPT;

        $issueList = !empty($recommendations)
            ? "Masalah SEO: " . implode('; ', array_slice($recommendations, 0, 5))
            : 'Tidak ada masalah spesifik';

        $factorSummary = collect($factors)->map(function ($f, $key) {
            return "$key: {$f['score']}/{$f['max']}";
        })->implode(', ');

        $userPrompt = <<<PROMPT
Optimalkan SEO metadata untuk artikel berikut:

Judul: {$current['title']}
Kategori: {$current['category']}
Tags: {$this->formatTags($current['tags'])}
Slug: {$current['slug']}

Meta title saat ini: {$current['meta_title']}
Meta description saat ini: {$current['meta_description']}
Meta keywords saat ini: {$current['meta_keywords']}
Excerpt saat ini: {$current['excerpt']}

Skor faktor: {$factorSummary}
{$issueList}

Preview konten: {$current['content_preview']}

Buat versi yang LEBIH BAIK untuk semua field. Pastikan meta_title mengandung tahun 2026 dan brand Bizmark.
PROMPT;

        try {
            $payload = [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 800,
                'response_format' => ['type' => 'json_object'],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Bizmark SEO Optimizer',
            ])->timeout(self::REQUEST_TIMEOUT)->post(self::OPENROUTER_API_URL, $payload);

            if (!$response->successful()) {
                Log::warning('SeoFixService: AI API error', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 200),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content', '');
            if (empty($content)) return null;

            // Parse JSON from response
            $parsed = json_decode($content, true);
            if (!$parsed || !is_array($parsed)) {
                // Try to extract JSON from markdown code block
                if (preg_match('/\{[^{}]*"meta_title"[^{}]*\}/s', $content, $m)) {
                    $parsed = json_decode($m[0], true);
                }
            }

            if (!$parsed || empty($parsed['meta_title'])) {
                Log::warning('SeoFixService: Could not parse AI response', ['content' => Str::limit($content, 300)]);
                return null;
            }

            Log::info('SeoFixService: AI optimization successful', [
                'model' => $model,
                'tokens' => $response->json('usage.total_tokens', 0),
            ]);

            return $parsed;

        } catch (\Exception $e) {
            Log::warning('SeoFixService: AI exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── AI Content Expander ────────────────────────────────

    protected function callAIContentExpander(Article $article, array $recommendations): ?string
    {
        $apiKey = config('services.openrouter.api_key');
        if (empty($apiKey)) return null;

        $model = config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash');
        $content = $article->content ?? '';
        $isMarkdown = $this->isMarkdownContent($content);
        $format = $isMarkdown ? 'Markdown' : 'HTML';
        $wordCount = str_word_count(strip_tags($content));
        $targetWords = max(500, 800 - $wordCount);

        $systemPrompt = <<<PROMPT
Kamu adalah content writer SEO Indonesia untuk website konsultan perizinan bisnis "Bizmark" (bizmark.id).
Tugasmu: PERKAYA dan PERLUAS konten artikel yang terlalu pendek dengan tetap mempertahankan isi asli.

FORMAT OUTPUT: {$format}

ATURAN KONTEN:
1. PERTAHANKAN seluruh konten asli — jangan hapus atau ubah teks yang sudah ada
2. TAMBAHKAN minimal {$targetWords} kata baru yang relevan
3. WAJIB ada minimal 3 heading H2 (## untuk Markdown, <h2> untuk HTML)
4. WAJIB ada minimal 2 heading H3 di bawah H2 yang relevan
5. WAJIB ada minimal 1 bullet list atau numbered list
6. WAJIB ada minimal 5 kata/frasa yang di-bold (**kata** untuk Markdown, <strong>kata</strong> untuk HTML)
7. WAJIB ada minimal 5 paragraf terpisah
8. Tulis dalam bahasa Indonesia yang profesional dan informatif
9. Fokus pada topik perizinan, regulasi, atau layanan bisnis yang relevan
10. Tambahkan tips praktis, langkah-langkah, atau penjelasan mendalam

JANGAN menambahkan internal links atau "Artikel Terkait" — itu ditangani sistem lain.
Output HANYA konten artikel yang sudah diperkaya, tanpa penjelasan tambahan.
PROMPT;

        $userPrompt = "Perkaya artikel berikut (saat ini {$wordCount} kata, target minimal 800 kata):\n\n"
            . "Judul: {$article->title}\n"
            . "Kategori: {$article->category}\n\n"
            . "Konten saat ini:\n{$content}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Bizmark Content Expander',
            ])->timeout(self::CONTENT_EXPAND_TIMEOUT)->post(self::OPENROUTER_API_URL, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.5,
                'max_tokens' => 4000,
            ]);

            if (!$response->successful()) {
                Log::warning('SeoFixService: Content expander API error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $expandedContent = $response->json('choices.0.message.content', '');
            if (empty($expandedContent)) return null;

            // Validate: expanded content must be longer than original
            $newWordCount = str_word_count(strip_tags($expandedContent));
            if ($newWordCount <= $wordCount + 50) {
                Log::warning('SeoFixService: Content expander produced insufficient content', [
                    'original_words' => $wordCount,
                    'new_words' => $newWordCount,
                ]);
                return null;
            }

            Log::info('SeoFixService: Content expanded successfully', [
                'article_id' => $article->id,
                'words_before' => $wordCount,
                'words_after' => $newWordCount,
                'tokens' => $response->json('usage.total_tokens', 0),
            ]);

            return $expandedContent;

        } catch (\Exception $e) {
            Log::warning('SeoFixService: Content expander exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Rule-Based Fallback ────────────────────────────────

    protected function applyRuleBasedFixes(Article $article, array $factors = []): array
    {
        $fixes = [];
        $changed = false;

        // Fix reading_time
        if (!$article->reading_time && !empty($article->content)) {
            $wordCount = str_word_count(strip_tags($article->content));
            $article->reading_time = max(1, ceil($wordCount / 200));
            $fixes[] = '⚙️ Reading time dihitung';
            $changed = true;
        }

        // Fix slug length
        if ($article->slug && strlen($article->slug) > 70) {
            $newSlug = Str::limit($article->slug, 60, '');
            $newSlug = rtrim($newSlug, '-');
            if (!Article::where('slug', $newSlug)->where('id', '!=', $article->id)->exists()) {
                $article->slug = $newSlug;
                $fixes[] = '⚙️ Slug diperpendek';
                $changed = true;
            }
        }

        // Ensure year in meta_title (if AI didn't add it)
        if ($article->meta_title && !preg_match('/20\d{2}/', $article->meta_title)) {
            $year = date('Y');
            $titleLen = mb_strlen($article->meta_title);
            if ($titleLen + strlen(" $year") <= 60) {
                $article->meta_title .= " $year";
                $fixes[] = "⚙️ Tahun $year ditambahkan ke meta title";
                $changed = true;
            } elseif ($titleLen > 50) {
                // Truncate to make room for year
                $article->meta_title = Str::limit($article->meta_title, 50, '') . " $year";
                $fixes[] = "⚙️ Meta title dipersingkat + tahun $year ditambahkan";
                $changed = true;
            }
        }

        // Ensure Bizmark in meta_title (if AI didn't add it)
        if ($article->meta_title && !Str::contains($article->meta_title, ['Bizmark', 'bizmark'], true)) {
            $titleLen = mb_strlen($article->meta_title);
            if ($titleLen + 10 <= 60) {
                $article->meta_title .= ' | Bizmark';
                $fixes[] = '⚙️ Brand Bizmark ditambahkan';
                $changed = true;
            } elseif ($titleLen > 45) {
                // Truncate and add Bizmark
                $article->meta_title = Str::limit($article->meta_title, 45, '') . ' | Bizmark';
                $fixes[] = '⚙️ Meta title dipersingkat + brand Bizmark ditambahkan';
                $changed = true;
            }
        }

        // Generate excerpt if still empty
        if (empty($article->excerpt) && $article->content) {
            $text = strip_tags($article->content);
            $article->excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', $text)), 200);
            $fixes[] = '⚙️ Excerpt di-generate dari konten';
            $changed = true;
        }

        // Generate meta_keywords if still empty
        if (empty($article->meta_keywords)) {
            $kw = $this->generateKeywordsFallback($article);
            if ($kw) {
                $article->meta_keywords = $kw;
                $fixes[] = '⚙️ Meta keywords di-generate';
                $changed = true;
            }
        }

        // Generate meta_description if still empty
        if (empty($article->meta_description)) {
            $source = $article->excerpt ?: strip_tags($article->content ?? '');
            if ($source) {
                $desc = Str::limit(trim(preg_replace('/\s+/', ' ', $source)), 130, '');
                $desc .= ' Konsultasi gratis di Bizmark!';
                $article->meta_description = $desc;
                $fixes[] = '⚙️ Meta description di-generate';
                $changed = true;
            }
        }

        // Generate tags if too few (for excerpt_schema score)
        $currentTags = $article->tags ?? [];
        if (count($currentTags) < 2) {
            $generatedTags = $this->generateTagsFallback($article);
            if (!empty($generatedTags)) {
                $article->tags = array_unique(array_merge($currentTags, $generatedTags));
                $fixes[] = '⚙️ Tags ditambahkan (' . count($article->tags) . ' total)';
                $changed = true;
            }
        }

        // Fix image alt text in content
        if (!empty($article->content)) {
            $imageFixResult = $this->fixImageAltText($article);
            if ($imageFixResult['changed']) {
                $fixes = array_merge($fixes, $imageFixResult['fixes']);
                $changed = true;
            }
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }

    // ─── Content Enhancement ────────────────────────────────

    protected function enhanceContent(Article $article, array $factors = []): array
    {
        $fixes = [];
        $changed = false;
        $content = $article->content ?? '';
        if (empty($content)) return ['fixes' => $fixes, 'changed' => false];

        $baseUrl = rtrim(config('app.url'), '/');

        // Add internal links if missing (internal_links factor)
        $linkScore = $factors['internal_links']['score'] ?? 0;
        $linkMax = $factors['internal_links']['max'] ?? 10;
        if ($linkScore < $linkMax) {
            $hasHtmlLinks = preg_match('/href=["\'](' . preg_quote($baseUrl, '/') . '|\/)[^"\']*["\']/i', $content);
            $hasMdLinks = preg_match('/\[([^\]]+)\]\((' . preg_quote($baseUrl, '/') . '|\/)[^)]*\)/i', $content);
            $hasBacaJuga = Str::contains($content, ['Baca juga', 'Baca Juga', 'Artikel Terkait']);

            if (!$hasHtmlLinks && !$hasMdLinks && !$hasBacaJuga) {
                $relatedArticles = $this->findRelatedArticles($article, 5);
                if ($relatedArticles->isNotEmpty()) {
                    $isMarkdown = $this->isMarkdownContent($content);
                    $linksSection = $this->buildLinksSection($relatedArticles, $baseUrl, $isMarkdown);
                    $article->content = $article->content . "\n\n" . $linksSection;
                    $fixes[] = '🔗 Internal links ditambahkan (' . $relatedArticles->count() . ' artikel terkait)';
                    $changed = true;
                }
            }
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }

    protected function isMarkdownContent(string $content): bool
    {
        $mdPatterns = preg_match_all('/^#{1,3}\s/m', $content)
            + preg_match_all('/\*\*[^*]+\*\*/', $content)
            + preg_match_all('/^[-*]\s/m', $content);
        $htmlPatterns = preg_match_all('/<(h[1-6]|p|ul|ol|strong|em)\b/i', $content);

        return $mdPatterns > $htmlPatterns;
    }

    protected function findRelatedArticles(Article $article, int $limit = 5)
    {
        // Try by same category first, then by tags
        $query = Article::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->select('id', 'title', 'slug');

        if ($article->category) {
            $related = (clone $query)->where('category', $article->category)
                ->inRandomOrder()
                ->limit($limit)
                ->get();

            if ($related->count() >= $limit) return $related;
        }

        // Fallback: get any published articles
        return $query->inRandomOrder()->limit($limit)->get();
    }

    protected function buildLinksSection($articles, string $baseUrl, bool $isMarkdown): string
    {
        if ($isMarkdown) {
            $section = "---\n\n## Artikel Terkait\n\n";
            $section .= "Baca juga artikel lainnya di Bizmark:\n\n";
            foreach ($articles as $a) {
                $section .= "- [{$a->title}]({$baseUrl}/blog/{$a->slug})\n";
            }
        } else {
            $section = '<hr><h2>Artikel Terkait</h2>';
            $section .= '<p>Baca juga artikel lainnya di Bizmark:</p><ul>';
            foreach ($articles as $a) {
                $url = htmlspecialchars("{$baseUrl}/blog/{$a->slug}", ENT_QUOTES, 'UTF-8');
                $title = htmlspecialchars($a->title, ENT_QUOTES, 'UTF-8');
                $section .= "<li><a href=\"{$url}\">{$title}</a></li>";
            }
            $section .= '</ul>';
        }

        return $section;
    }

    protected function generateKeywordsFallback(Article $article): ?string
    {
        $keywords = [];
        if (!empty($article->tags) && is_array($article->tags)) {
            $keywords = array_merge($keywords, $article->tags);
        }
        if ($article->category) {
            $keywords[] = $article->category;
        }
        $titleWords = array_filter(
            explode(' ', strtolower($article->title)),
            fn($w) => mb_strlen($w) > 3 && !in_array($w, ['yang', 'untuk', 'dari', 'dengan', 'dalam', 'pada', 'atau', 'serta'])
        );
        $keywords = array_merge($keywords, array_slice($titleWords, 0, 3));
        $keywords[] = 'Bizmark';
        $keywords = array_unique(array_map('trim', $keywords));

        return count($keywords) > 0 ? implode(', ', array_slice($keywords, 0, 8)) : null;
    }

    protected function formatTags($tags): string
    {
        if (empty($tags)) return '-';
        if (is_array($tags)) return implode(', ', $tags);
        return (string) $tags;
    }

    // ─── Tags & Image Helpers ───────────────────────────────

    protected function generateTagsFallback(Article $article): array
    {
        $tags = [];

        if ($article->category) {
            $tags[] = $article->category;
        }

        // Extract meaningful words from title
        $titleWords = array_filter(
            explode(' ', $article->title),
            fn($w) => mb_strlen($w) > 3 && !in_array(strtolower($w), [
                'yang', 'untuk', 'dari', 'dengan', 'dalam', 'pada', 'atau', 'serta',
                'akan', 'bisa', 'agar', 'jika', 'oleh', 'saat', 'anda',
            ])
        );
        $tags = array_merge($tags, array_slice(array_values($titleWords), 0, 2));

        $tags[] = 'Perizinan';
        $tags[] = 'Bizmark';

        return array_values(array_unique(array_slice($tags, 0, 5)));
    }

    protected function fixImageAltText(Article $article): array
    {
        $fixes = [];
        $changed = false;
        $content = $article->content;

        // Find images without alt text or with empty alt
        $fixCount = 0;
        $content = preg_replace_callback('/<img([^>]*?)>/i', function ($match) use ($article, &$fixCount) {
            $attrs = $match[1];

            // Skip if already has non-empty alt
            if (preg_match('/alt=["\'][^"\']+["\']/i', $attrs)) {
                return $match[0];
            }

            $fixCount++;
            $altText = htmlspecialchars(Str::limit($article->title, 80, ''), ENT_QUOTES, 'UTF-8');

            // Replace empty alt="" or add alt if missing
            if (preg_match('/alt=["\']["\']/', $attrs)) {
                $attrs = preg_replace('/alt=["\']["\']/', "alt=\"{$altText}\"", $attrs);
            } else {
                $attrs .= " alt=\"{$altText}\"";
            }

            return "<img{$attrs}>";
        }, $content);

        if ($fixCount > 0) {
            $article->content = $content;
            $fixes[] = "🖼️ Alt text ditambahkan ke {$fixCount} gambar";
            $changed = true;
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }
}
