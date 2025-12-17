<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SitemapGeneratorService
{
    protected string $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
    }
    
    /**
     * Generate complete sitemap and save to public folder
     */
    public function generate(): string
    {
        $xml = $this->generateXml();
        
        // Save to public folder
        $path = public_path('sitemap.xml');
        file_put_contents($path, $xml);
        
        Log::info('✅ Sitemap generated', [
            'path' => $path,
            'size' => strlen($xml)
        ]);
        
        return $this->baseUrl . '/sitemap.xml';
    }
    
    /**
     * Generate sitemap XML content
     */
    protected function generateXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= $this->generateUrlEntry(
            $this->baseUrl,
            now(),
            '1.0',
            'daily'
        );
        
        // Static pages
        $staticPages = [
            '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
            '/blog' => ['priority' => '0.9', 'changefreq' => 'daily'],
            '/about' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            '/contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            '/services' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        ];
        
        foreach ($staticPages as $page => $meta) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl . $page,
                now(),
                $meta['priority'],
                $meta['changefreq']
            );
        }
        
        // Blog articles
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
        
        foreach ($articles as $article) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl . '/blog/' . $article->slug,
                $article->updated_at ?? $article->published_at,
                '0.8',
                'weekly'
            );
        }
        
        // Blog categories
        $categories = Article::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        foreach ($categories as $category) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl . '/blog/category/' . $category,
                now(),
                '0.6',
                'daily'
            );
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Generate single URL entry
     */
    protected function generateUrlEntry(
        string $url,
        $lastmod,
        string $priority = '0.5',
        string $changefreq = 'weekly'
    ): string {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $xml .= "    <lastmod>" . $lastmod->toW3cString() . "</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";
        
        return $xml;
    }
    
    /**
     * Get sitemap URL
     */
    public function getSitemapUrl(): string
    {
        return $this->baseUrl . '/sitemap.xml';
    }
    
    /**
     * Check if sitemap exists
     */
    public function exists(): bool
    {
        return file_exists(public_path('sitemap.xml'));
    }
    
    /**
     * Get sitemap last modified time
     */
    public function getLastModified(): ?\DateTime
    {
        $path = public_path('sitemap.xml');
        
        if (!file_exists($path)) {
            return null;
        }
        
        return new \DateTime('@' . filemtime($path));
    }
}
