<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the client's projects.
     */
    public function index(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        // Authorize
        $this->authorize('viewAnyAsClient', [Project::class, $client]);
        
        $query = Project::with(['status', 'institution', 'permitApplication.permitType'])
            ->where('client_id', $client->id);
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'ILIKE', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $projects = $query->paginate(12);
        
        // Get all statuses for filter
        $statuses = \App\Models\ProjectStatus::active()->ordered()->get();
        
        // Statistics
        $stats = [
            'total' => $client->projects()->count(),
            'active' => $client->projects()->whereHas('status', function ($q) {
                $q->where('is_final', false);
            })->count(),
            'completed' => $client->projects()->whereHas('status', function ($q) {
                $q->where('is_final', true);
            })->count(),
            'total_value' => $client->projects()->sum('contract_value'),
        ];
        
        return view('client.projects.index', compact('projects', 'statuses', 'stats'));
    }
    
    /**
     * Display the specified project.
     */
    public function show($id)
    {
        $client = Auth::guard('client')->user();
        $project = Project::with([
                'status', 
                'institution', 
                'permitApplication.permitType',
                'tasks.assignedUser', 
                'documents', 
                'logs.user',
                'permits.permitType'
            ])
            ->findOrFail($id);
        
        // Authorize
        $this->authorize('viewAsClient', [$project, $client]);
        
        // Get project statistics
        $stats = [
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
            'total_documents' => $project->documents()->count(),
            'pending_payments' => $project->contract_value - $project->payment_received,
            'progress' => $project->progress_percentage ?? 0,
        ];
        
        // Get recent activities
        $recentActivities = $project->logs()
            ->latest()
            ->limit(10)
            ->get();
        
        return view('client.projects.show', compact('project', 'stats', 'recentActivities'));
    }
}
