<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Models\JobVacancy;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap
     */
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $sitemap .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
        
        // Homepage
        $sitemap .= $this->addUrl('https://bizmark.id/', now()->toAtomString(), 'daily', '1.0');
        
        // Static pages
        $staticPages = [
            ['url' => '/#about', 'priority' => '0.8'],
            ['url' => '/#services', 'priority' => '0.9'],
            ['url' => '/#why-us', 'priority' => '0.7'],
            ['url' => '/#faq', 'priority' => '0.8'],
            ['url' => '/#contact', 'priority' => '0.8'],
            ['url' => '/contact', 'priority' => '0.9'],
            ['url' => '/kebijakan-privasi', 'priority' => '0.5'],
            ['url' => '/syarat-ketentuan', 'priority' => '0.5'],
        ];
        
        foreach ($staticPages as $page) {
            $sitemap .= $this->addUrl(
                'https://bizmark.id' . $page['url'],
                now()->toAtomString(),
                'monthly',
                $page['priority']
            );
        }
        
        // Blog index
        $sitemap .= $this->addUrl('https://bizmark.id/blog', now()->toAtomString(), 'daily', '0.9');
        
        // Blog articles
        $articles = Article::published()
            ->orderBy('published_at', 'desc')
            ->get();
        
        foreach ($articles as $article) {
            $images = [];
            
            // Add featured image
            if ($article->featured_image) {
                $images[] = 'https://bizmark.id/storage/' . $article->featured_image;
            }
            
            // Extract images from content
            preg_match_all('/<img[^>]+src="([^">]+)"/', $article->content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $imgSrc) {
                    if (!str_starts_with($imgSrc, 'http')) {
                        $imgSrc = 'https://bizmark.id' . $imgSrc;
                    }
                    $images[] = $imgSrc;
                }
            }
            
            $sitemap .= $this->addUrl(
                'https://bizmark.id/blog/' . $article->slug,
                $article->updated_at->toAtomString(),
                'weekly',
                '0.8',
                $images
            );
        }
        
        // Blog categories
        $categories = Article::getCategories();
        foreach ($categories as $slug => $name) {
            $sitemap .= $this->addUrl(
                'https://bizmark.id/blog/category/' . $slug,
                now()->toAtomString(),
                'weekly',
                '0.7'
            );
        }
        
        // Services page (static for now)
        $sitemap .= $this->addUrl('https://bizmark.id/#services', now()->toAtomString(), 'monthly', '0.9');
        
        // Careers index
        $sitemap .= $this->addUrl('https://bizmark.id/karir', now()->toAtomString(), 'daily', '0.8');
        
        // Job vacancies (active and not expired)
        $jobs = JobVacancy::where('status', 'active')
            ->where('deadline', '>=', now())
            ->get();
        
            foreach ($jobs as $job) {
                $sitemap .= $this->addUrl(
                    'https://bizmark.id/karir/' . $job->slug,
                    $job->updated_at->toAtomString(),
                    'daily',
                    '0.7'
                );
            }
        
        // Service inquiry
        $sitemap .= $this->addUrl('https://bizmark.id/inquiry', now()->toAtomString(), 'yearly', '0.6');
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }
    
    /**
     * Add URL to sitemap
     */
    private function addUrl($loc, $lastmod, $changefreq, $priority, $images = [])
    {
        $url = '    <url>' . PHP_EOL;
        $url .= '        <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        $url .= '        <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $url .= '        <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $url .= '        <priority>' . $priority . '</priority>' . PHP_EOL;
        
        // Add images if provided
        foreach ($images as $image) {
            $url .= '        <image:image>' . PHP_EOL;
            $url .= '            <image:loc>' . htmlspecialchars($image) . '</image:loc>' . PHP_EOL;
            $url .= '        </image:image>' . PHP_EOL;
        }
        
        $url .= '    </url>' . PHP_EOL;
        
        return $url;
    }
    
    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $robots = "# Robots.txt for Bizmark.ID" . PHP_EOL;
        $robots .= "# https://bizmark.id/robots.txt" . PHP_EOL . PHP_EOL;
        
        $robots .= "User-agent: *" . PHP_EOL;
        $robots .= "Allow: /" . PHP_EOL;
        $robots .= "Disallow: /hadez" . PHP_EOL;
        $robots .= "Disallow: /dashboard" . PHP_EOL;
        $robots .= "Disallow: /admin" . PHP_EOL;
        $robots .= "Disallow: /projects" . PHP_EOL;
        $robots .= "Disallow: /tasks" . PHP_EOL;
        $robots .= "Disallow: /documents" . PHP_EOL;
        $robots .= "Disallow: /institutions" . PHP_EOL;
        $robots .= "Disallow: /clients" . PHP_EOL;
        $robots .= "Disallow: /settings" . PHP_EOL;
        $robots .= "Disallow: /api/" . PHP_EOL . PHP_EOL;
        
        $robots .= "# Sitemap location" . PHP_EOL;
        $robots .= "Sitemap: https://bizmark.id/sitemap.xml" . PHP_EOL . PHP_EOL;
        
        $robots .= "# Crawl-delay for respectful crawling" . PHP_EOL;
        $robots .= "Crawl-delay: 1" . PHP_EOL . PHP_EOL;
        
        $robots .= "# Specific rules for major search engines" . PHP_EOL;
        $robots .= "User-agent: Googlebot" . PHP_EOL;
        $robots .= "Allow: /" . PHP_EOL;
        $robots .= "Disallow: /hadez" . PHP_EOL;
        $robots .= "Disallow: /dashboard" . PHP_EOL;
        $robots .= "Disallow: /admin" . PHP_EOL;
        $robots .= "Disallow: /api/" . PHP_EOL . PHP_EOL;
        
        $robots .= "User-agent: Bingbot" . PHP_EOL;
        $robots .= "Allow: /" . PHP_EOL;
        $robots .= "Disallow: /hadez" . PHP_EOL;
        $robots .= "Disallow: /dashboard" . PHP_EOL;
        $robots .= "Disallow: /admin" . PHP_EOL;
        $robots .= "Disallow: /api/" . PHP_EOL . PHP_EOL;
        
        $robots .= "# Block bad bots" . PHP_EOL;
        $robots .= "User-agent: AhrefsBot" . PHP_EOL;
        $robots .= "Disallow: /" . PHP_EOL . PHP_EOL;
        
        $robots .= "User-agent: SemrushBot" . PHP_EOL;
        $robots .= "Disallow: /" . PHP_EOL . PHP_EOL;
        
        $robots .= "User-agent: DotBot" . PHP_EOL;
        $robots .= "Disallow: /" . PHP_EOL . PHP_EOL;
        
        $robots .= "User-agent: MJ12bot" . PHP_EOL;
        $robots .= "Disallow: /" . PHP_EOL . PHP_EOL;
        
        return response($robots, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
