<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class PublicArticleController extends Controller
{
    /**
     * Display landing page with latest articles
     */
    public function landing()
    {
        // Cache latest articles for 10 minutes
        $latestArticles = cache()->remember('landing.latest_articles', 600, function () {
            return Article::published()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
        });

        return view('landing.index', compact('latestArticles'));
    }

    /**
     * Display a listing of all published articles
     */
    public function index()
    {
        $articles = Article::published()
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

        // Get related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('blog.show', compact('article', 'relatedArticles'));
    }

    /**
     * Display articles by category
     */
    public function category($category)
    {
        $articles = Article::published()
            ->where('category', $category)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categoryLabel = Article::getCategoryLabel($category);

        return view('blog.category', compact('articles', 'category', 'categoryLabel'));
    }

    /**
     * Display articles by tag
     */
    public function tag($tag)
    {
        $articles = Article::published()
            ->whereJsonContains('tags', $tag)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.tag', compact('articles', 'tag'));
    }
}
