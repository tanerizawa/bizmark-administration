<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class PublicArticleController extends Controller
{
    /**
     * Display landing page with latest articles
     * Supports multi-locale (ID/EN) and market segmentation (Local/PMA)
     */
    public function landing(?Request $request = null)
    {
        $locale = app()->getLocale();
        $marketSegment = session('market_segment', 'local');
        
        // Cache latest articles for 10 minutes per locale
        $latestArticles = cache()->remember("landing.latest_articles.{$locale}", 600, function () {
            return Article::published()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
        });
        
        // Load appropriate config based on market segment
        $services = $marketSegment === 'pma' 
            ? config('services_pma') 
            : config('services_data');
        
        // Select appropriate view based on locale
        $view = $locale === 'en' 
            ? 'landing.en.index' 
            : 'landing.id.index';
        
        return view($view, compact('latestArticles', 'services', 'locale', 'marketSegment'));
    }

    /**
     * Display a listing of all published articles
     * Filters by language based on route locale (ID vs EN)
     */
    public function index()
    {
        $locale = app()->getLocale();
        
        $articles = Article::published()
            ->byLanguage($locale)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.index', compact('articles'));
    }

    /**
     * Display the specified article
     */
    public function show($slug)
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $article->incrementViews();

        // Get related articles (same category and language)
        $relatedArticles = Article::published()
            ->byLanguage($article->language)
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('blog.show', compact('article', 'relatedArticles'));
    }

    /**
     * Display articles by category
     * Filters by language based on route locale
     */
    public function category($category)
    {
        $locale = app()->getLocale();
        
        $articles = Article::published()
            ->byLanguage($locale)
            ->where('category', $category)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categoryLabel = Article::getCategoryLabel($category);
        
        // Route names based on locale
        $blogIndexRoute = $locale === 'en' ? 'blog.index.en' : 'blog.index.id';
        $blogArticleRoute = $locale === 'en' ? 'blog.article.en' : 'blog.article.id';
        $blogCategoryRoute = $locale === 'en' ? 'blog.category.en' : 'blog.category.id';
        $blogTagRoute = $locale === 'en' ? 'blog.tag.en' : 'blog.tag.id';

        return view('blog.category', compact('articles', 'category', 'categoryLabel', 'blogIndexRoute', 'blogArticleRoute', 'blogCategoryRoute', 'blogTagRoute'));
    }

    /**
     * Display articles by tag
     * Filters by language based on route locale
     */
    public function tag($tag)
    {
        $locale = app()->getLocale();
        
        $articles = Article::published()
            ->byLanguage($locale)
            ->whereJsonContains('tags', $tag)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.tag', compact('articles', 'tag'));
    }
}
