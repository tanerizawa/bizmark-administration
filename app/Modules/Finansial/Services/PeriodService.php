<?php

namespace App\Modules\Finansial\Services;

use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * PeriodService — Centralizes all date/period logic.
 *
 * Eliminates the switch/case duplication that existed between
 * CashAccountController::index() and CashAccountController::show().
 */
class PeriodService
{
    /**
     * Resolve date range and filter parameters from request.
     *
     * Consolidates the ~50-line switch/case block that was duplicated
     * in both index() and show() methods.
     *
     * @return array{startDate: Carbon, endDate: Carbon, filterType: string, selectedMonth: int, selectedYear: int}
     */
    public function resolveDateRange(Request $request): array
    {
        $filterType = $request->input('filter_type', 'month');
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);
        $selectedYear = (int) $request->input('year', Carbon::now()->year);
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        switch ($filterType) {
            case 'quarter':
                $quarter = (int) $request->input('quarter', (int) ceil($selectedMonth / 3));
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate = Carbon::create($selectedYear, $startMonth, 1)->startOfMonth();
                $endDate = Carbon::create($selectedYear, $startMonth + 2, 1)->endOfMonth();
                break;

            case 'year':
                $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
                break;

            case 'custom':
                if ($startDateInput && $endDateInput) {
                    $startDate = Carbon::parse($startDateInput)->startOfDay();
                    $endDate = Carbon::parse($endDateInput)->endOfDay();
                } else {
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                }
                break;

            case 'month':
            default:
                $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
                break;
        }

        return compact('startDate', 'endDate', 'filterType', 'selectedMonth', 'selectedYear');
    }

    /**
     * Get available periods from all transaction sources.
     *
     * Migrated from CashAccountController::getAvailablePeriods().
     * Merges invoice payment dates, manual payment dates, and expense dates.
     */
    public function getAvailablePeriods(): array
    {
        $periods = [];

        // Invoice payment dates from payment_schedules
        $invoicePaymentDates = DB::table('payment_schedules')
            ->selectRaw('EXTRACT(YEAR FROM paid_date) as year, EXTRACT(MONTH FROM paid_date) as month')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Legacy manual payment dates
        $manualPaymentDates = ProjectPayment::selectRaw('EXTRACT(YEAR FROM payment_date) as year, EXTRACT(MONTH FROM payment_date) as month')
            ->whereNull('invoice_id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Expense dates
        $expenseDates = ProjectExpense::selectRaw('EXTRACT(YEAR FROM expense_date) as year, EXTRACT(MONTH FROM expense_date) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Merge and deduplicate
        $allDates = $invoicePaymentDates->concat($manualPaymentDates)->concat($expenseDates);
        foreach ($allDates as $date) {
            $key = $date->year.'-'.str_pad($date->month, 2, '0', STR_PAD_LEFT);
            if (! isset($periods[$key])) {
                $periods[$key] = [
                    'year' => $date->year,
                    'month' => $date->month,
                    'label' => Carbon::create($date->year, $date->month, 1)->isoFormat('MMMM Y'),
                ];
            }
        }

        krsort($periods);

        return array_values($periods);
    }

    /**
     * Get the most recent date that has transactions across all sources.
     *
     * Migrated from CashAccountController::getLatestTransactionDate().
     * Used for auto-detecting default period when no filter is set.
     */
    public function getLatestTransactionDate(): ?Carbon
    {
        $latestPayment = ProjectPayment::whereNull('invoice_id')
            ->orderBy('payment_date', 'desc')
            ->first();

        $latestInvoicePayment = DB::table('payment_schedules')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->orderBy('paid_date', 'desc')
            ->first();

        $latestExpense = ProjectExpense::orderBy('expense_date', 'desc')->first();

        $dates = collect();
        if ($latestPayment) {
            $dates->push(Carbon::parse($latestPayment->payment_date));
        }
        if ($latestInvoicePayment) {
            $dates->push(Carbon::parse($latestInvoicePayment->paid_date));
        }
        if ($latestExpense) {
            $dates->push(Carbon::parse($latestExpense->expense_date));
        }

        if ($dates->isEmpty()) {
            return null;
        }

        return $dates->max();
    }

    /**
     * Auto-detect the default month and year based on latest transaction data.
     * If no transactions exist, falls back to the current month.
     *
     * @return array{month: int, year: int}
     */
    public function getDefaultMonthYear(): array
    {
        $latestDate = $this->getLatestTransactionDate();
        if ($latestDate) {
            return [
                'month' => $latestDate->month,
                'year' => $latestDate->year,
            ];
        }

        return [
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ];
    }
}
