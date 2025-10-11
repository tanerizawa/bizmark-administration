<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page with featured articles
     */
    public function index()
    {
        // Get 3 latest published articles for landing page
        $latestArticles = Article::published()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('landing.index', compact('latestArticles'));
    }

    /**
     * Display all published articles (blog page)
     */
    public function blog(Request $request)
    {
        $query = Article::published()->with('author');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->byCategory($request->category);
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag != '') {
            $query->byTag($request->tag);
        }

        // Sort
        $sortBy = $request->get('sort', 'published_at');
        if ($sortBy === 'popular') {
            $query->orderBy('views_count', 'desc');
        } else {
            $query->orderBy('published_at', 'desc');
        }

        $articles = $query->paginate(12);
        $categories = Article::getCategories();

        return view('landing.blog', compact('articles', 'categories'));
    }

    /**
     * Display a single article
     */
    public function article($slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $article->incrementViews();

        // Get related articles
        $relatedArticles = $article->getRelatedArticles(3);

        return view('landing.article', compact('article', 'relatedArticles'));
    }

    /**
     * Display articles by category
     */
    public function category($category)
    {
        $articles = Article::published()
            ->byCategory($category)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Article::getCategories();
        $categoryLabel = $categories[$category] ?? 'Kategori';

        return view('landing.category', compact('articles', 'category', 'categoryLabel', 'categories'));
    }

    /**
     * Display articles by tag
     */
    public function tag($tag)
    {
        $articles = Article::published()
            ->byTag($tag)
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Article::getCategories();

        return view('landing.tag', compact('articles', 'tag', 'categories'));
    }
}
