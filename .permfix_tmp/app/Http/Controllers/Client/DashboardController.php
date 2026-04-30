<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display client dashboard with projects, documents, and metrics.
     */
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        // Authorize that client can view their projects
        $this->authorize('viewAnyAsClient', [Project::class, $client]);
        
        // Eager-load ALL needed relations in a single query to avoid N+1
        $projects = $client->projects()
            ->with([
                'status',
                'permitApplication.permitType',
                'tasks' => function($query) {
                    $query->orderBy('due_date', 'asc');
                },
                'tasks.assignedUser',
                'documents',
            ])
            ->latest()
            ->get();
        
        // Calculate metrics from already-loaded collection (no extra queries)
        $activeProjects = $projects->filter(function($project) {
            return $project->status && $project->status->name !== 'Selesai';
        })->count();
        
        $completedProjects = $projects->filter(function($project) {
            return $project->status && $project->status->name === 'Selesai';
        })->count();
        
        // FIX: Project model uses 'contract_value', not 'project_value'
        $totalInvested = $projects->sum('contract_value');
        
        // Get recent documents from already-loaded relation (no extra query)
        $recentDocuments = $projects
            ->pluck('documents')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5);
        
        // Get all documents stats for progress calculation
        $allDocuments = $projects->pluck('documents')->flatten();
        $totalDocuments = $allDocuments->count();
        $uploadedDocuments = $allDocuments->filter(fn($doc) => !empty($doc->file_path))->count();
        
        // Get upcoming deadlines from already-loaded tasks (no extra query)
        // FIX: Task model uses 'status' and 'completed_at', not 'completed' boolean
        $upcomingDeadlines = $projects
            ->pluck('tasks')
            ->flatten()
            ->filter(function($task) {
                return $task->due_date && 
                       $task->due_date->isFuture() && 
                       $task->due_date->diffInDays(now()) <= 7 &&
                       $task->status !== 'done' &&
                       $task->completed_at === null;
            })
            ->sortBy('due_date')
            ->take(5);
        
        // Get pending documents count from already-loaded data (no extra query)
        $pendingDocuments = $allDocuments
            ->filter(function($doc) {
                return empty($doc->file_path);
            })
            ->count();
        
        // Get submitted projects count (projects in process)
        $submittedCount = $projects->filter(function($project) {
            return $project->status && in_array($project->status->name, ['Dalam Proses', 'Sedang Diproses']);
        })->count();
        
        return view('client.dashboard', compact(
            'client',
            'projects',
            'activeProjects',
            'completedProjects',
            'totalInvested',
            'recentDocuments',
            'upcomingDeadlines',
            'pendingDocuments',
            'submittedCount',
            'totalDocuments',
            'uploadedDocuments'
        ));
    }
}
