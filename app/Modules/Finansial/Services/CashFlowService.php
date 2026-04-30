<?php

namespace App\Modules\Finansial\Services;

use App\Models\CashAccount;
use App\Models\Invoice;
use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * CashFlowService — Financial analytics and PSAK 2 cash flow statement.
 *
 * Migrated from CashAccountController::getFinancialSummary()
 * and CashAccountController::getCashFlowStatement().
 *
 * All existing BUG-01 fix (unassigned transaction exclusion) is preserved.
 */
class CashFlowService
{
    /**
     * Get Financial Summary for Dashboard Cards.
     *
     * Migrated from CashAccountController::getFinancialSummary().
     * Calculates: liquid assets, receivables, cash inflow/outflow, net cash flow, trends.
     *
     * @return array{liquid_assets: float, total_receivables: float, invoice_receivables: float,
     *               kasbon_receivables: float, cash_inflow_this_month: float,
     *               cash_outflow_this_month: float, net_cash_flow: float,
     *               cash_flow_trend: float, is_positive_trend: bool}
     */
    public function getFinancialSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Use provided dates or default to current month
        if (! $startDate || ! $endDate) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $thisMonth = $startDate->copy();
        $lastMonth = $startDate->copy()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();

        // Total Liquid Assets (Bank + Cash)
        $liquidAssets = CashAccount::whereIn('account_type', ['bank', 'cash'])
            ->where('is_active', true)
            ->sum('current_balance');

        // Outstanding Receivables (Unpaid Invoices + Kasbon)
        $invoiceReceivables = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) {
                $query->where('remaining_amount', '>', 0)
                    ->orWhereNull('remaining_amount');
            })
            ->sum('remaining_amount');

        $invoiceReceivablesNull = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereNull('remaining_amount')
            ->sum('total_amount');

        $invoiceReceivables += $invoiceReceivablesNull;

        $kasbonReceivables = ProjectExpense::where('is_receivable', true)
            ->where(function ($query) {
                $query->where('receivable_status', '!=', 'paid')
                    ->orWhereNull('receivable_status');
            })
            ->sum('amount');

        $kasbonPaid = ProjectExpense::where('is_receivable', true)
            ->sum('receivable_paid_amount');
        $kasbonReceivables -= $kasbonPaid;

        $totalReceivables = $invoiceReceivables + $kasbonReceivables;

        // This Period Cash Inflow
        $invoiceInflowThisMonth = DB::table('payment_schedules')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->sum('amount');

        $directInflowThisMonth = ProjectPayment::whereNull('invoice_id')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $cashInflowThisMonth = $invoiceInflowThisMonth + $directInflowThisMonth;

        // This Period Cash Outflow
        $cashOutflowThisMonth = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_receivable', false)
            ->sum('amount');

        // Net Cash Flow This Period
        $netCashFlow = $cashInflowThisMonth - $cashOutflowThisMonth;

        // Last Period for comparison
        $invoiceInflowLastMonth = DB::table('payment_schedules')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->whereBetween('paid_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $directInflowLastMonth = ProjectPayment::whereNull('invoice_id')
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $cashInflowLastMonth = $invoiceInflowLastMonth + $directInflowLastMonth;

        $cashOutflowLastMonth = ProjectExpense::whereBetween('expense_date', [$lastMonthStart, $lastMonthEnd])
            ->where('is_receivable', false)
            ->sum('amount');

        $netCashFlowLastMonth = $cashInflowLastMonth - $cashOutflowLastMonth;

        // Calculate trend
        $cashFlowTrend = $netCashFlowLastMonth > 0
            ? round((($netCashFlow - $netCashFlowLastMonth) / $netCashFlowLastMonth) * 100, 1)
            : ($netCashFlow > 0 ? 100 : 0);

        return [
            'liquid_assets' => $liquidAssets,
            'total_receivables' => $totalReceivables,
            'invoice_receivables' => $invoiceReceivables,
            'kasbon_receivables' => $kasbonReceivables,
            'cash_inflow_this_month' => $cashInflowThisMonth,
            'cash_outflow_this_month' => $cashOutflowThisMonth,
            'net_cash_flow' => $netCashFlow,
            'cash_flow_trend' => $cashFlowTrend,
            'is_positive_trend' => $cashFlowTrend >= 0,
        ];
    }

    /**
     * Get Cash Flow Statement (PSAK 2 Compliant — Direct Method).
     *
     * Migrated from CashAccountController::getCashFlowStatement().
     * Provides operating activities and financing activities breakdown.
     *
     * @return array{period_start: string, period_end: string, operating_receipts: float,
     *               operating_payments: float, net_operating: float, kasbon_given: float,
     *               kasbon_received: float, net_financing: float, net_change: float,
     *               cash_beginning: float, cash_ending: float}
     */
    public function getCashFlowStatement(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Use provided dates or find last month with data
        if (! $startDate || ! $endDate) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            $hasCurrentMonthData = ProjectPayment::whereBetween('payment_date', [$startDate, $endDate])->exists()
                || ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])->exists();

            if (! $hasCurrentMonthData) {
                $latestPayment = ProjectPayment::orderBy('payment_date', 'desc')->first();
                $latestExpense = ProjectExpense::orderBy('expense_date', 'desc')->first();

                $latestDate = null;
                if ($latestPayment && $latestExpense) {
                    $latestDate = Carbon::parse($latestPayment->payment_date)->gt(Carbon::parse($latestExpense->expense_date))
                        ? Carbon::parse($latestPayment->payment_date)
                        : Carbon::parse($latestExpense->expense_date);
                } elseif ($latestPayment) {
                    $latestDate = Carbon::parse($latestPayment->payment_date);
                } elseif ($latestExpense) {
                    $latestDate = Carbon::parse($latestExpense->expense_date);
                }

                if ($latestDate) {
                    $startDate = $latestDate->copy()->startOfMonth();
                    $endDate = $latestDate->copy()->endOfMonth();
                }
            }
        }

        // AKTIVITAS OPERASI
        $invoiceReceipts = DB::table('payment_schedules')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->sum('amount');

        $directReceipts = ProjectPayment::whereNull('invoice_id')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $operatingReceipts = $invoiceReceipts + $directReceipts;

        $operatingPayments = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_receivable', false)
            ->sum('amount');

        $netOperatingCashFlow = $operatingReceipts - $operatingPayments;

        // AKTIVITAS PENDANAAN
        $kasbonGiven = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_receivable', true)
            ->sum('amount');

        $kasbonReceived = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_receivable', true)
            ->where('receivable_status', 'paid')
            ->sum('receivable_paid_amount');

        $netFinancingCashFlow = $kasbonReceived - $kasbonGiven;

        // NET CHANGE IN CASH
        $netChangeInCash = $netOperatingCashFlow + $netFinancingCashFlow;

        // Cash at beginning
        $cashBeginning = CashAccount::whereIn('account_type', ['bank', 'cash'])
            ->where('is_active', true)
            ->sum('initial_balance');

        $cashEnding = $cashBeginning + $netChangeInCash;

        return [
            'period_start' => $startDate->format('d M Y'),
            'period_end' => $endDate->format('d M Y'),
            'operating_receipts' => $operatingReceipts,
            'operating_payments' => $operatingPayments,
            'net_operating' => $netOperatingCashFlow,
            'kasbon_given' => $kasbonGiven,
            'kasbon_received' => $kasbonReceived,
            'net_financing' => $netFinancingCashFlow,
            'net_change' => $netChangeInCash,
            'cash_beginning' => $cashBeginning,
            'cash_ending' => $cashEnding,
        ];
    }
}
