<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleViewLog;
use App\Models\ContentSyndication;
use App\Models\KeywordCluster;
use App\Models\SeoReport;
use App\Models\SeoScore;
use App\Models\TopicCluster;
use Illuminate\Support\Facades\DB;

class SeoReportService
{
    /**
     * Snapshot daily views from articles table
     * (Called daily to build view trend history)
     */
    public function snapshotDailyViews(): int
    {
        $today = now()->toDateString();
        $snapped = 0;

        Article::where('status', 'published')
            ->select('id', 'views_count')
            ->chunk(100, function ($articles) use ($today, &$snapped) {
                foreach ($articles as $article) {
                    // Get yesterday's snapshot to calculate delta
                    $yesterday = ArticleViewLog::where('article_id', $article->id)
                        ->where('date', now()->subDay()->toDateString())
                        ->first();

                    $previousTotal = $yesterday
                        ? DB::table('article_view_logs')
                            ->where('article_id', $article->id)
                            ->sum('views')
                        : 0;

                    $todayViews = max(0, $article->views_count - $previousTotal);

                    ArticleViewLog::updateOrCreate(
                        ['article_id' => $article->id, 'date' => $today],
                        ['views' => $todayViews, 'unique_views' => (int) ($todayViews * 0.7)]
                    );
                    $snapped++;
                }
            });

        return $snapped;
    }

    /**
     * Generate weekly SEO report
     */
    public function generateWeeklyReport(): SeoReport
    {
        $end = now()->toDateString();
        $start = now()->subWeek()->toDateString();

        return $this->generateReport('weekly', $start, $end);
    }

    /**
     * Generate monthly SEO report
     */
    public function generateMonthlyReport(): SeoReport
    {
        $end = now()->toDateString();
        $start = now()->subMonth()->toDateString();

        return $this->generateReport('monthly', $start, $end);
    }

    /**
     * Core report generation
     */
    protected function generateReport(string $period, string $start, string $end): SeoReport
    {
        // Article stats
        $totalArticles = Article::where('status', 'published')->count();
        $newArticles = Article::where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->count();

        // View stats
        $viewLogs = ArticleViewLog::inRange($start, $end);
        $totalViews = $viewLogs->sum('views');
        $prevStart = now()->parse($start)->subDays(now()->parse($start)->diffInDays($end))->toDateString();
        $prevViews = ArticleViewLog::inRange($prevStart, $start)->sum('views');
        $viewsGrowth = $prevViews > 0 ? round((($totalViews - $prevViews) / $prevViews) * 100, 1) : 0;

        // Cumulative total views
        $cumulativeViews = Article::where('status', 'published')->sum('views_count');

        // SEO scores
        $avgSeoScore = SeoScore::avg('total_score') ?? 0;
        $excellentCount = SeoScore::excellent()->count();
        $needsWorkCount = SeoScore::needsWork()->count();

        // Content infrastructure
        $sitemapUrls = 0;
        $sitemapPath = public_path('sitemap.xml');
        if (file_exists($sitemapPath)) {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($sitemapPath);
            if ($xml) {
                $sitemapUrls = count($xml->url);
            }
            libxml_clear_errors();
        }

        $syndicationStats = ContentSyndication::selectRaw("status, count(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $keywordClusters = KeywordCluster::count();
        $topicClusters = TopicCluster::count();

        // Top articles this period
        $topArticles = ArticleViewLog::inRange($start, $end)
            ->select('article_id', DB::raw('SUM(views) as period_views'))
            ->groupBy('article_id')
            ->orderByDesc('period_views')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $article = Article::find($log->article_id);
                return $article ? [
                    'id' => $article->id,
                    'title' => $article->title,
                    'views' => $log->period_views,
                    'total_views' => $article->views_count,
                    'seo_score' => $article->seoScore?->total_score ?? null,
                ] : null;
            })
            ->filter()
            ->values()
            ->toArray();

        // Alerts
        $alerts = $this->generateAlerts($start, $end, $totalViews, $prevViews);

        $metrics = [
            'total_published' => $totalArticles,
            'new_articles' => $newArticles,
            'period_views' => $totalViews,
            'previous_period_views' => $prevViews,
            'views_growth_pct' => $viewsGrowth,
            'cumulative_views' => $cumulativeViews,
            'avg_seo_score' => round($avgSeoScore, 1),
            'excellent_seo_count' => $excellentCount,
            'needs_work_count' => $needsWorkCount,
            'sitemap_urls' => $sitemapUrls,
            'syndication' => $syndicationStats,
            'keyword_clusters' => $keywordClusters,
            'topic_clusters' => $topicClusters,
        ];

        return SeoReport::create([
            'period' => $period,
            'period_start' => $start,
            'period_end' => $end,
            'metrics' => $metrics,
            'top_articles' => $topArticles,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Generate alerts based on performance data
     */
    protected function generateAlerts(string $start, string $end, int $currentViews, int $prevViews): array
    {
        $alerts = [];

        // Views drop alert
        if ($prevViews > 0 && $currentViews < $prevViews * 0.7) {
            $drop = round((($prevViews - $currentViews) / $prevViews) * 100, 1);
            $alerts[] = [
                'level' => 'warning',
                'type' => 'views_drop',
                'message' => "Views turun {$drop}% dibanding periode sebelumnya",
            ];
        }

        // No new content alert
        $newArticles = Article::where('status', 'published')
            ->whereBetween('published_at', [$start, $end])
            ->count();
        if ($newArticles === 0) {
            $alerts[] = [
                'level' => 'info',
                'type' => 'no_new_content',
                'message' => 'Tidak ada artikel baru dipublish periode ini',
            ];
        }

        // Low SEO score articles
        $lowScoreCount = SeoScore::needsWork()->count();
        if ($lowScoreCount > 5) {
            $alerts[] = [
                'level' => 'warning',
                'type' => 'low_seo_scores',
                'message' => "{$lowScoreCount} artikel memiliki SEO score di bawah 60",
            ];
        }

        // Stale sitemap
        $sitemapPath = public_path('sitemap.xml');
        if (file_exists($sitemapPath)) {
            $hoursSince = (time() - filemtime($sitemapPath)) / 3600;
            if ($hoursSince > 48) {
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'stale_sitemap',
                    'message' => 'Sitemap belum di-update lebih dari 48 jam',
                ];
            }
        }

        return $alerts;
    }

    /**
     * Get view trends for an article
     */
    public function getArticleTrends(int $articleId, int $days = 30): array
    {
        $start = now()->subDays($days)->toDateString();

        return ArticleViewLog::where('article_id', $articleId)
            ->where('date', '>=', $start)
            ->orderBy('date')
            ->get()
            ->map(fn ($log) => [
                'date' => $log->date->format('Y-m-d'),
                'views' => $log->views,
            ])
            ->toArray();
    }

    /**
     * Get site-wide view trends
     */
    public function getSiteTrends(int $days = 30): array
    {
        $start = now()->subDays($days)->toDateString();

        return ArticleViewLog::where('date', '>=', $start)
            ->select('date', DB::raw('SUM(views) as total_views'), DB::raw('COUNT(DISTINCT article_id) as articles_active'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'views' => $row->total_views,
                'articles_active' => $row->articles_active,
            ])
            ->toArray();
    }
}
