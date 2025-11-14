<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Controllers\Traits\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizePermission('content.manage', 'Anda tidak memiliki akses untuk mengelola artikel.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with('author');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->byCategory($request->category);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured == '1') {
            $query->featured();
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $articles = $query->paginate(15);

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Article::getCategories();
        return view('articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|in:general,news,case-study,tips,regulation',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // Set author
        $validated['author_id'] = Auth::id();

        // Handle tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags']);
        }

        // Convert is_featured checkbox
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        $article = Article::create($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->load('author');
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = Article::getCategories();
        return view('articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|in:general,news,case-study,tips,regulation',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // Handle tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags']);
        }

        // Convert is_featured checkbox
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete image if exists
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil dihapus!');
    }

    /**
     * Publish an article
     */
    public function publish(Article $article)
    {
        $article->publish();

        return redirect()->back()
            ->with('success', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Unpublish an article
     */
    public function unpublish(Article $article)
    {
        $article->unpublish();

        return redirect()->back()
            ->with('success', 'Artikel berhasil di-unpublish!');
    }

    /**
     * Archive an article
     */
    public function archive(Article $article)
    {
        $article->archive();

        return redirect()->back()
            ->with('success', 'Artikel berhasil diarsipkan!');
    }

    /**
     * Upload image via AJAX for editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('image')->store('articles/content', 'public');
        $url = Storage::url($path);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}

