<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class OverviewController extends Controller
{
    /**
     * Show financial tab for a project (totals + chart data).
     */
    public function index(Project $project)
    {
        $project->load([
            'invoices.items',
            'paymentSchedules',
            'expenses',
        ]);

        $totalBudget = $project->contract_value ?? 0;
        $totalInvoiced = $project->invoices()->sum('total_amount');
        $totalReceived = $project->invoices()->sum('paid_amount');
        $totalExpenses = $project->expenses()->sum('amount');
        $totalScheduled = $project->paymentSchedules()->where('status', 'pending')->sum('amount');

        $budgetRemaining = $totalBudget - $totalInvoiced;

        $receivableOutstanding = $project->expenses()
            ->where('is_receivable', true)
            ->whereIn('receivable_status', ['pending', 'partial'])
            ->sum('amount');

        $profitMargin = $totalReceived - $totalExpenses;

        $directIncomes = $project->payments()
            ->whereNull('invoice_id')
            ->with(['bankAccount', 'createdBy'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalDirectIncome = $directIncomes->sum('amount');

        $monthlyData = $this->getMonthlyFinancialData($project);

        return view('projects.partials.financial-tab', compact(
            'project',
            'totalBudget',
            'totalInvoiced',
            'totalReceived',
            'totalExpenses',
            'totalScheduled',
            'budgetRemaining',
            'receivableOutstanding',
            'profitMargin',
            'monthlyData',
            'directIncomes',
            'totalDirectIncome'
        ));
    }

    /**
     * Build income/expense breakdown for 6 bulan terakhir (dipakai chart).
     */
    private function getMonthlyFinancialData(Project $project): array
    {
        $months = [];
        $income = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $invoiceIncome = $project->paymentSchedules()
                ->where('status', 'paid')
                ->whereNotNull('paid_date')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('amount');

            $directIncome = $project->payments()
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $income[] = (float) ($invoiceIncome + $directIncome);

            $monthExpense = $project->expenses()
                ->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');
            $expenses[] = (float) $monthExpense;
        }

        return [
            'labels' => $months,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }
}
