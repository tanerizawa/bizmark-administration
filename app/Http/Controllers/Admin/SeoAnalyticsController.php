<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleViewLog;
use App\Models\CompetitorAnalysis;
use App\Models\ContentRefreshLog;
use App\Models\ContentSyndication;
use App\Models\KeywordCluster;
use App\Models\KeywordPositionHistory;
use App\Models\MetaAbTest;
use App\Models\RankingAlert;
use App\Models\SearchConsoleData;
use App\Models\SeoReport;
use App\Models\SeoScore;
use App\Models\TopicCluster;
use App\Services\CompetitiveIntelligenceService;
use App\Services\MetaAbTestService;
use App\Services\SearchConsoleService;
use App\Services\ContentRefreshService;
use App\Services\SeoFixService;
use App\Services\SeoReportService;
use App\Services\SeoScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SeoAnalyticsController extends Controller
{
    /**
     * SEO Command Center — main dashboard
     */
    public function dashboard(Request $request, SeoReportService $reportService)
    {
        $period = $request->get('period', '30days');
        $days = match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 30,
        };

        // KPI stats
        $stats = Cache::remember("seo_dashboard_stats_{$period}", 300, function () use ($days) {
            $publishedCount = Article::where('status', 'published')->count();
            $totalViews = Article::where('status', 'published')->sum('views_count');
            $avgViews = $publishedCount > 0 ? round($totalViews / $publishedCount) : 0;
            $avgSeoScore = round(SeoScore::avg('total_score') ?? 0, 1);

            // Sitemap URLs
            $sitemapUrls = 0;
            $sitemapPath = public_path('sitemap.xml');
            if (file_exists($sitemapPath)) {
                libxml_use_internal_errors(true);
                $xml = simplexml_load_file($sitemapPath);
                if ($xml) $sitemapUrls = count($xml->url);
                libxml_clear_errors();
            }

            // Recent articles (last 30 days)
            $recentArticles = Article::where('status', 'published')
                ->where('published_at', '>=', now()->subDays(30))
                ->count();

            return compact(
                'publishedCount', 'totalViews', 'avgViews', 'avgSeoScore',
                'sitemapUrls', 'recentArticles'
            );
        });

        // View trends
        $viewTrends = $reportService->getSiteTrends($days);

        // SEO score distribution
        $scoreDistribution = SeoScore::selectRaw("
            CASE
                WHEN total_score >= 80 THEN 'excellent'
                WHEN total_score >= 60 THEN 'good'
                WHEN total_score >= 40 THEN 'average'
                ELSE 'poor'
            END as grade_group,
            COUNT(*) as cnt
        ")->groupBy('grade_group')->pluck('cnt', 'grade_group')->toArray();

        // Top articles by views
        $topArticles = Article::where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'views_count', 'published_at', 'category']);

        // Worst SEO scores — need attention
        $worstScores = SeoScore::with('article:id,title,slug')
            ->orderBy('total_score', 'asc')
            ->limit(10)
            ->get();

        // Infrastructure counts
        $infra = [
            'keyword_clusters' => KeywordCluster::count(),
            'topic_clusters' => TopicCluster::count(),
            'syndications_published' => ContentSyndication::where('status', 'published')->count(),
            'syndications_pending' => ContentSyndication::where('status', 'pending')->count(),
            'push_subscribers' => DB::table('push_subscriptions')->count(),
        ];

        // Latest report
        $latestReport = SeoReport::latest()->first();

        // Recent alerts
        $alerts = $latestReport?->alerts ?? [];

        // Recent competitor analyses with content gaps (for Smart Fix)
        $recentCompetitorAnalyses = CompetitorAnalysis::whereNotNull('content_gaps')
            ->whereJsonLength('content_gaps', '>', 0)
            ->orderByDesc('analyzed_at')
            ->limit(5)
            ->get();

        return view('admin.seo.dashboard', compact(
            'stats', 'viewTrends', 'scoreDistribution', 'topArticles',
            'worstScores', 'infra', 'latestReport', 'alerts', 'period',
            'recentCompetitorAnalyses'
        ));
    }

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
            $query->whereHas('article', fn($q) => $q->where('title', 'ilike', "%{$search}%"));
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

        if (!$score) {
            // Generate score on the fly
            $scorer = app(SeoScoringService::class);
            $score = $scorer->scoreArticle($article);
        }

        $viewTrends = app(SeoReportService::class)->getArticleTrends($articleId, 30);

        // Find matching competitor analysis for Smart Fix link
        $competitorAnalysis = CompetitorAnalysis::where(function ($q) use ($article) {
                $q->where('our_url', 'LIKE', '%' . $article->slug . '%')
                  ->orWhere('keyword', 'LIKE', '%' . $article->title . '%');
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
     * Reports list
     */
    public function reports()
    {
        $reports = SeoReport::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.seo.reports', compact('reports'));
    }

    /**
     * Report detail
     */
    public function reportDetail(int $reportId)
    {
        $report = SeoReport::findOrFail($reportId);
        return view('admin.seo.report-detail', compact('report'));
    }

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
            return redirect()->route('admin.seo.competitor-detail', $newAnalysis->id)
                ->with('success', "🔄 Re-analisis selesai untuk \"{$old->keyword}\".\nPosisi: " . ($old->our_position ? "#{$old->our_position}" : 'N/A') . " → " . ($newAnalysis->our_position ? "#{$newAnalysis->our_position}" : 'N/A'));
        }

        return redirect()->back()->with('error', "❌ Gagal re-analisis keyword \"{$old->keyword}\". Cek log untuk detail.");
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
            return redirect()->route('admin.seo.competitor-detail', $analysis->id)
                ->with('success', "🔍 Analisis selesai untuk \"{$keyword}\".");
        }

        return redirect()->route('admin.seo.competitors')
            ->with('error', "❌ Gagal menganalisis keyword \"{$keyword}\". Cek log untuk detail.");
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
            return redirect()->back()->with('error', '❌ Tidak ada artikel terkait yang ditemukan untuk keyword ini.');
        }

        $result = $fixer->fixArticle($article);

        $aiTag = $result['ai_powered'] ? '🤖 AI' : '⚙️ Rule-based';
        $fixList = implode("\n", $result['fixes_applied']);

        $msg = $result['fixes_count'] > 0
            ? "{$aiTag}: {$result['fixes_count']} perbaikan diterapkan untuk \"{$article->title}\".\nSkor: {$result['old_score']} → {$result['new_score']} ({$result['new_grade']}, +{$result['score_change']})\n{$fixList}"
            : "✅ Artikel \"{$article->title}\" sudah optimal. Skor: {$result['new_score']} ({$result['new_grade']})";

        return redirect()->back()->with('success', $msg);
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
            return redirect()->back()->with('error', '❌ Tidak ada artikel terkait yang ditemukan untuk keyword ini.');
        }

        $score = $scorer->scoreArticle($article);

        $msg = "✅ Verifikasi selesai untuk \"{$article->title}\".\nSkor: {$score->total_score} ({$score->grade})\nRekomendasi tersisa: " . count($score->recommendations ?? []);

        return redirect()->back()->with('success', $msg);
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
                $article = Article::published()
                    ->where(function ($q) use ($words) {
                        foreach ($words as $word) {
                            $q->orWhere('title', 'LIKE', "%{$word}%")
                              ->orWhere('slug', 'LIKE', "%{$word}%");
                        }
                    })
                    ->orderByRaw('(' . $words->map(fn($w) => "CASE WHEN LOWER(title) LIKE '%{$w}%' THEN 1 ELSE 0 END")->implode(' + ') . ') DESC')
                    ->first();
            }
        }

        return $article;
    }

    /**
     * Meta A/B Tests management
     */
    public function abTests(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = MetaAbTest::with('article:id,title,slug,views_count');

        if ($filter === 'running') {
            $query->where('status', 'running');
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed');
        }

        $tests = $query->orderByDesc('created_at')->paginate(20);

        $summary = [
            'running' => MetaAbTest::where('status', 'running')->count(),
            'completed' => MetaAbTest::where('status', 'completed')->count(),
            'b_wins' => MetaAbTest::where('winner', 'b')->count(),
            'a_wins' => MetaAbTest::where('winner', 'a')->count(),
            'inconclusive' => MetaAbTest::where('winner', 'inconclusive')->count(),
        ];

        return view('admin.seo.ab-tests', compact('tests', 'summary', 'filter'));
    }

    /**
     * Search Console data dashboard
     */
    public function searchConsole(Request $request, SearchConsoleService $service)
    {
        $days = (int) $request->get('days', 30);

        $summary = $service->getSummary($days);
        $topQueries = $service->getTopQueries($days, 20);
        $topPages = $service->getTopPages($days, 20);
        $opportunities = $service->getOpportunities($days);
        $lastImport = SearchConsoleData::max('updated_at');

        return view('admin.seo.search-console', compact('summary', 'topQueries', 'topPages', 'opportunities', 'days', 'lastImport'));
    }

    /**
     * Import/sync Search Console data
     */
    public function importSearchConsole(Request $request, SearchConsoleService $service)
    {
        $days = (int) $request->get('days', 7);
        $result = $service->importFromGSC($days);

        $source = $result['source'] === 'simulated' ? 'Simulated' : 'Google API';
        return redirect()->route('admin.seo.search-console', ['days' => $days])
            ->with('success', "{$result['imported']} records imported ({$source}).");
    }

    /**
     * Clear all Search Console data
     */
    public function clearSearchConsole()
    {
        $count = SearchConsoleData::count();
        SearchConsoleData::truncate();

        return redirect()->route('admin.seo.search-console')
            ->with('success', "{$count} records cleared.");
    }

    /**
     * Content Refresh audit logs
     */
    public function refreshLogs(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = ContentRefreshLog::with('article:id,title,slug');

        if ($filter === 'refreshed') {
            $query->where('status', 'refreshed');
        } elseif ($filter === 'error') {
            $query->where('status', 'error');
        }

        $logs = $query->orderByDesc('created_at')->paginate(25);

        $stats = ContentRefreshLog::selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'refreshed' THEN 1 ELSE 0 END) as refreshed, SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors, MAX(created_at) as last_run, COALESCE(SUM(ai_tokens_used), 0) as total_tokens")->first();

        $summary = [
            'total' => (int) $stats->total,
            'refreshed' => (int) $stats->refreshed,
            'errors' => (int) $stats->errors,
            'last_run' => $stats->last_run ? \Carbon\Carbon::parse($stats->last_run) : null,
            'total_tokens' => (int) $stats->total_tokens,
        ];

        return view('admin.seo.refresh-logs', compact('logs', 'summary', 'filter'));
    }

    /**
     * Run content refresh on stale articles
     */
    public function runContentRefresh(Request $request, ContentRefreshService $service)
    {
        $limit = min((int) $request->input('limit', 2), 5);
        $stale = $service->getStaleArticles(90, $limit);

        if ($stale->isEmpty()) {
            return redirect()->route('admin.seo.refresh-logs')->with('success', 'Tidak ada artikel stale yang perlu di-refresh (semua sudah diperbarui dalam 90 hari terakhir).');
        }

        $results = ['refreshed' => 0, 'errors' => 0];
        foreach ($stale as $article) {
            $r = $service->refreshArticle($article, 'manual');
            $r['status'] === 'refreshed' ? $results['refreshed']++ : $results['errors']++;
        }

        return redirect()->route('admin.seo.refresh-logs')->with('success', "Content refresh selesai: {$results['refreshed']} berhasil, {$results['errors']} error dari {$stale->count()} artikel.");
    }

    /**
     * Retry refresh for a specific failed log entry
     */
    public function retryRefresh(int $id, ContentRefreshService $service)
    {
        $log = ContentRefreshLog::with('article')->findOrFail($id);

        if (!$log->article) {
            return redirect()->route('admin.seo.refresh-logs')->with('error', 'Artikel sudah dihapus, tidak bisa retry.');
        }

        $result = $service->refreshArticle($log->article, 'manual');

        return redirect()->route('admin.seo.refresh-logs')->with('success', "Retry untuk \"{$log->article->title}\": status {$result['status']}.");
    }

    /**
     * Delete a refresh log entry
     */
    public function deleteRefreshLog(int $id)
    {
        $log = ContentRefreshLog::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.seo.refresh-logs')->with('success', 'Log entry berhasil dihapus.');
    }

    /**
     * Show detail of a refresh log (for before/after snapshots)
     */
    public function showRefreshLog(int $id)
    {
        $log = ContentRefreshLog::with('article:id,title,slug')->findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'article_title' => $log->article->title ?? 'Deleted',
            'status' => $log->status,
            'triggered_by' => $log->triggered_by,
            'ai_tokens_used' => $log->ai_tokens_used,
            'changes' => $log->changes,
            'before_snapshot' => $log->before_snapshot,
            'after_snapshot' => $log->after_snapshot,
            'error_message' => $log->error_message,
            'created_at' => $log->created_at->format('d M Y H:i:s'),
        ]);
    }

    /**
     * Programmatic SEO stats page
     */
    public function programmatic(Request $request)
    {
        $config = config('programmatic_seo');
        $cities = $config['cities'] ?? [];
        $serviceSlugs = $config['services'] ?? [];
        $servicesData = config('services_data', []);

        // Enrich services with name/icon from services_data
        $services = collect($serviceSlugs)->map(function ($slug) use ($servicesData) {
            $data = $servicesData[$slug] ?? [];
            return [
                'slug' => $slug,
                'title' => $data['title'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'icon' => $data['icon'] ?? 'fa-cog',
                'color' => $data['color'] ?? '#6B7280',
                'category' => $data['category'] ?? '-',
            ];
        });

        // Filter by province if requested
        $provinceFilter = $request->get('province');
        $filteredCities = $cities;
        if ($provinceFilter) {
            $filteredCities = collect($cities)->filter(fn($c) => ($c['province'] ?? '') === $provinceFilter)->all();
        }

        $stats = [
            'total_cities' => count($cities),
            'total_services' => $services->count(),
            'total_pages' => count($cities) * $services->count() + count($cities),
            'service_location_pages' => count($cities) * $services->count(),
            'city_index_pages' => count($cities),
        ];

        // Group cities by province
        $byProvince = collect($cities)->groupBy('province')->map->count()->sortDesc();

        return view('admin.seo.programmatic', compact('stats', 'cities', 'filteredCities', 'services', 'byProvince', 'provinceFilter'));
    }

    /**
     * Clear programmatic SEO cache
     */
    public function clearProgrammaticCache()
    {
        $cleared = 0;
        $config = config('programmatic_seo');
        $cities = $config['cities'] ?? [];
        $services = $config['services'] ?? [];

        foreach ($cities as $city) {
            $slug = $city['slug'] ?? '';
            if ($slug && \Illuminate\Support\Facades\Cache::forget("programmatic_city_{$slug}")) {
                $cleared++;
            }
            foreach ($services as $svc) {
                if (\Illuminate\Support\Facades\Cache::forget("programmatic_{$svc}_{$slug}")) {
                    $cleared++;
                }
            }
        }

        return redirect()->route('admin.seo.programmatic')->with('success', "Cache programmatic SEO di-clear. {$cleared} entri dihapus.");
    }

    /**
     * Fix SEO issues for a single article (AI-powered)
     */
    public function fixSingle(int $articleId, SeoFixService $fixer)
    {
        $article = Article::findOrFail($articleId);
        $result = $fixer->fixArticle($article);

        $aiTag = $result['ai_powered'] ? '🤖 AI' : '⚙️ Rule-based';
        $fixList = implode("\n", $result['fixes_applied']);

        $msg = $result['fixes_count'] > 0
            ? "{$aiTag}: {$result['fixes_count']} perbaikan diterapkan. Skor: {$result['old_score']} → {$result['new_score']} ({$result['new_grade']}, +{$result['score_change']}). {$fixList}"
            : "Tidak ada perbaikan ditemukan. Skor tetap: {$result['new_score']} ({$result['new_grade']})";

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Batch fix all articles with scores below threshold (AI-powered)
     */
    public function fixBatch(Request $request, SeoFixService $fixer)
    {
        $threshold = (int) $request->get('threshold', 80);
        $result = $fixer->fixBatch([], $threshold);

        $details = collect($result['details'])
            ->filter(fn($d) => $d['fixes_count'] > 0)
            ->map(fn($d) => "• {$d['title']}: {$d['old_score']}→{$d['new_score']} (+{$d['score_change']}, {$d['fixes_count']} fix)")
            ->implode("\n");

        $msg = "🤖 AI Batch Fix selesai!\n"
             . "📊 {$result['total_processed']} artikel diproses, {$result['total_fixed']} diperbaiki\n"
             . "🔧 {$result['total_fixes']} total perbaikan ({$result['ai_powered_count']} AI-powered)\n"
             . "📈 Rata-rata skor baru: {$result['avg_new_score']} (perubahan: +{$result['avg_score_change']})\n"
             . ($details ? "\nDetail:\n{$details}" : '');

        return redirect()->route('admin.seo.scores')->with('success', $msg);
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
            ->map(fn($s) => [
                'id' => $s->article_id,
                'title' => $s->article?->title ?? 'Deleted',
                'score' => $s->total_score,
            ]);

        $unscored = Article::where('status', 'published')
            ->whereDoesntHave('seoScore')
            ->select('id', 'title')
            ->get()
            ->map(fn($a) => [
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

        // Clear dashboard cache so KPIs reflect new scores
        Cache::forget('seo_dashboard_stats_7days');
        Cache::forget('seo_dashboard_stats_30days');
        Cache::forget('seo_dashboard_stats_90days');

        $msg = "{$result['scored']} artikel di-rescore. Rata-rata: {$result['avg_score']}";

        return redirect()->route('admin.seo.scores')->with('success', $msg);
    }

    /**
     * Snapshot daily views via web (replaces: php artisan seo:snapshot-views)
     */
    public function runSnapshotViews(SeoReportService $reportService)
    {
        $count = $reportService->snapshotDailyViews();

        Cache::forget('seo_dashboard_stats_7days');
        Cache::forget('seo_dashboard_stats_30days');
        Cache::forget('seo_dashboard_stats_90days');

        return redirect()->back()->with('success', "📸 View snapshot selesai: {$count} artikel terekam.");
    }

    /**
     * Generate weekly report via web (replaces: php artisan seo:weekly-report)
     */
    public function runGenerateReport(Request $request, SeoReportService $reportService)
    {
        $type = $request->get('type', 'weekly');

        $report = $type === 'monthly'
            ? $reportService->generateMonthlyReport()
            : $reportService->generateWeeklyReport();

        $period = ucfirst($report->period);
        $msg = "📋 {$period} report berhasil di-generate ({$report->period_start->format('d M')} — {$report->period_end->format('d M Y')}).";

        return redirect()->route('admin.seo.reports')->with('success', $msg);
    }

    /**
     * Run competitor analysis via web (replaces: php artisan seo:competitor-analyze)
     */
    public function runCompetitorAnalyze()
    {
        Artisan::call('seo:competitor-analyze');
        $output = trim(Artisan::output());

        return redirect()->route('admin.seo.competitors')->with('success', "🔍 Analisis kompetitor selesai.\n{$output}");
    }

    /**
     * Run A/B test generation via web (replaces: php artisan seo:meta-ab-test --all)
     */
    public function runAbTests()
    {
        Artisan::call('seo:meta-ab-test', ['--all' => true]);
        $output = trim(Artisan::output());

        return redirect()->route('admin.seo.ab-tests')->with('success', "A/B test generation selesai.\n{$output}");
    }

    /**
     * Evaluate a single A/B test
     */
    public function evaluateAbTest(int $id, MetaAbTestService $service)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Test sudah selesai.');
        }

        $totalImpressions = $test->variant_a_impressions + $test->variant_b_impressions;
        if ($totalImpressions < 2) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Data belum cukup untuk evaluasi. Masukkan data impressions & clicks terlebih dahulu.');
        }

        // Force evaluate this test regardless of age
        $ctrA = $test->ctr_a;
        $ctrB = $test->ctr_b;
        $confidence = $this->calculateAbTestConfidence($test);

        if ($confidence >= 90) {
            $winner = $ctrB > $ctrA ? 'b' : 'a';
        } elseif ($confidence >= 70) {
            $winner = $ctrB > $ctrA ? 'b' : 'a';
        } else {
            $winner = 'inconclusive';
        }

        $test->update([
            'winner' => $winner,
            'confidence' => $confidence,
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        $winnerLabel = $winner === 'b' ? 'B (AI)' : ($winner === 'a' ? 'A (Original)' : 'Inconclusive');
        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$test->id} dievaluasi: Winner = {$winnerLabel} (confidence {$confidence}%)");
    }

    /**
     * Stop/cancel a running A/B test
     */
    public function stopAbTest(int $id)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Test tidak sedang berjalan.');
        }

        $test->update([
            'status' => 'completed',
            'winner' => 'inconclusive',
            'ended_at' => now(),
        ]);

        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$test->id} dihentikan.");
    }

    /**
     * Apply winning variant B to the article's meta tags
     */
    public function applyAbTestWinner(int $id)
    {
        $test = MetaAbTest::with('article')->findOrFail($id);

        if ($test->winner !== 'b') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Hanya variant B (AI) yang bisa diterapkan.');
        }

        $article = $test->article;
        if (!$article) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Artikel tidak ditemukan.');
        }

        if ($test->variant_b_title) {
            $article->meta_title = $test->variant_b_title;
        }
        if ($test->variant_b_description) {
            $article->meta_description = $test->variant_b_description;
        }
        $article->save();

        return redirect()->route('admin.seo.ab-tests')->with('success', "Meta tags variant B berhasil diterapkan ke artikel \"{$article->title}\"");
    }

    /**
     * Update impressions/clicks data for a running test (manual input)
     */
    public function updateAbTestData(Request $request, int $id)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Hanya test yang running bisa diupdate datanya.');
        }

        $validated = $request->validate([
            'variant_a_impressions' => 'required|integer|min:0',
            'variant_a_clicks' => 'required|integer|min:0',
            'variant_b_impressions' => 'required|integer|min:0',
            'variant_b_clicks' => 'required|integer|min:0',
        ]);

        // Ensure clicks <= impressions
        if ($validated['variant_a_clicks'] > $validated['variant_a_impressions'] ||
            $validated['variant_b_clicks'] > $validated['variant_b_impressions']) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Clicks tidak boleh lebih besar dari impressions.');
        }

        $test->update($validated);

        return redirect()->route('admin.seo.ab-tests')->with('success', "Data test #{$test->id} berhasil diupdate.");
    }

    /**
     * Delete an A/B test
     */
    public function deleteAbTest(int $id)
    {
        $test = MetaAbTest::findOrFail($id);
        $testId = $test->id;
        $test->delete();

        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$testId} berhasil dihapus.");
    }

    /**
     * Evaluate all running tests at once
     */
    public function evaluateAllAbTests(MetaAbTestService $service)
    {
        $results = $service->evaluateTests();
        $count = count($results);

        if ($count === 0) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Tidak ada test yang siap dievaluasi (butuh min 7 hari & 100 impressions).');
        }

        return redirect()->route('admin.seo.ab-tests')->with('success', "{$count} test berhasil dievaluasi.");
    }

    /**
     * Calculate confidence for single test evaluation
     */
    private function calculateAbTestConfidence(MetaAbTest $test): float
    {
        $nA = max($test->variant_a_impressions, 1);
        $nB = max($test->variant_b_impressions, 1);
        $pA = $test->variant_a_clicks / $nA;
        $pB = $test->variant_b_clicks / $nB;

        $pPool = ($test->variant_a_clicks + $test->variant_b_clicks) / ($nA + $nB);
        $se = sqrt($pPool * (1 - $pPool) * (1 / $nA + 1 / $nB));

        if ($se == 0) return 0;

        $z = abs($pA - $pB) / $se;

        if ($z >= 2.576) return 99;
        if ($z >= 1.960) return 95;
        if ($z >= 1.645) return 90;
        if ($z >= 1.282) return 80;
        return round(min($z / 1.645 * 90, 89), 1);
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
            ->map(fn($a) => ['id' => $a->id, 'title' => $a->title]);

        return response()->json([
            'candidates' => $unscored->values(),
            'total' => $unscored->count(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // POSITION TRACKING (Phase 5: Intelligence)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Position tracking dashboard.
     */
    public function positions(Request $request, CompetitiveIntelligenceService $intelligence)
    {
        $period = $request->get('period', '7days');
        $days = match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 7,
        };

        // Get summary stats
        $summary = $intelligence->getPositionTrackingSummary();

        // Get latest positions per keyword
        $latestPositions = KeywordPositionHistory::latestPerKeyword()
            ->orderBy('position')
            ->orderByDesc('tracked_at')
            ->paginate(25);

        // Get big movers (significant changes)
        $bigMovers = KeywordPositionHistory::lastDays($days)
            ->significantChanges(5)
            ->orderByDesc('position_change')
            ->limit(10)
            ->get();

        // Position tier distribution
        $tierDistribution = KeywordPositionHistory::latestPerKeyword()
            ->selectRaw("
                CASE 
                    WHEN position <= 3 THEN 'top3'
                    WHEN position <= 10 THEN 'page1'
                    WHEN position <= 20 THEN 'page2'
                    WHEN position <= 30 THEN 'page3'
                    WHEN position IS NOT NULL THEN 'beyond'
                    ELSE 'notranking'
                END as tier,
                COUNT(*) as cnt
            ")
            ->groupBy('tier')
            ->pluck('cnt', 'tier')
            ->toArray();

        // Recent tracking count
        $recentTracking = [
            'today' => KeywordPositionHistory::today()->count(),
            'yesterday' => KeywordPositionHistory::whereDate('tracked_at', now()->subDay())->count(),
            'last_week' => KeywordPositionHistory::lastDays(7)->count(),
        ];

        // Trend chart data (average position over time)
        $trendData = KeywordPositionHistory::lastDays($days)
            ->whereNotNull('position')
            ->selectRaw('DATE(tracked_at) as date, AVG(position) as avg_position, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date' => $r->date,
                'avg_position' => round($r->avg_position, 1),
                'count' => $r->count,
            ])
            ->toArray();

        return view('admin.seo.positions', compact(
            'summary', 'latestPositions', 'bigMovers', 'tierDistribution',
            'recentTracking', 'trendData', 'period', 'days'
        ));
    }

    /**
     * Position trend for a specific keyword.
     */
    public function positionTrend(string $keyword, CompetitiveIntelligenceService $intelligence)
    {
        $keyword = urldecode($keyword);
        $trend = $intelligence->getKeywordTrend($keyword, 30);

        return view('admin.seo.position-trend', compact('keyword', 'trend'));
    }

    /**
     * Trigger position tracking manually.
     */
    public function trackPositions(Request $request, CompetitiveIntelligenceService $intelligence)
    {
        $keyword = $request->get('keyword');
        $limit = (int) $request->get('limit', 20);

        if ($keyword) {
            // Track single keyword
            $result = $intelligence->trackPosition($keyword);
            
            if ($result) {
                return back()->with('success', "Position tracked for '{$keyword}': #{$result->position}");
            }
            
            return back()->with('error', "Failed to track position for '{$keyword}'");
        }

        // Batch tracking
        $results = $intelligence->trackAllPositions($limit);

        return back()->with('success', 
            "Tracked {$results['tracked']} keywords. " .
            "Skipped {$results['skipped']}. " .
            "Created {$results['alerts_created']} alerts."
        );
    }

    /**
     * Ranking alerts page.
     */
    public function rankingAlerts(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $severity = $request->get('severity', 'all');

        $query = RankingAlert::with('positionHistory');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'drops') {
            $query->drops();
        } elseif ($filter === 'gains') {
            $query->gains();
        }

        if ($severity === 'critical') {
            $query->critical();
        } elseif ($severity === 'warning') {
            $query->warnings();
        }

        $alerts = $query->orderByDesc('created_at')->paginate(25);

        // Summary
        $summary = RankingAlert::getDashboardSummary();

        return view('admin.seo.alerts', compact('alerts', 'summary', 'filter', 'severity'));
    }

    /**
     * Mark alert as read.
     */
    public function markAlertRead(int $id)
    {
        $alert = RankingAlert::findOrFail($id);
        $alert->markAsRead();

        return back()->with('success', 'Alert marked as read.');
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAlertsRead()
    {
        RankingAlert::unread()->update(['is_read' => true]);

        return back()->with('success', 'All alerts marked as read.');
    }
}
