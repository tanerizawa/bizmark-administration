<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashAccountController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get filter parameters
            $filterType = $request->input('filter_type', 'month'); // month, quarter, year, custom
            $selectedMonth = $request->input('month', Carbon::now()->month);
            $selectedYear = $request->input('year', Carbon::now()->year);
            $startDateInput = $request->input('start_date');
            $endDateInput = $request->input('end_date');
        
        // Create date range based on filter type
        switch ($filterType) {
            case 'quarter':
                $quarter = $request->input('quarter', ceil(Carbon::now()->month / 3));
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
                    // Fallback to current month
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
        
        // Get available periods from transactions
        $availablePeriods = $this->getAvailablePeriods();
        
        $accounts = CashAccount::orderBy('account_type')->orderBy('account_name')->get();
        
        // Get comprehensive financial data with date range
        $financialSummary = $this->getFinancialSummary($startDate, $endDate);
        $cashFlowStatement = $this->getCashFlowStatement($startDate, $endDate);
        $recentTransactions = $this->getRecentTransactions(50, $startDate, $endDate); // Increased from 15 to 50
        
            return view('cash-accounts.index', compact(
                'accounts',
                'financialSummary',
                'cashFlowStatement',
                'recentTransactions',
                'availablePeriods',
                'selectedMonth',
                'selectedYear',
                'filterType',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            \Log::error('CashAccountController@index error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error loading cash accounts page');
        }
    }
    
    /**
     * Get available periods from transactions
     */
    private function getAvailablePeriods()
    {
        $periods = [];
        
        // Get invoice payment dates from payment_schedules
        $invoicePaymentDates = DB::table('payment_schedules')
            ->selectRaw('EXTRACT(YEAR FROM paid_date) as year, EXTRACT(MONTH FROM paid_date) as month')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Get legacy manual payment dates
        $manualPaymentDates = ProjectPayment::selectRaw('EXTRACT(YEAR FROM payment_date) as year, EXTRACT(MONTH FROM payment_date) as month')
            ->whereNull('invoice_id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Get expense dates
        $expenseDates = ProjectExpense::selectRaw('EXTRACT(YEAR FROM expense_date) as year, EXTRACT(MONTH FROM expense_date) as month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Merge and deduplicate
        $allDates = $invoicePaymentDates->concat($manualPaymentDates)->concat($expenseDates);
        foreach ($allDates as $date) {
            $key = $date->year . '-' . str_pad($date->month, 2, '0', STR_PAD_LEFT);
            if (!isset($periods[$key])) {
                $periods[$key] = [
                    'year' => $date->year,
                    'month' => $date->month,
                    'label' => Carbon::create($date->year, $date->month, 1)->isoFormat('MMMM Y')
                ];
            }
        }
        
        // Sort by date desc
        krsort($periods);
        
        return array_values($periods);
    }

    /**
     * Get Financial Summary for Dashboard Cards
     */
    private function getFinancialSummary($startDate = null, $endDate = null)
    {
        // Use provided dates or default to current month
        if (!$startDate || !$endDate) {
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
        // Use remaining_amount for accurate calculation
        $invoiceReceivables = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where(function($query) {
                $query->where('remaining_amount', '>', 0)
                      ->orWhereNull('remaining_amount'); // If NULL, means not paid yet
            })
            ->sum('remaining_amount');
        
        // If remaining_amount is NULL, use total_amount
        $invoiceReceivablesNull = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereNull('remaining_amount')
            ->sum('total_amount');
        
        $invoiceReceivables += $invoiceReceivablesNull;
        
        $kasbonReceivables = ProjectExpense::where('is_receivable', true)
            ->where(function($query) {
                $query->where('receivable_status', '!=', 'paid')
                      ->orWhereNull('receivable_status'); // NULL means unpaid
            })
            ->sum('amount');
        
        // Subtract already paid amount from kasbon
        $kasbonPaid = ProjectExpense::where('is_receivable', true)
            ->sum('receivable_paid_amount');
        $kasbonReceivables -= $kasbonPaid;
        
        $totalReceivables = $invoiceReceivables + $kasbonReceivables;
        
        // This Period Cash Inflow from multiple sources:
        // 1. Invoice payments (from payment_schedules with paid status)
        $invoiceInflowThisMonth = \DB::table('payment_schedules')
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->sum('amount');
        
        // 2. Direct project payments (legacy - not linked to invoice)
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
        $invoiceInflowLastMonth = \DB::table('payment_schedules')
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
     * Get Cash Flow Statement (PSAK 2 Compliant)
     */
    private function getCashFlowStatement($startDate = null, $endDate = null)
    {
        // Use provided dates or find last month with data
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            
            // Check if current month has any transactions
            $hasCurrentMonthData = ProjectPayment::whereBetween('payment_date', [$startDate, $endDate])->exists() 
                || ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])->exists();
            
            if (!$hasCurrentMonthData) {
                // Get the most recent transaction date and use that month
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
        // Cash Inflow: Invoice payments + Legacy manual payments
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
        
        // Cash at beginning (use initial_balance as baseline)
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

    /**
     * Get Recent Transactions Timeline
     */
    private function getRecentTransactions($limit = 15, $startDate = null, $endDate = null)
    {
        // Get recent invoice payments
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
        
        $invoicePayments = $invoicePaymentsQuery
            ->get()
            ->map(function($payment) {
                // Format description with client name if available
                $description = 'Pembayaran Invoice ' . ($payment->invoice_number ?? '');
                if ($payment->client_name) {
                    $description .= ' - ' . $payment->client_name . ' (' . $payment->project_name . ')';
                } else {
                    $description .= ' - ' . $payment->project_name;
                }
                
                return [
                    'type' => 'inflow',
                    'date' => $payment->date,
                    'description' => $description,
                    'amount' => $payment->amount,
                    'account_name' => $payment->payment_method ?? 'Unknown',
                    'project_id' => $payment->project_id,
                    'project_name' => $payment->project_name,
                    'client_name' => $payment->client_name,
                ];
            });
        
        // Get legacy manual payments (not linked to invoice)
        $directPaymentsQuery = ProjectPayment::with(['project.client'])
            ->whereNull('invoice_id')
            ->orderBy('payment_date', 'desc');
        
        if ($startDate && $endDate) {
            $directPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        
        $directPayments = $directPaymentsQuery
            ->get()
            ->map(function($payment) {
                $description = 'Pembayaran Manual';
                if ($payment->project) {
                    $clientName = $payment->project->client ? $payment->project->client->name : null;
                    if ($clientName) {
                        $description .= ' - ' . $clientName . ' (' . $payment->project->name . ')';
                    } else {
                        $description .= ' - ' . $payment->project->name;
                    }
                } else {
                    $description .= ' - Unknown Project';
                }
                
                return [
                    'type' => 'inflow',
                    'date' => $payment->payment_date,
                    'description' => $description,
                    'amount' => $payment->amount,
                    'account_name' => 'Manual Payment',
                    'project_id' => $payment->project_id,
                    'project_name' => $payment->project->name ?? null,
                    'client_name' => $payment->project && $payment->project->client ? $payment->project->client->name : null,
                ];
            });
        
        // Get recent expenses
        $expensesQuery = ProjectExpense::with(['project'])
            ->orderBy('expense_date', 'desc');
        
        if ($startDate && $endDate) {
            $expensesQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }
        
        $expenses = $expensesQuery
            ->get()
            ->map(function($expense) {
                return [
                    'type' => $expense->is_receivable ? 'kasbon' : 'outflow',
                    'date' => $expense->expense_date,
                    'description' => $expense->description ?? ($expense->vendor_name ? 'Pembayaran ke ' . $expense->vendor_name : ($expense->project->name ?? 'Unknown')),
                    'amount' => $expense->amount,
                    'account_name' => $expense->category ?? 'Unknown',
                    'project_id' => $expense->project_id,
                    'project_name' => $expense->project->name ?? null,
                    'notes' => $expense->category ?? null,
                ];
            });
        
        // Merge all transactions and sort by date
        $transactions = $invoicePayments
            ->concat($directPayments)
            ->concat($expenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
        
        return $transactions;
    }

    public function create()
    {
        return view('cash-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:bank,cash,receivable,payable',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['current_balance'] = $validated['initial_balance'];
        $validated['is_active'] = true;

        CashAccount::create($validated);

        return redirect()->route('cash-accounts.index')
            ->with('success', 'Akun kas berhasil ditambahkan');
    }

    public function show(CashAccount $cashAccount, Request $request)
    {
        // Get filter parameters
        $filterType = $request->input('filter_type', 'month');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $transactionType = $request->input('transaction_type', 'all'); // all, income, expense
        
        // Create date range based on filter type
        switch ($filterType) {
            case 'quarter':
                $quarter = $request->input('quarter', ceil(Carbon::now()->month / 3));
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
        
        // Get account mutations (transaction history)
        $mutations = $this->getAccountMutations($cashAccount, $startDate, $endDate, $transactionType);
        
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
            'transactionType'
        ));
    }

    /**
     * Get comprehensive account mutations (all transactions)
     * 
     * NOTE: Invoice payments (payment_schedules) tidak bisa di-filter per cash account
     * karena tabel payment_schedules tidak memiliki kolom cash_account_id.
     * Semua invoice payments akan ditampilkan untuk semua akun.
     * 
     * TODO: Tambahkan kolom cash_account_id ke payment_schedules untuk filtering yang lebih akurat
     */
    private function getAccountMutations(CashAccount $cashAccount, $startDate, $endDate, $transactionType = 'all')
    {
        $mutations = collect();
        
        // WARNING: payment_schedules tidak memiliki cash_account_id FK
        // Semua invoice payments ditampilkan tanpa filter akun
        // Ini bisa menyebabkan transaksi dari akun lain muncul di sini
        
        // Get invoice payments from payment_schedules (status = paid)
        if ($transactionType === 'all' || $transactionType === 'income') {
            $invoicePayments = DB::table('payment_schedules')
                ->join('invoices', 'payment_schedules.invoice_id', '=', 'invoices.id')
                ->join('projects', 'invoices.project_id', '=', 'projects.id')
                ->where('payment_schedules.status', 'paid')
                ->whereNotNull('payment_schedules.paid_date')
                ->whereBetween('payment_schedules.paid_date', [$startDate, $endDate])
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
                ->map(function($item) {
                    return [
                        'date' => Carbon::parse($item->date),
                        'description' => 'Pembayaran Invoice ' . $item->invoice_number . ' - ' . $item->project_name,
                        'reference' => $item->reference_number,
                        'type' => 'income',
                        'transaction_type' => 'invoice_payment',
                        'amount' => $item->amount,
                        'payment_method' => $item->payment_method,
                    ];
                });
            
            $mutations = $mutations->concat($invoicePayments);
        }
        
        // Get manual payments (ProjectPayment without invoice_id)
        if ($transactionType === 'all' || $transactionType === 'income') {
            $manualPayments = ProjectPayment::with('project')
                ->whereNull('invoice_id')
                ->where('bank_account_id', $cashAccount->id)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->get()
                ->map(function($payment) {
                    return [
                        'date' => $payment->payment_date,
                        'description' => 'Pembayaran Manual - ' . ($payment->project->name ?? 'Unknown Project'),
                        'reference' => $payment->reference_number ?? '-',
                        'type' => 'income',
                        'transaction_type' => 'manual_payment',
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                    ];
                });
            
            $mutations = $mutations->concat($manualPayments);
        }
        
        // Get expenses
        // Strategy: Include expenses from this account OR unassigned expenses (bank_account_id = NULL)
        // Unassigned expenses will appear in ALL accounts (by design, to ensure they're visible)
        // This is intentional to prevent "lost" transactions
        if ($transactionType === 'all' || $transactionType === 'expense') {
            $expenses = ProjectExpense::with('project')
                ->where(function($query) use ($cashAccount) {
                    $query->where('bank_account_id', $cashAccount->id)
                          ->orWhereNull('bank_account_id');
                })
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->get()
                ->map(function($expense) {
                    $description = $expense->description ?? $expense->category_name;
                    if ($expense->vendor_name) {
                        $description .= ' - ' . $expense->vendor_name;
                    }
                    if ($expense->project) {
                        $description .= ' (' . $expense->project->name . ')';
                    }
                    
                    return [
                        'date' => $expense->expense_date,
                        'description' => $description,
                        'reference' => $expense->receipt_file ?? '-',
                        'type' => 'expense',
                        'transaction_type' => $expense->is_receivable ? 'kasbon' : 'expense',
                        'amount' => $expense->amount,
                        'payment_method' => $expense->payment_method,
                        'category' => $expense->category_name,
                    ];
                });
            
            $mutations = $mutations->concat($expenses);
        }
        
        // Sort by date descending
        $sorted = $mutations->sortByDesc('date')->values();
        
        // Calculate running balance
        $runningBalance = $cashAccount->current_balance;
        $sortedAsc = $mutations->sortBy('date')->values();
        
        // Start from opening balance before first transaction
        foreach ($sortedAsc as $index => $mutation) {
            // Calculate balance at this point (work backwards from current)
            $futureTransactions = $sortedAsc->slice($index + 1);
            $futureIncome = $futureTransactions->where('type', 'income')->sum('amount');
            $futureExpense = $futureTransactions->where('type', 'expense')->sum('amount');
            
            $mutation['balance'] = $cashAccount->current_balance - $futureIncome + $futureExpense;
        }
        
        // Resort to descending for display
        return $sorted->map(function($mutation) use ($sortedAsc) {
            $match = $sortedAsc->first(function($item) use ($mutation) {
                return $item['date'] == $mutation['date'] 
                    && $item['description'] == $mutation['description']
                    && $item['amount'] == $mutation['amount'];
            });
            
            if ($match && isset($match['balance'])) {
                $mutation['balance'] = $match['balance'];
            }
            
            return $mutation;
        });
    }

    public function edit(CashAccount $cashAccount)
    {
        return view('cash-accounts.edit', compact('cashAccount'));
    }

    public function update(Request $request, CashAccount $cashAccount)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:bank,cash,receivable,payable',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $cashAccount->update($validated);

        return redirect()->route('cash-accounts.index')
            ->with('success', 'Akun kas berhasil diperbarui');
    }

    public function destroy(CashAccount $cashAccount)
    {
        // Prevent deletion if has transactions
        if ($cashAccount->payments()->count() > 0 || $cashAccount->expenses()->count() > 0) {
            return redirect()->route('cash-accounts.index')
                ->with('error', 'Akun kas tidak dapat dihapus karena masih memiliki transaksi');
        }

        $cashAccount->delete();

        return redirect()->route('cash-accounts.index')
            ->with('success', 'Akun kas berhasil dihapus');
    }
    
    /**
     * Get active cash accounts for API (used in payment forms)
     */
    public function getActiveCashAccounts()
    {
        $accounts = CashAccount::active()
            ->orderBy('account_type')
            ->orderBy('account_name')
            ->get(['id', 'account_name', 'account_type', 'bank_name', 'current_balance', 'is_active']);
        
        return response()->json($accounts);
    }
}
