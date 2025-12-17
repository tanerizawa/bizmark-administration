<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\SitemapGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SeoHealthController extends Controller
{
    protected SitemapGeneratorService $sitemapGenerator;
    
    public function __construct(SitemapGeneratorService $sitemapGenerator)
    {
        $this->sitemapGenerator = $sitemapGenerator;
    }
    
    /**
     * SEO health check endpoint
     */
    public function health(): JsonResponse
    {
        // Cache results for 5 minutes to avoid overhead
        $health = Cache::remember('seo_health_check', 300, function () {
            return $this->performHealthCheck();
        });
        
        return response()->json($health);
    }
    
    /**
     * Perform comprehensive SEO health check
     */
    private function performHealthCheck(): array
    {
        $checks = [];
        $overallScore = 0;
        $maxScore = 0;
        
        // 1. Sitemap Check
        $sitemapCheck = $this->checkSitemap();
        $checks['sitemap'] = $sitemapCheck;
        $overallScore += $sitemapCheck['score'];
        $maxScore += 20;
        
        // 2. Content Check
        $contentCheck = $this->checkContent();
        $checks['content'] = $contentCheck;
        $overallScore += $contentCheck['score'];
        $maxScore += 20;
        
        // 3. Robots.txt Check
        $robotsCheck = $this->checkRobots();
        $checks['robots'] = $robotsCheck;
        $overallScore += $robotsCheck['score'];
        $maxScore += 15;
        
        // 4. URL Structure Check
        $urlCheck = $this->checkUrls();
        $checks['urls'] = $urlCheck;
        $overallScore += $urlCheck['score'];
        $maxScore += 15;
        
        // 5. Performance Check
        $perfCheck = $this->checkPerformance();
        $checks['performance'] = $perfCheck;
        $overallScore += $perfCheck['score'];
        $maxScore += 15;
        
        // 6. Meta Tags Check
        $metaCheck = $this->checkMetaTags();
        $checks['meta_tags'] = $metaCheck;
        $overallScore += $metaCheck['score'];
        $maxScore += 15;
        
        $percentageScore = round(($overallScore / $maxScore) * 100);
        
        return [
            'status' => $percentageScore >= 80 ? 'excellent' : ($percentageScore >= 60 ? 'good' : 'needs_improvement'),
            'score' => $percentageScore,
            'max_score' => 100,
            'checks' => $checks,
            'checked_at' => now()->toIso8601String(),
            'recommendations' => $this->generateRecommendations($checks)
        ];
    }
    
    private function checkSitemap(): array
    {
        $sitemapPath = public_path('sitemap.xml');
        $score = 0;
        $issues = [];
        
        if (!File::exists($sitemapPath)) {
            return [
                'status' => 'fail',
                'score' => 0,
                'message' => 'Sitemap not found',
                'issues' => ['Sitemap file does not exist']
            ];
        }
        
        $score += 5; // File exists
        
        $fileSize = File::size($sitemapPath);
        if ($fileSize < 50 * 1024 * 1024) { // < 50MB
            $score += 5;
        } else {
            $issues[] = 'Sitemap file too large (>50MB)';
        }
        
        $lastModified = File::lastModified($sitemapPath);
        $hoursSinceUpdate = (time() - $lastModified) / 3600;
        
        if ($hoursSinceUpdate < 24) {
            $score += 5;
        } else {
            $issues[] = 'Sitemap not updated in last 24 hours';
        }
        
        // Check if XML is valid
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($sitemapPath);
        if ($xml !== false) {
            $score += 5;
            $urlCount = count($xml->url);
            
            if ($urlCount > 0) {
                $score += 5; // Has URLs
            }
        } else {
            $issues[] = 'Invalid XML format';
        }
        libxml_clear_errors();
        
        return [
            'status' => $score >= 15 ? 'pass' : ($score >= 10 ? 'warning' : 'fail'),
            'score' => min($score, 20),
            'message' => $score >= 15 ? 'Sitemap is healthy' : 'Sitemap needs attention',
            'issues' => $issues,
            'details' => [
                'file_size' => $this->formatBytes($fileSize),
                'last_modified' => date('Y-m-d H:i:s', $lastModified),
                'hours_since_update' => round($hoursSinceUpdate, 2),
                'url_count' => $xml ? count($xml->url) : 0
            ]
        ];
    }
    
    private function checkContent(): array
    {
        $score = 0;
        $issues = [];
        
        $totalArticles = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles = Article::where('status', 'draft')->count();
        
        if ($publishedArticles > 0) {
            $score += 5;
        }
        
        if ($publishedArticles >= 10) {
            $score += 5;
        } elseif ($publishedArticles >= 5) {
            $score += 3;
            $issues[] = 'Consider publishing more articles for better SEO';
        }
        
        // Check recent content
        $recentArticles = Article::where('status', 'published')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
            
        if ($recentArticles > 0) {
            $score += 5;
        } else {
            $issues[] = 'No articles published in last 30 days';
        }
        
        // Check content with proper excerpts
        $withExcerpts = Article::where('status', 'published')
            ->whereNotNull('excerpt')
            ->where('excerpt', '!=', '')
            ->count();
            
        if ($publishedArticles > 0 && $withExcerpts / $publishedArticles >= 0.8) {
            $score += 5;
        } else {
            $issues[] = 'Some articles missing excerpts';
        }
        
        return [
            'status' => $score >= 15 ? 'pass' : ($score >= 10 ? 'warning' : 'fail'),
            'score' => min($score, 20),
            'message' => $score >= 15 ? 'Content strategy is healthy' : 'Content needs improvement',
            'issues' => $issues,
            'details' => [
                'total_articles' => $totalArticles,
                'published' => $publishedArticles,
                'drafts' => $draftArticles,
                'recent_30_days' => $recentArticles,
                'with_excerpts' => $withExcerpts
            ]
        ];
    }
    
    private function checkRobots(): array
    {
        $robotsPath = public_path('robots.txt');
        $score = 0;
        $issues = [];
        
        if (!File::exists($robotsPath)) {
            return [
                'status' => 'fail',
                'score' => 0,
                'message' => 'robots.txt not found',
                'issues' => ['robots.txt file does not exist']
            ];
        }
        
        $score += 5;
        
        $content = File::get($robotsPath);
        
        if (str_contains($content, 'Sitemap:')) {
            $score += 5;
        } else {
            $issues[] = 'robots.txt missing Sitemap directive';
        }
        
        if (str_contains($content, 'User-agent:')) {
            $score += 5;
        } else {
            $issues[] = 'robots.txt missing User-agent directive';
        }
        
        return [
            'status' => $score >= 10 ? 'pass' : 'warning',
            'score' => min($score, 15),
            'message' => $score >= 10 ? 'robots.txt is configured' : 'robots.txt needs improvement',
            'issues' => $issues
        ];
    }
    
    private function checkUrls(): array
    {
        $score = 15; // Start with full score
        $issues = [];
        
        $appUrl = config('app.url');
        
        if (!str_starts_with($appUrl, 'https://')) {
            $score -= 5;
            $issues[] = 'Site not using HTTPS';
        }
        
        // Check for clean URLs (no query strings in published articles)
        $articlesWithQuery = Article::where('status', 'published')
            ->where('slug', 'like', '%?%')
            ->count();
            
        if ($articlesWithQuery > 0) {
            $score -= 5;
            $issues[] = 'Some articles have query strings in URLs';
        }
        
        return [
            'status' => $score >= 10 ? 'pass' : 'warning',
            'score' => max($score, 0),
            'message' => $score >= 10 ? 'URL structure is SEO-friendly' : 'URL structure needs improvement',
            'issues' => $issues,
            'details' => [
                'base_url' => $appUrl,
                'using_https' => str_starts_with($appUrl, 'https://')
            ]
        ];
    }
    
    private function checkPerformance(): array
    {
        $score = 0;
        $issues = [];
        
        // Check sitemap generation speed
        $start = microtime(true);
        $sitemapPath = public_path('sitemap.xml');
        if (File::exists($sitemapPath)) {
            File::get($sitemapPath); // Read file
            $readTime = (microtime(true) - $start) * 1000;
            
            if ($readTime < 100) {
                $score += 8;
            } elseif ($readTime < 500) {
                $score += 5;
            } else {
                $issues[] = 'Sitemap load time is slow';
            }
        }
        
        // Check database response
        $start = microtime(true);
        Article::where('status', 'published')->count();
        $dbTime = (microtime(true) - $start) * 1000;
        
        if ($dbTime < 50) {
            $score += 7;
        } elseif ($dbTime < 100) {
            $score += 5;
        } else {
            $issues[] = 'Database queries are slow';
        }
        
        return [
            'status' => $score >= 10 ? 'pass' : 'warning',
            'score' => min($score, 15),
            'message' => $score >= 10 ? 'Performance is good' : 'Performance needs optimization',
            'issues' => $issues,
            'details' => [
                'sitemap_read_time' => isset($readTime) ? round($readTime, 2) . 'ms' : 'N/A',
                'db_query_time' => round($dbTime, 2) . 'ms'
            ]
        ];
    }
    
    private function checkMetaTags(): array
    {
        $score = 0;
        $issues = [];
        
        // Check articles with proper meta
        $publishedCount = Article::where('status', 'published')->count();
        
        if ($publishedCount === 0) {
            return [
                'status' => 'warning',
                'score' => 0,
                'message' => 'No published articles to check',
                'issues' => ['No published content']
            ];
        }
        
        $withTitles = Article::where('status', 'published')
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->count();
            
        $withExcerpts = Article::where('status', 'published')
            ->whereNotNull('excerpt')
            ->where('excerpt', '!=', '')
            ->count();
            
        $titlePercentage = ($withTitles / $publishedCount) * 100;
        $excerptPercentage = ($withExcerpts / $publishedCount) * 100;
        
        if ($titlePercentage >= 95) {
            $score += 8;
        } elseif ($titlePercentage >= 80) {
            $score += 5;
        } else {
            $issues[] = 'Some articles missing titles';
        }
        
        if ($excerptPercentage >= 95) {
            $score += 7;
        } elseif ($excerptPercentage >= 80) {
            $score += 5;
        } else {
            $issues[] = 'Some articles missing excerpts/descriptions';
        }
        
        return [
            'status' => $score >= 12 ? 'pass' : 'warning',
            'score' => min($score, 15),
            'message' => $score >= 12 ? 'Meta tags are well optimized' : 'Meta tags need improvement',
            'issues' => $issues,
            'details' => [
                'articles_with_titles' => $withTitles . '/' . $publishedCount,
                'articles_with_excerpts' => $withExcerpts . '/' . $publishedCount,
                'title_coverage' => round($titlePercentage, 1) . '%',
                'excerpt_coverage' => round($excerptPercentage, 1) . '%'
            ]
        ];
    }
    
    private function generateRecommendations(array $checks): array
    {
        $recommendations = [];
        
        foreach ($checks as $category => $check) {
            if ($check['status'] === 'fail' || $check['status'] === 'warning') {
                $recommendations[] = [
                    'category' => $category,
                    'priority' => $check['status'] === 'fail' ? 'high' : 'medium',
                    'message' => $check['message'],
                    'issues' => $check['issues'] ?? []
                ];
            }
        }
        
        // Add general recommendations
        if (empty($recommendations)) {
            $recommendations[] = [
                'category' => 'general',
                'priority' => 'low',
                'message' => 'Everything looks great! Keep publishing quality content regularly.',
                'issues' => []
            ];
        }
        
        return $recommendations;
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
