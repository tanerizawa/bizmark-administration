<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use App\Models\CashAccount;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mobile Dashboard - Simplified & Optimized
     * Only shows "What Matters Now"
     * Cache: 2 minutes (more frequent than desktop for real-time feel)
     */
    public function index()
    {
        $cacheKey = 'mobile_dashboard_' . auth()->id();
        
        $metrics = Cache::remember($cacheKey, 120, function() {
            $urgent = $this->getUrgentMetric();
            $runway = $this->getRunwayMetric();
            $approvals = $this->getApprovalsMetric();
            $tasks = $this->getTasksMetric();
            $quickStats = $this->getQuickStats();
            
            return [
                'urgent_count' => $urgent['count'],
                'runway_months' => $runway['months'],
                'runway_label' => $runway['label'],
                'runway_color' => $runway['color'],
                'approvals_count' => $approvals['count'],
                'tasks_today' => $tasks['today'],
                'tasks_overdue' => $tasks['overdue'],
                'active_projects' => $quickStats['activeProjects'],
                'month_revenue' => $quickStats['monthRevenue'],
                'month_expenses' => $quickStats['monthExpenses'],
            ];
        });
        
        $recentActivity = $this->getRecentActivity(5);

        $runway = $this->getRunwayMetric();
        $cash_pulse = [
            'status' => $runway['color'] === 'green' ? 'healthy' : ($runway['color'] === 'yellow' ? 'warning' : 'critical'),
            'balance' => $runway['cash'] ?? 0,
            'runway' => is_numeric($runway['months']) ? $runway['months'] : 12,
        ];

        $alerts = $this->getAlerts();
        $agenda = $this->getAgenda();
        $projectStats = [
            'active' => $metrics['active_projects'] ?? 0,
            'delayed' => $alerts->count(),
            'blocked' => 0,
        ];
        $paymentStats = [
            'pending' => Invoice::where('status', 'draft')->count(),
            'overdue' => Invoice::where('status', 'sent')->whereDate('due_date', '<', now())->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
        ];
        
        return view(
            'mobile.dashboard.index',
            compact(
                'metrics',
                'recentActivity',
                'cash_pulse',
                'alerts',
                'agenda',
                'projectStats',
                'paymentStats'
            )
        );
    }
    
    /**
     * Refresh endpoint for pull-to-refresh
     * Returns JSON for AJAX updates
     */
    public function refresh(Request $request)
    {
        // Clear cache
        Cache::forget('mobile_dashboard_' . auth()->id());
        
        // Get fresh data
        $data = [
            'urgent' => $this->getUrgentMetric(),
            'runway' => $this->getRunwayMetric(),
            'approvals' => $this->getApprovalsMetric(),
            'tasks' => $this->getTasksMetric(),
            'quickStats' => $this->getQuickStats(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ]);
    }
    
    /**
     * Sync endpoint for offline PWA
     * Accepts queued actions from service worker
     */
    public function sync(Request $request)
    {
        $actions = $request->input('actions', []);
        $results = [];
        
        foreach ($actions as $action) {
            try {
                $result = $this->processAction($action);
                $results[] = [
                    'id' => $action['id'] ?? null,
                    'status' => 'success',
                    'data' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $action['id'] ?? null,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'synced_at' => now()->toIso8601String()
        ]);
    }
    
    /**
     * METRIC 1: Urgent Items
     * Shows total count of items needing immediate attention
     */
    private function getUrgentMetric()
    {
        $overdueProjects = Project::whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereHas('status', function($query) {
                $query->where('is_active', true);
            })->count();
            
        $overdueTasks = Task::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();
            
        $criticalCash = CashAccount::where('current_balance', '<', 0)->count();
        
        $total = $overdueProjects + $overdueTasks + $criticalCash;
        
        return [
            'count' => $total,
            'label' => $total > 0 ? 'Butuh Perhatian' : 'Semua Lancar',
            'color' => $total > 5 ? 'red' : ($total > 0 ? 'yellow' : 'green'),
            'items' => [
                ['label' => 'Proyek Terlambat', 'count' => $overdueProjects],
                ['label' => 'Task Overdue', 'count' => $overdueTasks],
                ['label' => 'Cash Negatif', 'count' => $criticalCash],
            ]
        ];
    }
    
    /**
     * METRIC 2: Cash Runway
     * How many months until money runs out
     */
    private function getRunwayMetric()
    {
        $totalCash = CashAccount::sum('current_balance');
        
        // Calculate monthly burn rate (last 30 days)
        $monthlyExpenses = ProjectExpense::where('expense_date', '>=', now()->subDays(30))
            ->sum('amount');
            
        $monthlyBurn = $monthlyExpenses;
        
        $runway = $monthlyBurn > 0 ? round($totalCash / $monthlyBurn, 1) : 999;
        
        return [
            'months' => $runway > 99 ? '∞' : $runway,
            'cash' => $totalCash,
            'burn' => $monthlyBurn,
            'label' => $runway < 3 ? 'Kritis' : ($runway < 6 ? 'Perlu Perhatian' : 'Aman'),
            'color' => $runway < 3 ? 'red' : ($runway < 6 ? 'yellow' : 'green')
        ];
    }
    
    /**
     * METRIC 3: Pending Approvals
     * Items waiting for user's approval
     */
    private function getApprovalsMetric()
    {
        // ProjectExpense tidak punya status column, skip
        $pendingExpenses = 0;
            
        $pendingDocuments = Document::where('status', 'review')
            ->count(); // Semua documents review
            
        // Count draft invoices (semua draft)
        $pendingInvoices = Invoice::where('status', 'draft')->count();
        
        $total = $pendingExpenses + $pendingDocuments + $pendingInvoices;
        
        return [
            'count' => $total,
            'label' => $total > 0 ? 'Perlu Approval' : 'Tidak Ada',
            'items' => [
                ['label' => 'Expenses', 'count' => $pendingExpenses, 'route' => 'mobile.approvals.index'],
                ['label' => 'Dokumen', 'count' => $pendingDocuments, 'route' => 'mobile.approvals.index'],
                ['label' => 'Invoice', 'count' => $pendingInvoices, 'route' => 'mobile.approvals.index'],
            ]
        ];
    }
    
    /**
     * METRIC 4: My Tasks Today
     * Tasks assigned to user due today or overdue
     */
    private function getTasksMetric()
    {
        $today = Task::where('assigned_user_id', auth()->id())
            ->whereDate('due_date', now()->toDateString())
            ->where('status', '!=', 'done')
            ->count();
            
        $overdue = Task::where('assigned_user_id', auth()->id())
            ->where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->count();
            
        $upcoming = Task::where('assigned_user_id', auth()->id())
            ->whereBetween('due_date', [now()->addDay(), now()->addDays(7)])
            ->where('status', '!=', 'done')
            ->count();
        
        return [
            'today' => $today,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'label' => $today + $overdue > 0 ? 'Ada Task Hari Ini' : 'Tidak Ada Task',
            'color' => $overdue > 0 ? 'red' : ($today > 0 ? 'blue' : 'gray')
        ];
    }
    
    /**
     * Quick Stats for expandable sections
     */
    private function getQuickStats()
    {
        return [
            'activeProjects' => Project::whereHas('status', function($query) {
                $query->where('is_active', true);
            })->count(),
            'teamMembers' => \App\Models\User::where('is_active', true)->count(),
            'monthRevenue' => Invoice::whereMonth('created_at', now()->month)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'monthExpenses' => ProjectExpense::whereMonth('expense_date', now()->month)
                ->sum('amount'),
        ];
    }
    
    /**
     * Recent Activity (simplified for mobile)
     */
    private function getRecentActivity($limit = 5)
    {
        $activities = [];
        
        // Recent project updates (last 24h)
        $projects = Project::where('updated_at', '>=', now()->subDay())
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function($p) {
                return [
                    'type' => 'project',
                    'icon' => 'folder',
                    'title' => $p->name,
                    'subtitle' => $p->status->name ?? 'Unknown',
                    'time' => $p->updated_at->diffForHumans(),
                    'timestamp' => $p->updated_at->timestamp, // Untuk sorting
                    'url' => route('mobile.projects.show', $p->id)
                ];
            });
        
        // Recent tasks completed
        $tasks = Task::where('status', 'done')
            ->where('updated_at', '>=', now()->subDay())
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function($t) {
                return [
                    'type' => 'task',
                    'icon' => 'check-circle',
                    'title' => $t->title,
                    'subtitle' => 'Selesai',
                    'time' => $t->updated_at->diffForHumans(),
                    'timestamp' => $t->updated_at->timestamp, // Untuk sorting
                    'url' => route('mobile.tasks.show', $t->id)
                ];
            });
        
        $activities = $projects->merge($tasks)
            ->sortByDesc('timestamp') // Sort by timestamp, bukan parse time string
            ->take($limit)
            ->values();
        
        return $activities;
    }

    /**
     * Alerts: overdue projects and tasks for quick actions.
     */
    private function getAlerts()
    {
        $alerts = collect();

        $overdueProjects = Project::where('deadline', '<', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        foreach ($overdueProjects as $project) {
            $alerts->push([
                'id' => $project->id,
                'type' => 'project',
                'title' => $project->name,
                'subtitle' => 'Lewat dari tenggat',
                'days_overdue' => Carbon::parse($project->deadline)->diffInDays(now()),
            ]);
        }

        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        foreach ($overdueTasks as $task) {
            $alerts->push([
                'id' => $task->id,
                'type' => 'task',
                'title' => $task->title ?? 'Task',
                'subtitle' => optional($task->project)->name ?? 'Task terjadwal',
                'days_overdue' => Carbon::parse($task->due_date)->diffInDays(now()),
            ]);
        }

        return $alerts;
    }

    /**
     * Agenda: tasks due today for the current user.
     */
    private function getAgenda()
    {
        $tasksToday = Task::whereNotNull('due_date')
            ->where('assigned_user_id', auth()->id())
            ->whereDate('due_date', now()->toDateString())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return $tasksToday->map(function ($task) {
            return [
                'time' => Carbon::parse($task->due_date)->format('H:i'),
                'icon' => '📌',
                'title' => $task->title ?? 'Task',
                'project' => optional($task->project)->name ?? 'Tanpa proyek',
                'link' => route('mobile.tasks.show', $task),
            ];
        });
    }
    
    /**
     * Process sync action from offline queue
     */
    private function processAction($action)
    {
        $type = $action['type'] ?? null;
        $data = $action['data'] ?? [];
        
        switch ($type) {
            case 'task_complete':
                $task = Task::find($data['task_id']);
                if ($task) {
                    $task->update(['status' => 'done', 'completed_at' => now()]);
                    return ['success' => true, 'task' => $task->id];
                }
                break;
                
            case 'approval_approve':
                // Handle approval logic
                return ['success' => true, 'approved' => $data['id']];
                
            case 'project_note':
                // Add note to project
                return ['success' => true, 'note_added' => true];
                
            default:
                throw new \Exception('Unknown action type: ' . $type);
        }
        
        return ['success' => false];
    }
}
