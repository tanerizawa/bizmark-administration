<?php

namespace App\Modules\ContentSeo\Services;

use App\Models\Article;
use App\Models\SeoScore;
use Illuminate\Support\Str;

class SeoScoringService
{
    /**
     * Score a single article (0-100)
     */
    public function scoreArticle(Article $article): SeoScore
    {
        $factors = [];

        // 1. Title optimization (0-15)
        $factors['title'] = $this->scoreTitle($article);

        // 2. Meta description (0-12)
        $factors['meta_description'] = $this->scoreMetaDescription($article);

        // 3. Meta keywords (0-5)
        $factors['meta_keywords'] = $this->scoreMetaKeywords($article);

        // 4. Content quality (0-20)
        $factors['content'] = $this->scoreContent($article);

        // 5. Heading structure (0-10)
        $factors['headings'] = $this->scoreHeadings($article);

        // 6. Internal links (0-10)
        $factors['internal_links'] = $this->scoreInternalLinks($article);

        // 7. Image optimization (0-8)
        $factors['images'] = $this->scoreImages($article);

        // 8. URL/Slug quality (0-5)
        $factors['slug'] = $this->scoreSlug($article);

        // 9. Freshness (0-8)
        $factors['freshness'] = $this->scoreFreshness($article);

        // 10. Excerpt/Schema readiness (0-7)
        $factors['excerpt_schema'] = $this->scoreExcerptSchema($article);

        $totalScore = collect($factors)->sum('score');
        $recommendations = collect($factors)
            ->flatMap(fn ($f) => $f['issues'] ?? [])
            ->values()
            ->all();

        return SeoScore::updateOrCreate(
            ['article_id' => $article->id],
            [
                'total_score' => min($totalScore, 100),
                'factors' => $factors,
                'recommendations' => $recommendations,
                'scored_at' => now(),
            ]
        );
    }

    /**
     * Score all published articles
     */
    public function scoreAll(int $limit = 0): array
    {
        $query = Article::where('status', 'published');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $results = ['scored' => 0, 'avg_score' => 0, 'scores' => []];
        $total = 0;

        $query->chunk(50, function ($articles) use (&$results, &$total) {
            foreach ($articles as $article) {
                $score = $this->scoreArticle($article);
                $results['scores'][] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'score' => $score->total_score,
                    'grade' => $score->grade,
                ];
                $total += $score->total_score;
                $results['scored']++;
            }
        });

        $results['avg_score'] = $results['scored'] > 0
            ? round($total / $results['scored'], 1)
            : 0;

        return $results;
    }

    // ─── Scoring Factors ────────────────────────────────────────

    protected function scoreTitle(Article $article): array
    {
        $score = 0;
        $issues = [];
        $title = $article->meta_title ?: $article->title;
        $len = mb_strlen($title);

        if ($len >= 30 && $len <= 60) {
            $score += 5;
        } elseif ($len > 60) {
            $score += 2;
            $issues[] = "Judul terlalu panjang ({$len} chars, optimal 50-60)";
        } else {
            $score += 1;
            $issues[] = "Judul terlalu pendek ({$len} chars, minimal 30)";
        }

        // Contains year
        if (preg_match('/20\d{2}/', $title)) {
            $score += 3;
        } else {
            $issues[] = 'Tambahkan tahun di judul (contoh: 2026)';
        }

        // Contains keyword-like structure
        if (preg_match('/panduan|cara|tips|lengkap|terbaru|update/i', $title)) {
            $score += 3;
        }

        // Meta title differs from title (good for SEO)
        if ($article->meta_title && $article->meta_title !== $article->title) {
            $score += 2;
        }

        // Contains brand name
        if (Str::contains($title, ['Bizmark', 'bizmark'], true)) {
            $score += 2;
        } else {
            $issues[] = 'Tambahkan "Bizmark" di meta title';
        }

        return ['score' => min($score, 15), 'max' => 15, 'issues' => $issues];
    }

    protected function scoreMetaDescription(Article $article): array
    {
        $score = 0;
        $issues = [];
        $desc = $article->meta_description;

        if (!$desc) {
            $issues[] = 'Meta description kosong — wajib diisi untuk CTR di Google';
            return ['score' => 0, 'max' => 12, 'issues' => $issues];
        }

        $len = mb_strlen($desc);
        if ($len >= 120 && $len <= 160) {
            $score += 5;
        } elseif ($len >= 80) {
            $score += 3;
            $issues[] = "Meta description kurang optimal ({$len} chars, target 120-155)";
        } else {
            $score += 1;
            $issues[] = "Meta description terlalu pendek ({$len} chars)";
        }

        // Contains CTA words
        if (preg_match('/hubungi|konsultasi|pelajari|baca|dapatkan|gratis/i', $desc)) {
            $score += 3;
        } else {
            $issues[] = 'Tambahkan CTA di meta description (contoh: "Konsultasi gratis!")';
        }

        // Contains keyword from title
        $titleWords = array_filter(explode(' ', strtolower($article->title)), fn ($w) => mb_strlen($w) > 3);
        $matchCount = 0;
        foreach (array_slice($titleWords, 0, 5) as $word) {
            if (Str::contains(strtolower($desc), $word)) {
                $matchCount++;
            }
        }
        if ($matchCount >= 2) {
            $score += 4;
        } elseif ($matchCount >= 1) {
            $score += 2;
        } else {
            $issues[] = 'Meta description tidak mengandung kata kunci dari judul';
        }

        return ['score' => min($score, 12), 'max' => 12, 'issues' => $issues];
    }

    protected function scoreMetaKeywords(Article $article): array
    {
        $score = 0;
        $issues = [];
        $keywords = $article->meta_keywords;

        if (!$keywords) {
            $issues[] = 'Meta keywords kosong';
            return ['score' => 0, 'max' => 5, 'issues' => $issues];
        }

        $count = count(array_filter(explode(',', $keywords)));
        if ($count >= 3 && $count <= 10) {
            $score += 5;
        } elseif ($count >= 1) {
            $score += 3;
            $issues[] = "Tambahkan lebih banyak keywords (saat ini: {$count}, target: 5-8)";
        }

        return ['score' => min($score, 5), 'max' => 5, 'issues' => $issues];
    }

    protected function scoreContent(Article $article): array
    {
        $score = 0;
        $issues = [];
        $content = $article->content ?? '';
        $text = strip_tags($content);
        $wordCount = str_word_count($text);

        // Word count
        if ($wordCount >= 1500) {
            $score += 8;
        } elseif ($wordCount >= 800) {
            $score += 5;
        } elseif ($wordCount >= 300) {
            $score += 3;
            $issues[] = "Konten terlalu pendek ({$wordCount} kata, target >800)";
        } else {
            $score += 1;
            $issues[] = "Konten sangat pendek ({$wordCount} kata, minimal 300)";
        }

        // Has paragraphs (HTML or Markdown double-newline)
        $paragraphs = substr_count($content, '</p>');
        if ($paragraphs < 3) {
            $paragraphs += preg_match_all('/\n\n(?!\s*[-*#>\d])/', $content);
        }
        if ($paragraphs >= 5) {
            $score += 3;
        } elseif ($paragraphs >= 3) {
            $score += 2;
        } else {
            $issues[] = 'Tambahkan lebih banyak paragraf untuk readability';
        }

        // Has lists (HTML ul/ol or Markdown - / * / 1.)
        $hasList = preg_match('/<[uo]l/i', $content) || preg_match('/^[-*]\s/m', $content) || preg_match('/^\d+\.\s/m', $content);
        if ($hasList) {
            $score += 3;
        } else {
            $issues[] = 'Tambahkan bullet points atau numbered list';
        }

        // Has bold/strong emphasis (HTML or Markdown **text**)
        if (preg_match('/<(strong|b)>/i', $content) || preg_match('/\*\*[^*]+\*\*/', $content)) {
            $score += 2;
        }

        // Reading time set
        if ($article->reading_time) {
            $score += 2;
        }

        // No thin content
        if ($wordCount < 100) {
            $issues[] = 'Konten terlalu tipis — Google bisa menganggap thin content';
        }

        return ['score' => min($score, 20), 'max' => 20, 'issues' => $issues];
    }

    protected function scoreHeadings(Article $article): array
    {
        $score = 0;
        $issues = [];
        $content = $article->content ?? '';

        // H2 headings (HTML or Markdown ##)
        preg_match_all('/<h2/i', $content, $h2Html);
        preg_match_all('/^##[^#]/m', $content, $h2Md);
        $h2Count = count($h2Html[0]) + count($h2Md[0]);
        if ($h2Count >= 3) {
            $score += 5;
        } elseif ($h2Count >= 1) {
            $score += 3;
        } else {
            $issues[] = 'Tidak ada heading H2 — tambahkan minimal 2-3 subheading';
        }

        // H3 headings (HTML or Markdown ###)
        preg_match_all('/<h3/i', $content, $h3Html);
        preg_match_all('/^###/m', $content, $h3Md);
        $h3Count = count($h3Html[0]) + count($h3Md[0]);
        if ($h3Count >= 2) {
            $score += 3;
        } elseif ($h3Count >= 1) {
            $score += 2;
        }

        // Heading hierarchy (H2 before H3)
        if ($h2Count > 0 && $h3Count > 0) {
            $score += 2;
        }

        return ['score' => min($score, 10), 'max' => 10, 'issues' => $issues];
    }

    protected function scoreInternalLinks(Article $article): array
    {
        $score = 0;
        $issues = [];
        $content = $article->content ?? '';
        $baseUrl = rtrim(config('app.url'), '/');

        // Count internal links (HTML href or Markdown [text](url))
        preg_match_all('/href=["\'](' . preg_quote($baseUrl, '/') . '|\/)[^"\']*["\']/i', $content, $htmlLinks);
        preg_match_all('/\[([^\]]+)\]\((' . preg_quote($baseUrl, '/') . '|\/)[^)]*\)/i', $content, $mdLinks);
        $linkCount = count($htmlLinks[0]) + count($mdLinks[0]);

        if ($linkCount >= 5) {
            $score += 7;
        } elseif ($linkCount >= 3) {
            $score += 5;
        } elseif ($linkCount >= 1) {
            $score += 3;
        } else {
            $issues[] = 'Tidak ada internal links — tambahkan 3-5 link ke artikel/layanan terkait';
        }

        // Has "Baca juga" section (from auto-linking)
        if (Str::contains($content, ['Baca juga', 'Baca Juga', 'Artikel Terkait', 'artikel terkait'])) {
            $score += 3;
        }

        return ['score' => min($score, 10), 'max' => 10, 'issues' => $issues];
    }

    protected function scoreImages(Article $article): array
    {
        $score = 0;
        $issues = [];
        $content = $article->content ?? '';

        // Featured image
        if ($article->featured_image) {
            $score += 3;
        } else {
            $issues[] = 'Tidak ada featured image';
        }

        // Images in content
        preg_match_all('/<img[^>]*>/i', $content, $images);
        $imageCount = count($images[0]);

        if ($imageCount >= 2) {
            $score += 2;
        } elseif ($imageCount >= 1) {
            $score += 1;
        }

        // Alt tags on images
        $withAlt = 0;
        foreach ($images[0] as $img) {
            if (preg_match('/alt=["\'][^"\']+["\']/i', $img)) {
                $withAlt++;
            }
        }
        if ($imageCount > 0 && $withAlt === $imageCount) {
            $score += 3;
        } elseif ($withAlt > 0) {
            $score += 1;
            $issues[] = "Beberapa gambar tidak memiliki alt text ({$withAlt}/{$imageCount})";
        } elseif ($imageCount > 0) {
            $issues[] = 'Semua gambar tidak memiliki alt text — penting untuk SEO';
        }

        return ['score' => min($score, 8), 'max' => 8, 'issues' => $issues];
    }

    protected function scoreSlug(Article $article): array
    {
        $score = 0;
        $issues = [];
        $slug = $article->slug;

        if ($slug) {
            $score += 2;

            // Slug length
            $len = strlen($slug);
            if ($len <= 60) {
                $score += 2;
            } else {
                $issues[] = "URL slug terlalu panjang ({$len} chars)";
            }

            // No numbers-only slug
            if (!preg_match('/^\d+$/', $slug)) {
                $score += 1;
            }
        }

        return ['score' => min($score, 5), 'max' => 5, 'issues' => $issues];
    }

    protected function scoreFreshness(Article $article): array
    {
        $score = 0;
        $issues = [];
        $daysSincePublish = $article->published_at ? $article->published_at->diffInDays(now()) : 999;
        $daysSinceUpdate = $article->updated_at->diffInDays(now());

        // Recently published
        if ($daysSincePublish <= 30) {
            $score += 4;
        } elseif ($daysSincePublish <= 90) {
            $score += 2;
        } else {
            $issues[] = "Artikel sudah lama ({$daysSincePublish} hari) — pertimbangkan refresh";
        }

        // Recently updated
        if ($daysSinceUpdate <= 30) {
            $score += 4;
        } elseif ($daysSinceUpdate <= 90) {
            $score += 2;
        } else {
            $issues[] = 'Artikel belum di-update > 90 hari';
        }

        return ['score' => min($score, 8), 'max' => 8, 'issues' => $issues];
    }

    protected function scoreExcerptSchema(Article $article): array
    {
        $score = 0;
        $issues = [];

        // Excerpt
        if ($article->excerpt && mb_strlen($article->excerpt) >= 50) {
            $score += 4;
        } elseif ($article->excerpt) {
            $score += 2;
            $issues[] = 'Excerpt terlalu pendek (target >50 chars)';
        } else {
            $issues[] = 'Excerpt kosong — penting untuk social sharing dan RSS';
        }

        // Tags
        if (!empty($article->tags) && count($article->tags) >= 2) {
            $score += 3;
        } else {
            $issues[] = 'Tambahkan minimal 2-3 tags untuk kategorisasi';
        }

        return ['score' => min($score, 7), 'max' => 7, 'issues' => $issues];
    }
}
