<?php

namespace App\Modules\ContentSeo\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CompetitorAnalysis;
use App\Models\SeoScore;
use App\Modules\ContentSeo\Controllers\Admin\Concerns\SeoAdminFlashRedirect;
use App\Services\SeoFixService;
use App\Services\SeoReportService;
use App\Services\SeoScoringService;
use App\Support\SeoDashboardCache;
use Illuminate\Http\Request;

class SeoScoresController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * SEO scores list for all articles
     */
    public function scores(Request $request)
    {
        $sort = $request->get('sort', 'score_asc');
        $filter = $request->get('filter', 'all');
        $search = $request->get('search', '');

        $query = SeoScore::with('article:id,title,slug,views_count,published_at,category');

        if ($search) {
            $query->whereHas('article', fn ($q) => $q->where('title', 'ilike', "%{$search}%"));
        }

        if ($filter === 'poor') {
            $query->where('total_score', '<', 60);
        } elseif ($filter === 'excellent') {
            $query->where('total_score', '>=', 80);
        }

        $query = match ($sort) {
            'score_asc' => $query->orderBy('total_score', 'asc'),
            'score_desc' => $query->orderBy('total_score', 'desc'),
            'recent' => $query->orderBy('scored_at', 'desc'),
            default => $query->orderBy('total_score', 'asc'),
        };

        $scores = $query->paginate(25);

        $summary = [
            'total' => SeoScore::count(),
            'avg' => round(SeoScore::avg('total_score') ?? 0, 1),
            'excellent' => SeoScore::excellent()->count(),
            'needs_work' => SeoScore::needsWork()->count(),
        ];

        return view('admin.seo.scores', compact('scores', 'summary', 'sort', 'filter', 'search'));
    }

    /**
     * Score detail for a single article
     */
    public function scoreDetail(int $articleId)
    {
        $article = Article::findOrFail($articleId);
        $score = SeoScore::where('article_id', $articleId)->first();

        if (! $score) {
            // Generate score on the fly
            $scorer = app(SeoScoringService::class);
            $score = $scorer->scoreArticle($article);
        }

        $viewTrends = app(SeoReportService::class)->getArticleTrends($articleId, 30);

        // Find matching competitor analysis for Smart Fix link
        $competitorAnalysis = CompetitorAnalysis::where(function ($q) use ($article) {
            $q->where('our_url', 'LIKE', '%'.$article->slug.'%')
                ->orWhere('keyword', 'LIKE', '%'.$article->title.'%');
        })
            ->orWhere(function ($q) use ($article) {
                if ($article->meta_keywords) {
                    foreach (explode(',', $article->meta_keywords) as $kw) {
                        $kw = trim($kw);
                        if (mb_strlen($kw) > 3) {
                            $q->orWhere('keyword', 'LIKE', "%{$kw}%");
                        }
                    }
                }
            })
            ->orderByDesc('analyzed_at')
            ->first();

        return view('admin.seo.score-detail', compact('article', 'score', 'viewTrends', 'competitorAnalysis'));
    }

    /**
     * Fix SEO issues for a single article (AI-powered)
     */
    public function fixSingle(int $articleId, SeoFixService $fixer, \Illuminate\Http\Request $request)
    {
        // GET requests (e.g. cached links) should redirect to score detail page
        if ($request->isMethod('get')) {
            return redirect()->route('admin.seo.score-detail', $articleId)
                ->with('info', 'Gunakan tombol "Auto-Fix SEO" di halaman ini untuk menjalankan perbaikan.');
        }

        $article = Article::findOrFail($articleId);
        $result = $fixer->fixArticle($article);

        $aiTag = $result['ai_powered'] ? '🤖 AI' : '⚙️ Rule-based';
        $fixList = implode("\n", $result['fixes_applied']);

        $msg = $result['fixes_count'] > 0
            ? "{$aiTag}: {$result['fixes_count']} perbaikan diterapkan. Skor: {$result['old_score']} → {$result['new_score']} ({$result['new_grade']}, +{$result['score_change']}). {$fixList}"
            : "Tidak ada perbaikan ditemukan. Skor tetap: {$result['new_score']} ({$result['new_grade']})";

        return $this->seoBackFlash('success', $msg);
    }

    /**
     * Batch fix all articles with scores below threshold (AI-powered)
     */
    public function fixBatch(Request $request, SeoFixService $fixer)
    {
        $threshold = (int) $request->get('threshold', 80);
        $result = $fixer->fixBatch([], $threshold);

        $details = collect($result['details'])
            ->filter(fn ($d) => $d['fixes_count'] > 0)
            ->map(fn ($d) => "• {$d['title']}: {$d['old_score']}→{$d['new_score']} (+{$d['score_change']}, {$d['fixes_count']} fix)")
            ->implode("\n");

        $msg = "🤖 AI Batch Fix selesai!\n"
             ."📊 {$result['total_processed']} artikel diproses, {$result['total_fixed']} diperbaiki\n"
             ."🔧 {$result['total_fixes']} total perbaikan ({$result['ai_powered_count']} AI-powered)\n"
             ."📈 Rata-rata skor baru: {$result['avg_new_score']} (perubahan: +{$result['avg_score_change']})\n"
             .($details ? "\nDetail:\n{$details}" : '');

        return $this->seoRouteFlash('admin.seo.scores', 'success', $msg);
    }

    /**
     * Return JSON list of article IDs that need fixing (for AJAX batch)
     */
    public function fixCandidates(Request $request)
    {
        $threshold = (int) $request->get('threshold', 80);

        $belowThreshold = SeoScore::where('total_score', '<', $threshold)
            ->with('article:id,title')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->article_id,
                'title' => $s->article?->title ?? 'Deleted',
                'score' => $s->total_score,
            ]);

        $unscored = Article::where('status', 'published')
            ->whereDoesntHave('seoScore')
            ->select('id', 'title')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'score' => 0,
            ]);

        $candidates = $belowThreshold->merge($unscored)->unique('id')->values();

        return response()->json(['candidates' => $candidates, 'total' => $candidates->count()]);
    }

    /**
     * Fix single article via AJAX — returns JSON
     */
    public function fixSingleAjax(int $articleId, SeoFixService $fixer)
    {
        $article = Article::findOrFail($articleId);
        $result = $fixer->fixArticle($article);

        return response()->json($result);
    }

    /**
     * Re-score all published articles
     */
    public function rescoreAll(SeoScoringService $scorer)
    {
        $result = $scorer->scoreAll();

        SeoDashboardCache::forgetStatsCaches();

        $msg = "{$result['scored']} artikel di-rescore. Rata-rata: {$result['avg_score']}";

        return $this->seoRouteFlash('admin.seo.scores', 'success', $msg);
    }

    /**
     * Score articles via AJAX with progress — returns JSON for each article
     * Used for the progress modal on scores page.
     */
    public function scoreArticlesProgress(Request $request, SeoScoringService $scorer)
    {
        $articleId = (int) $request->get('article_id');

        if ($articleId) {
            $article = Article::findOrFail($articleId);
            $score = $scorer->scoreArticle($article);

            return response()->json([
                'id' => $article->id,
                'title' => $article->title,
                'score' => $score->total_score,
                'grade' => $score->grade,
                'recommendations' => count($score->recommendations ?? []),
            ]);
        }

        // Return list of unscored articles
        $unscored = Article::where('status', 'published')
            ->whereDoesntHave('seoScore')
            ->select('id', 'title')
            ->get()
            ->map(fn ($a) => ['id' => $a->id, 'title' => $a->title]);

        return response()->json([
            'candidates' => $unscored->values(),
            'total' => $unscored->count(),
        ]);
    }
}
