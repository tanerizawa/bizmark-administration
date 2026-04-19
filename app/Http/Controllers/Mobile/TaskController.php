<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Tasks list with filters
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, today, week, overdue
        
        $query = Task::with(['project', 'assignedUser'])
            ->where('assigned_user_id', auth()->id())
            ->select('id', 'title', 'description', 'status', 'due_date', 'project_id', 'assigned_user_id', 'priority');
        
        // Apply filters
        switch ($filter) {
            case 'today':
                $query->whereDate('due_date', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'overdue':
                $query->where('due_date', '<', now())
                    ->where('status', '!=', 'done');
                break;
        }
        
        // Order by priority and due date (database-agnostic)
        $today = now()->toDateString();
        $tasks = $query->orderByRaw("
            CASE 
                WHEN status = 'done' THEN 3
                WHEN due_date < ? THEN 0
                WHEN DATE(due_date) = ? THEN 1
                ELSE 2
            END
        ", [now(), $today])
        ->orderBy('due_date', 'asc')
        ->paginate(20);
        
        $stats = $this->buildTaskStats(auth()->id());
        
        if ($request->expectsJson()) {
            return response()->json([
                'tasks' => $tasks->items(),
                'hasMore' => $tasks->hasMorePages(),
                'nextPage' => $tasks->currentPage() + 1
            ]);
        }
        
        return view('mobile.tasks.index', [
            'tasks' => $tasks,
            'currentFilter' => $filter,
            'stats' => $stats
        ]);
    }
    
    /**
     * My tasks (assigned to current user)
     */
    public function myTasks(Request $request)
    {
        $tasks = Task::where('assigned_user_id', auth()->id())
            ->with(['project', 'assignedUser'])
            ->orderBy('due_date', 'asc')
            ->paginate(20);
        
        // Transform tasks for mobile display
        $transformedTasks = $tasks->map(function($task) {
            return $this->transformTask($task);
        });
        
        $stats = $this->buildTaskStats(auth()->id());
        
        if ($request->expectsJson()) {
            return response()->json([
                'tasks' => $transformedTasks,
                'has_more' => $tasks->hasMorePages(),
                'stats' => $stats
            ]);
        }
        
        return view('mobile.tasks.my', compact('tasks', 'stats'));
    }
    
    /**
     * Transform task for mobile display
     */
    private function transformTask($task)
    {
        $now = Carbon::now();
        $dueDate = Carbon::parse($task->due_date);
        
        $priorityLabels = [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low'
        ];
        
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority ?? 'medium',
            'priority_label' => $priorityLabels[$task->priority ?? 'medium'] ?? 'Medium',
            'due_date' => $task->due_date,
            'due_date_formatted' => $this->formatDueDate($dueDate),
            'is_overdue' => $dueDate->isPast() && $task->status !== 'done',
            'is_due_soon' => $dueDate->isToday() || ($dueDate->isTomorrow() && $task->status !== 'done'),
            'project_id' => $task->project_id,
            'project_name' => $task->project->name ?? null,
            'assigned_to_id' => $task->assigned_user_id,
            'assigned_to_name' => $task->assignedUser->name ?? null,
        ];
    }
    
    /**
     * Format due date for display
     */
    private function formatDueDate($date)
    {
        if ($date->isToday()) {
            return 'Hari Ini';
        } elseif ($date->isTomorrow()) {
            return 'Besok';
        } elseif ($date->isYesterday()) {
            return 'Kemarin';
        } elseif ($date->diffInDays() <= 7) {
            return $date->format('l'); // Day name
        } else {
            return $date->format('d M');
        }
    }
    
    /**
     * Urgent tasks (overdue + due today)
     */
    public function urgent(Request $request)
    {
        $tasks = Task::where('assigned_user_id', auth()->id())
            ->where(function($q) {
                $q->where('due_date', '<', now())
                  ->orWhereDate('due_date', now()->toDateString());
            })
            ->where('status', '!=', 'done')
            ->with(['project'])
            ->orderBy('due_date', 'asc')
            ->get();
        
        if ($request->expectsJson()) {
            return response()->json(['tasks' => $tasks]);
        }
        
        return view('mobile.tasks.urgent', compact('tasks'));
    }
    
    /**
     * Task detail
     */
    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);

        $task->load(['project.status', 'assignedUser']);
        
        $relatedTasks = Task::where('project_id', $task->project_id)
            ->where('assigned_user_id', auth()->id())
            ->where('id', '!=', $task->id)
            ->take(5)
            ->get();
        
        return view('mobile.tasks.show', compact('task', 'relatedTasks'));
    }
    
    /**
     * Mark task as complete
     */
    public function complete(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);
        
        $task->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);
        
        // Log activity (only if spatie/laravel-activitylog is installed)
        if (function_exists('activity')) {
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->log('task_completed');
        }
        
        return response()->json([
            'success' => true,
            'task' => $task,
            'message' => 'Task berhasil diselesaikan!'
        ]);
    }
    
    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $request->validate([
            'status' => 'required|in:todo,in_progress,done'
        ]);
        
        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);
        
        if ($request->status === 'done') {
            $task->update([
                'completed_at' => now(),
            ]);
        }
        
        // Log activity (only if spatie/laravel-activitylog is installed)
        if (function_exists('activity')) {
            activity()
                ->performedOn($task)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $request->status
                ])
                ->log('task_status_changed');
        }
        
        return response()->json([
            'success' => true,
            'task' => $task,
            'message' => 'Status berhasil diupdate'
        ]);
    }
    
    /**
     * Add comment to task
     */
    public function addComment(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);
        
        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->comment,
            'created_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'message' => 'Comment berhasil ditambahkan'
        ]);
    }
    
    /**
     * Quick create task from bottom sheet
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);
        
        $task = Task::create([
            'title' => $request->title,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'priority' => $request->priority ?? 'medium',
            'assigned_user_id' => $request->assigned_user_id ?? auth()->id(),
            'status' => 'todo'
        ]);
        
        return response()->json([
            'success' => true,
            'task' => $task,
            'redirect' => route('mobile.tasks.show', $task->id),
            'message' => 'Task berhasil dibuat'
        ], 201);
    }

    private function buildTaskStats(int $userId): array
    {
        $now = now();
        $today = $now->toDateString();
        $weekEnd = $now->copy()->addDays(7)->endOfDay();

        $stats = Task::query()
            ->where('assigned_user_id', $userId)
            ->selectRaw("SUM(CASE WHEN status != 'done' THEN 1 ELSE 0 END) as all_count")
            ->selectRaw("SUM(CASE WHEN status != 'done' AND DATE(due_date) = ? THEN 1 ELSE 0 END) as today_count", [$today])
            ->selectRaw("SUM(CASE WHEN status != 'done' AND due_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as week_count", [$now->copy()->startOfDay(), $weekEnd])
            ->selectRaw("SUM(CASE WHEN status != 'done' AND due_date < ? THEN 1 ELSE 0 END) as overdue_count", [$now])
            ->first();

        return [
            'all' => (int) ($stats->all_count ?? 0),
            'today' => (int) ($stats->today_count ?? 0),
            'week' => (int) ($stats->week_count ?? 0),
            'overdue' => (int) ($stats->overdue_count ?? 0),
        ];
    }

    private function authorizeTaskAccess(Task $task): void
    {
        if ($task->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }
}
