<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Projects List - Mobile Optimized
     * Show only essential info, grouped by status
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $search = $request->get('q');
        
        $query = Project::with(['status', 'institution', 'manager'])
            ->select('id', 'name', 'budget', 'deadline', 'project_status_id', 'institution_id', 'manager_id', 'progress');
        
        // Filter by status
        if ($status === 'active') {
            $query->whereHas('status', fn($q) => $q->where('name', 'Aktif'));
        } elseif ($status === 'overdue') {
            $query->where('deadline', '<', now())
                ->whereDoesntHave('status', fn($q) => $q->where('name', 'Selesai'));
        } elseif ($status === 'completed') {
            $query->whereHas('status', fn($q) => $q->where('name', 'Selesai'));
        }
        
        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('project_code', 'ilike', "%{$search}%")
                  ->orWhereHas('institution', fn($iq) => 
                      $iq->where('name', 'ilike', "%{$search}%")
                  );
            });
        }
        
        $projects = $query->orderBy('deadline', 'asc')
            ->paginate(20);
        
        // If AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'projects' => $projects->items(),
                'hasMore' => $projects->hasMorePages(),
                'nextPage' => $projects->currentPage() + 1
            ]);
        }
        
        return view('mobile.projects.index', [
            'projects' => $projects,
            'currentStatus' => $status
        ]);
    }
    
    /**
     * Project Detail - Mobile View
     * Tabs: Overview, Tasks, Timeline, Files
     */
    public function show(Project $project)
    {
        $project->load([
            'status',
            'institution',
            'manager',
            'tasks' => fn($q) => $q->orderBy('due_date'),
            'documents' => fn($q) => $q->latest()->take(5),
            'expenses' => fn($q) => $q->latest()->take(5)
        ]);
        
        $stats = [
            'totalTasks' => $project->tasks->count(),
            'completedTasks' => $project->tasks->where('status', 'done')->count(),
            'overdueTasks' => $project->tasks->where('due_date', '<', now())
                ->where('status', '!=', 'done')->count(),
            'totalBudget' => $project->budget,
            'totalSpent' => $project->expenses->sum('amount'),
            'daysLeft' => now()->diffInDays($project->deadline, false),
        ];
        
        return view('mobile.projects.show', compact('project', 'stats'));
    }
    
    /**
     * Search projects (for mobile search bar)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }
        
        $projects = Project::where('name', 'ilike', "%{$query}%")
            ->orWhere('project_code', 'ilike', "%{$query}%")
            ->orWhereHas('institution', fn($q) => 
                $q->where('name', 'ilike', "%{$query}%")
            )
            ->with(['status', 'institution'])
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->project_code,
                'status' => $p->status->name ?? 'Unknown',
                'institution' => $p->institution->name ?? '-',
                'url' => route('mobile.projects.show', $p->id)
            ]);
        
        return response()->json(['results' => $projects]);
    }
    
    /**
     * Add quick note to project
     * For mobile quick actions
     */
    public function addNote(Request $request, Project $project)
    {
        $request->validate([
            'note' => 'required|string|max:500'
        ]);
        
        // Add to project notes (assuming there's a notes field or relation)
        $note = $project->notes()->create([
            'content' => $request->note,
            'user_id' => auth()->id(),
            'created_at' => now()
        ]);
        
        // Clear cache
        Cache::forget('project_timeline_' . $project->id);
        
        return response()->json([
            'success' => true,
            'note' => $note,
            'message' => 'Catatan berhasil ditambahkan'
        ]);
    }
    
    /**
     * Update project status
     * Quick action from mobile
     */
    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status_id' => 'required|exists:project_statuses,id'
        ]);
        
        $oldStatus = $project->status->name ?? 'Unknown';
        $project->update(['project_status_id' => $request->status_id]);
        $newStatus = $project->fresh()->status->name ?? 'Unknown';
        
        // Log activity
        activity()
            ->performedOn($project)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ])
            ->log('status_changed');
        
        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Status diubah ke {$newStatus}"
        ]);
    }
    
    /**
     * Project timeline (for mobile timeline tab)
     * Shows milestones, tasks, activities
     */
    public function timeline(Project $project)
    {
        $cacheKey = 'project_timeline_' . $project->id;
        
        $timeline = Cache::remember($cacheKey, 300, function() use ($project) {
            $events = collect();
            
            // Tasks as timeline events
            $project->tasks->each(function($task) use (&$events) {
                $events->push([
                    'type' => 'task',
                    'date' => $task->due_date,
                    'title' => $task->title,
                    'status' => $task->status,
                    'icon' => $task->status === 'done' ? 'check-circle' : 'circle',
                    'color' => $task->status === 'done' ? 'green' : 
                              ($task->due_date < now() ? 'red' : 'blue')
                ]);
            });
            
            // Documents
            $project->documents()->latest()->take(10)->get()->each(function($doc) use (&$events) {
                $events->push([
                    'type' => 'document',
                    'date' => $doc->created_at,
                    'title' => $doc->title,
                    'status' => $doc->status,
                    'icon' => 'file-alt',
                    'color' => 'purple'
                ]);
            });
            
            // Project milestones
            $events->push([
                'type' => 'milestone',
                'date' => $project->start_date,
                'title' => 'Proyek Dimulai',
                'icon' => 'flag',
                'color' => 'green'
            ]);
            
            $events->push([
                'type' => 'milestone',
                'date' => $project->deadline,
                'title' => 'Deadline',
                'icon' => 'flag-checkered',
                'color' => $project->deadline < now() ? 'red' : 'blue'
            ]);
            
            return $events->sortBy('date')->values();
        });
        
        return response()->json(['timeline' => $timeline]);
    }
    
    /**
     * Quick create project (from mobile bottom sheet)
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'deadline' => 'required|date|after:today',
            'budget' => 'nullable|numeric|min:0'
        ]);
        
        $project = Project::create([
            'name' => $request->name,
            'institution_id' => $request->institution_id,
            'deadline' => $request->deadline,
            'budget' => $request->budget ?? 0,
            'manager_id' => auth()->id(),
            'project_status_id' => ProjectStatus::where('name', 'Aktif')->first()?->id,
            'start_date' => now(),
            'progress' => 0
        ]);
        
        return response()->json([
            'success' => true,
            'project' => $project,
            'redirect' => route('mobile.projects.show', $project->id)
        ], 201);
    }
}
