<?php

namespace App\Services\Dashboard;

use App\Models\CashAccount;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardFinancialService
{
    public function getCashFlowStatus(): array
    {
        try {
            $currentBalance = CashAccount::where('is_active', true)->sum('current_balance');
            $threeMonthsAgo = Carbon::now()->subMonths(3);

            $monthlyExpenses = ProjectExpense::where('expense_date', '>=', $threeMonthsAgo)
                ->selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
                ->groupBy('year', 'month')
                ->get();

            $monthsWithExpenses = $monthlyExpenses->count();
            $totalExpenses = $monthlyExpenses->sum('total');

            if ($monthsWithExpenses === 0) {
                $allTimeExpenses = ProjectExpense::selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
                    ->groupBy('year', 'month')
                    ->get();
                $monthsWithExpenses = $allTimeExpenses->count();
                $totalExpenses = $allTimeExpenses->sum('total');
            }

            $monthlyBurnRate = $monthsWithExpenses > 0 ? $totalExpenses / $monthsWithExpenses : 0;

            if ($monthlyBurnRate > 0) {
                // Normal case: divide current balance by monthly burn rate
                $runway = $currentBalance > 0
                    ? min($currentBalance / $monthlyBurnRate, 99)
                    : 0;
            } elseif ($currentBalance <= 0) {
                // No burn rate AND no cash balance → 0 runway
                $runway = 0;
            } else {
                // Has cash but zero expenses → effectively infinite runway (capped at 99)
                $runway = 99;
            }

            $overdueInvoices = Invoice::where('status', 'overdue')
                ->orWhere(function ($query) {
                    $query->where('due_date', '<', Carbon::today())
                        ->where('status', '!=', 'paid')
                        ->where('remaining_amount', '>', 0);
                })
                ->sum('remaining_amount');

            $status = 'healthy';
            $statusColor = '#34C759';
            if ($runway < 2) {
                $status = 'critical';
                $statusColor = '#FF3B30';
            } elseif ($runway < 6) {
                $status = 'warning';
                $statusColor = '#FF9500';
            }

            if ($currentBalance < 0) {
                Log::warning('Dashboard: Negative cash balance detected', [
                    'balance' => $currentBalance,
                    'date' => Carbon::now()->toDateTimeString(),
                ]);
            }

            return [
                'total_balance' => $currentBalance,
                'available_cash' => $currentBalance,
                'current_balance' => $currentBalance,
                'monthly_burn_rate' => $monthlyBurnRate,
                'runway_months' => round($runway, 1),
                'overdue_invoices' => $overdueInvoices,
                'status' => $status,
                'status_color' => $statusColor,
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard getCashFlowStatus error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'total_balance' => 0,
                'available_cash' => 0,
                'current_balance' => 0,
                'monthly_burn_rate' => 0,
                'runway_months' => 0,
                'overdue_invoices' => 0,
                'status' => 'unknown',
                'status_color' => '#8E8E93',
            ];
        }
    }

    public function getFinancialSummary(): array
    {
        try {
            $thisMonth = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();

            $totalCashBalance = CashAccount::sum('current_balance');

            $invoicePaymentsThisMonth = DB::table('payment_schedules')
                ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
                ->where('payment_schedules.status', 'paid')
                ->whereNotNull('payment_schedules.paid_date')
                ->whereYear('payment_schedules.paid_date', $thisMonth->year)
                ->whereMonth('payment_schedules.paid_date', $thisMonth->month)
                ->sum('payment_schedules.amount');

            $directPaymentsThisMonth = ProjectPayment::whereNull('invoice_id')
                ->whereYear('payment_date', $thisMonth->year)
                ->whereMonth('payment_date', $thisMonth->month)
                ->sum('amount');

            $paymentsThisMonth = $invoicePaymentsThisMonth + $directPaymentsThisMonth;

            $expensesThisMonth = ProjectExpense::whereYear('expense_date', $thisMonth->year)
                ->whereMonth('expense_date', $thisMonth->month)
                ->sum('amount');

            $invoicePaymentsYTD = DB::table('payment_schedules')
                ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
                ->where('payment_schedules.status', 'paid')
                ->whereNotNull('payment_schedules.paid_date')
                ->whereYear('payment_schedules.paid_date', $thisMonth->year)
                ->sum('payment_schedules.amount');

            $directPaymentsYTD = ProjectPayment::whereNull('invoice_id')
                ->whereYear('payment_date', $thisMonth->year)
                ->sum('amount');

            $paymentsYTD = $invoicePaymentsYTD + $directPaymentsYTD;
            $expensesYTD = ProjectExpense::whereYear('expense_date', $thisMonth->year)->sum('amount');

            $invoicePaymentsLastMonth = DB::table('payment_schedules')
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
            $expensesLastMonth = ProjectExpense::whereYear('expense_date', $lastMonth->year)
                ->whereMonth('expense_date', $lastMonth->month)
                ->sum('amount');

            $netThisMonth = $paymentsThisMonth - $expensesThisMonth;
            $netLastMonth = $paymentsLastMonth - $expensesLastMonth;
            $netYTD = $paymentsYTD - $expensesYTD;

            $paymentsGrowth = $paymentsLastMonth > 0
                ? round((($paymentsThisMonth - $paymentsLastMonth) / $paymentsLastMonth) * 100, 1)
                : 0;

            $expensesGrowth = $expensesLastMonth > 0
                ? round((($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100, 1)
                : 0;

            $totalInvoiced = Invoice::sum('total_amount');
            $totalReceived = Invoice::sum('paid_amount');

            return [
                'total_cash_balance' => $totalCashBalance,
                'this_month_income' => $paymentsThisMonth,
                'this_month_expenses' => $expensesThisMonth,
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
                'total_invoiced' => $totalInvoiced,
                'total_received' => $totalReceived,
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard getFinancialSummary error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

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

    public function getReceivablesAging(): array
    {
        $today = Carbon::today();

        $unpaidInvoices = Invoice::where('status', '!=', 'paid')
            ->where('remaining_amount', '>', 0)
            ->get();

        $internalReceivables = ProjectExpense::where('is_receivable', 1)
            ->where('receivable_status', '!=', 'paid')
            ->get();

        $aging = [
            'under_30' => 0,
            'days_30_60' => 0,
            'days_60_90' => 0,
            'over_90' => 0,
        ];

        foreach ($unpaidInvoices as $invoice) {
            $dueDate = Carbon::parse($invoice->due_date);
            $daysOverdue = $today->diffInDays($dueDate, false);

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
        $internalReceivablesTotal = $internalReceivables->sum(function ($r) {
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
            'internal_list' => $internalReceivables->map(function ($r) {
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
            }),
        ];
    }

    public function getBudgetStatus(): array
    {
        $projects = Project::with(['status', 'expenses'])
            ->whereNotNull('contract_value')
            ->where('contract_value', '>', 0)
            ->get()
            ->map(function ($project) {
                $budget = $project->contract_value > 0 ? $project->contract_value : ($project->budget ?? 0);
                $actualExpenses = $project->expenses()->sum('amount');

                $project->variance = $actualExpenses - $budget;
                $project->variance_percentage = $budget > 0 ? round(($actualExpenses / $budget) * 100, 1) : 0;
                $project->is_over_budget = $project->variance > 0;
                $project->is_near_budget = $project->variance_percentage >= 80 && $project->variance_percentage <= 100;
                $project->actual_expenses = $actualExpenses;
                $project->budget_display = $budget;

                if ($project->variance_percentage > 100) {
                    $project->status_color = '#FF3B30';
                } elseif ($project->variance_percentage >= 80) {
                    $project->status_color = '#FF9500';
                } else {
                    $project->status_color = '#34C759';
                }

                return $project;
            })
            ->sortByDesc('variance_percentage')
            ->take(5);

        $totalBudget = Project::selectRaw('SUM(COALESCE(NULLIF(contract_value, 0), budget)) as total')
            ->value('total') ?? 0;

        $totalSpent = ProjectExpense::sum('amount');
        $overallUtilization = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;

        return [
            'top_projects' => $projects,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'overall_utilization' => $overallUtilization,
        ];
    }
}
