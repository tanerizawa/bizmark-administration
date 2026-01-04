<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostSchedule;
use App\Models\ArticleTopic;
use App\Services\ArticleAutoPostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AutoPostScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = AutoPostSchedule::with(['topic', 'article']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }
        
        $schedules = $query->orderBy('scheduled_at', 'desc')
            ->paginate(30);
        
        $stats = [
            'pending' => AutoPostSchedule::where('status', 'pending')->count(),
            'processing' => AutoPostSchedule::where('status', 'processing')->count(),
            'completed' => AutoPostSchedule::where('status', 'completed')->count(),
            'failed' => AutoPostSchedule::where('status', 'failed')->count(),
        ];
        
        return view('admin.auto-post.schedules.index', compact('schedules', 'stats'));
    }
    
    public function create()
    {
        $availableTopics = ArticleTopic::where('status', 'available')
            ->orderBy('priority', 'desc')
            ->get();
        
        return view('admin.auto-post.schedules.create', compact('availableTopics'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:article_topics,id',
            'scheduled_at' => 'required|date|after:now',
        ]);
        
        $topic = ArticleTopic::find($validated['topic_id']);
        
        if ($topic->status !== 'available') {
            return back()->with('error', 'Topic tidak tersedia untuk dijadwalkan');
        }
        
        $schedule = AutoPostSchedule::create([
            'topic_id' => $validated['topic_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'pending',
        ]);
        
        $topic->markAsScheduled();
        
        return redirect()
            ->route('admin.auto-post.schedules.index')
            ->with('success', 'Schedule berhasil ditambahkan');
    }
    
    public function destroy(AutoPostSchedule $schedule)
    {
        if ($schedule->status === 'completed') {
            return back()->with('error', 'Tidak dapat menghapus schedule yang sudah selesai');
        }
        
        if ($schedule->status === 'pending' && $schedule->topic) {
            // Clear scheduling info when deleting pending schedule
            $schedule->topic->clearScheduling();
        }
        
        $schedule->delete();
        
        return back()->with('success', 'Schedule berhasil dihapus');
    }
    
    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after:today',
        ]);
        
        Artisan::call('articles:schedule-daily', [
            'date' => $validated['date'],
        ]);
        
        $output = Artisan::output();
        
        return back()->with('success', 'Batch schedule berhasil dibuat: ' . $output);
    }
    
    public function retry(AutoPostSchedule $schedule)
    {
        if ($schedule->status !== 'failed') {
            return back()->with('error', 'Hanya schedule yang failed yang bisa di-retry');
        }
        
        $schedule->update([
            'status' => 'pending',
            'processed_at' => null,
            'error_message' => null,
        ]);
        
        // Dispatch to queue
        \App\Jobs\GenerateAutoPostArticle::dispatch($schedule);
        
        return back()->with('success', 'Schedule berhasil di-retry');
    }
    
    public function processNow(AutoPostSchedule $schedule)
    {
        if (!$schedule->isPending()) {
            return back()->with('error', 'Schedule harus dalam status pending');
        }
        
        // Mark as processing
        $schedule->markAsProcessing();
        
        // Dispatch to queue with high priority
        \App\Jobs\GenerateAutoPostArticle::dispatch($schedule)->onQueue('default');
        
        return back()->with('success', 'Schedule sedang diproses oleh AI. Refresh halaman untuk melihat hasilnya.');
    }
}
