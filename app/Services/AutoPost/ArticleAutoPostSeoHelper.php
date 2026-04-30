<?php

namespace App\Services\AutoPost;

use App\Models\Article;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use App\Services\SeoScoringService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleAutoPostSeoHelper
{
    public function __construct(protected SeoScoringService $seoScorer) {}

    /**
     * SEO Compliance: Score the article and auto-fix if below target
     * This saves OpenRouter API costs by fixing issues immediately
     * instead of requiring separate SeoFixService runs later.
     */
    public function applySeoCompliance(Article $article, AutoPostSchedule $schedule): array
    {
        $targetScore = 80;

        try {
            // Score the article with our 10-factor SEO scoring algorithm
            $initialScore = $this->seoScorer->scoreArticle($article);

            Log::info('📊 SEO Score check', [
                'article_id' => $article->id,
                'score' => $initialScore->total_score,
                'grade' => $initialScore->grade,
                'issues' => count($initialScore->recommendations ?? []),
            ]);

            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'level' => 'info',
                'event' => 'seo_score_initial',
                'message' => "📊 SEO Score: {$initialScore->total_score}/100 (Grade {$initialScore->grade})",
                'context' => [
                    'score' => $initialScore->total_score,
                    'grade' => $initialScore->grade,
                    'recommendations' => count($initialScore->recommendations ?? []),
                    'factors' => $initialScore->factors,
                ],
            ]);

            // If score is already good, no fix needed — API cost saved!
            if ($initialScore->total_score >= $targetScore) {
                Log::info('✅ SEO Score already meets target, no fix needed', [
                    'score' => $initialScore->total_score,
                ]);

                return [
                    'initial_score' => $initialScore->total_score,
                    'final_score' => $initialScore->total_score,
                    'grade' => $initialScore->grade,
                    'fixes_count' => 0,
                    'api_saved' => true,
                ];
            }

            // Score below target: apply rule-based fixes only (no extra API call)
            // This uses the same logic as SeoFixService but skips the AI API calls
            $fixResult = $this->applyRuleBasedSeoFixes($article, $initialScore);

            // Re-score after fixes
            $article->refresh();
            $finalScore = $this->seoScorer->scoreArticle($article);

            $scoreChange = $finalScore->total_score - $initialScore->total_score;

            AutoPostLog::create([
                'schedule_id' => $schedule->id,
                'article_id' => $article->id,
                'level' => $finalScore->total_score >= $targetScore ? 'success' : 'warning',
                'event' => 'seo_compliance_result',
                'message' => "🔧 SEO Fix: {$initialScore->total_score} → {$finalScore->total_score} (+".max(0, $scoreChange).") Grade {$finalScore->grade}",
                'context' => [
                    'initial_score' => $initialScore->total_score,
                    'final_score' => $finalScore->total_score,
                    'score_change' => $scoreChange,
                    'grade' => $finalScore->grade,
                    'fixes' => $fixResult['fixes'],
                    'remaining_issues' => count($finalScore->recommendations ?? []),
                ],
            ]);

            Log::info('🔧 SEO Compliance applied', [
                'article_id' => $article->id,
                'score' => $initialScore->total_score.' → '.$finalScore->total_score,
                'fixes' => count($fixResult['fixes']),
            ]);

            return [
                'initial_score' => $initialScore->total_score,
                'final_score' => $finalScore->total_score,
                'grade' => $finalScore->grade,
                'fixes_count' => count($fixResult['fixes']),
                'fixes' => $fixResult['fixes'],
                'api_saved' => true,
            ];

        } catch (\Exception $e) {
            Log::warning('⚠️ SEO compliance check failed (non-fatal)', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'initial_score' => 0,
                'final_score' => 0,
                'grade' => '?',
                'fixes_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Apply rule-based SEO fixes (no extra AI API calls) to save costs.
     * Fixes: meta_title (year, Bizmark, length), meta_description (CTA),
     * tags, excerpt, reading_time, slug — all deterministic.
     */
    public function applyRuleBasedSeoFixes(Article $article, $seoScore): array
    {
        $fixes = [];
        $changed = false;
        $factors = $seoScore->factors ?? [];

        // Fix meta_title: ensure year + Bizmark + ≤55 chars
        $titleScore = $factors['title']['score'] ?? 0;
        if ($titleScore < 15 && $article->meta_title) {
            $metaTitle = $article->meta_title;
            $year = date('Y');

            // Add year if missing
            if (! preg_match('/20\d{2}/', $metaTitle)) {
                if (mb_strlen($metaTitle) + strlen(" $year") <= 55) {
                    $metaTitle .= " $year";
                } elseif (mb_strlen($metaTitle) > 45) {
                    $metaTitle = Str::limit($metaTitle, 45, '')." $year";
                }
            }

            // Add Bizmark if missing
            if (! Str::contains($metaTitle, ['Bizmark', 'bizmark'], true)) {
                if (mb_strlen($metaTitle) + 10 <= 55) {
                    $metaTitle .= ' | Bizmark';
                } elseif (mb_strlen($metaTitle) > 40) {
                    $metaTitle = Str::limit($metaTitle, 40, '').' | Bizmark';
                }
            }

            if ($metaTitle !== $article->meta_title) {
                $article->meta_title = $metaTitle;
                $fixes[] = '⚙️ Meta title optimized for SEO';
                $changed = true;
            }
        }

        // Ensure meta_title differs from title (for +2 bonus)
        if ($article->meta_title === $article->title && $article->meta_title) {
            $year = date('Y');
            $suffix = " $year | Bizmark";
            $base = preg_replace('/\s*20\d{2}\s*/', ' ', $article->title);
            $base = preg_replace('/\s*\|?\s*[Bb]izmark\s*/', '', $base);
            $base = trim(Str::limit(trim($base), 55 - mb_strlen($suffix), ''));
            $article->meta_title = $base.$suffix;
            $fixes[] = '⚙️ Meta title differentiated from title';
            $changed = true;
        }

        // Fix meta_description: ensure CTA
        $descScore = $factors['meta_description']['score'] ?? 0;
        if ($descScore < 12 && $article->meta_description) {
            $desc = $article->meta_description;
            $hasCta = preg_match('/hubungi|konsultasi|pelajari|baca|dapatkan|gratis/i', $desc);
            if (! $hasCta) {
                $cta = ' Konsultasi gratis di Bizmark!';
                if (mb_strlen($desc) + mb_strlen($cta) <= 160) {
                    $article->meta_description = trim($desc).$cta;
                } else {
                    $article->meta_description = Str::limit($desc, 160 - mb_strlen($cta), '').$cta;
                }
                $fixes[] = '⚙️ CTA added to meta description';
                $changed = true;
            }
        }

        // Ensure meta_keywords has Bizmark
        if ($article->meta_keywords && ! Str::contains($article->meta_keywords, 'Bizmark', true)) {
            $article->meta_keywords .= ', Bizmark';
            $fixes[] = '⚙️ Bizmark added to meta keywords';
            $changed = true;
        }

        // Fix tags: ensure ≥2
        $tags = $article->tags ?? [];
        if (count($tags) < 2) {
            $fillers = [$article->category, 'Perizinan', 'Bizmark'];
            foreach ($fillers as $filler) {
                if ($filler && count($tags) < 3 && ! in_array($filler, $tags)) {
                    $tags[] = $filler;
                }
            }
            $article->tags = array_values(array_unique($tags));
            $fixes[] = '⚙️ Tags added for SEO';
            $changed = true;
        }

        // Fix reading_time if missing
        if (! $article->reading_time && $article->content) {
            $article->reading_time = max(1, ceil(str_word_count(strip_tags($article->content)) / 200));
            $fixes[] = '⚙️ Reading time calculated';
            $changed = true;
        }

        // Fix excerpt if too short
        if (! $article->excerpt || mb_strlen($article->excerpt) < 50) {
            $text = strip_tags($article->content ?? '');
            $article->excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', $text)), 180, '').'. Selengkapnya di Bizmark.';
            $fixes[] = '⚙️ Excerpt generated for SEO';
            $changed = true;
        }

        if ($changed) {
            $article->updated_at = now();
            $article->saveQuietly();
        }

        return ['fixes' => $fixes, 'changed' => $changed];
    }
}
