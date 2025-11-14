<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientProjectController extends Controller
{
    /**
     * Display list of client's projects
     */
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        $projects = Project::where('client_id', $client->id)
            ->with(['status', 'permitApplication.permitType'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Statistics
        $stats = [
            'total' => Project::where('client_id', $client->id)->count(),
            'active' => Project::where('client_id', $client->id)
                ->whereHas('status', function($q) {
                    $q->where('is_final', false);
                })->count(),
            'completed' => Project::where('client_id', $client->id)
                ->whereHas('status', function($q) {
                    $q->where('code', 'completed');
                })->count(),
        ];
        
        return view('client.projects.index', compact('projects', 'stats'));
    }

    /**
     * Display project detail
     */
    public function show($id)
    {
        $client = Auth::guard('client')->user();
        
        $project = Project::where('client_id', $client->id)
            ->with([
                'status',
                'permitApplication.permitType',
                'permits.permitType',
                'tasks' => function($q) {
                    $q->orderBy('due_date', 'asc');
                },
                'tasks.assignedUser',
                'documents' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(10);
                }
            ])
            ->findOrFail($id);
        
        // Progress calculation
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'done')->count();
        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        return view('client.projects.show', compact('project', 'taskProgress'));
    }

    /**
     * Download project document
     */
    public function downloadDocument($projectId, $documentId)
    {
        $client = Auth::guard('client')->user();
        
        $project = Project::where('client_id', $client->id)->findOrFail($projectId);
        $document = $project->documents()->findOrFail($documentId);
        
        // Check if file exists
        if (!Storage::exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan');
        }
        
        return Storage::download($document->file_path, $document->file_name);
    }
}
