<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        
        // Order by priority and due date
        $tasks = $query->orderByRaw("
            CASE 
                WHEN status = 'done' THEN 3
                WHEN due_date < NOW() THEN 0
                WHEN due_date::date = CURRENT_DATE THEN 1
                ELSE 2
            END
        ")
        ->orderBy('due_date', 'asc')
        ->paginate(20);
        
        $stats = [
            'all' => Task::where('assigned_user_id', auth()->id())->where('status', '!=', 'done')->count(),
            'today' => Task::where('assigned_user_id', auth()->id())
                ->whereDate('due_date', now()->toDateString())
                ->where('status', '!=', 'done')
                ->count(),
            'overdue' => Task::where('assigned_user_id', auth()->id())
                ->where('due_date', '<', now())
                ->where('status', '!=', 'done')
                ->count(),
        ];
        
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
        
        // Calculate stats
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $weekEnd = $today->copy()->addDays(7);
        
        $stats = [
            'all' => Task::where('assigned_user_id', auth()->id())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'today' => Task::where('assigned_user_id', auth()->id())
                ->whereDate('due_date', $today)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'week' => Task::where('assigned_user_id', auth()->id())
                ->whereBetween('due_date', [$today, $weekEnd])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'overdue' => Task::where('assigned_user_id', auth()->id())
                ->where('due_date', '<', $today)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
        ];
        
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
            'is_overdue' => $dueDate->isPast() && $task->status !== 'completed',
            'is_due_soon' => $dueDate->isToday() || ($dueDate->isTomorrow() && $task->status !== 'completed'),
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
        $task->load(['project.status', 'assignedUser']);
        
        $relatedTasks = Task::where('project_id', $task->project_id)
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
        // Check permission - hanya assigned user yang bisa complete
        if ($task->assigned_user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $task->update([
            'status' => 'done',
            'completed_at' => now(),
            'completed_by' => auth()->id()
        ]);
        
        // Log activity
        activity()
            ->performedOn($task)
            ->causedBy(auth()->user())
            ->log('task_completed');
        
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
        $request->validate([
            'status' => 'required|in:todo,in_progress,done'
        ]);
        
        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);
        
        if ($request->status === 'done') {
            $task->update([
                'completed_at' => now(),
                'completed_by' => auth()->id()
            ]);
        }
        
        // Log activity
        activity()
            ->performedOn($task)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ])
            ->log('task_status_changed');
        
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
}
