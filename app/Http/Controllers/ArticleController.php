<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Controllers\Traits\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
        // Validate and normalize active tab.
        $allowedTabs = ['all', 'manual', 'auto-generated', 'auto-post-settings'];
        $tab = in_array($request->get('tab', 'all'), $allowedTabs, true)
            ? $request->get('tab', 'all')
            : 'all';
        
        $articles = null;

        // Auto-post settings tab does not require article list query.
        if ($tab !== 'auto-post-settings') {
            $query = Article::with('author');

            // Filter by tab
            if ($tab === 'manual') {
                $query->where(function ($q) {
                    $q->where('source_type', 'manual')
                        ->orWhereNull('source_type');
                });
            } elseif ($tab === 'auto-generated') {
                $query->where('source_type', 'auto-generated');
            }

            // Search
            $search = trim((string) $request->get('search', ''));
            if ($search !== '') {
                $query->search($search);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->filled('category')) {
                $query->byCategory($request->category);
            }

            // Filter by featured
            if ($request->get('featured') === '1') {
                $query->featured();
            }

            // Sort with whitelist to prevent invalid or unsafe columns.
            $allowedSortColumns = ['created_at', 'published_at', 'title', 'views_count', 'status'];
            $sortBy = in_array($request->get('sort_by', 'created_at'), $allowedSortColumns, true)
                ? $request->get('sort_by', 'created_at')
                : 'created_at';
            $sortOrder = strtolower((string) $request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $articles = $query->paginate(15);
        }

        // Calculate stats for tabs
        $stats = [
            'all' => Article::count(),
            'manual' => Article::where(function ($q) {
                $q->where('source_type', 'manual')
                    ->orWhereNull('source_type');
            })->count(),
            'auto_generated' => Article::where('source_type', 'auto-generated')->count(),
            'published' => Article::where('status', 'published')->count(),
            'draft' => Article::where('status', 'draft')->count(),
        ];

        // Get auto-post config and upcoming schedules
        // Get auto-post config (use current() which auto-creates default if missing)
        $autoPostConfig = \App\Models\AutoPostConfig::current();
        $scheduleDateColumn = Schema::hasColumn('auto_post_schedules', 'scheduled_at')
            ? 'scheduled_at'
            : 'scheduled_for';

        $upcomingSchedules = \App\Models\AutoPostSchedule::with('topic')
            ->where($scheduleDateColumn, '>', now())
            ->where('status', 'pending')
            ->whereHas('topic') // Only schedules with existing topics
            ->orderBy($scheduleDateColumn, 'asc')
            ->take(5)
            ->get();

        return view('articles.index', compact('articles', 'tab', 'stats', 'autoPostConfig', 'upcomingSchedules'));
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
            'pexels_image_path' => 'nullable|string',
            'category' => 'required|in:general,news,case-study,tips,regulation',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle image upload (either from file or Pexels)
        if ($request->filled('pexels_image_path')) {
            // Use Pexels image
            $validated['featured_image'] = $request->input('pexels_image_path');
        } elseif ($request->hasFile('featured_image')) {
            // Upload from file
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

        // Remove pexels_image_path from validated data (not in database)
        unset($validated['pexels_image_path']);

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
            'pexels_image_path' => 'nullable|string',
            'category' => 'required|in:general,news,case-study,tips,regulation',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle image upload (either from file or Pexels)
        if ($request->filled('pexels_image_path')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            // Use Pexels image
            $validated['featured_image'] = $request->input('pexels_image_path');
        } elseif ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            // Upload from file
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // Handle tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter($validated['tags']);
        }

        // Convert is_featured checkbox
        $validated['is_featured'] = $request->has('is_featured') ? true : false;

        // Remove pexels_image_path from validated data (not in database)
        unset($validated['pexels_image_path']);

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

