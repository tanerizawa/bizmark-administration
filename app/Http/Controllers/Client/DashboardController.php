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
        
        // Get client's projects with latest status
        $projects = $client->projects()
            ->with(['status', 'permitApplication.permitType', 'tasks' => function($query) {
                $query->latest()->limit(5);
            }])
            ->latest()
            ->get();
        
        // Calculate metrics
        $activeProjects = $projects->filter(function($project) {
            return $project->status && $project->status->name !== 'Selesai';
        })->count();
        
        $completedProjects = $projects->filter(function($project) {
            return $project->status && $project->status->name === 'Selesai';
        })->count();
        
        $totalInvested = $projects->sum('project_value');
        
        // Get recent documents
        $recentDocuments = $client->projects()
            ->with('documents')
            ->get()
            ->pluck('documents')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5);
        
        // Get upcoming deadlines (tasks due within 7 days)
        $upcomingDeadlines = $client->projects()
            ->with('tasks')
            ->get()
            ->pluck('tasks')
            ->flatten()
            ->filter(function($task) {
                return $task->due_date && 
                       $task->due_date->isFuture() && 
                       $task->due_date->diffInDays(now()) <= 7 &&
                       !$task->completed;
            })
            ->sortBy('due_date')
            ->take(5);
        
        // Get pending documents count (documents not yet uploaded)
        $pendingDocuments = $client->projects()
            ->with('documents')
            ->get()
            ->pluck('documents')
            ->flatten()
            ->filter(function($doc) {
                return empty($doc->file_path) || !$doc->verified_at;
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
            'submittedCount'
        ));
    }
}
