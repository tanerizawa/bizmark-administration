<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostSchedule;
use App\Models\ArticleTopic;
use App\Services\ArticleAutoPostService;
use App\Services\TopicGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AutoPostScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = AutoPostSchedule::with([
            'topic' => function ($q) {
                $q->withTrashed();
            },
            'article',
        ]);
        
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
        $availableTopics = ArticleTopic::where('status', 'pending')
            ->where(function($q) {
                $q->whereNull('scheduled_for')
                  ->orWhere('scheduled_for', '<', now()->subHours(24));
            })
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
        
        if ($topic->status !== 'pending') {
            return back()->with('error', 'Topic tidak tersedia untuk dijadwalkan');
        }
        
        $schedule = AutoPostSchedule::create([
            'topic_id' => $validated['topic_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'pending',
        ]);
        
        $topic->markAsScheduled($validated['scheduled_at']);
        
        return redirect()
            ->route('auto-post.schedules.index')
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
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        // Check if auto-posting is enabled
        $config = \App\Models\AutoPostConfig::current();
        if (!$config->is_enabled) {
            return back()->with('error', 'Auto-posting sedang dinonaktifkan. Aktifkan terlebih dahulu di konfigurasi.');
        }
        
        // Auto-recover: reset stuck processing topics (>2h) and failed topics back to pending
        $resetStuck = ArticleTopic::where('status', 'processing')
            ->where('updated_at', '<', now()->subHours(2))
            ->update(['status' => 'pending', 'scheduled_for' => null]);
            
        $resetFailed = ArticleTopic::where('status', 'failed')
            ->update(['status' => 'pending', 'scheduled_for' => null]);
        
        if ($resetStuck > 0 || $resetFailed > 0) {
            \Log::info('Auto-recovered topics', [
                'stuck_reset' => $resetStuck,
                'failed_reset' => $resetFailed,
            ]);
        }
        
        // Check if topics are available; auto-generate if pool is empty
        $availableTopicsCount = ArticleTopic::available()->count();
            
        if ($availableTopicsCount < $config->posts_per_day) {
            // Auto-replenish topics via AI
            try {
                $topicService = app(TopicGenerationService::class);
                $generated = $topicService->replenishIfNeeded($config->posts_per_day);
                if ($generated > 0) {
                    $availableTopicsCount = ArticleTopic::available()->count();
                    \Log::info("Auto-generated {$generated} topics to replenish pool");
                }
            } catch (\Exception $e) {
                \Log::warning('Topic auto-generation failed, continuing with existing pool', [
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Re-check after replenish attempt
            $availableTopicsCount = ArticleTopic::available()->count();
            if ($availableTopicsCount === 0) {
                return back()->with('error', 'Tidak ada topic tersedia dan auto-generate gagal. Tambahkan topic manual melalui "Kelola Topic Pool".');
            }
        }
        
        try {
            $service = app(ArticleAutoPostService::class);
            $date = \Carbon\Carbon::parse($validated['date']);
            $schedules = $service->scheduleNextBatch($date);
            
            if (empty($schedules)) {
                // Check why no schedules were created
                $existingCount = AutoPostSchedule::whereDate('scheduled_at', $date)->where('status', '!=', 'cancelled')->count();
                if ($existingCount >= $config->posts_per_day) {
                    return back()->with('error', "Semua {$config->posts_per_day} slot untuk " . $date->format('d M Y') . ' sudah terjadwal.');
                }
                return back()->with('error', 'Tidak ada jadwal yang dibuat. Kemungkinan semua slot waktu sudah terjadwal atau tidak ada topic yang cocok.');
            }
            
            $count = count($schedules);
            $remaining = ArticleTopic::available()->count();
            $msg = "Berhasil membuat {$count} jadwal auto-post untuk " . $date->format('d M Y') . '.';
            if ($remaining < 10) {
                $msg .= " (Sisa {$remaining} topic di pool, segera tambahkan topic baru)";
            }
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            \Log::error('Generate batch failed', [
                'date' => $validated['date'],
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal membuat jadwal: ' . $e->getMessage());
        }
    }
    
    public function retry(AutoPostSchedule $schedule)
    {
        if ($schedule->status !== 'failed') {
            return back()->with('error', 'Hanya schedule yang failed yang bisa di-retry');
        }
        
        $schedule->update([
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
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

        // Do not mark as processing here.
        // Queue job will transition status atomically when it actually starts execution.
        // Dispatch to queue with high priority
        \App\Jobs\GenerateAutoPostArticle::dispatch($schedule)->onQueue('default');
        
        return back()->with('success', 'Schedule sedang diproses oleh AI. Refresh halaman untuk melihat hasilnya.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:process_pending,retry_failed,cancel_pending',
            'scope' => 'nullable|in:filtered,all',
            'schedule_status' => 'nullable|string',
            'schedule_search' => 'nullable|string',
            'schedule_date_from' => 'nullable|date',
            'schedule_date_to' => 'nullable|date',
        ]);

        $query = AutoPostSchedule::with([
            'topic' => function ($q) {
                $q->withTrashed();
            },
        ]);

        if (($validated['scope'] ?? 'filtered') === 'filtered') {
            if (!empty($validated['schedule_status'])) {
                $query->where('status', $validated['schedule_status']);
            }

            if (!empty($validated['schedule_search'])) {
                $search = $validated['schedule_search'];
                $query->whereHas('topic', function ($q) use ($search) {
                    $q->withTrashed()->where('title', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($validated['schedule_date_from'])) {
                $query->whereDate('scheduled_at', '>=', $validated['schedule_date_from']);
            }

            if (!empty($validated['schedule_date_to'])) {
                $query->whereDate('scheduled_at', '<=', $validated['schedule_date_to']);
            }
        }

        $processed = 0;

        switch ($validated['action']) {
            case 'process_pending':
                $query->where('status', 'pending')->chunkById(100, function ($rows) use (&$processed) {
                    foreach ($rows as $schedule) {
                        \App\Jobs\GenerateAutoPostArticle::dispatch($schedule)->onQueue('default');
                        $processed++;
                    }
                });
                $message = "{$processed} schedule pending sedang diproses.";
                break;

            case 'retry_failed':
                $query->where('status', 'failed')->chunkById(100, function ($rows) use (&$processed) {
                    foreach ($rows as $schedule) {
                        $schedule->update([
                            'status' => 'pending',
                            'started_at' => null,
                            'completed_at' => null,
                            'error_message' => null,
                        ]);
                        \App\Jobs\GenerateAutoPostArticle::dispatch($schedule)->onQueue('default');
                        $processed++;
                    }
                });
                $message = "{$processed} schedule failed berhasil di-retry.";
                break;

            case 'cancel_pending':
                $query->where('status', 'pending')->chunkById(100, function ($rows) use (&$processed) {
                    foreach ($rows as $schedule) {
                        if ($schedule->topic) {
                            $schedule->topic->clearScheduling();
                        }
                        $schedule->update(['status' => 'cancelled']);
                        $processed++;
                    }
                });
                $message = "{$processed} schedule pending dibatalkan.";
                break;
        }

        return back()->with('success', $message);
    }
}
