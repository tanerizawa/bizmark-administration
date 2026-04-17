<?php

namespace App\Services\Dashboard;

class DashboardDataService
{
    public function __construct(
        private DashboardAlertService $alerts,
        private DashboardFinancialService $financial,
        private DashboardOperationalService $operational,
    ) {}

    public function build(): array
    {
        return [
            'criticalAlerts' => $this->alerts->getCriticalAlerts(),
            'cashFlowStatus' => $this->financial->getCashFlowStatus(),
            'pendingApprovals' => $this->alerts->getPendingActions(),
            'cashFlowSummary' => $this->financial->getFinancialSummary(),
            'receivablesAging' => $this->financial->getReceivablesAging(),
            'budgetStatus' => $this->financial->getBudgetStatus(),
            'thisWeek' => $this->operational->getWeeklyTimeline(),
            'projectStatusDistribution' => $this->operational->getProjectStatusDistribution(),
            'recentActivities' => $this->operational->getRecentActivities(),
            'ragMetrics' => $this->operational->getRagMetrics(),
        ];
    }
}
