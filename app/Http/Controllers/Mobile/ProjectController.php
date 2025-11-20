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
        // If requesting stats only
        if ($request->has('stats')) {
            $stats = [
                'total' => Project::count(),
                'active' => Project::whereHas('status', fn($q) => $q->where('is_active', true))->count(),
                'overdue' => Project::where('deadline', '<', now())
                    ->whereHas('status', fn($q) => $q->where('is_active', true))->count(),
                'completed' => Project::whereHas('status', fn($q) => $q->where('is_active', false))->count(),
            ];
            
            return response()->json(['stats' => $stats]);
        }
        
        $status = $request->get('status', 'active');
        $search = $request->get('q');
        
        $query = Project::with(['status', 'institution', 'client'])
            ->select('id', 'name', 'budget', 'deadline', 'status_id', 'institution_id', 'client_id', 'progress_percentage');
        
        // Filter by status
        if ($status === 'active') {
            $query->whereHas('status', fn($q) => $q->where('is_active', true));
        } elseif ($status === 'overdue') {
            $query->where('deadline', '<', now())
                ->whereHas('status', fn($q) => $q->where('is_active', true));
        } elseif ($status === 'completed') {
            $query->whereHas('status', fn($q) => $q->where('is_active', false));
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
            'client',
            'tasks' => fn($q) => $q->orderBy('due_date'),
            'documents' => fn($q) => $q->latest()->take(5),
            'expenses' => fn($q) => $q->latest()->take(10),
            'payments' => fn($q) => $q->latest()->take(10)
        ]);
        
        $stats = [
            'totalTasks' => $project->tasks->count(),
            'completedTasks' => $project->tasks->where('status', 'done')->count(),
            'overdueTasks' => $project->tasks->where('due_date', '<', now())
                ->where('status', '!=', 'done')->count(),
            'totalBudget' => (float) $project->budget,
            'totalSpent' => (float) $project->expenses->sum('amount'),
            'totalIncome' => (float) $project->payments->sum('amount'),
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
            ->orWhereHas('client', fn($q) =>
                $q->where('company_name', 'ilike', "%{$query}%")
            )
            ->with(['status', 'institution', 'client'])
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->project_code,
                'status' => $p->status->name ?? 'Unknown',
                'institution' => $p->institution->name ?? '-',
                'client' => $p->client->company_name ?? '-',
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
        
        // Add note to project_logs table
        $log = $project->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'description' => $request->note,
            'notes' => $request->note,
            'old_values' => null,
            'new_values' => ['note' => $request->note],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Clear cache
        Cache::forget('project_timeline_' . $project->id);
        
        return response()->json([
            'success' => true,
            'note' => $log,
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
        $oldStatusId = $project->status_id;
        
        $project->update(['status_id' => $request->status_id]);
        $newStatus = $project->fresh()->status->name ?? 'Unknown';
        
        // Log to project_logs
        $project->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'description' => "Status diubah dari '{$oldStatus}' ke '{$newStatus}'",
            'old_values' => ['status_id' => $oldStatusId, 'status_name' => $oldStatus],
            'new_values' => ['status_id' => $request->status_id, 'status_name' => $newStatus],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Also log to activity if spatie/laravel-activitylog is installed
        if (function_exists('activity')) {
            activity()
                ->performedOn($project)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ])
                ->log('status_changed');
        }
        
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
     * Show create project form
     */
    public function createForm()
    {
        $institutions = \App\Models\Institution::orderBy('name')->get();
        $clients = \App\Models\Client::where('status', 'active')->orderBy('company_name')->get();
        $statuses = ProjectStatus::where('is_active', true)->orderBy('name')->get();
        
        return view('mobile.projects.create', compact('institutions', 'clients', 'statuses'));
    }
    
    /**
     * Quick store project from create form
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'institution_id' => 'required|exists:institutions,id',
            'deadline' => 'required|date|after_or_equal:today',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000'
        ]);
        
        try {
            $project = Project::create([
                'name' => $request->name,
                'client_id' => $request->client_id,
                'institution_id' => $request->institution_id,
                'deadline' => $request->deadline,
                'budget' => $request->budget ?? 0,
                'description' => $request->description,
                'status_id' => ProjectStatus::where('is_active', true)->first()?->id ?? 1,
                'start_date' => now(),
                'progress_percentage' => 0
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project berhasil dibuat',
                    'project' => $project,
                    'redirect' => route('mobile.projects.show', $project->id)
                ], 201);
            }
            
            return redirect()->route('mobile.projects.show', $project->id)
                ->with('success', 'Project berhasil dibuat');
                
        } catch (\Exception $e) {
            \Log::error('Project create error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat project: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Gagal membuat project')->withInput();
        }
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
            'status_id' => ProjectStatus::where('is_active', true)->first()?->id ?? 1,
            'start_date' => now(),
            'progress_percentage' => 0
        ]);
        
        return response()->json([
            'success' => true,
            'project' => $project,
            'redirect' => route('mobile.projects.show', $project->id)
        ], 201);
    }
}
