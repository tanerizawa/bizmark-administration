<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleTopic;
use Illuminate\Http\Request;

class ArticleTopicController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleTopic::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('search')) {
            $query->where('title', 'ILIKE', '%' . $request->search . '%');
        }
        
        $topics = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => ArticleTopic::count(),
            'available' => ArticleTopic::where('status', 'available')->count(),
            'scheduled' => ArticleTopic::where('status', 'scheduled')->count(),
            'used' => ArticleTopic::where('status', 'used')->count(),
        ];
        
        return view('admin.auto-post.topics.index', compact('topics', 'stats'));
    }
    
    public function create()
    {
        return view('admin.auto-post.topics.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:tips,guide,case-study,news,regulation,general',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'target_audience' => 'nullable|string',
            'content_angle' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:10',
            'is_evergreen' => 'boolean',
        ]);
        
        $validated['status'] = 'available';
        
        ArticleTopic::create($validated);
        
        return redirect()
            ->route('admin.auto-post.topics.index')
            ->with('success', 'Topic berhasil ditambahkan');
    }
    
    public function edit(ArticleTopic $topic)
    {
        return view('admin.auto-post.topics.edit', compact('topic'));
    }
    
    public function update(Request $request, ArticleTopic $topic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:tips,guide,case-study,news,regulation,general',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'target_audience' => 'nullable|string',
            'content_angle' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:10',
            'is_evergreen' => 'boolean',
        ]);
        
        $topic->update($validated);
        
        return redirect()
            ->route('admin.auto-post.topics.index')
            ->with('success', 'Topic berhasil diperbarui');
    }
    
    public function destroy(ArticleTopic $topic)
    {
        if ($topic->status === 'scheduled') {
            return back()->with('error', 'Tidak dapat menghapus topic yang sudah dijadwalkan');
        }
        
        $topic->delete();
        
        return redirect()
            ->route('admin.auto-post.topics.index')
            ->with('success', 'Topic berhasil dihapus');
    }
    
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,reset,change_priority',
            'topics' => 'required|array|min:1',
            'topics.*' => 'exists:article_topics,id',
            'priority' => 'required_if:action,change_priority|integer|min:1|max:10',
        ]);
        
        $topics = ArticleTopic::whereIn('id', $validated['topics']);
        
        switch ($validated['action']) {
            case 'delete':
                $topics->where('status', '!=', 'scheduled')->delete();
                $message = 'Topics berhasil dihapus';
                break;
                
            case 'reset':
                $topics->update(['status' => 'available', 'times_used' => 0]);
                $message = 'Topics berhasil direset';
                break;
                
            case 'change_priority':
                $topics->update(['priority' => $validated['priority']]);
                $message = 'Priority berhasil diubah';
                break;
        }
        
        return back()->with('success', $message);
    }
}
