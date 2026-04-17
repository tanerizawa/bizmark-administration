<?php

namespace App\Services\Dashboard;

use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use App\Models\Invoice;
use Carbon\Carbon;

class DashboardDataService
{
    public function __construct(
        private DashboardFinancialService $financial,
        private DashboardOperationalService $operational,
    ) {}

    public function build(): array
    {
        return [
            'criticalAlerts' => $this->getCriticalAlerts(),
            'cashFlowStatus' => $this->financial->getCashFlowStatus(),
            'pendingApprovals' => $this->getPendingActions(),
            'cashFlowSummary' => $this->financial->getFinancialSummary(),
            'receivablesAging' => $this->financial->getReceivablesAging(),
            'budgetStatus' => $this->financial->getBudgetStatus(),
            'thisWeek' => $this->operational->getWeeklyTimeline(),
            'projectStatusDistribution' => $this->operational->getProjectStatusDistribution(),
            'recentActivities' => $this->operational->getRecentActivities(),
            'ragMetrics' => $this->operational->getRagMetrics(),
        ];
    }
    // ==========================================
    // PHASE 1: CRITICAL ALERTS METHODS
    // ==========================================

    private function getCriticalAlerts()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Overdue projects - Fix: Exclude completed projects
        $overdueProjects = Project::with(['status', 'institution'])
            ->where('deadline', '<', $today)
            ->whereNull('completed_at')  // Not completed
            ->whereDoesntHave('status', function($query) {
                $query->where('name', 'Selesai');
            })
            ->orderBy('deadline', 'asc')
            ->get()
            ->map(function($project) use ($today) {
                $project->days_overdue = $today->diffInDays($project->deadline);
                return $project;
            });

        // Overdue tasks
        $overdueTasks = Task::with(['project', 'assignedUser'])
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function($task) use ($today) {
                $task->days_overdue = $today->diffInDays($task->due_date);
                return $task;
            });

        // Due today (projects + tasks)
        $dueToday = collect();
        
        // Projects due today - Fix: Exclude already completed
        $projectsDueToday = Project::with(['status', 'institution'])
            ->whereDate('deadline', $today)
            ->whereNull('completed_at')  // Not completed
            ->whereDoesntHave('status', function($query) {
                $query->where('name', 'Selesai');
            })
            ->get()
            ->map(function($project) {
                $project->type = 'project';
                return $project;
            });

        $tasksDueToday = Task::with(['project', 'assignedUser'])
            ->whereDate('due_date', $today)
            ->where('status', '!=', 'done')
            ->get()
            ->map(function($task) {
                $task->type = 'task';
                return $task;
            });

        $dueToday = $projectsDueToday->concat($tasksDueToday);

        return [
            'overdue_projects' => $overdueProjects,
            'overdue_projects_count' => $overdueProjects->count(),
            'overdue_tasks' => $overdueTasks,
            'overdue_tasks_count' => $overdueTasks->count(),
            'due_today' => $dueToday,
            'due_today_count' => $dueToday->count(),
            'total_urgent' => $overdueProjects->count() + $overdueTasks->count() + $dueToday->count(),
            'has_critical_alerts' => ($overdueProjects->count() > 0 || $overdueTasks->count() > 0)
        ];
    }
    private function getPendingActions()
    {
        // Get pending invoices
        $pendingInvoices = Invoice::where('status', 'pending')->get();

        // Get pending documents (check if model exists)
        $pendingDocuments = collect();
        try {
            if (class_exists('App\Models\Document')) {
                $pendingDocuments = Document::where('status', 'pending')->get();
            }
        } catch (\Exception $e) {
            // Document model doesn't exist or table doesn't exist
        }

        return [
            'pending_invoices' => $pendingInvoices,
            'pending_invoices_count' => $pendingInvoices->count(),
            'pending_documents' => $pendingDocuments,
            'pending_documents_count' => $pendingDocuments->count(),
            'total_pending' => $pendingInvoices->count() + $pendingDocuments->count()
        ];
    }

    // ==========================================
    // PHASE 2: FINANCIAL DASHBOARD METHODS
    // ==========================================
    /**
     * PHASE 3: Operational Insights
     * Get monthly timeline - tasks and milestones for next 30 days
     */
    /**
     * Get project status distribution
     */
    
    /**
     * Get team performance metrics (deprecated - replaced with project status)
     */
    /**
     * Get recent activities - latest updates across projects, tasks, payments
     */
    /**
     * RAG AI quality metrics for consultation leads
     */
}
