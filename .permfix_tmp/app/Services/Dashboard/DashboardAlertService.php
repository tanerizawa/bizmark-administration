<?php

namespace App\Services\Dashboard;

use App\Models\Document;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

class DashboardAlertService
{
    public function getCriticalAlerts(): array
    {
        $today = Carbon::today();

        $overdueProjects = Project::with(['status', 'institution'])
            ->where('deadline', '<', $today)
            ->whereNull('completed_at')
            ->whereDoesntHave('status', function ($query) {
                $query->where('name', 'Selesai');
            })
            ->orderBy('deadline', 'asc')
            ->get()
            ->map(function ($project) use ($today) {
                $project->days_overdue = $today->diffInDays($project->deadline);
                return $project;
            });

        $overdueTasks = Task::with(['project', 'assignedUser'])
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) use ($today) {
                $task->days_overdue = $today->diffInDays($task->due_date);
                return $task;
            });

        $projectsDueToday = Project::with(['status', 'institution'])
            ->whereDate('deadline', $today)
            ->whereNull('completed_at')
            ->whereDoesntHave('status', function ($query) {
                $query->where('name', 'Selesai');
            })
            ->get()
            ->map(function ($project) {
                $project->type = 'project';
                return $project;
            });

        $tasksDueToday = Task::with(['project', 'assignedUser'])
            ->whereDate('due_date', $today)
            ->where('status', '!=', 'done')
            ->get()
            ->map(function ($task) {
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
            'has_critical_alerts' => ($overdueProjects->count() > 0 || $overdueTasks->count() > 0),
        ];
    }

    public function getPendingActions(): array
    {
        $pendingInvoices = Invoice::where('status', 'pending')->get();

        $pendingDocuments = collect();
        try {
            if (class_exists('App\Models\Document')) {
                $pendingDocuments = Document::where('status', 'pending')->get();
            }
        } catch (\Exception $e) {
            // Document model or table may not exist in some environments.
        }

        return [
            'pending_invoices' => $pendingInvoices,
            'pending_invoices_count' => $pendingInvoices->count(),
            'pending_documents' => $pendingDocuments,
            'pending_documents_count' => $pendingDocuments->count(),
            'total_pending' => $pendingInvoices->count() + $pendingDocuments->count(),
        ];
    }
}

