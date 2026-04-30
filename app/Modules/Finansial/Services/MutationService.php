<?php

namespace App\Modules\Finansial\Services;

use App\Models\CashAccount;
use App\Models\ProjectExpense;
use App\Models\ProjectPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * MutationService — All transaction/mutation queries.
 *
 * Migrated from CashAccountController:
 * - getAccountMutations()          → getAccountMutations()
 * - getRecentTransactions()        → getRecentTransactions()
 * - getGeneralTransactions()       → getGeneralTransactions()
 * - Unassigned tracking (BUG-01)   → getUnassignedCounts()
 *
 * All BUG-01 fixes are preserved:
 * - Only explicitly assigned transactions in getAccountMutations()
 * - Unassigned transactions counted separately in getUnassignedCounts()
 */
class MutationService
{
    /**
     * Get comprehensive account mutations (all transactions assigned to an account).
     *
     * ✅ FIXED (BUG-01): Only fetches transactions explicitly assigned to this account.
     * Removed orWhereNull(bank_account_id) that caused unassigned transactions
     * to appear in ALL accounts and corrupt running balance calculations.
     *
     * Migrated from CashAccountController::getAccountMutations().
     */
    public function getAccountMutations(CashAccount $cashAccount, Carbon $startDate, Carbon $endDate, string $transactionType = 'all'): Collection
    {
        $mutations = collect();

        // Invoice payments assigned to this account
        if ($transactionType === 'all' || $transactionType === 'income') {
            $invoicePayments = DB::table('payment_schedules')
                ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
                ->join('projects', 'invoices.project_id', '=', 'projects.id')
                ->where('payment_schedules.status', 'paid')
                ->whereNotNull('payment_schedules.paid_date')
                ->whereBetween('payment_schedules.paid_date', [$startDate, $endDate])
                ->where('payment_schedules.cash_account_id', $cashAccount->id)
                ->select(
                    'payment_schedules.paid_date as date',
                    'payment_schedules.amount',
                    'payment_schedules.payment_method',
                    'payment_schedules.reference_number',
                    'invoices.invoice_number',
                    'projects.name as project_name',
                    DB::raw("'income' as type"),
                    DB::raw("'invoice_payment' as transaction_type")
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => Carbon::parse($item->date),
                        'description' => 'Pembayaran Invoice '.$item->invoice_number.' - '.$item->project_name,
                        'reference' => $item->reference_number,
                        'type' => 'income',
                        'transaction_type' => 'invoice_payment',
                        'amount' => (float) $item->amount,
                        'payment_method' => $item->payment_method,
                    ];
                });

            $mutations = $mutations->concat($invoicePayments);
        }

        // Manual payments assigned to this account
        if ($transactionType === 'all' || $transactionType === 'income') {
            $manualPayments = ProjectPayment::with('project')
                ->whereNull('invoice_id')
                ->where('bank_account_id', $cashAccount->id)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->get()
                ->map(function ($payment) {
                    return [
                        'date' => $payment->payment_date,
                        'description' => 'Pembayaran Manual - '.($payment->project->name ?? 'Unknown Project'),
                        'reference' => $payment->reference_number ?? '-',
                        'type' => 'income',
                        'transaction_type' => 'manual_payment',
                        'amount' => (float) $payment->amount,
                        'payment_method' => $payment->payment_method,
                    ];
                });

            $mutations = $mutations->concat($manualPayments);
        }

        // Expenses assigned to this account only (BUG-01: removed orWhereNull)
        if ($transactionType === 'all' || $transactionType === 'expense') {
            $expenses = ProjectExpense::with('project')
                ->where('bank_account_id', $cashAccount->id)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->get()
                ->map(function ($expense) {
                    $description = $expense->description ?? $expense->category_name;
                    if ($expense->vendor_name) {
                        $description .= ' - '.$expense->vendor_name;
                    }
                    if ($expense->project) {
                        $description .= ' ('.$expense->project->name.')';
                    }

                    return [
                        'date' => $expense->expense_date,
                        'description' => $description,
                        'reference' => $expense->receipt_file ?? '-',
                        'type' => 'expense',
                        'transaction_type' => $expense->is_receivable ? 'kasbon' : 'expense',
                        'amount' => (float) $expense->amount,
                        'payment_method' => $expense->payment_method,
                        'category' => $expense->category_name,
                    ];
                });

            $mutations = $mutations->concat($expenses);
        }

        // Calculate running balance
        $sortedAsc = $mutations->sortBy('date')->values();

        foreach ($sortedAsc as $index => $mutation) {
            $futureTransactions = $sortedAsc->slice($index + 1);
            $futureIncome = (float) $futureTransactions->where('type', 'income')->sum('amount');
            $futureExpense = (float) $futureTransactions->where('type', 'expense')->sum('amount');

            $sortedAsc[$index]['balance'] = (float) $cashAccount->current_balance - $futureIncome + $futureExpense;
        }

        // Sort by date descending for display and attach balances
        $sortedDesc = $sortedAsc->sortByDesc('date')->values();

        return $sortedDesc->map(function ($mutation) use ($sortedAsc) {
            $match = $sortedAsc->first(function ($item) use ($mutation) {
                return $item['date'] == $mutation['date']
                    && $item['description'] == $mutation['description']
                    && (float) $item['amount'] === (float) $mutation['amount'];
            });

            if ($match && isset($match['balance'])) {
                $mutation['balance'] = $match['balance'];
            }

            return $mutation;
        });
    }

    /**
     * Get Recent Transactions Timeline.
     *
     * Merges invoice payments, direct payments, and expenses into a single
     * timeline sorted by date.
     *
     * Migrated from CashAccountController::getRecentTransactions().
     */
    public function getRecentTransactions(int $limit = 15, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        // Invoice payments
        $invoicePaymentsQuery = DB::table('payment_schedules')
            ->join('projects', 'payment_schedules.project_id', '=', 'projects.id')
            ->leftJoin('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
            ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
            ->select(
                'payment_schedules.paid_date as date',
                'payment_schedules.amount',
                'payment_schedules.payment_method',
                'projects.id as project_id',
                'projects.name as project_name',
                'clients.name as client_name',
                'invoices.invoice_number',
                DB::raw("'inflow' as type")
            )
            ->where('payment_schedules.status', 'paid')
            ->whereNotNull('payment_schedules.paid_date')
            ->orderBy('payment_schedules.paid_date', 'desc');

        if ($startDate && $endDate) {
            $invoicePaymentsQuery->whereBetween('payment_schedules.paid_date', [$startDate, $endDate]);
        }

        $invoicePayments = $invoicePaymentsQuery->get()->map(function ($payment) {
            $description = 'Pembayaran Invoice '.($payment->invoice_number ?? '');
            if ($payment->client_name) {
                $description .= ' - '.$payment->client_name.' ('.$payment->project_name.')';
            } else {
                $description .= ' - '.$payment->project_name;
            }

            return [
                'type' => 'inflow',
                'date' => $payment->date,
                'description' => $description,
                'amount' => (float) $payment->amount,
                'account_name' => $payment->payment_method ?? 'Unknown',
                'project_id' => $payment->project_id,
                'project_name' => $payment->project_name,
                'client_name' => $payment->client_name,
            ];
        });

        // Direct payments (legacy — not linked to invoice)
        $directPaymentsQuery = ProjectPayment::with(['project.client'])
            ->whereNull('invoice_id')
            ->orderBy('payment_date', 'desc');

        if ($startDate && $endDate) {
            $directPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $directPayments = $directPaymentsQuery->get()->map(function ($payment) {
            $description = 'Pembayaran Manual';
            if ($payment->project) {
                $clientName = $payment->project->client ? $payment->project->client->name : null;
                if ($clientName) {
                    $description .= ' - '.$clientName.' ('.$payment->project->name.')';
                } else {
                    $description .= ' - '.$payment->project->name;
                }
            } else {
                $description .= ' - Unknown Project';
            }

            return [
                'type' => 'inflow',
                'date' => $payment->payment_date,
                'description' => $description,
                'amount' => (float) $payment->amount,
                'account_name' => 'Manual Payment',
                'project_id' => $payment->project_id,
                'project_name' => $payment->project->name ?? null,
                'client_name' => $payment->project && $payment->project->client ? $payment->project->client->name : null,
            ];
        });

        // Expenses
        $expensesQuery = ProjectExpense::with(['project'])
            ->orderBy('expense_date', 'desc');

        if ($startDate && $endDate) {
            $expensesQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }

        $expenses = $expensesQuery->get()->map(function ($expense) {
            return [
                'type' => $expense->is_receivable ? 'kasbon' : 'outflow',
                'date' => $expense->expense_date,
                'description' => $expense->description ?? ($expense->vendor_name ? 'Pembayaran ke '.$expense->vendor_name : ($expense->project->name ?? 'Unknown')),
                'amount' => (float) $expense->amount,
                'account_name' => $expense->category ?? 'Unknown',
                'project_id' => $expense->project_id,
                'project_name' => $expense->project->name ?? null,
                'notes' => $expense->category ?? null,
            ];
        });

        // Merge all transactions and sort by date
        return $invoicePayments
            ->concat($directPayments)
            ->concat($expenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }

    /**
     * Get General (Non-Project) Transactions.
     *
     * These are company operational income/expenses not tied to specific projects.
     *
     * Migrated from CashAccountController::getGeneralTransactions().
     *
     * @return array{income: Collection, expenses: Collection}
     */
    public function getGeneralTransactions(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // General Income (project_id IS NULL)
        $generalIncomeQuery = ProjectPayment::with(['bankAccount', 'createdBy'])
            ->whereNull('project_id')
            ->whereNull('invoice_id')
            ->orderBy('payment_date', 'desc');

        if ($startDate && $endDate) {
            $generalIncomeQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $generalIncome = $generalIncomeQuery->get();

        // General Expenses (project_id IS NULL)
        $generalExpensesQuery = ProjectExpense::with(['bankAccount', 'createdBy'])
            ->whereNull('project_id')
            ->orderBy('expense_date', 'desc');

        if ($startDate && $endDate) {
            $generalExpensesQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }

        $generalExpenses = $generalExpensesQuery->get();

        return [
            'income' => $generalIncome,
            'expenses' => $generalExpenses,
        ];
    }

    /**
     * Count unassigned transactions for UI warning banner.
     *
     * Extracted from CashAccountController::show() (BUG-01 fix).
     * These transactions have NULL cash_account_id / bank_account_id
     * and don't affect any account's running balance.
     *
     * @return array{unassignedInvoicePayments: int, unassignedInvoiceTotal: float,
     *               unassignedExpenses: int, unassignedExpenseTotal: float}
     */
    public function getUnassignedCounts(Carbon $startDate, Carbon $endDate, string $transactionType = 'all'): array
    {
        $unassignedInvoicePayments = 0;
        $unassignedInvoiceTotal = 0.0;

        if ($transactionType === 'all' || $transactionType === 'income') {
            $unassigned = DB::table('payment_schedules')
                ->where('status', 'paid')
                ->whereNotNull('paid_date')
                ->whereBetween('paid_date', [$startDate, $endDate])
                ->whereNull('cash_account_id')
                ->select(DB::raw('COUNT(*) as cnt'), DB::raw('COALESCE(SUM(amount), 0) as total'))
                ->first();
            $unassignedInvoicePayments = (int) ($unassigned->cnt ?? 0);
            $unassignedInvoiceTotal = (float) ($unassigned->total ?? 0);
        }

        $unassignedExpenses = 0;
        $unassignedExpenseTotal = 0.0;

        if ($transactionType === 'all' || $transactionType === 'expense') {
            $unassigned = ProjectExpense::whereNull('bank_account_id')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->select(DB::raw('COUNT(*) as cnt'), DB::raw('COALESCE(SUM(amount), 0) as total'))
                ->first();
            $unassignedExpenses = (int) ($unassigned->cnt ?? 0);
            $unassignedExpenseTotal = (float) ($unassigned->total ?? 0);
        }

        return compact(
            'unassignedInvoicePayments',
            'unassignedInvoiceTotal',
            'unassignedExpenses',
            'unassignedExpenseTotal'
        );
    }
}
