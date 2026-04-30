<?php

namespace App\Services\Dashboard;

use App\Models\ConsultRequest;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\Task;
use Carbon\Carbon;

class DashboardOperationalService
{
    public function getWeeklyTimeline(): array
    {
        $today = Carbon::now()->startOfDay();
        $endOfMonth = Carbon::now()->addDays(30)->endOfDay();

        $tasks = Task::with(['project', 'assignedUser'])
            ->whereBetween('due_date', [$today, $endOfMonth])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) {
                $dueDate = $task->due_date ? Carbon::parse($task->due_date) : null;
                if (!$dueDate) {
                    return null;
                }

                $daysUntil = Carbon::now()->diffInDays($dueDate, false);
                $isPast = $daysUntil < 0;
                $isToday = Carbon::now()->isSameDay($dueDate);

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'project' => $task->project->name ?? 'No Project',
                    'project_id' => $task->project_id,
                    'deadline' => $dueDate,
                    'deadline_formatted' => $dueDate->format('D, d M'),
                    'days_until' => abs($daysUntil),
                    'is_past' => $isPast,
                    'is_today' => $isToday,
                    'status' => $task->status,
                    'assigned_to' => $task->assignedUser->name ?? 'Unassigned',
                    'priority_color' => $isPast ? '#FF3B30' : ($isToday ? '#FFCC00' : '#34C759'),
                ];
            })->filter();

        $projects = Project::whereBetween('deadline', [$today, $endOfMonth])
            ->whereNull('completed_at')
            ->orderBy('deadline', 'asc')
            ->get()
            ->map(function ($project) {
                $daysUntil = Carbon::now()->diffInDays($project->deadline, false);
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'deadline' => $project->deadline,
                    'deadline_formatted' => $project->deadline->format('D, d M'),
                    'days_until' => abs($daysUntil),
                    'is_past' => $daysUntil < 0,
                    'status' => $project->status,
                    'status_color' => $project->status_color ?? '#0A84FF',
                ];
            });

        return [
            'tasks' => $tasks,
            'projects' => $projects,
            'period_start' => $today->format('d M'),
            'period_end' => $endOfMonth->format('d M'),
            'total_items' => $tasks->count() + $projects->count(),
        ];
    }

    public function getProjectStatusDistribution(): array
    {
        $projects = Project::with('status')->get();

        $statusGroups = $projects->groupBy(function ($project) {
            return $project->status ? $project->status->name : 'No Status';
        });

        $distribution = $statusGroups->map(function ($group, $statusName) {
            $count = $group->count();
            $firstProject = $group->first();
            $color = $firstProject && $firstProject->status
                ? $firstProject->status->color
                : 'rgba(142, 142, 147, 1)';

            return [
                'label' => $statusName,
                'status_name' => $statusName,
                'count' => $count,
                'color' => $color,
                'projects' => $group->take(3)->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'code' => $p->project_code ?? '',
                    ];
                }),
            ];
        })
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return [
            'groups' => $distribution,
            'total' => $projects->count(),
        ];
    }

    public function getRecentActivities(): array
    {
        $activities = collect();

        $recentProjects = Project::with('status')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => '📁',
                    'title' => $project->name,
                    'description' => 'Project '.($project->status ? $project->status->name : 'N/A'),
                    'time' => $project->updated_at,
                    'time_formatted' => $project->updated_at->diffForHumans(),
                    'link' => route('projects.show', $project),
                    'color' => 'rgba(0, 122, 255, 1)',
                ];
            });

        $recentTasks = Task::where('status', 'done')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task',
                    'icon' => '✅',
                    'title' => $task->title,
                    'description' => 'Task completed',
                    'time' => $task->updated_at,
                    'time_formatted' => $task->updated_at->diffForHumans(),
                    'link' => route('tasks.show', $task),
                    'color' => 'rgba(52, 199, 89, 1)',
                ];
            });

        $recentPayments = ProjectPayment::with('project')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'icon' => 'money-bill-wave',
                    'title' => 'Payment Received',
                    'description' => 'Rp '.number_format($payment->amount).' - '.($payment->project ? $payment->project->name : 'N/A'),
                    'time' => $payment->payment_date,
                    'time_formatted' => $payment->payment_date->diffForHumans(),
                    'link' => $payment->project ? route('projects.show', $payment->project) : '#',
                    'color' => 'rgba(52, 199, 89, 1)',
                ];
            });

        $recentInvoices = Invoice::with('project')
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'invoice',
                    'icon' => '📄',
                    'title' => $invoice->invoice_number,
                    'description' => 'Invoice '.ucfirst($invoice->status).' - '.($invoice->project ? $invoice->project->name : 'N/A'),
                    'time' => $invoice->created_at,
                    'time_formatted' => $invoice->created_at->diffForHumans(),
                    'link' => route('invoices.show', $invoice),
                    'color' => 'rgba(175, 82, 222, 1)',
                ];
            });

        $activities = $activities
            ->concat($recentProjects)
            ->concat($recentTasks)
            ->concat($recentPayments)
            ->concat($recentInvoices)
            ->sortByDesc('time')
            ->take(10);

        return [
            'activities' => $activities,
            'count' => $activities->count(),
        ];
    }

    public function getRagMetrics(): array
    {
        $total = ConsultRequest::whereNotNull('rag_processed_at')->count();
        $avgConfidence = ConsultRequest::whereNotNull('rag_confidence')->avg('rag_confidence') ?? 0;
        $highConfidence = ConsultRequest::where('rag_confidence', '>=', 0.7)->count();
        $lowConfidence = ConsultRequest::where('rag_confidence', '<', 0.4)->whereNotNull('rag_confidence')->count();

        $recent = ConsultRequest::whereNotNull('rag_processed_at')
            ->orderBy('rag_processed_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'company_name', 'rag_confidence', 'rag_processed_at']);

        return [
            'total_processed' => $total,
            'avg_confidence' => round($avgConfidence * 100),
            'high_confidence' => $highConfidence,
            'low_confidence' => $lowConfidence,
            'recent' => $recent,
        ];
    }
}

