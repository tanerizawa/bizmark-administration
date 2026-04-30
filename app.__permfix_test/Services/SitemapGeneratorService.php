<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Log;

class SitemapGeneratorService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
    }

    /**
     * Generate sitemap index + individual sitemaps and save to public folder
     */
    public function generate(): string
    {
        $sitemaps = [];

        // Generate individual sitemaps
        $sitemaps[] = $this->generateStaticSitemap();
        $sitemaps[] = $this->generateServicesSitemap();
        $sitemaps[] = $this->generateArticlesSitemap();
        $sitemaps[] = $this->generateCitySitemap();

        // Generate sitemap index
        $indexXml = $this->generateSitemapIndex($sitemaps);
        $indexPath = public_path('sitemap.xml');
        file_put_contents($indexPath, $indexXml);

        $totalUrls = array_sum(array_column($sitemaps, 'count'));

        Log::info('✅ Sitemap index generated', [
            'sitemaps' => count($sitemaps),
            'total_urls' => $totalUrls,
        ]);

        return $this->baseUrl.'/sitemap.xml';
    }

    /**
     * Generate sitemap index XML
     */
    protected function generateSitemapIndex(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>'.htmlspecialchars($sitemap['url'])."</loc>\n";
            $xml .= '    <lastmod>'.now()->toW3cString()."</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Static pages + blog categories sitemap
     */
    protected function generateStaticSitemap(): array
    {
        $xml = $this->openUrlset(true);

        // Homepage (ID)
        $xml .= $this->generateUrlEntryWithHreflang(
            $this->baseUrl,
            now(),
            '1.0',
            'daily',
            ['id' => $this->baseUrl, 'en' => $this->baseUrl.'/en']
        );

        // Static pages with hreflang
        $staticPages = [
            '/blog' => ['priority' => '0.9', 'changefreq' => 'daily', 'en' => '/en/blog'],
            '/layanan' => ['priority' => '0.9', 'changefreq' => 'weekly', 'en' => '/en/services'],
            '/faq' => ['priority' => '0.7', 'changefreq' => 'weekly', 'en' => null],
            '/panduan' => ['priority' => '0.7', 'changefreq' => 'weekly', 'en' => null],
            '/contact' => ['priority' => '0.6', 'changefreq' => 'monthly', 'en' => null],
        ];

        foreach ($staticPages as $page => $meta) {
            $hreflang = ['id' => $this->baseUrl.$page];
            if (! empty($meta['en'])) {
                $hreflang['en'] = $this->baseUrl.$meta['en'];
            }
            $xml .= $this->generateUrlEntryWithHreflang(
                $this->baseUrl.$page,
                now(),
                $meta['priority'],
                $meta['changefreq'],
                $hreflang
            );
        }

        // EN static pages
        $enPages = [
            '/en' => ['priority' => '0.9', 'changefreq' => 'daily'],
            '/en/blog' => ['priority' => '0.8', 'changefreq' => 'daily'],
            '/en/services' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        foreach ($enPages as $page => $meta) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl.$page,
                now(),
                $meta['priority'],
                $meta['changefreq']
            );
        }

        // Blog categories
        $categories = Article::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        foreach ($categories as $category) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl.'/blog/kategori/'.$category,
                now(),
                '0.6',
                'daily'
            );
        }

        // Blog tags
        $tags = Article::where('status', 'published')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->take(50);

        foreach ($tags as $tag) {
            $xml .= $this->generateUrlEntry(
                $this->baseUrl.'/blog/tag/'.urlencode($tag),
                now(),
                '0.5',
                'weekly'
            );
        }

        $xml .= '</urlset>';

        $count = substr_count($xml, '<url>');
        $path = public_path('sitemap-static.xml');
        file_put_contents($path, $xml);

        return ['url' => $this->baseUrl.'/sitemap-static.xml', 'count' => $count];
    }

    /**
     * Service pages sitemap (main services + sub-services)
     */
    protected function generateServicesSitemap(): array
    {
        $xml = $this->openUrlset(false);

        $services = config('services_data', []);

        foreach ($services as $serviceKey => $service) {
            $slug = $service['slug'] ?? $serviceKey;

            // Main service page
            $xml .= $this->generateUrlEntry(
                $this->baseUrl.'/layanan/'.$slug,
                now(),
                '0.9',
                'weekly'
            );

            // Sub-service pages
            if (! empty($service['sub_services'])) {
                foreach ($service['sub_services'] as $subKey => $sub) {
                    $xml .= $this->generateUrlEntry(
                        $this->baseUrl.'/layanan/'.$slug.'/sub/'.$subKey,
                        now(),
                        '0.7',
                        'monthly'
                    );
                }
            }
        }

        // Service comparison pages
        $xml .= $this->generateUrlEntry(
            $this->baseUrl.'/layanan/perbandingan',
            now(),
            '0.6',
            'monthly'
        );

        $xml .= '</urlset>';

        $count = substr_count($xml, '<url>');
        $path = public_path('sitemap-services.xml');
        file_put_contents($path, $xml);

        return ['url' => $this->baseUrl.'/sitemap-services.xml', 'count' => $count];
    }

    /**
     * Articles sitemap with image entries
     */
    protected function generateArticlesSitemap(): array
    {
        $xml = $this->openUrlset(true);

        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();

        foreach ($articles as $article) {
            $imageXml = '';
            $imagePath = $article->featured_image;
            if ($imagePath && file_exists(public_path('storage/'.$imagePath))) {
                $imageUrl = $this->baseUrl.'/storage/'.$imagePath;
                $imageXml = "    <image:image>\n";
                $imageXml .= '      <image:loc>'.htmlspecialchars($imageUrl)."</image:loc>\n";
                $imageXml .= '      <image:title>'.htmlspecialchars($article->title)."</image:title>\n";
                $imageXml .= "    </image:image>\n";
            }

            // Hreflang: ID articles link to EN blog and vice versa
            $articleUrl = $this->baseUrl.'/blog/'.$article->slug;
            $hreflang = [];
            if ($article->language === 'en') {
                $hreflang['en'] = $articleUrl;
            } else {
                $hreflang['id'] = $articleUrl;
            }

            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($articleUrl)."</loc>\n";
            $xml .= '    <lastmod>'.($article->updated_at ?? $article->published_at)->toW3cString()."</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            if (! empty($hreflang)) {
                foreach ($hreflang as $lang => $href) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="'.$lang.'" href="'.htmlspecialchars($href).'" />'."\n";
                }
            }
            $xml .= $imageXml;
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $count = substr_count($xml, '<url>');
        $path = public_path('sitemap-articles.xml');
        file_put_contents($path, $xml);

        return ['url' => $this->baseUrl.'/sitemap-articles.xml', 'count' => $count];
    }

    /**
     * City SEO pages sitemap (city index + service-location combos)
     */
    protected function generateCitySitemap(): array
    {
        $xml = $this->openUrlset(false);

        $cities = config('programmatic_seo.cities', []);
        $services = config('services_data', []);

        foreach ($cities as $cityKey => $city) {
            $citySlug = $city['slug'] ?? $cityKey;

            // City index page: /layanan/kota/{citySlug}
            $xml .= $this->generateUrlEntry(
                $this->baseUrl.'/layanan/kota/'.$citySlug,
                now(),
                '0.7',
                'weekly'
            );

            // Service-location combos: /layanan/{serviceSlug}/{citySlug}
            foreach ($services as $serviceKey => $service) {
                $serviceSlug = $service['slug'] ?? $serviceKey;
                $xml .= $this->generateUrlEntry(
                    $this->baseUrl.'/layanan/'.$serviceSlug.'/'.$citySlug,
                    now(),
                    '0.6',
                    'monthly'
                );
            }
        }

        $xml .= '</urlset>';

        $count = substr_count($xml, '<url>');
        $path = public_path('sitemap-cities.xml');
        file_put_contents($path, $xml);

        return ['url' => $this->baseUrl.'/sitemap-cities.xml', 'count' => $count];
    }

    /**
     * Open urlset tag with appropriate namespaces
     */
    protected function openUrlset(bool $withExtensions): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        if ($withExtensions) {
            $xml .= "\n".'  xmlns:xhtml="http://www.w3.org/1999/xhtml"';
            $xml .= "\n".'  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }
        $xml .= '>'."\n";

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
        $xml .= '    <loc>'.htmlspecialchars($url)."</loc>\n";
        $xml .= '    <lastmod>'.$lastmod->toW3cString()."</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Generate URL entry with hreflang alternates
     */
    protected function generateUrlEntryWithHreflang(
        string $url,
        $lastmod,
        string $priority,
        string $changefreq,
        array $hreflang = []
    ): string {
        $xml = "  <url>\n";
        $xml .= '    <loc>'.htmlspecialchars($url)."</loc>\n";
        $xml .= '    <lastmod>'.$lastmod->toW3cString()."</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        foreach ($hreflang as $lang => $href) {
            $xml .= '    <xhtml:link rel="alternate" hreflang="'.$lang.'" href="'.htmlspecialchars($href).'" />'."\n";
        }
        if (count($hreflang) > 1) {
            // x-default points to the ID version
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.htmlspecialchars($hreflang['id'] ?? $url).'" />'."\n";
        }
        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Get sitemap URL
     */
    public function getSitemapUrl(): string
    {
        return $this->baseUrl.'/sitemap.xml';
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

        if (! file_exists($path)) {
            return null;
        }

        return new \DateTime('@'.filemtime($path));
    }
}
