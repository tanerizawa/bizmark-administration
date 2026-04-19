<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Admin\Seo\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CompetitorAnalysis;
use App\Models\SeoScore;
use App\Services\CompetitiveIntelligenceService;
use App\Services\SeoFixService;
use App\Services\SeoScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SeoCompetitorsController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * Competitive Intelligence dashboard
     */
    public function competitors(Request $request)
    {
        $query = CompetitorAnalysis::orderByDesc('analyzed_at');

        // Filters
        if ($difficulty = $request->get('difficulty')) {
            $query->where('difficulty', $difficulty);
        }
        if ($position = $request->get('position')) {
            match ($position) {
                'top10' => $query->whereNotNull('our_position')->where('our_position', '<=', 10),
                'opportunity' => $query->whereNotNull('our_position')->whereBetween('our_position', [11, 30]),
                'low' => $query->whereNotNull('our_position')->where('our_position', '>', 30),
                'unranked' => $query->whereNull('our_position'),
                default => null,
            };
        }
        if ($request->get('has_gaps') === '1') {
            $query->whereNotNull('content_gaps')->whereJsonLength('content_gaps', '>', 0);
        }
        if ($search = $request->get('search')) {
            $query->where('keyword', 'LIKE', "%{$search}%");
        }

        $analyses = $query->paginate(20)->appends($request->query());

        $summary = [
            'total_analyzed' => CompetitorAnalysis::count(),
            'ranking_top10' => CompetitorAnalysis::whereNotNull('our_position')->where('our_position', '<=', 10)->count(),
            'opportunity' => CompetitorAnalysis::whereNotNull('our_position')->whereBetween('our_position', [11, 30])->count(),
            'not_ranking' => CompetitorAnalysis::whereNull('our_position')->count(),
            'total_gaps' => CompetitorAnalysis::whereNotNull('content_gaps')
                ->get()->sum(fn($a) => count($a->content_gaps ?? [])),
            'avg_position' => round(CompetitorAnalysis::whereNotNull('our_position')->avg('our_position') ?? 0, 1),
        ];

        return view('admin.seo.competitors', compact('analyses', 'summary'));
    }

    /**
     * Competitor detail — enhanced with linked article & SEO score
     */
    public function competitorDetail(int $id)
    {
        $analysis = CompetitorAnalysis::findOrFail($id);

        $article = $this->findLinkedArticle($analysis);
        $seoScore = $article ? SeoScore::where('article_id', $article->id)->first() : null;

        // Find previous analysis for the same keyword (for trend)
        $previousAnalysis = CompetitorAnalysis::where('keyword', $analysis->keyword)
            ->where('id', '<', $analysis->id)
            ->orderByDesc('analyzed_at')
            ->first();

        return view('admin.seo.competitor-detail', compact('analysis', 'article', 'seoScore', 'previousAnalysis'));
    }

    /**
     * Re-analyze a single keyword from competitor detail
     */
    public function reAnalyzeKeyword(int $id, CompetitiveIntelligenceService $ciService)
    {
        $old = CompetitorAnalysis::findOrFail($id);
        $newAnalysis = $ciService->analyzeKeyword($old->keyword);

        if ($newAnalysis) {
            $msg = "🔄 Re-analisis selesai untuk \"{$old->keyword}\".\nPosisi: "
                . ($old->our_position ? "#{$old->our_position}" : 'N/A') . ' → '
                . ($newAnalysis->our_position ? "#{$newAnalysis->our_position}" : 'N/A');

            return $this->seoRouteFlash('admin.seo.competitor-detail', 'success', $msg, $newAnalysis->id);
        }

        return $this->seoBackFlash('error', "❌ Gagal re-analisis keyword \"{$old->keyword}\". Cek log untuk detail.");
    }

    /**
     * Analyze a custom keyword entered by user
     */
    public function analyzeCustomKeyword(Request $request, CompetitiveIntelligenceService $ciService)
    {
        $request->validate(['keyword' => 'required|string|min:3|max:200']);
        $keyword = trim($request->input('keyword'));

        $analysis = $ciService->analyzeKeyword($keyword);

        if ($analysis) {
            return $this->seoRouteFlash('admin.seo.competitor-detail', 'success', "🔍 Analisis selesai untuk \"{$keyword}\".", $analysis->id);
        }

        return $this->seoRouteFlash('admin.seo.competitors', 'error', "❌ Gagal menganalisis keyword \"{$keyword}\". Cek log untuk detail.");
    }

    /**
     * Apply smart fix to the article linked to a competitor analysis
     */
    public function applyCompetitorFix(int $id, SeoFixService $fixer)
    {
        $analysis = CompetitorAnalysis::findOrFail($id);

        // Find linked article
        $article = null;
        if ($analysis->our_url) {
            $slug = basename(parse_url($analysis->our_url, PHP_URL_PATH));
            $article = Article::where('slug', $slug)->first();
        }
        if (!$article) {
            $article = Article::published()
                ->where(function ($q) use ($analysis) {
                    $q->where('title', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_keywords', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_title', 'LIKE', "%{$analysis->keyword}%");
                })
                ->orderByDesc('views_count')
                ->first();
        }

        if (!$article) {
            return $this->seoBackFlash('error', '❌ Tidak ada artikel terkait yang ditemukan untuk keyword ini.');
        }

        $result = $fixer->fixArticle($article);

        $aiTag = $result['ai_powered'] ? '🤖 AI' : '⚙️ Rule-based';
        $fixList = implode("\n", $result['fixes_applied']);

        $msg = $result['fixes_count'] > 0
            ? "{$aiTag}: {$result['fixes_count']} perbaikan diterapkan untuk \"{$article->title}\".\nSkor: {$result['old_score']} → {$result['new_score']} ({$result['new_grade']}, +{$result['score_change']})\n{$fixList}"
            : "✅ Artikel \"{$article->title}\" sudah optimal. Skor: {$result['new_score']} ({$result['new_grade']})";

        return $this->seoBackFlash('success', $msg);
    }

    /**
     * Verify/re-score the article linked to a competitor analysis
     */
    public function verifyCompetitorFix(int $id, SeoScoringService $scorer)
    {
        $analysis = CompetitorAnalysis::findOrFail($id);

        $article = null;
        if ($analysis->our_url) {
            $slug = basename(parse_url($analysis->our_url, PHP_URL_PATH));
            $article = Article::where('slug', $slug)->first();
        }
        if (!$article) {
            $article = Article::published()
                ->where(function ($q) use ($analysis) {
                    $q->where('title', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_keywords', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_title', 'LIKE', "%{$analysis->keyword}%");
                })
                ->orderByDesc('views_count')
                ->first();
        }

        if (!$article) {
            return $this->seoBackFlash('error', '❌ Tidak ada artikel terkait yang ditemukan untuk keyword ini.');
        }

        $score = $scorer->scoreArticle($article);

        $msg = "✅ Verifikasi selesai untuk \"{$article->title}\".\nSkor: {$score->total_score} ({$score->grade})\nRekomendasi tersisa: " . count($score->recommendations ?? []);

        return $this->seoBackFlash('success', $msg);
    }

    /**
     * Smart Fix page — comprehensive AI-powered fix based on competitor analysis
     */
    public function competitorSmartFix(int $id)
    {
        $analysis = CompetitorAnalysis::findOrFail($id);

        // Find linked article
        $article = $this->findLinkedArticle($analysis);
        $seoScore = $article ? SeoScore::where('article_id', $article->id)->first() : null;

        return view('admin.seo.competitor-smart-fix', compact('analysis', 'article', 'seoScore'));
    }

    /**
     * Execute comprehensive smart fix via AJAX — returns JSON progress
     */
    public function executeSmartFix(int $id, \App\Services\CompetitorSmartFixService $fixer)
    {
        $analysis = CompetitorAnalysis::findOrFail($id);
        $article = $this->findLinkedArticle($analysis);

        if (!$article) {
            // Auto-create article from competitor analysis context
            $result = $fixer->createAndFix($analysis);
        } else {
            $result = $fixer->fix($analysis, $article);
        }

        // Defensive fallback: always provide canonical edit URL (slug-based route model binding).
        if (empty($result['article_edit_url']) && !empty($result['article_id'])) {
            $resultArticle = Article::find($result['article_id']);
            if ($resultArticle) {
                $result['article_edit_url'] = route('articles.edit', $resultArticle);
                $result['article_slug'] = $resultArticle->slug;
            }
        }

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Find the article linked to a competitor analysis by URL or keyword match.
     */
    protected function findLinkedArticle(CompetitorAnalysis $analysis): ?Article
    {
        $article = null;

        if ($analysis->our_url) {
            $slug = basename(parse_url($analysis->our_url, PHP_URL_PATH));
            $article = Article::where('slug', $slug)->first();
        }

        // Exact keyword match
        if (!$article) {
            $article = Article::published()
                ->where(function ($q) use ($analysis) {
                    $q->where('title', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_keywords', 'LIKE', "%{$analysis->keyword}%")
                      ->orWhere('meta_title', 'LIKE', "%{$analysis->keyword}%");
                })
                ->orderByDesc('views_count')
                ->first();
        }

        // Fuzzy match: split keyword into significant words (>3 chars) and find best match
        if (!$article) {
            $words = collect(explode(' ', $analysis->keyword))
                ->map(fn($w) => mb_strtolower(trim($w)))
                ->filter(fn($w) => mb_strlen($w) > 3)
                ->values();

            if ($words->isNotEmpty()) {
                $orderBindings = $words
                    ->map(fn ($w) => '%' . $w . '%')
                    ->all();

                $article = Article::published()
                    ->where(function ($q) use ($words) {
                        foreach ($words as $word) {
                            $q->orWhere('title', 'LIKE', "%{$word}%")
                              ->orWhere('slug', 'LIKE', "%{$word}%");
                        }
                    })
                    ->orderByRaw(
                        '(' . $words->map(fn () => 'CASE WHEN LOWER(title) LIKE ? THEN 1 ELSE 0 END')->implode(' + ') . ') DESC',
                        $orderBindings
                    )
                    ->first();
            }
        }

        return $article;
    }

    /**
     * Run competitor analysis via web (replaces: php artisan seo:competitor-analyze)
     */
    public function runCompetitorAnalyze()
    {
        Artisan::call('seo:competitor-analyze');
        $output = trim(Artisan::output());

        return $this->seoRouteFlash('admin.seo.competitors', 'success', "🔍 Analisis kompetitor selesai.\n{$output}");
    }
}
