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
        
        $query = Task::with(['project', 'assignee'])
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
            ->with(['project'])
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->paginate(20);
        
        return view('mobile.tasks.my', compact('tasks'));
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
        $task->load(['project.status', 'assignee', 'creator', 'comments.user']);
        
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
            'due_date' => 'required|date|after:today',
            'priority' => 'nullable|in:low,medium,high',
            'assigned_user_id' => 'nullable|exists:users,id'
        ]);
        
        $task = Task::create([
            'title' => $request->title,
            'project_id' => $request->project_id,
            'due_date' => $request->due_date,
            'priority' => $request->priority ?? 'medium',
            'assigned_user_id' => $request->assigned_user_id ?? auth()->id(),
            'creator_id' => auth()->id(),
            'status' => 'todo'
        ]);
        
        return response()->json([
            'success' => true,
            'task' => $task,
            'redirect' => route('mobile.tasks.show', $task->id)
        ], 201);
    }
}
