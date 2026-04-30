<?php

namespace App\Modules\Proyek\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatus;
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
            ->withCount(['documents', 'tasks'])
            ->where('client_id', $client->id);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%'.$request->search.'%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // Sort
        $allowedSortBy = ['created_at', 'name', 'deadline', 'progress_percentage'];
        $sortBy = $request->get('sort_by', 'created_at');
        if (! in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'created_at';
        }

        $sortOrder = strtolower((string) $request->get('sort_order', 'desc'));
        if (! in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $projects = $query->paginate(12);

        // Get all statuses for filter
        $statuses = ProjectStatus::active()->ordered()->get();

        // Statistics
        $baseProjectsQuery = Project::where('client_id', $client->id);
        $stats = [
            'total' => (clone $baseProjectsQuery)->count(),
            'active' => (clone $baseProjectsQuery)->whereHas('status', function ($q) {
                $q->where('is_final', false);
            })->count(),
            'completed' => (clone $baseProjectsQuery)->whereHas('status', function ($q) {
                $q->where('is_final', true);
            })->count(),
            'total_value' => (clone $baseProjectsQuery)->sum('contract_value'),
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
            'permits.permitType',
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
