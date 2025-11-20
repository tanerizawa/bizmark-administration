<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use App\Models\ProjectPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    /**
     * Financial dashboard overview
     */
    public function index()
    {
        $data = [
            'cashBalance' => $this->getCashBalance(),
            'runway' => $this->getRunway(),
            'thisMonth' => $this->getMonthlyStats(),
            'pendingReceivables' => $this->getPendingReceivables(),
            'recentTransactions' => $this->getRecentTransactions(10)
        ];
        
        return view('mobile.financial.index', $data);
    }
    
    /**
     * Quick input form for income/expense
     */
    public function quickInput(Request $request)
    {
        // Get active projects for dropdown
        $projects = \App\Models\Project::select('id', 'name')
            ->where('status_id', '!=', 5) // Exclude completed projects
            ->orderBy('name')
            ->get();
        
        return view('mobile.financial.quick-input', compact('projects'));
    }
    
    /**
     * Store transaction from quick input
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable|string|max:500',
            'receipt' => 'nullable|image|max:5120' // 5MB max
        ]);
        
        try {
            DB::beginTransaction();
            
            if ($request->type === 'income') {
                // Store as ProjectPayment
                $payment = \App\Models\ProjectPayment::create([
                    'project_id' => $request->project_id,
                    'amount' => $request->amount,
                    'payment_date' => $request->transaction_date,
                    'description' => $request->description ?? $this->getCategoryLabel($request->category),
                    'payment_type' => 'other', // or map from category
                    'payment_method' => 'bank_transfer',
                    'created_by' => auth()->id(),
                ]);
                
                // Update cash account
                if ($cashAccount = CashAccount::first()) {
                    $cashAccount->increment('current_balance', $request->amount);
                }
                
                $transaction = $payment;
            } else {
                // Store as ProjectExpense
                $expense = ProjectExpense::create([
                    'project_id' => $request->project_id,
                    'amount' => $request->amount,
                    'expense_date' => $request->transaction_date,
                    'description' => $request->description ?? $this->getCategoryLabel($request->category),
                    'recorded_by' => auth()->id(),
                    'status' => 'approved', // Auto-approve mobile entries
                ]);
                
                // Update cash account
                if ($cashAccount = CashAccount::first()) {
                    $cashAccount->decrement('current_balance', $request->amount);
                }
                
                $transaction = $expense;
            }
            
            // Handle receipt upload
            if ($request->hasFile('receipt')) {
                $path = $request->file('receipt')->store('receipts', 'public');
                $transaction->update(['receipt_path' => $path]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $request->type === 'income' 
                    ? 'Pemasukan berhasil dicatat!' 
                    : 'Pengeluaran berhasil dicatat!',
                'transaction' => $transaction
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cash flow details
     */
    public function cashFlow(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year
        
        $startDate = match($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
        
        // Income (Payments + Invoices paid)
        $income = (float) ProjectPayment::where('payment_date', '>=', $startDate)
            ->sum('amount');
        
        $invoicePaid = (float) Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->sum('total_amount');
        
        // Expenses
        $expenses = (float) ProjectExpense::where('expense_date', '>=', $startDate)
            ->where('is_reconciled', true)
            ->sum('amount');
        
        // Daily breakdown
        $dailyFlow = DB::table(DB::raw('
            (SELECT payment_date as date, amount as income, 0 as expense
             FROM project_payments
             WHERE payment_date >= :start1
             UNION ALL
             SELECT expense_date as date, 0 as income, amount as expense
             FROM project_expenses
             WHERE expense_date >= :start2 AND status = \'approved\')
        '))
        ->setBindings(['start1' => $startDate, 'start2' => $startDate])
        ->select(DB::raw('
            date,
            SUM(income) as daily_income,
            SUM(expense) as daily_expense,
            SUM(income - expense) as daily_net
        '))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();
        
        return view('mobile.financial.cash-flow', [
            'period' => $period,
            'totalIncome' => $income + $invoicePaid,
            'totalExpenses' => $expenses,
            'netCashFlow' => ($income + $invoicePaid) - $expenses,
            'dailyFlow' => $dailyFlow
        ]);
    }
    
    /**
     * Receivables aging
     */
    public function receivables()
    {
        $receivables = Invoice::where('status', '!=', 'paid')
            ->with(['project', 'client'])
            ->orderBy('issue_date', 'asc')
            ->get()
            ->map(function($invoice) {
                $daysOverdue = now()->diffInDays(Carbon::parse($invoice->due_date), false);
                
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client' => $invoice->client->name ?? '-',
                    'amount' => $invoice->total_amount,
                    'due_date' => $invoice->due_date,
                    'days_overdue' => $daysOverdue < 0 ? abs($daysOverdue) : 0,
                    'aging_bucket' => $this->getAgingBucket($daysOverdue),
                    'status' => $invoice->status
                ];
            });
        
        $summary = [
            'current' => (float) $receivables->where('aging_bucket', 'current')->sum('amount'),
            '1-30' => (float) $receivables->where('aging_bucket', '1-30')->sum('amount'),
            '31-60' => (float) $receivables->where('aging_bucket', '31-60')->sum('amount'),
            '60+' => (float) $receivables->where('aging_bucket', '60+')->sum('amount'),
        ];
        
        return view('mobile.financial.receivables', [
            'receivables' => $receivables,
            'summary' => $summary
        ]);
    }
    
    /**
     * Expenses list
     */
    public function expenses(Request $request)
    {
        $status = $request->get('status', 'all'); // all, pending, approved, rejected
        
        $query = ProjectExpense::with(['project', 'category', 'approver'])
            ->orderBy('expense_date', 'desc');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $expenses = $query->paginate(20);
        
        $stats = [
            'all' => ProjectExpense::count(),
            'receivable' => ProjectExpense::where('is_receivable', true)
                ->where('receivable_status', '!=', 'paid')->count(),
            'billable' => ProjectExpense::where('is_billable', true)->count(),
            'thisMonth' => (float) ProjectExpense::whereMonth('expense_date', now()->month)->sum('amount'),
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'expenses' => $expenses->items(),
                'hasMore' => $expenses->hasMorePages()
            ]);
        }
        
        return view('mobile.financial.expenses', [
            'expenses' => $expenses,
            'currentStatus' => $status,
            'stats' => $stats
        ]);
    }
    
    /**
     * Invoice detail
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['project', 'client', 'items']);
        
        return view('mobile.financial.invoice', compact('invoice'));
    }
    
    /**
     * Quick expense entry (from bottom sheet)
     */
    public function quickExpense(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:expense_categories,id'
        ]);
        
        $expense = ProjectExpense::create([
            'project_id' => $request->project_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'expense_date' => $request->expense_date,
            'category_id' => $request->category_id,
            'recorded_by' => auth()->id(),
            'status' => 'pending'
        ]);
        
        return response()->json([
            'success' => true,
            'expense' => $expense,
            'message' => 'Expense berhasil dicatat'
        ], 201);
    }
    
    /**
     * Get total cash balance
     */
    private function getCashBalance()
    {
        return [
            'total' => (float) CashAccount::sum('current_balance'),
            'accounts' => CashAccount::select('id', 'account_name as name', 'current_balance as balance')
                ->orderBy('current_balance', 'desc')
                ->get()
        ];
    }
    
    /**
     * Calculate cash runway
     */
    private function getRunway()
    {
        $totalCash = (float) CashAccount::sum('current_balance');
        
        // Monthly burn = total expenses in last 30 days (only reconciled ones)
        $monthlyBurn = (float) ProjectExpense::where('expense_date', '>=', now()->subDays(30))
            ->where('is_reconciled', true)
            ->sum('amount');
        
        $runway = $monthlyBurn > 0 ? round($totalCash / $monthlyBurn, 1) : 999;
        
        return [
            'months' => $runway > 99 ? '∞' : $runway,
            'cash' => $totalCash,
            'monthly_burn' => $monthlyBurn,
            'status' => $runway < 3 ? 'critical' : ($runway < 6 ? 'warning' : 'healthy')
        ];
    }
    
    /**
     * Monthly stats (current month)
     */
    private function getMonthlyStats()
    {
        $startOfMonth = now()->startOfMonth();
        
        $income = (float) ProjectPayment::where('payment_date', '>=', $startOfMonth)->sum('amount');
        $expenses = (float) ProjectExpense::where('expense_date', '>=', $startOfMonth)
            ->where('is_reconciled', true)
            ->sum('amount');
        
        $lastMonthIncome = (float) ProjectPayment::whereBetween('payment_date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->sum('amount');
        
        $lastMonthExpenses = (float) ProjectExpense::whereBetween('expense_date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->where('is_reconciled', true)->sum('amount');
        
        return [
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
            'income_change' => $lastMonthIncome > 0 ? 
                round((($income - $lastMonthIncome) / $lastMonthIncome) * 100, 1) : 0,
            'expense_change' => $lastMonthExpenses > 0 ?
                round((($expenses - $lastMonthExpenses) / $lastMonthExpenses) * 100, 1) : 0,
        ];
    }
    
    /**
     * Pending receivables
     */
    private function getPendingReceivables()
    {
        return Invoice::where('status', '!=', 'paid')
            ->select('id', 'invoice_number', 'total_amount', 'due_date')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get()
            ->map(function($invoice) {
                $daysOverdue = now()->diffInDays(Carbon::parse($invoice->due_date), false);
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => (float) $invoice->total_amount,
                    'due_date' => $invoice->due_date,
                    'is_overdue' => $daysOverdue < 0,
                    'days' => abs($daysOverdue)
                ];
            });
    }
    
    /**
     * Recent transactions
     */
    private function getRecentTransactions($limit = 10)
    {
        $payments = ProjectPayment::with('project')
            ->select('id', 'project_id', 'amount', 'payment_date', 'description')
            ->orderBy('payment_date', 'desc')
            ->take($limit)
            ->get()
            ->map(function($p) {
                return [
                    'type' => 'income',
                    'date' => $p->payment_date,
                    'description' => $p->description ?? ($p->project ? $p->project->name : 'Payment'),
                    'amount' => (float) $p->amount,
                    'project_name' => $p->project ? $p->project->name : null,
                    'icon' => 'arrow-down',
                    'color' => 'green'
                ];
            });
        
        $expenses = ProjectExpense::with('project')
            ->where('is_reconciled', true)
            ->select('id', 'project_id', 'amount', 'expense_date', 'description')
            ->orderBy('expense_date', 'desc')
            ->take($limit)
            ->get()
            ->map(function($e) {
                return [
                    'type' => 'expense',
                    'date' => $e->expense_date,
                    'description' => $e->description ?? ($e->project ? $e->project->name : 'Expense'),
                    'amount' => (float) $e->amount,
                    'project_name' => $e->project ? $e->project->name : null,
                    'icon' => 'arrow-up',
                    'color' => 'red'
                ];
            });
        
        return $payments->merge($expenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values();
    }
    
    /**
     * Get aging bucket for receivable
     */
    private function getAgingBucket($daysOverdue)
    {
        if ($daysOverdue >= 0) return 'current';
        
        $days = abs($daysOverdue);
        if ($days <= 30) return '1-30';
        if ($days <= 60) return '31-60';
        return '60+';
    }
    
    /**
     * Get category label from code
     */
    private function getCategoryLabel($code)
    {
        $labels = [
            // Income
            'client_payment' => 'Pembayaran Klien',
            'down_payment' => 'DP Proyek',
            'final_payment' => 'Pelunasan',
            'other_income' => 'Pemasukan Lainnya',
            // Expense
            'operational' => 'Operasional',
            'salary' => 'Gaji Karyawan',
            'tax_payment' => 'Pembayaran Pajak',
            'permit_fee' => 'Biaya Perizinan',
            'transport' => 'Transportasi',
            'other_expense' => 'Pengeluaran Lainnya',
        ];
        
        return $labels[$code] ?? $code;
    }
}

