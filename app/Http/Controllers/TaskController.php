<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedUser', 'institution']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_user_id')) {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by priority and due date
        $tasks = $query->orderByRaw(
            "CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'normal' THEN 3 
                WHEN 'low' THEN 4 
            END"
        )->orderBy('due_date', 'asc')
          ->orderBy('created_at', 'desc')
          ->paginate(15);

        // Get filter options
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();
        
        // Pre-select project if coming from project page
        $selectedProject = $request->filled('project_id') ? 
            Project::find($request->project_id) : null;
        
        return view('tasks.create', compact('projects', 'users', 'institutions', 'selectedProject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sop_notes' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:todo,in_progress,done,blocked',
            'priority' => 'required|in:low,normal,high,urgent',
            'institution_id' => 'nullable|exists:institutions,id',
            'depends_on_task_id' => 'nullable|exists:tasks,id',
            'estimated_hours' => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer',
        ]);

        // Auto-set sort_order if not provided (append to end)
        if (!isset($validated['sort_order'])) {
            $maxOrder = Task::where('project_id', $validated['project_id'])->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        $task = Task::create($validated);

        // Redirect back to project tasks tab
        $redirectUrl = route('projects.show', $task->project_id) . '?tab=tasks';
        
        return redirect($redirectUrl)
            ->with('success', 'Task berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignedUser', 'institution', 'documents', 'dependsOnTask', 'permit']);
        
        // If JSON request (AJAX), return JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($task);
        }
        
        // Get related tasks
        $dependentTasks = Task::where('depends_on_task_id', $task->id)->get();
        $relatedTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->limit(5)
            ->get();
            
        return view('tasks.show', compact('task', 'dependentTasks', 'relatedTasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $institutions = Institution::orderBy('name')->get();
        
        // Get tasks that can be dependencies (exclude current task and its dependencies)
        $availableTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->where('depends_on_task_id', '!=', $task->id)
            ->get();
        
        return view('tasks.edit', compact('task', 'projects', 'users', 'institutions', 'availableTasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sop_notes' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,done,blocked',
            'priority' => 'required|in:low,normal,high,urgent',
            'institution_id' => 'nullable|exists:institutions,id',
            'depends_on_task_id' => 'nullable|exists:tasks,id|not_in:' . $task->id,
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|integer|min:0',
            'completion_notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        // Set completed_at when status changes to done
        if ($validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'done') {
            $validated['completed_at'] = null;
        }

        // Set started_at when status changes to in_progress
        if ($validated['status'] === 'in_progress' && $task->status === 'todo') {
            $validated['started_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Check if other tasks depend on this
        $dependents = Task::where('depends_on_task_id', $task->id)->get();

        if ($dependents->count() > 0) {
            $redirectUrl = route('projects.show', $task->project_id);
            
            // If request has redirect_to_tab parameter, add it to URL
            if (request()->has('redirect_to_tab')) {
                $redirectUrl .= '?tab=' . request('redirect_to_tab');
            }
            
            return redirect($redirectUrl)
                ->with('error', 'Tidak dapat menghapus task ini karena masih menjadi prasyarat task lain!');
        }

        $projectId = $task->project_id;
        $task->delete();

        $redirectUrl = route('projects.show', $projectId);
        
        // If request has redirect_to_tab parameter, add it to URL
        if (request()->has('redirect_to_tab')) {
            $redirectUrl .= '?tab=' . request('redirect_to_tab');
        }

        return redirect($redirectUrl)
            ->with('success', 'Task berhasil dihapus!');
    }

    /**
     * Update task status (AJAX endpoint)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done,blocked',
            'completion_notes' => 'nullable|string',
        ]);

        $oldStatus = $task->status;
        $newStatus = $validated['status'];

        // Check if task can be started (dependency validation)
        if ($newStatus === 'in_progress' && !$task->canStart()) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak dapat dimulai karena prasyarat belum selesai!',
                'blockers' => $task->getBlockers()
            ], 422);
        }

        // Set timestamps based on status change
        if ($newStatus === 'done' && $oldStatus !== 'done') {
            $task->completed_at = now();
        } elseif ($newStatus !== 'done') {
            $task->completed_at = null;
        }

        if ($newStatus === 'in_progress' && $oldStatus === 'todo') {
            $task->started_at = now();
        }

        $task->status = $newStatus;
        
        if (isset($validated['completion_notes'])) {
            $task->completion_notes = $validated['completion_notes'];
        }
        
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Status task berhasil diperbarui!',
            'status' => $newStatus,
            'status_label' => $task->getStatusLabel(),
            'status_color' => $task->getStatusColor(),
            'progress' => $task->getProgress()
        ]);
    }

    /**
     * Reorder tasks in project (drag-and-drop)
     */
    public function reorder(Request $request, Project $project)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.sort_order' => 'required|integer|min:1',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])
                ->where('project_id', $project->id)
                ->update(['sort_order' => $taskData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan task berhasil diperbarui!'
        ]);
    }

    /**
     * Update task assignment
     */
    public function updateAssignment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task->assigned_user_id = $validated['assigned_user_id'];
        $task->save();

        $assignedUser = $task->assignedUser;

        return response()->json([
            'success' => true,
            'message' => $assignedUser 
                ? "Task berhasil di-assign ke {$assignedUser->name}!" 
                : 'Assignment task berhasil dihapus!',
            'assigned_user' => $assignedUser ? [
                'id' => $assignedUser->id,
                'name' => $assignedUser->name,
                'email' => $assignedUser->email,
            ] : null
        ]);
    }
}
