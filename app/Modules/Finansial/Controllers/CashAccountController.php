<?php

namespace App\Modules\Finansial\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankReconciliation;
use App\Models\CashAccount;
use App\Models\ProjectExpense;
use App\Modules\Finansial\Services\CashAccountService;
use App\Modules\Finansial\Services\CashFlowService;
use App\Modules\Finansial\Services\MutationService;
use App\Modules\Finansial\Services\PeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * CashAccountController — Thin orchestrator.
 *
 * Business logic has been decomposed into 4 services:
 * - PeriodService:        Date range resolution, available periods
 * - CashFlowService:      Financial analytics, PSAK 2 cash flow
 * - MutationService:      Transaction queries, running balance, unassigned counts
 * - CashAccountService:   CRUD operations, account listing
 *
 * BUG-01, BUG-08 fixes are preserved in their respective services.
 *
 * @see PeriodService
 * @see CashFlowService
 * @see MutationService
 * @see CashAccountService
 */
class CashAccountController extends Controller
{
    public function __construct(
        private CashAccountService $cashAccount,
        private CashFlowService $cashFlow,
        private MutationService $mutation,
        private PeriodService $period,
    ) {}

    /**
     * Display cash accounts index with financial summary, cash flow, and transactions.
     */
    public function index(Request $request)
    {
        try {
            $dateRange = $this->period->resolveDateRange($request);
            extract($dateRange); // $startDate, $endDate, $filterType, $selectedMonth, $selectedYear

            // Auto-detect default month when no filter is provided
            if (! $request->has('month') && ! $request->has('year') && ! $request->has('start_date')) {
                $default = $this->period->getDefaultMonthYear();
                $selectedMonth = $default['month'];
                $selectedYear = $default['year'];
                // Re-resolve with detected month/year
                $clonedRequest = $request->merge(['month' => $selectedMonth, 'year' => $selectedYear]);
                $dateRange = $this->period->resolveDateRange($clonedRequest);
                extract($dateRange);
            }

            $availablePeriods = $this->period->getAvailablePeriods();
            $accounts = $this->cashAccount->getAllOrdered();
            $financialSummary = $this->cashFlow->getFinancialSummary($startDate, $endDate);
            $cashFlowStatement = $this->cashFlow->getCashFlowStatement($startDate, $endDate);
            $recentTransactions = $this->mutation->getRecentTransactions(50, $startDate, $endDate);
            $generalTransactions = $this->mutation->getGeneralTransactions($startDate, $endDate);

            // Reconciliation data (kept in controller as it's a separate concern)
            $reconciliations = BankReconciliation::with(['cashAccount'])
                ->latest()
                ->paginate(20, ['*'], 'reconciliations_page')
                ->withQueryString();
            $cashAccountsList = CashAccount::where('is_active', true)->get();
            $pendingReconciliations = BankReconciliation::where('status', 'pending')->count();

            // Expense categories for dropdown
            $expenseCategories = ProjectExpense::categoriesByGroup();

            return view('cash-accounts.index', compact(
                'accounts',
                'financialSummary',
                'cashFlowStatement',
                'recentTransactions',
                'generalTransactions',
                'availablePeriods',
                'selectedMonth',
                'selectedYear',
                'filterType',
                'startDate',
                'endDate',
                'reconciliations',
                'cashAccountsList',
                'pendingReconciliations',
                'expenseCategories'
            ));
        } catch (\Exception $e) {
            Log::error('CashAccountController@index error: '.$e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Error loading cash accounts page');
        }
    }

    /**
     * Show the form for creating a new cash account.
     */
    public function create()
    {
        return view('cash-accounts.create');
    }

    /**
     * Store a newly created cash account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255', Rule::unique('cash_accounts', 'account_name')],
            'account_type' => 'required|in:bank,cash,receivable,payable',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $this->cashAccount->store($validated);

        return redirect()->route('cash-accounts.index')
            ->with('success', 'Akun kas berhasil ditambahkan');
    }

    /**
     * Display the specified cash account with mutations.
     */
    public function show(CashAccount $cashAccount, Request $request)
    {
        $dateRange = $this->period->resolveDateRange($request);
        extract($dateRange); // $startDate, $endDate, $filterType, $selectedMonth, $selectedYear

        $transactionType = $request->input('transaction_type', 'all');

        // Get account mutations (transaction history)
        $mutations = $this->mutation->getAccountMutations($cashAccount, $startDate, $endDate, $transactionType);

        // Count unassigned transactions for UI warning banner
        $unassigned = $this->mutation->getUnassignedCounts($startDate, $endDate, $transactionType);
        extract($unassigned); // $unassignedInvoicePayments, $unassignedInvoiceTotal, $unassignedExpenses, $unassignedExpenseTotal

        // Calculate summary statistics
        $totalIncome = $mutations->where('type', 'income')->sum('amount');
        $totalExpense = $mutations->where('type', 'expense')->sum('amount');
        $netChange = $totalIncome - $totalExpense;

        return view('cash-accounts.show', compact(
            'cashAccount',
            'mutations',
            'totalIncome',
            'totalExpense',
            'netChange',
            'startDate',
            'endDate',
            'filterType',
            'selectedMonth',
            'selectedYear',
            'transactionType',
            'unassignedInvoicePayments',
            'unassignedInvoiceTotal',
            'unassignedExpenses',
            'unassignedExpenseTotal'
        ));
    }

    /**
     * Show the form for editing the specified cash account.
     */
    public function edit(CashAccount $cashAccount)
    {
        return view('cash-accounts.edit', compact('cashAccount'));
    }

    /**
     * Update the specified cash account in storage.
     */
    public function update(Request $request, CashAccount $cashAccount)
    {
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255', Rule::unique('cash_accounts', 'account_name')->ignore($cashAccount->id)],
            'account_type' => 'required|in:bank,cash,receivable,payable',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $this->cashAccount->update($cashAccount, $validated);

        return redirect()->route('cash-accounts.index')
            ->with('success', 'Akun kas berhasil diperbarui');
    }

    /**
     * Remove the specified cash account from storage.
     */
    public function destroy(CashAccount $cashAccount)
    {
        $result = $this->cashAccount->delete($cashAccount);

        return redirect()->route('cash-accounts.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Get active cash accounts for API (used in payment forms).
     */
    public function getActiveCashAccounts()
    {
        return $this->cashAccount->getActiveAccounts();
    }
}
