<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Log;

class DashboardDataService
{
    public function __construct(
        private DashboardAlertService $alerts,
        private DashboardFinancialService $financial,
        private DashboardOperationalService $operational,
    ) {}

    /**
     * Build dashboard data with graceful degradation per section.
     *
     * FIX (BUG-14): Each sub-service is wrapped in rescue() so a failure in one
     * section (e.g., financial) doesn't crash the entire dashboard.
     * Failed sections return empty arrays and errors are logged.
     */
    public function build(): array
    {
        return [
            'criticalAlerts' => rescue(fn () => $this->alerts->getCriticalAlerts(), [], $this->getReporter('alerts')),
            'cashFlowStatus' => rescue(fn () => $this->financial->getCashFlowStatus(), ['runway_months' => 0, 'total_cash' => 0, 'monthly_burn' => 0], $this->getReporter('financial.cashFlowStatus')),
            'pendingApprovals' => rescue(fn () => $this->alerts->getPendingActions(), ['total_pending' => 0], $this->getReporter('alerts.pendingApprovals')),
            'cashFlowSummary' => rescue(fn () => $this->financial->getFinancialSummary(), ['total_income' => 0, 'total_expense' => 0, 'net_cash_flow' => 0, 'payments_this_month' => 0, 'expenses_this_month' => 0], $this->getReporter('financial.cashFlowSummary')),
            'receivablesAging' => rescue(fn () => $this->financial->getReceivablesAging(), [], $this->getReporter('financial.receivablesAging')),
            'budgetStatus' => rescue(fn () => $this->financial->getBudgetStatus(), ['total_budget' => 0, 'total_actual' => 0, 'percentage' => 0], $this->getReporter('financial.budgetStatus')),
            'thisWeek' => rescue(fn () => $this->operational->getWeeklyTimeline(), ['total_items' => 0, 'items' => []], $this->getReporter('operational.thisWeek')),
            'projectStatusDistribution' => rescue(fn () => $this->operational->getProjectStatusDistribution(), [], $this->getReporter('operational.projectStatusDistribution')),
            'recentActivities' => rescue(fn () => $this->operational->getRecentActivities(), [], $this->getReporter('operational.recentActivities')),
            'ragMetrics' => rescue(fn () => $this->operational->getRagMetrics(), ['red' => 0, 'amber' => 0, 'green' => 0], $this->getReporter('operational.ragMetrics')),
        ];
    }

    /**
     * Create a callable that logs errors for a specific data section.
     */
    private function getReporter(string $section): callable
    {
        return function (\Throwable $e) use ($section) {
            Log::error("Dashboard data service failed for section [{$section}]: ".$e->getMessage(), [
                'section' => $section,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        };
    }
}
