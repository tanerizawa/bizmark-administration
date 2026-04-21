<?php

namespace App\Modules\ContentSeo\Services;

use App\Models\Article;
use App\Models\CompetitorAnalysis;
use App\Models\SeoScore;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Competitor Smart Fix Service
 *
 * Applies comprehensive AI-powered fixes to an article based on
 * competitor analysis data: content gaps, recommendations, and
 * competitor strengths — all using real SERP intelligence.
 */
class CompetitorSmartFixService
{
    protected OpenRouterService $ai;
    protected SeoScoringService $scorer;
    protected PexelsService $pexels;

    public function __construct(OpenRouterService $ai, SeoScoringService $scorer, PexelsService $pexels)
    {
        $this->ai = $ai;
        $this->scorer = $scorer;
        $this->pexels = $pexels;
    }

    /**
     * Create a brand-new article from competitor analysis, then run smart fix.
     * Used when no existing article targets the analyzed keyword.
     */
    public function createAndFix(CompetitorAnalysis $analysis): array
    {
        $article = $this->createArticleFromAnalysis($analysis);

        $result = $this->fix($analysis, $article);
        $result['article_created'] = true;

        // Update analysis with our new URL
        $analysis->our_url = url("/blog/{$article->slug}");
        $analysis->save();

        return $result;
    }

    /**
     * Run comprehensive smart fix on an article based on competitor analysis.
     * Returns structured result with all changes made.
     */
    public function fix(CompetitorAnalysis $analysis, Article $article): array
    {
        $oldScore = SeoScore::where('article_id', $article->id)->first();
        $oldScoreValue = $oldScore?->total_score ?? 0;

        $steps = [];
        $changed = false;

        // ── Step 1: AI Meta Optimization (title, description, keywords) ──
        $metaResult = $this->optimizeMeta($analysis, $article);
        $steps[] = $metaResult;
        $changed = $changed || $metaResult['changed'];

        // ── Step 2: AI Content Enhancement (based on gaps & competitor snippets) ──
        $contentResult = $this->enhanceContent($analysis, $article);
        $steps[] = $contentResult;
        $changed = $changed || $contentResult['changed'];

        // ── Step 3: Structural SEO Fixes (headings, internal links, bold keywords) ──
        $structureResult = $this->fixStructure($analysis, $article);
        $steps[] = $structureResult;
        $changed = $changed || $structureResult['changed'];

        // ── Step 4: Featured image automation via Pexels (if missing) ──
        $imageResult = $this->ensureFeaturedImage($analysis, $article);
        $steps[] = $imageResult;
        $changed = $changed || $imageResult['changed'];

        // ── Save & Re-score ──
        if ($changed) {
            $article->updated_at = now();
            $article->saveQuietly();
        }

        $article->refresh();
        $newScore = $this->scorer->scoreArticle($article);

        $allFixes = collect($steps)->pluck('fixes')->flatten()->values()->all();

        return [
            'analysis_id' => $analysis->id,
            'article_id' => $article->id,
            'article_slug' => $article->slug,
            'article_edit_url' => route('articles.edit', $article),
            'keyword' => $analysis->keyword,
            'title' => $article->title,
            'steps' => $steps,
            'total_fixes' => count($allFixes),
            'fixes' => $allFixes,
            'old_score' => $oldScoreValue,
            'new_score' => $newScore->total_score,
            'score_change' => $newScore->total_score - $oldScoreValue,
            'new_grade' => $newScore->grade,
            'remaining_issues' => count($newScore->recommendations ?? []),
            'data_source' => $analysis->data_source,
        ];
    }

    /**
     * Step 1: Optimize meta tags based on competitor analysis.
     * Uses competitor titles/snippets as benchmark for AI to beat.
     */
    protected function optimizeMeta(CompetitorAnalysis $analysis, Article $article): array
    {
        $fixes = [];
        $changed = false;

        $competitorMeta = collect($analysis->top_competitors ?? [])
            ->take(5)
            ->map(fn($c) => "#{$c['position']} {$c['domain']}: \"{$c['title']}\" — " . ($c['snippet'] ?? ''))
            ->implode("\n");

        $recommendations = collect($analysis->recommendations ?? [])
            ->filter(fn($r) => Str::contains(mb_strtolower($r), ['title', 'meta', 'description', 'keyword', 'judul', 'deskripsi']))
            ->implode("\n- ");

        $prompt = <<<PROMPT
Kamu adalah SEO expert Indonesia untuk bizmark.id (konsultan perizinan lingkungan: AMDAL, UKL-UPL, izin limbah B3, OSS, SLF, K3).

KEYWORD TARGET: "{$analysis->keyword}"
POSISI KITA: {$this->formatPosition($analysis->our_position)}

KOMPETITOR TERATAS (dari SERP real):
{$competitorMeta}

REKOMENDASI DARI ANALISIS:
- {$recommendations}

ARTIKEL KITA SAAT INI:
- Title: {$article->title}
- Meta Title: {$article->meta_title}
- Meta Description: {$article->meta_description}
- Meta Keywords: {$article->meta_keywords}
- Excerpt: {$article->excerpt}

TUGAS: Buat meta tags yang LEBIH BAIK dari semua kompetitor di atas.

ATURAN KETAT:
1. meta_title: MAX 55 chars, WAJIB ada tahun 2026, WAJIB ada "Bizmark", power words (Lengkap/Panduan/Terpercaya/Proses Cepat)
2. meta_description: 130-155 chars, CTA kuat (Konsultasi Gratis!), keyword utama, lebih menarik dari snippet kompetitor
3. meta_keywords: 6-10 keywords relevan, termasuk long-tail variations dari "{$analysis->keyword}"
4. excerpt: 120-200 chars, ringkasan menarik, CTA, informatif
5. tags: array 4-6 tags relevan

REFERENSI JUDUL KOMPETITOR (untuk kalahkan):
{$competitorMeta}

Output JSON saja:
{
  "meta_title": "...",
  "meta_description": "...",
  "meta_keywords": "keyword1, keyword2, ...",
  "excerpt": "...",
  "tags": ["tag1", "tag2"]
}
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.3,
            'max_tokens' => 1000,
        ]);

        if (!$response['success']) {
            return ['step' => 'Meta Optimization', 'status' => 'failed', 'fixes' => [], 'changed' => false, 'error' => 'AI API gagal'];
        }

        $data = json_decode(
            preg_replace('/```json\s*|\s*```/', '', trim($response['content'])),
            true
        );

        if (!$data) {
            return ['step' => 'Meta Optimization', 'status' => 'failed', 'fixes' => [], 'changed' => false, 'error' => 'Gagal parse AI response'];
        }

        // Apply meta title
        if (!empty($data['meta_title']) && $data['meta_title'] !== $article->meta_title) {
            $old = $article->meta_title;
            $article->meta_title = Str::limit($data['meta_title'], 65, '');
            $fixes[] = "🏷️ Meta title: \"{$old}\" → \"{$article->meta_title}\"";
            $changed = true;
        }

        // Apply meta description
        if (!empty($data['meta_description']) && $data['meta_description'] !== $article->meta_description) {
            $article->meta_description = Str::limit($data['meta_description'], 160, '');
            $fixes[] = "📝 Meta description dioptimasi (berdasarkan analisis kompetitor)";
            $changed = true;
        }

        // Apply meta keywords
        if (!empty($data['meta_keywords'])) {
            $newKw = is_array($data['meta_keywords']) ? implode(', ', $data['meta_keywords']) : $data['meta_keywords'];
            if ($newKw !== $article->meta_keywords) {
                $article->meta_keywords = $newKw;
                $fixes[] = "🔑 Meta keywords diperkaya ({$newKw})";
                $changed = true;
            }
        }

        // Apply excerpt
        if (!empty($data['excerpt']) && (empty($article->excerpt) || mb_strlen($article->excerpt) < 80)) {
            $article->excerpt = Str::limit($data['excerpt'], 250, '');
            $fixes[] = "📋 Excerpt dioptimasi AI";
            $changed = true;
        }

        // Apply tags
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $currentTags = $article->tags ?? [];
            $merged = array_unique(array_merge($currentTags, array_slice($data['tags'], 0, 6)));
            if ($merged !== $currentTags) {
                $article->tags = $merged;
                $fixes[] = "🏷️ Tags ditambahkan: " . implode(', ', $data['tags']);
                $changed = true;
            }
        }

        return [
            'step' => 'Meta Optimization',
            'status' => $changed ? 'applied' : 'skipped',
            'fixes' => $fixes,
            'changed' => $changed,
            'ai_data' => $data,
        ];
    }

    /**
     * Step 2: Enhance content based on content gaps and competitor analysis.
     * AI generates additional sections covering topics competitors have but we don't.
     */
    protected function enhanceContent(CompetitorAnalysis $analysis, Article $article): array
    {
        $fixes = [];
        $changed = false;

        $content = $article->content ?? '';
        $wordCount = str_word_count(strip_tags($content));
        $isMarkdown = $this->isMarkdownContent($content);
        $format = $isMarkdown ? 'Markdown' : 'HTML';

        // Build content gap context
        $gaps = collect($analysis->content_gaps ?? [])->implode("\n- ");
        $recommendations = collect($analysis->recommendations ?? [])
            ->filter(fn($r) => Str::contains(mb_strtolower($r), ['konten', 'content', 'halaman', 'landing', 'tambahkan', 'sertakan', 'buat', 'tulis', 'informasi']))
            ->implode("\n- ");

        $competitorSnippets = collect($analysis->top_competitors ?? [])
            ->take(3)
            ->map(fn($c) => "- {$c['domain']}: " . ($c['snippet'] ?? $c['title'] ?? ''))
            ->implode("\n");

        if (empty($gaps) && empty($recommendations)) {
            return ['step' => 'Content Enhancement', 'status' => 'skipped', 'fixes' => [], 'changed' => false, 'reason' => 'Tidak ada content gap'];
        }

        $prompt = <<<PROMPT
Kamu content writer SEO Indonesia untuk bizmark.id (konsultan perizinan lingkungan: AMDAL, UKL-UPL, izin limbah B3, OSS, SLF, K3).

KEYWORD TARGET: "{$analysis->keyword}"
FORMAT OUTPUT: {$format}

CONTENT GAPS — topik yang HARUS kita cover (kompetitor sudah punya, kita belum):
- {$gaps}

REKOMENDASI KONTEN:
- {$recommendations}

REFERENSI SNIPPET KOMPETITOR:
{$competitorSnippets}

KONTEN KITA SAAT INI ({$wordCount} kata):
{$content}

TUGAS: Perkaya konten dengan menambahkan section-section BARU yang menutupi content gaps di atas.

ATURAN:
1. PERTAHANKAN 100% konten asli — jangan hapus atau ubah teks yang sudah ada
2. TAMBAHKAN section baru di posisi yang logis dalam konten
3. Setiap section baru WAJIB punya heading H2 atau H3
4. Gunakan bahasa Indonesia profesional, informatif
5. Sertakan data/fakta konkret & tips praktis
6. Buat minimal 2 section baru yang masing-masing 150+ kata
7. WAJIB ada bold pada keyword penting
8. Jika word count < 800, tambahkan cukup konten agar mencapai 800+
9. Jangan tambahkan "artikel terkait" atau internal links — itu ditangani sistem lain

Output HANYA konten artikel yang sudah diperkaya, tanpa penjelasan tambahan.
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.5,
            'max_tokens' => 4000,
        ]);

        if (!$response['success']) {
            return ['step' => 'Content Enhancement', 'status' => 'failed', 'fixes' => [], 'changed' => false, 'error' => 'AI API gagal'];
        }

        $expanded = trim($response['content']);

        // Remove markdown code fences if present
        $expanded = preg_replace('/^```(?:html|markdown)?\s*/m', '', $expanded);
        $expanded = preg_replace('/\s*```$/m', '', $expanded);

        $newWordCount = str_word_count(strip_tags($expanded));

        if ($newWordCount <= $wordCount + 50) {
            return ['step' => 'Content Enhancement', 'status' => 'skipped', 'fixes' => [], 'changed' => false, 'reason' => 'Konten AI terlalu pendek'];
        }

        $article->content = $expanded;
        $article->reading_time = max(1, ceil($newWordCount / 200));
        $addedWords = $newWordCount - $wordCount;
        $fixes[] = "📝 Konten diperkaya: +{$addedWords} kata ({$wordCount} → {$newWordCount})";

        $gapsCovered = count($analysis->content_gaps ?? []);
        $fixes[] = "🔍 {$gapsCovered} content gaps dari kompetitor ditutup";

        $changed = true;

        return [
            'step' => 'Content Enhancement',
            'status' => 'applied',
            'fixes' => $fixes,
            'changed' => $changed,
            'words_before' => $wordCount,
            'words_after' => $newWordCount,
        ];
    }

    /**
     * Step 3: Fix structural SEO elements.
     * Ensures headings hierarchy, keyword density, and bold keywords.
     */
    protected function fixStructure(CompetitorAnalysis $analysis, Article $article): array
    {
        $fixes = [];
        $changed = false;
        $content = $article->content ?? '';

        if (empty($content)) {
            return ['step' => 'Structure Fix', 'status' => 'skipped', 'fixes' => [], 'changed' => false, 'reason' => 'Tidak ada konten'];
        }

        $keyword = $analysis->keyword;
        $isMarkdown = $this->isMarkdownContent($content);

        // Check: Does content have the target keyword?
        $keywordLower = mb_strtolower($keyword);
        $contentLower = mb_strtolower(strip_tags($content));
        $keywordCount = mb_substr_count($contentLower, $keywordLower);

        // Ensure keyword appears at least 3 times naturally
        if ($keywordCount < 2) {
            // Add keyword in a natural context at the beginning
            if ($isMarkdown) {
                $intro = "**{$keyword}** merupakan aspek penting dalam perizinan di Indonesia. Berikut panduan lengkap mengenai {$keyword} yang perlu Anda ketahui.\n\n";
                $content = $intro . $content;
            } else {
                $intro = "<p><strong>{$keyword}</strong> merupakan aspek penting dalam perizinan di Indonesia. Berikut panduan lengkap mengenai {$keyword} yang perlu Anda ketahui.</p>\n\n";
                $content = $intro . $content;
            }
            $fixes[] = "🔑 Keyword \"{$keyword}\" ditambahkan di awal konten (densitas rendah)";
            $changed = true;
        }

        // Check: Does content have enough headings?
        if ($isMarkdown) {
            $h2Count = preg_match_all('/^## /m', $content);
        } else {
            $h2Count = preg_match_all('/<h2[^>]*>/i', $content);
        }

        if ($h2Count < 2) {
            $fixes[] = "⚠️ Konten hanya memiliki {$h2Count} heading H2 (rekomendasi: minimal 3)";
            // Content enhancement step should have added these; don't double-fix
        }

        // Bold keyword instances that aren't already bold
        if ($isMarkdown) {
            // Bold first plain occurrence of keyword (not already bolded)
            $pattern = '/(?<!\*\*)(' . preg_quote($keyword, '/') . ')(?!\*\*)/iu';
            $content = preg_replace($pattern, '**$1**', $content, 1, $boldCount);
        } else {
            $pattern = '/(?<!<strong>)(' . preg_quote($keyword, '/') . ')(?!<\/strong>)/iu';
            $content = preg_replace($pattern, '<strong>$1</strong>', $content, 1, $boldCount);
        }

        if ($boldCount > 0) {
            $fixes[] = "🅱️ Keyword \"{$keyword}\" di-bold untuk penekanan SEO";
            $changed = true;
        }

        if ($changed) {
            $article->content = $content;
        }

        return [
            'step' => 'Structure Fix',
            'status' => $changed ? 'applied' : 'skipped',
            'fixes' => $fixes,
            'changed' => $changed,
        ];
    }

    // ── Helpers ──

    protected function formatPosition(?int $pos): string
    {
        return $pos ? "#{$pos}" : 'Tidak ranking di halaman 1';
    }

    protected function isMarkdownContent(string $content): bool
    {
        $markdownIndicators = ['## ', '### ', '**', '- ', '1. ', '```'];
        $htmlIndicators = ['<p>', '<h2', '<h3', '<strong>', '<ul', '<ol'];

        $mdScore = 0;
        $htmlScore = 0;

        foreach ($markdownIndicators as $ind) {
            if (str_contains($content, $ind)) $mdScore++;
        }
        foreach ($htmlIndicators as $ind) {
            if (str_contains($content, $ind)) $htmlScore++;
        }

        return $mdScore > $htmlScore;
    }

    /**
     * Generate a full article via AI from competitor analysis context.
     */
    protected function createArticleFromAnalysis(CompetitorAnalysis $analysis): Article
    {
        $keyword = $analysis->keyword;
        $gaps = collect($analysis->content_gaps ?? [])->implode("\n- ");
        $recommendations = collect($analysis->recommendations ?? [])->implode("\n- ");

        $competitorContext = collect($analysis->top_competitors ?? [])
            ->take(5)
            ->map(fn($c) => "#{$c['position']} {$c['domain']}: \"{$c['title']}\" — " . ($c['snippet'] ?? ''))
            ->implode("\n");

        $prompt = <<<PROMPT
Kamu content writer SEO Indonesia ahli untuk bizmark.id (konsultan perizinan lingkungan: AMDAL, UKL-UPL, izin limbah B3, OSS, SLF, K3).

KEYWORD TARGET: "{$keyword}"

KOMPETITOR TERATAS (dari SERP):
{$competitorContext}

CONTENT GAPS (topik yang WAJIB dicover):
- {$gaps}

REKOMENDASI:
- {$recommendations}

TUGAS: Buat artikel SEO yang LEBIH LENGKAP & LEBIH BAIK dari semua kompetitor di atas.

ATURAN:
1. Format HTML (gunakan <h2>, <h3>, <p>, <strong>, <ul>, <li>)
2. Minimal 1200 kata, informatif dan komprehensif
3. Judul (H1) yang SEO-friendly, tersandung keyword utama, menarik klik
4. Minimal 5 heading H2 yang terstruktur
5. Setiap section minimal 150 kata
6. Keyword "{$keyword}" muncul 4-6 kali secara natural, bold minimal 2x
7. Bahasa Indonesia profesional, autoritas tinggi
8. Sertakan data/fakta konkret, tips praktis, pro-tips
9. Tutup SEMUA content gaps yang disebutkan
10. Konten harus lebih informatif dari snippet kompetitor
11. Akhiri dengan CTA: "Hubungi Bizmark sekarang untuk konsultasi gratis"

Output JSON:
{
  "title": "Judul Artikel SEO-Friendly",
  "content": "<h2>...</h2><p>...</p>...",
  "meta_title": "Max 55 chars, ada tahun 2026 & Bizmark",
  "meta_description": "130-155 chars, CTA, keyword utama",
  "meta_keywords": "keyword1, keyword2, ...",
  "excerpt": "120-200 chars ringkasan menarik",
  "tags": ["tag1", "tag2", ...],
  "category": "perizinan-lingkungan"
}
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.5,
            'max_tokens' => 6000,
        ]);

        $data = [];
        if ($response['success']) {
            $data = json_decode(
                preg_replace('/```json\s*|\s*```/', '', trim($response['content'])),
                true
            ) ?? [];
        }

        $title = $data['title'] ?? "Panduan Lengkap {$keyword} 2026 - Bizmark";

        $article = Article::create([
            'title' => $title,
            'content' => $data['content'] ?? "<h2>Panduan {$keyword}</h2><p>Artikel ini membahas secara komprehensif tentang <strong>{$keyword}</strong> untuk membantu Anda memahami proses dan persyaratan yang diperlukan.</p>",
            'meta_title' => $data['meta_title'] ?? Str::limit($title, 55, ''),
            'meta_description' => $data['meta_description'] ?? "Panduan lengkap {$keyword} 2026. Konsultasi gratis di Bizmark!",
            'meta_keywords' => $data['meta_keywords'] ?? $keyword,
            'excerpt' => $data['excerpt'] ?? "Panduan komprehensif tentang {$keyword}. Pelajari proses, persyaratan, dan tips praktis.",
            'tags' => $data['tags'] ?? [Str::slug($keyword)],
            'category' => $data['category'] ?? 'perizinan-lingkungan',
            'language' => 'id',
            'status' => 'draft',
            'source_type' => 'auto-generated',
            'author_id' => auth()->id() ?? 1,
            'published_at' => null,
        ]);

        Log::info("SmartFix: Created new article #{$article->id} for keyword '{$keyword}'");

        // Auto-enrich with Pexels image using keyword/title context.
        $this->ensureFeaturedImage($analysis, $article);

        return $article;
    }

    /**
     * Ensure article has featured image from Pexels based on keyword context.
     */
    protected function ensureFeaturedImage(CompetitorAnalysis $analysis, Article $article): array
    {
        if (!empty($article->featured_image)) {
            return [
                'step' => 'Featured Image',
                'status' => 'skipped',
                'fixes' => [],
                'changed' => false,
            ];
        }

        $queries = $this->buildImageQueries($analysis, $article);

        foreach ($queries as $query) {
            try {
                $results = $this->pexels->searchPhotos($query, 8, 1, [
                    'orientation' => 'landscape',
                    'size' => 'large',
                    'locale' => 'id-ID',
                ]);

                if (!empty($results['photos'])) {
                    $photo = $results['photos'][0];
                    $url = $photo['src']['large2x'] ?? $photo['src']['large'] ?? $photo['src']['original'];

                    $path = $this->pexels->downloadAndSavePhoto(
                        $url,
                        $photo['photographer'] ?? 'Unknown',
                        $photo['id']
                    );

                    if ($path) {
                        $article->featured_image = $path;
                        $article->saveQuietly();

                        return [
                            'step' => 'Featured Image',
                            'status' => 'applied',
                            'fixes' => ["🖼️ Featured image ditambahkan otomatis dari Pexels (query: {$query})"],
                            'changed' => true,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('SmartFix image automation failed for query', [
                    'article_id' => $article->id,
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'step' => 'Featured Image',
            'status' => 'failed',
            'fixes' => [],
            'changed' => false,
            'error' => 'Pexels image not found',
        ];
    }

    protected function buildImageQueries(CompetitorAnalysis $analysis, Article $article): array
    {
        $queries = [];

        if (!empty($analysis->keyword)) {
            $queries[] = $analysis->keyword;
            $queries[] = $analysis->keyword . ' Indonesia';
        }

        if (!empty($article->title)) {
            $cleanTitle = preg_replace('/\b\d{4}\b/', '', $article->title);
            $cleanTitle = preg_replace('/[^\w\s]/u', ' ', $cleanTitle);
            $queries[] = trim(preg_replace('/\s+/', ' ', $cleanTitle));
        }

        if (!empty($article->meta_keywords)) {
            $kw = array_map('trim', explode(',', $article->meta_keywords));
            $queries[] = implode(' ', array_slice($kw, 0, 2));
        }

        $categoryFallback = [
            'tips' => 'konsultasi bisnis kantor',
            'regulation' => 'regulasi pemerintah dokumen legal',
            'general' => 'bisnis indonesia kantor profesional',
            'case-study' => 'tim bisnis meeting sukses',
            'news' => 'berita bisnis indonesia',
        ];

        $queries[] = $categoryFallback[$article->category] ?? 'perizinan usaha indonesia';
        $queries[] = 'business office document';

        return array_values(array_unique(array_filter($queries)));
    }
}
