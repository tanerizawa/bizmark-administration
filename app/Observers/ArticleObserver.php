<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\GoogleIndexingService;
use App\Services\SitemapGeneratorService;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    protected GoogleIndexingService $indexingService;
    protected SitemapGeneratorService $sitemapGenerator;
    
    public function __construct(
        GoogleIndexingService $indexingService,
        SitemapGeneratorService $sitemapGenerator
    ) {
        $this->indexingService = $indexingService;
        $this->sitemapGenerator = $sitemapGenerator;
    }
    
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        if ($article->status === 'published') {
            $this->handlePublished($article);
        }
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        // Check if article just got published
        if ($article->status === 'published' && $article->wasChanged('status')) {
            $this->handlePublished($article);
        }
        
        // If already published and content/slug changed, re-index
        if ($article->status === 'published' && ($article->wasChanged('content') || $article->wasChanged('slug'))) {
            $this->handleUpdated($article);
        }
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        if ($article->status === 'published') {
            $this->handleDeleted($article);
        }
    }
    
    /**
     * Handle newly published article
     */
    protected function handlePublished(Article $article): void
    {
        Log::info('📝 Article published, triggering SEO automation', [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug
        ]);
        
        $sitemapGenerator = $this->sitemapGenerator;
        $indexingService = $this->indexingService;
        
        // Regenerate sitemap
        dispatch(function () use ($sitemapGenerator) {
            $sitemapGenerator->generate();
            Log::info('✅ Sitemap regenerated');
        })->afterResponse();
        
        // Request indexing via Google Indexing API
        dispatch(function () use ($article, $indexingService, $sitemapGenerator) {
            $url = config('app.url') . '/blog/' . $article->slug;
            
            if ($indexingService->requestIndexing($url, 'URL_UPDATED')) {
                Log::info('✅ Indexing requested', ['url' => $url]);
            }
            
            // Ping search engines
            $sitemapUrl = $sitemapGenerator->getSitemapUrl();
            $indexingService->pingSearchEngines($sitemapUrl);
            
        })->delay(now()->addSeconds(10))->afterResponse();
    }
    
    /**
     * Handle updated article
     */
    protected function handleUpdated(Article $article): void
    {
        Log::info('📝 Article updated, triggering SEO update', [
            'id' => $article->id,
            'title' => $article->title
        ]);
        
        $sitemapGenerator = $this->sitemapGenerator;
        $indexingService = $this->indexingService;
        
        // Regenerate sitemap
        dispatch(function () use ($sitemapGenerator) {
            $sitemapGenerator->generate();
        })->afterResponse();
        
        // Request re-indexing
        dispatch(function () use ($article, $indexingService) {
            $url = config('app.url') . '/blog/' . $article->slug;
            $indexingService->requestIndexing($url, 'URL_UPDATED');
        })->delay(now()->addSeconds(10))->afterResponse();
    }
    
    /**
     * Handle deleted article
     */
    protected function handleDeleted(Article $article): void
    {
        Log::info('🗑️  Article deleted, triggering SEO cleanup', [
            'id' => $article->id,
            'title' => $article->title
        ]);
        
        $sitemapGenerator = $this->sitemapGenerator;
        $indexingService = $this->indexingService;
        
        // Regenerate sitemap (removes deleted article)
        dispatch(function () use ($sitemapGenerator) {
            $sitemapGenerator->generate();
        })->afterResponse();
        
        // Notify Google about deletion
        dispatch(function () use ($article, $indexingService) {
            $url = config('app.url') . '/blog/' . $article->slug;
            $indexingService->requestIndexing($url, 'URL_DELETED');
        })->delay(now()->addSeconds(10))->afterResponse();
    }
}
