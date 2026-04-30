<?php

namespace Tests\Unit\Services\Dashboard;

use App\Services\Dashboard\DashboardAlertService;
use App\Services\Dashboard\DashboardDataService;
use App\Services\Dashboard\DashboardFinancialService;
use App\Services\Dashboard\DashboardOperationalService;
use PHPUnit\Framework\TestCase;

/**
 * Kontrak shape output dashboard tanpa bootstrap Laravel (hindari guard DB di Tests\TestCase).
 */
final class DashboardDataServiceContractTest extends TestCase
{
    public function test_build_exposes_stable_keys_and_delegates_to_domain_services(): void
    {
        $alerts = $this->createMock(DashboardAlertService::class);
        $financial = $this->createMock(DashboardFinancialService::class);
        $operational = $this->createMock(DashboardOperationalService::class);

        $stubCritical = ['critical' => true];
        $stubPending = ['pending' => true];
        $stubCashFlow = ['cash_flow' => true];
        $stubSummary = ['summary' => true];
        $stubReceivables = ['receivables' => true];
        $stubBudget = ['budget' => true];
        $stubWeek = ['week' => true];
        $stubDistribution = ['distribution' => true];
        $stubActivities = ['activities' => true];
        $stubRag = ['rag' => true];

        $alerts->expects($this->once())->method('getCriticalAlerts')->willReturn($stubCritical);
        $alerts->expects($this->once())->method('getPendingActions')->willReturn($stubPending);

        $financial->expects($this->once())->method('getCashFlowStatus')->willReturn($stubCashFlow);
        $financial->expects($this->once())->method('getFinancialSummary')->willReturn($stubSummary);
        $financial->expects($this->once())->method('getReceivablesAging')->willReturn($stubReceivables);
        $financial->expects($this->once())->method('getBudgetStatus')->willReturn($stubBudget);

        $operational->expects($this->once())->method('getWeeklyTimeline')->willReturn($stubWeek);
        $operational->expects($this->once())->method('getProjectStatusDistribution')->willReturn($stubDistribution);
        $operational->expects($this->once())->method('getRecentActivities')->willReturn($stubActivities);
        $operational->expects($this->once())->method('getRagMetrics')->willReturn($stubRag);

        $sut = new DashboardDataService($alerts, $financial, $operational);
        $built = $sut->build();

        $expectedKeys = [
            'criticalAlerts',
            'cashFlowStatus',
            'pendingApprovals',
            'cashFlowSummary',
            'receivablesAging',
            'budgetStatus',
            'thisWeek',
            'projectStatusDistribution',
            'recentActivities',
            'ragMetrics',
        ];

        $this->assertSame($expectedKeys, array_keys($built));

        $this->assertSame($stubCritical, $built['criticalAlerts']);
        $this->assertSame($stubCashFlow, $built['cashFlowStatus']);
        $this->assertSame($stubPending, $built['pendingApprovals']);
        $this->assertSame($stubSummary, $built['cashFlowSummary']);
        $this->assertSame($stubReceivables, $built['receivablesAging']);
        $this->assertSame($stubBudget, $built['budgetStatus']);
        $this->assertSame($stubWeek, $built['thisWeek']);
        $this->assertSame($stubDistribution, $built['projectStatusDistribution']);
        $this->assertSame($stubActivities, $built['recentActivities']);
        $this->assertSame($stubRag, $built['ragMetrics']);
    }
}
