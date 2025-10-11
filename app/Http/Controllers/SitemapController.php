<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class SitemapController extends Controller
{
    public function index()
    {
        $pages = [
            [
                'url' => url('/'),
                'priority' => '1.0',
                'changefreq' => 'daily',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => url('/blog'),
                'priority' => '0.9',
                'changefreq' => 'daily',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => url('/privacy-policy'),
                'priority' => '0.5',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
        ];
        
        // Add dynamic blog posts
        $articles = Article::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($articles as $article) {
            $pages[] = [
                'url' => url('/blog/' . $article->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $article->updated_at->toAtomString()
            ];
        }
        
        return response()->view('sitemap', compact('pages'))
            ->header('Content-Type', 'text/xml');
    }
    
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /api\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        
        return response($content)
            ->header('Content-Type', 'text/plain');
    }
}
