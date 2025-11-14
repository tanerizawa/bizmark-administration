<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Institution;
use App\Models\Task;
use App\Models\Document;
use App\Models\User;
use App\Models\CashAccount;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard index with caching for performance
     * Cache duration: 5 minutes
     */
    public function index()
    {
        // Cache key unique per user
        $cacheKey = 'dashboard_data_' . auth()->id();
        $cacheDuration = 5; // minutes

        $data = Cache::remember($cacheKey, $cacheDuration * 60, function() {
            return [
                'criticalAlerts' => $this->getCriticalAlerts(),
                'cashFlowStatus' => $this->getCashFlowStatus(),
                'pendingApprovals' => $this->getPendingActions(),
                'cashFlowSummary' => $this->getFinancialSummary(),
                'receivablesAging' => $this->getReceivablesAging(),
                'budgetStatus' => $this->getBudgetStatus(),
                'thisWeek' => $this->getWeeklyTimeline(),
                'projectStatusDistribution' => $this->getProjectStatusDistribution(),
                'recentActivities' => $this->getRecentActivities()
            ];
        });

        \Log::info('Dashboard data loaded', [
            'user_id' => auth()->id(),
            'critical_alerts_count' => $data['criticalAlerts']['total_urgent'] ?? 0,
            'data_keys' => array_keys($data)
        ]);

        return view('dashboard', $data);
    }

    /**
     * Clear dashboard cache manually (useful after data updates)
     */
    public function clearCache()
    {
        $cacheKey = 'dashboard_data_' . auth()->id();
        Cache::forget($cacheKey);
        
        return redirect()->route('dashboard')->with('success', 'Dashboard cache cleared!');
    }

    private function getDashboardStats()
    {
        $total_projects = Project::count();
        $total_tasks = Task::count();
        $total_documents = Document::count();
        $total_institutions = Institution::count();
        
        // Project status breakdown
        $active_projects = Project::whereHas('status', function($query) {
            $query->where('name', 'Aktif');
        })->count();
        
        $completed_projects = Project::whereHas('status', function($query) {
            $query->where('name', 'Selesai');
        })->count();
        
        $overdue_projects = Project::where('deadline', '<', now())
            ->whereDoesntHave('status', function($query) {
                $query->where('name', 'Selesai');
            })->count();
        
        // Task status breakdown
        $pending_tasks = Task::where('status', 'todo')->count();
        $in_progress_tasks = Task::where('status', 'in_progress')->count();
        $completed_tasks = Task::where('status', 'done')->count();
        $overdue_tasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'done')->count();
        
        // Document analytics
        $documents_this_month = Document::where('created_at', '>=', now()->startOfMonth())->count();
        $pending_reviews = Document::where('status', 'review')->count();
        
        // Financial data
        $total_budget = Project::sum('budget');
        $total_spent = Project::sum('actual_cost');
        
        // Financial data (Phase 1)
        $total_contract_value = Project::sum('contract_value');
        $total_payment_received = Project::sum('payment_received');
        $total_expenses = Project::sum('total_expenses');
        $outstanding_receivables = $total_contract_value - $total_payment_received;
        
        // This month financials
        $payments_this_month = ProjectPayment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        $expenses_this_month = ProjectExpense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        
        // Cash position
        $total_cash_balance = CashAccount::where('is_active', true)->sum('current_balance');
        $bank_balance = CashAccount::where('is_active', true)
            ->where('account_type', 'bank')
            ->sum('current_balance');
        $cash_balance = CashAccount::where('is_active', true)
            ->where('account_type', 'cash')
            ->sum('current_balance');
        
        // Completion rate calculation
        $completion_rate = $total_projects > 0 ? round(($completed_projects / $total_projects) * 100, 1) : 0;
        
        return [
            'total_projects' => $total_projects,
            'active_projects' => $active_projects,
            'completed_projects' => $completed_projects,
            'overdue_projects' => $overdue_projects,
            'completion_rate' => $completion_rate,
            'total_tasks' => $total_tasks,
            'pending_tasks' => $pending_tasks,
            'in_progress_tasks' => $in_progress_tasks,
            'completed_tasks' => $completed_tasks,
            'overdue_tasks' => $overdue_tasks,
            'total_documents' => $total_documents,
            'documents_this_month' => $documents_this_month,
            'pending_reviews' => $pending_reviews,
            'total_institutions' => $total_institutions,
            'total_budget' => $total_budget,
            'total_spent' => $total_spent,
            'budget_utilization' => $total_budget > 0 ? ($total_spent / $total_budget) * 100 : 0,
            // Financial metrics (Phase 1)
            'total_contract_value' => $total_contract_value,
            'total_payment_received' => $total_payment_received,
            'total_expenses' => $total_expenses,
            'outstanding_receivables' => $outstanding_receivables,
            'payments_this_month' => $payments_this_month,
            'expenses_this_month' => $expenses_this_month,
            'total_cash_balance' => $total_cash_balance,
            'bank_balance' => $bank_balance,
            'cash_balance' => $cash_balance,
        ];
    }

    private function getRecentProjects()
    {
        return Project::with(['status', 'institution'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRecentTasks()
    {
        return Task::with(['project', 'institution'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRecentDocuments()
    {
        return Document::with(['project', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getProjectsByStatus()
    {
        return ProjectStatus::select('project_statuses.name', 'project_statuses.color', DB::raw('count(projects.id) as projects_count'))
            ->leftJoin('projects', 'project_statuses.id', '=', 'projects.status_id')
            ->groupBy('project_statuses.id', 'project_statuses.name', 'project_statuses.color')
            ->get();
    }

    private function getTasksByStatus()
    {
        return Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    private function getDocumentsByCategory()
    {
        return Document::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');
    }

    private function getUpcomingDeadlines()
    {
        return collect([
            'projects' => Project::with(['status'])
                ->where('deadline', '>=', now())
                ->where('deadline', '<=', now()->addDays(30))
                ->whereDoesntHave('status', function($query) {
                    $query->where('name', 'Selesai');
                })
                ->orderBy('deadline', 'asc')
                ->limit(10)
                ->get(),
            'tasks' => Task::with(['project'])
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(30))
                ->where('status', '!=', 'done')
                ->orderBy('due_date', 'asc')
                ->limit(10)
                ->get()
        ]);
    }

    private function getMonthlyProgress()
    {
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            
            $projects_created = Project::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $projects_completed = Project::whereHas('status', function($query) {
                    $query->where('name', 'Selesai');
                })
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->count();
            
            $data[] = [
                'month' => $month->format('M Y'),
                'created' => $projects_created,
                'completed' => $projects_completed
            ];
        }
        
        return $data;
    }

    private function getTopInstitutions()
    {
        return Institution::withCount('projects')
            ->having('projects_count', '>', 0)
            ->orderBy('projects_count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getProjectCompletionRate()
    {
        $total = Project::count();
        if ($total === 0) return 0;
        
        $completed = Project::whereHas('status', function($query) {
            $query->where('name', 'Selesai');
        })->count();
        
        return round(($completed / $total) * 100, 1);
    }

    // ==========================================
    // PHASE 1: CRITICAL ALERTS METHODS
    // ==========================================

    private function getCriticalAlerts()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Overdue projects
        $overdueProjects = Project::with(['status', 'institution'])
            ->where('deadline', '<', $today)
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
        
        $projectsDueToday = Project::with(['status', 'institution'])
            ->whereDate('deadline', $today)
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

    private function getCashFlowStatus()
    {
        try {
            // Current cash balance
            $currentBalance = CashAccount::where('is_active', true)->sum('current_balance');

            // Calculate monthly burn rate (IMPROVED: only average months with expenses)
            $threeMonthsAgo = Carbon::now()->subMonths(3);
            
            // Get expenses grouped by month
            $monthlyExpenses = ProjectExpense::where('expense_date', '>=', $threeMonthsAgo)
                ->selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
                ->groupBy('year', 'month')
                ->get();

            // Calculate average only for months with expenses (more accurate)
            $monthsWithExpenses = $monthlyExpenses->count();
            $totalExpenses = $monthlyExpenses->sum('total');
            
            // If no expenses in last 3 months, use all-time average as fallback
            if ($monthsWithExpenses === 0) {
                $allTimeExpenses = ProjectExpense::selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
                    ->groupBy('year', 'month')
                    ->get();
                $monthsWithExpenses = $allTimeExpenses->count();
                $totalExpenses = $allTimeExpenses->sum('total');
            }
            
            $monthlyBurnRate = $monthsWithExpenses > 0 ? $totalExpenses / $monthsWithExpenses : 0;

            // Calculate runway (months)
            $runway = $monthlyBurnRate > 0 ? $currentBalance / $monthlyBurnRate : 999;

            // Overdue invoices total
            $overdueInvoices = Invoice::where('status', 'overdue')
                ->orWhere(function($query) {
                    $query->where('due_date', '<', Carbon::today())
                          ->where('status', '!=', 'paid')
                          ->where('remaining_amount', '>', 0);
                })
                ->sum('remaining_amount');

            // Status color based on runway
            $status = 'healthy';
            $statusColor = '#34C759'; // Green
            if ($runway < 2) {
                $status = 'critical';
                $statusColor = '#FF3B30'; // Red
            } elseif ($runway < 6) {
                $status = 'warning';
                $statusColor = '#FF9500'; // Orange
            }

            // Validation: Log warning if cash balance is negative
            if ($currentBalance < 0) {
                \Log::warning('Dashboard: Negative cash balance detected', [
                    'balance' => $currentBalance,
                    'date' => Carbon::now()->toDateTimeString()
                ]);
            }

            return [
                'total_balance' => $currentBalance, // Added for view compatibility
                'available_cash' => $currentBalance,
                'current_balance' => $currentBalance,
                'monthly_burn_rate' => $monthlyBurnRate,
                'runway_months' => round($runway, 1),
                'overdue_invoices' => $overdueInvoices,
                'status' => $status,
                'status_color' => $statusColor
            ];
            
        } catch (\Exception $e) {
            \Log::error('Dashboard getCashFlowStatus error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return safe defaults on error
            return [
                'total_balance' => 0,
                'available_cash' => 0,
                'current_balance' => 0,
                'monthly_burn_rate' => 0,
                'runway_months' => 0,
                'overdue_invoices' => 0,
                'status' => 'unknown',
                'status_color' => '#8E8E93' // Gray
            ];
        }
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

    private function getFinancialSummary()
    {
        try {
            $thisMonth = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();
            $startOfYear = Carbon::now()->startOfYear();

            // Total cash balance from all accounts
            $totalCashBalance = CashAccount::sum('current_balance');

        // This month income from multiple sources:
        // 1. Invoice payments (from payment_schedules with paid status)
        $invoicePaymentsThisMonth = \DB::table('payment_schedules')
            ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
            ->where('payment_schedules.status', 'paid')
            ->whereNotNull('payment_schedules.paid_date')
            ->whereYear('payment_schedules.paid_date', $thisMonth->year)
            ->whereMonth('payment_schedules.paid_date', $thisMonth->month)
            ->sum('payment_schedules.amount');

        // 2. Direct project payments (legacy - not linked to invoice)
        $directPaymentsThisMonth = ProjectPayment::whereNull('invoice_id')
            ->whereYear('payment_date', $thisMonth->year)
            ->whereMonth('payment_date', $thisMonth->month)
            ->sum('amount');

        $paymentsThisMonth = $invoicePaymentsThisMonth + $directPaymentsThisMonth;

        // This month expenses (out)
        $expensesThisMonth = ProjectExpense::whereYear('expense_date', $thisMonth->year)
            ->whereMonth('expense_date', $thisMonth->month)
            ->sum('amount');

        // Year to date totals
        $invoicePaymentsYTD = \DB::table('payment_schedules')
            ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
            ->where('payment_schedules.status', 'paid')
            ->whereNotNull('payment_schedules.paid_date')
            ->whereYear('payment_schedules.paid_date', $thisMonth->year)
            ->sum('payment_schedules.amount');

        $directPaymentsYTD = ProjectPayment::whereNull('invoice_id')
            ->whereYear('payment_date', $thisMonth->year)
            ->sum('amount');

        $paymentsYTD = $invoicePaymentsYTD + $directPaymentsYTD;
        
        $expensesYTD = ProjectExpense::whereYear('expense_date', $thisMonth->year)
            ->sum('amount');

        // Last month payments
        $invoicePaymentsLastMonth = \DB::table('payment_schedules')
            ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
            ->where('payment_schedules.status', 'paid')
            ->whereNotNull('payment_schedules.paid_date')
            ->whereYear('payment_schedules.paid_date', $lastMonth->year)
            ->whereMonth('payment_schedules.paid_date', $lastMonth->month)
            ->sum('payment_schedules.amount');

        $directPaymentsLastMonth = ProjectPayment::whereNull('invoice_id')
            ->whereYear('payment_date', $lastMonth->year)
            ->whereMonth('payment_date', $lastMonth->month)
            ->sum('amount');

        $paymentsLastMonth = $invoicePaymentsLastMonth + $directPaymentsLastMonth;

        // Last month expenses
        $expensesLastMonth = ProjectExpense::whereYear('expense_date', $lastMonth->year)
            ->whereMonth('expense_date', $lastMonth->month)
            ->sum('amount');

        // Net profit/loss this month
        $netThisMonth = $paymentsThisMonth - $expensesThisMonth;
        $netLastMonth = $paymentsLastMonth - $expensesLastMonth;
        $netYTD = $paymentsYTD - $expensesYTD;

        // Calculate growth percentage
        $paymentsGrowth = $paymentsLastMonth > 0 
            ? round((($paymentsThisMonth - $paymentsLastMonth) / $paymentsLastMonth) * 100, 1)
            : 0;

        $expensesGrowth = $expensesLastMonth > 0 
            ? round((($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100, 1)
            : 0;

        // Calculate totals for invoicing
        $totalInvoiced = Invoice::sum('total_amount');
        $totalReceived = Invoice::sum('paid_amount');

        return [
            'total_cash_balance' => $totalCashBalance,
            'this_month_income' => $paymentsThisMonth, // Added for compatibility
            'this_month_expenses' => $expensesThisMonth, // Added for compatibility
            'payments_this_month' => $paymentsThisMonth,
            'expenses_this_month' => $expensesThisMonth,
            'net_this_month' => $netThisMonth,
            'payments_ytd' => $paymentsYTD,
            'expenses_ytd' => $expensesYTD,
            'net_ytd' => $netYTD,
            'payments_last_month' => $paymentsLastMonth,
            'expenses_last_month' => $expensesLastMonth,
            'net_last_month' => $netLastMonth,
            'payments_growth' => $paymentsGrowth,
            'expenses_growth' => $expensesGrowth,
            'is_profitable' => $netThisMonth > 0,
            'total_invoiced' => $totalInvoiced, // Added for invoice stats
            'total_received' => $totalReceived, // Added for invoice stats
        ];
        
        } catch (\Exception $e) {
            \Log::error('Dashboard getFinancialSummary error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return safe defaults on error
            return [
                'total_cash_balance' => 0,
                'this_month_income' => 0,
                'this_month_expenses' => 0,
                'payments_this_month' => 0,
                'expenses_this_month' => 0,
                'net_this_month' => 0,
                'payments_ytd' => 0,
                'expenses_ytd' => 0,
                'net_ytd' => 0,
                'payments_last_month' => 0,
                'expenses_last_month' => 0,
                'net_last_month' => 0,
                'payments_growth' => 0,
                'expenses_growth' => 0,
                'is_profitable' => false,
                'total_invoiced' => 0,
                'total_received' => 0,
            ];
        }
    }

    private function getReceivablesAging()
    {
        $today = Carbon::today();

        // Get all unpaid invoices with remaining amount
        $unpaidInvoices = Invoice::where('status', '!=', 'paid')
            ->where('remaining_amount', '>', 0)
            ->get();

        // Get internal receivables (kasbon)
        $internalReceivables = ProjectExpense::where('is_receivable', 1)
            ->where('receivable_status', '!=', 'paid')
            ->get();

        // Group by aging buckets
        $aging = [
            'under_30' => 0,      // 0-30 days
            'days_30_60' => 0,    // 31-60 days
            'days_60_90' => 0,    // 61-90 days
            'over_90' => 0,       // 90+ days
        ];

        // Process invoice receivables
        foreach ($unpaidInvoices as $invoice) {
            $dueDate = Carbon::parse($invoice->due_date);
            $daysOverdue = $today->diffInDays($dueDate, false); // negative if overdue

            if ($daysOverdue >= 0 || abs($daysOverdue) <= 30) {
                $aging['under_30'] += $invoice->remaining_amount;
            } elseif (abs($daysOverdue) <= 60) {
                $aging['days_30_60'] += $invoice->remaining_amount;
            } elseif (abs($daysOverdue) <= 90) {
                $aging['days_60_90'] += $invoice->remaining_amount;
            } else {
                $aging['over_90'] += $invoice->remaining_amount;
            }
        }

        // Process internal receivables (kasbon)
        $internalAgingBuckets = [
            'under_30' => 0,
            'days_30_60' => 0,
            'days_60_90' => 0,
            'over_90' => 0,
        ];

        foreach ($internalReceivables as $receivable) {
            $expenseDate = Carbon::parse($receivable->expense_date);
            $daysOld = $today->diffInDays($expenseDate);
            $remainingAmount = $receivable->amount - $receivable->receivable_paid_amount;

            if ($daysOld <= 30) {
                $aging['under_30'] += $remainingAmount;
                $internalAgingBuckets['under_30'] += $remainingAmount;
            } elseif ($daysOld <= 60) {
                $aging['days_30_60'] += $remainingAmount;
                $internalAgingBuckets['days_30_60'] += $remainingAmount;
            } elseif ($daysOld <= 90) {
                $aging['days_60_90'] += $remainingAmount;
                $internalAgingBuckets['days_60_90'] += $remainingAmount;
            } else {
                $aging['over_90'] += $remainingAmount;
                $internalAgingBuckets['over_90'] += $remainingAmount;
            }
        }

        $invoiceReceivables = $unpaidInvoices->sum('remaining_amount');
        $internalReceivablesTotal = $internalReceivables->sum(function($r) {
            return $r->amount - $r->receivable_paid_amount;
        });
        $totalReceivables = $invoiceReceivables + $internalReceivablesTotal;

        return [
            'aging' => $aging,
            'total_receivables' => $totalReceivables,
            'invoice_receivables' => $invoiceReceivables,
            'internal_receivables' => $internalReceivablesTotal,
            'internal_aging' => $internalAgingBuckets,
            'invoice_count' => $unpaidInvoices->count(),
            'internal_count' => $internalReceivables->count(),
            'internal_list' => $internalReceivables->map(function($r) {
                return [
                    'id' => $r->id,
                    'from' => $r->receivable_from,
                    'amount' => $r->amount,
                    'paid_amount' => $r->receivable_paid_amount,
                    'remaining' => $r->amount - $r->receivable_paid_amount,
                    'date' => $r->expense_date,
                    'description' => $r->description,
                    'status' => $r->receivable_status,
                ];
            })
        ];
    }

    private function getBudgetStatus()
    {
        // Get top 5 projects by budget variance (over budget or close to budget)
        $projects = Project::with(['status', 'expenses'])
            ->whereNotNull('contract_value')
            ->where('contract_value', '>', 0)
            ->get()
            ->map(function($project) {
                // Use contract_value as budget (new system)
                $budget = $project->contract_value > 0 ? $project->contract_value : ($project->budget ?? 0);
                
                // Calculate actual expenses from project_expenses table
                $actualExpenses = $project->expenses()->sum('amount');
                
                $project->variance = $actualExpenses - $budget;
                $project->variance_percentage = $budget > 0 ? round(($actualExpenses / $budget) * 100, 1) : 0;
                $project->is_over_budget = $project->variance > 0;
                $project->is_near_budget = $project->variance_percentage >= 80 && $project->variance_percentage <= 100;
                $project->actual_expenses = $actualExpenses;
                $project->budget_display = $budget;
                
                // Status color
                if ($project->variance_percentage > 100) {
                    $project->status_color = '#FF3B30'; // Red - over budget
                } elseif ($project->variance_percentage >= 80) {
                    $project->status_color = '#FF9500'; // Orange - warning
                } else {
                    $project->status_color = '#34C759'; // Green - healthy
                }
                
                return $project;
            })
            ->sortByDesc('variance_percentage')
            ->take(5);

        // Summary stats - use contract_value (new) or fallback to budget (legacy)
        $totalBudget = Project::selectRaw('SUM(COALESCE(NULLIF(contract_value, 0), budget)) as total')
            ->value('total') ?? 0;
        
        // Total spent from all project expenses
        $totalSpent = ProjectExpense::sum('amount');
        
        $overallUtilization = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;

        return [
            'top_projects' => $projects,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'overall_utilization' => $overallUtilization
        ];
    }

    /**
     * PHASE 3: Operational Insights
     * Get monthly timeline - tasks and milestones for next 30 days
     */
    private function getWeeklyTimeline()
    {
        $today = Carbon::now()->startOfDay();
        $endOfMonth = Carbon::now()->addDays(30)->endOfDay();

        // Get tasks due in next 30 days
        $tasks = Task::with(['project', 'assignedUser'])
            ->whereBetween('due_date', [$today, $endOfMonth])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) {
                $dueDate = $task->due_date ? Carbon::parse($task->due_date) : null;
                if (!$dueDate) return null;
                
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
                    'priority_color' => $isPast ? '#FF3B30' : ($isToday ? '#FFCC00' : '#34C759')
                ];
            })->filter();

        // Get projects with milestones/deadlines in next 30 days
        $projects = Project::whereBetween('deadline', [$today, $endOfMonth])
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
                    'status_color' => $project->status_color ?? '#0A84FF'
                ];
            });

        return [
            'tasks' => $tasks,
            'projects' => $projects,
            'period_start' => $today->format('d M'),
            'period_end' => $endOfMonth->format('d M'),
            'total_items' => $tasks->count() + $projects->count()
        ];
    }

    /**
     * Get project status distribution
     */
    private function getProjectStatusDistribution()
    {
        // Get all projects grouped by status
        $projects = Project::with('status')->get();
        
        $statusGroups = $projects->groupBy(function ($project) {
            return $project->status ? $project->status->name : 'No Status';
        });
        
        $distribution = $statusGroups->map(function ($group, $statusName) {
            $count = $group->count();
            $projects = $group;
            
            // Get color from first project's status
            $firstProject = $group->first();
            $color = $firstProject && $firstProject->status 
                ? $firstProject->status->color 
                : 'rgba(142, 142, 147, 1)';
            
            return [
                'label' => $statusName,
                'status_name' => $statusName,
                'count' => $count,
                'color' => $color,
                'projects' => $projects->take(3)->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'code' => $p->project_code ?? '',
                    ];
                })
            ];
        })
        ->sortByDesc('count')
        ->take(5)
        ->values();
        
        $totalProjects = $projects->count();
        
        return [
            'groups' => $distribution,
            'total' => $totalProjects
        ];
    }
    
    /**
     * Get team performance metrics (deprecated - replaced with project status)
     */
    private function getTeamPerformance()
    {
        // Get all users with their task completion stats
        $users = User::withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => function ($query) {
                $query->where('status', 'done');
            },
            'tasks as overdue_tasks' => function ($query) {
                $query->where('due_date', '<', Carbon::now())
                      ->whereNotIn('status', ['done', 'cancelled']);
            }
        ])
        ->having('total_tasks', '>', 0)
        ->get()
        ->map(function ($user) {
            $completionRate = $user->total_tasks > 0 
                ? round(($user->completed_tasks / $user->total_tasks) * 100, 1) 
                : 0;
            
            $performance = 'good';
            if ($completionRate >= 80) {
                $performance = 'excellent';
                $color = 'rgba(52, 199, 89, 1)';
            } elseif ($completionRate >= 60) {
                $performance = 'good';
                $color = 'rgba(0, 122, 255, 1)';
            } elseif ($completionRate >= 40) {
                $performance = 'average';
                $color = 'rgba(255, 204, 0, 1)';
            } else {
                $performance = 'needs_improvement';
                $color = 'rgba(255, 149, 0, 1)';
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_tasks' => $user->total_tasks,
                'completed_tasks' => $user->completed_tasks,
                'overdue_tasks' => $user->overdue_tasks,
                'completion_rate' => $completionRate,
                'performance' => $performance,
                'color' => $color
            ];
        })
        ->sortByDesc('completion_rate')
        ->take(5);

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $overallCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        return [
            'top_performers' => $users,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'overall_completion_rate' => $overallCompletionRate
        ];
    }

    /**
     * Get recent activities - latest updates across projects, tasks, payments
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent project updates
        $recentProjects = Project::with('status')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => '📁',
                    'title' => $project->name,
                    'description' => 'Project ' . ($project->status ? $project->status->name : 'N/A'),
                    'time' => $project->updated_at,
                    'time_formatted' => $project->updated_at->diffForHumans(),
                    'link' => route('projects.show', $project),
                    'color' => 'rgba(0, 122, 255, 1)'
                ];
            });

        // Recent task completions
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
                    'color' => 'rgba(52, 199, 89, 1)'
                ];
            });

        // Recent payments
        $recentPayments = ProjectPayment::with('project')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'icon' => 'money-bill-wave',
                    'title' => 'Payment Received',
                    'description' => 'Rp ' . number_format($payment->amount) . ' - ' . ($payment->project ? $payment->project->name : 'N/A'),
                    'time' => $payment->payment_date,
                    'time_formatted' => $payment->payment_date->diffForHumans(),
                    'link' => $payment->project ? route('projects.show', $payment->project) : '#',
                    'color' => 'rgba(52, 199, 89, 1)'
                ];
            });

        // Recent invoices
        $recentInvoices = Invoice::with('project')
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'invoice',
                    'icon' => '📄',
                    'title' => $invoice->invoice_number,
                    'description' => 'Invoice ' . ucfirst($invoice->status) . ' - ' . ($invoice->project ? $invoice->project->name : 'N/A'),
                    'time' => $invoice->created_at,
                    'time_formatted' => $invoice->created_at->diffForHumans(),
                    'link' => route('invoices.show', $invoice),
                    'color' => 'rgba(175, 82, 222, 1)'
                ];
            });

        // Merge and sort by time
        $activities = $activities
            ->concat($recentProjects)
            ->concat($recentTasks)
            ->concat($recentPayments)
            ->concat($recentInvoices)
            ->sortByDesc('time')
            ->take(10);

        return [
            'activities' => $activities,
            'count' => $activities->count()
        ];
    }
}
