<?php

namespace App\Http\Controllers;

use App\Models\BankReconciliation;
use App\Models\BankStatementEntry;
use App\Models\CashAccount;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankReconciliationController extends Controller
{
    /**
     * Display a listing of reconciliations.
     */
    public function index(Request $request)
    {
        $query = BankReconciliation::with(['cashAccount', 'reconciledBy'])
            ->orderBy('reconciliation_date', 'desc');

        // Filter by cash account
        if ($request->filled('cash_account_id')) {
            $query->where('cash_account_id', $request->cash_account_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('reconciliation_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('reconciliation_date', '<=', $request->end_date);
        }

        $reconciliations = $query->paginate(20);
        $cashAccounts = CashAccount::active()->get();

        return view('reconciliations.index', compact('reconciliations', 'cashAccounts'));
    }

    /**
     * Show the form for creating a new reconciliation.
     */
    public function create()
    {
        $cashAccounts = CashAccount::active()->get();
        return view('reconciliations.create', compact('cashAccounts'));
    }

    /**
     * Store a newly created reconciliation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'opening_balance_bank' => 'required|numeric',
            'closing_balance_bank' => 'required|numeric',
            'bank_statement' => 'required|file|mimes:csv,xlsx,xls|max:5120', // 5MB max
        ]);

        try {
            DB::beginTransaction();

            $cashAccount = CashAccount::findOrFail($request->cash_account_id);

            // Get opening balance from books (system balance at start date)
            $openingBalanceBook = $this->getSystemBalance($cashAccount->id, $request->start_date);
            $closingBalanceBook = $this->getSystemBalance($cashAccount->id, $request->end_date);

            // Store bank statement file
            $file = $request->file('bank_statement');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bank-statements', $filename, 'public');

            // Create reconciliation
            $reconciliation = BankReconciliation::create([
                'cash_account_id' => $request->cash_account_id,
                'reconciliation_date' => now()->toDateString(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'opening_balance_book' => $openingBalanceBook,
                'opening_balance_bank' => $request->opening_balance_bank,
                'closing_balance_book' => $closingBalanceBook,
                'closing_balance_bank' => $request->closing_balance_bank,
                'adjusted_bank_balance' => $request->closing_balance_bank,
                'adjusted_book_balance' => $closingBalanceBook,
                'difference' => $closingBalanceBook - $request->closing_balance_bank,
                'status' => 'in_progress',
                'bank_statement_file' => $path,
                'bank_statement_format' => $file->getClientOriginalExtension(),
                'reconciled_by' => auth()->id(),
            ]);

            // Import bank statement
            $this->importBankStatement($reconciliation, $file);

            DB::commit();

            return redirect()
                ->route('reconciliations.match', $reconciliation)
                ->with('success', 'Rekonsiliasi berhasil dimulai. Silakan cocokkan transaksi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the matching interface.
     */
    public function match(BankReconciliation $reconciliation)
    {
        $reconciliation->load([
            'cashAccount',
            'bankStatementEntries' => function ($query) {
                $query->orderBy('transaction_date', 'asc');
            }
        ]);

        // Get unreconciled system transactions in the period
        $systemPayments = ProjectPayment::where('bank_account_id', $reconciliation->cash_account_id)
            ->whereBetween('payment_date', [$reconciliation->start_date, $reconciliation->end_date])
            ->where('is_reconciled', false)
            ->get();

        $systemExpenses = ProjectExpense::where('bank_account_id', $reconciliation->cash_account_id)
            ->whereBetween('expense_date', [$reconciliation->start_date, $reconciliation->end_date])
            ->where('is_reconciled', false)
            ->get();

        $systemInvoicePayments = PaymentSchedule::where('status', 'paid')
            ->whereBetween('paid_date', [$reconciliation->start_date, $reconciliation->end_date])
            ->where('is_reconciled', false)
            ->get();

        // Get matching stats
        $stats = $reconciliation->getMatchingStats();

        return view('reconciliations.match', compact(
            'reconciliation',
            'systemPayments',
            'systemExpenses',
            'systemInvoicePayments',
            'stats'
        ));
    }

    /**
     * Auto-match transactions.
     */
    public function autoMatch(BankReconciliation $reconciliation)
    {
        try {
            $matchedCount = 0;
            $bankEntries = $reconciliation->bankStatementEntries()->unmatched()->get();

            foreach ($bankEntries as $entry) {
                // Try to find exact match
                $match = $this->findExactMatch($entry, $reconciliation);

                if ($match) {
                    $entry->matchWith(
                        $match['type'],
                        $match['id'],
                        'exact',
                        'Auto-matched: Exact date and amount'
                    );
                    $matchedCount++;
                }
            }

            return back()->with('success', "Berhasil mencocokkan {$matchedCount} transaksi otomatis.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Manual match a bank entry with a system transaction.
     */
    public function manualMatch(Request $request, BankReconciliation $reconciliation)
    {
        $validated = $request->validate([
            'bank_entry_id' => 'required|exists:bank_statement_entries,id',
            'transaction_type' => 'required|in:payment,expense,invoice_payment',
            'transaction_id' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        try {
            $bankEntry = BankStatementEntry::findOrFail($request->bank_entry_id);
            
            $bankEntry->matchWith(
                $request->transaction_type,
                $request->transaction_id,
                'manual',
                $request->notes
            );

            return back()->with('success', 'Transaksi berhasil dicocokkan secara manual.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Unmatch a bank entry.
     */
    public function unmatch(Request $request, BankReconciliation $reconciliation)
    {
        $validated = $request->validate([
            'bank_entry_id' => 'required|exists:bank_statement_entries,id',
            'reason' => 'nullable|in:missing_in_system,bank_error,timing_difference,needs_investigation',
        ]);

        try {
            $bankEntry = BankStatementEntry::findOrFail($request->bank_entry_id);
            $bankEntry->unmatch($request->reason);

            return back()->with('success', 'Pencocokan transaksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Complete the reconciliation.
     */
    public function complete(Request $request, BankReconciliation $reconciliation)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            // Recalculate final balances
            $stats = $reconciliation->getMatchingStats();
            $unmatchedEntries = $reconciliation->bankStatementEntries()->unmatched()->get();

            // Calculate outstanding items
            $depositsInTransit = $unmatchedEntries->where('type', 'income')->sum('amount');
            $outstandingChecks = $unmatchedEntries->where('type', 'expense')->sum('amount');

            // Update reconciliation
            $reconciliation->update([
                'total_deposits_in_transit' => $depositsInTransit,
                'total_outstanding_checks' => $outstandingChecks,
                'adjusted_bank_balance' => $reconciliation->closing_balance_bank + $depositsInTransit - $outstandingChecks,
                'difference' => abs($reconciliation->closing_balance_book - ($reconciliation->closing_balance_bank + $depositsInTransit - $outstandingChecks)),
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            return redirect()
                ->route('reconciliations.show', $reconciliation)
                ->with('success', 'Rekonsiliasi berhasil diselesaikan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the reconciliation report.
     */
    public function show(BankReconciliation $reconciliation)
    {
        $reconciliation->load([
            'cashAccount',
            'bankStatementEntries',
            'reconciledBy',
            'reviewedBy',
            'approvedBy',
        ]);

        $stats = $reconciliation->getMatchingStats();
        
        $matchedEntries = $reconciliation->bankStatementEntries()->matched()->get();
        $unmatchedEntries = $reconciliation->bankStatementEntries()->unmatched()->get();

        return view('reconciliations.show', compact('reconciliation', 'stats', 'matchedEntries', 'unmatchedEntries'));
    }

    /**
     * Remove the specified reconciliation.
     */
    public function destroy(BankReconciliation $reconciliation)
    {
        try {
            // Only allow deletion if in_progress
            if ($reconciliation->status !== 'in_progress') {
                return back()->with('error', 'Hanya rekonsiliasi dengan status "in progress" yang bisa dihapus.');
            }

            // Delete bank statement file
            if ($reconciliation->bank_statement_file) {
                Storage::disk('public')->delete($reconciliation->bank_statement_file);
            }

            $reconciliation->delete();

            return redirect()
                ->route('reconciliations.index')
                ->with('success', 'Rekonsiliasi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ============= HELPER METHODS =============

    /**
     * Get system balance at a specific date.
     */
    private function getSystemBalance($accountId, $date)
    {
        $account = CashAccount::findOrFail($accountId);
        $initialBalance = $account->initial_balance;

        // Sum all payments before or on the date
        $totalIncome = ProjectPayment::where('bank_account_id', $accountId)
            ->whereDate('payment_date', '<=', $date)
            ->sum('amount');

        // Sum all expenses before or on the date
        $totalExpense = ProjectExpense::where('bank_account_id', $accountId)
            ->whereDate('expense_date', '<=', $date)
            ->sum('amount');

        return $initialBalance + $totalIncome - $totalExpense;
    }

    /**
     * Import bank statement entries.
     */
    private function importBankStatement(BankReconciliation $reconciliation, $file)
    {
        $extension = $file->getClientOriginalExtension();
        
        if ($extension === 'csv') {
            return $this->importCSV($reconciliation, $file);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return $this->importExcel($reconciliation, $file);
        }

        throw new \Exception('Format file tidak didukung.');
    }

    /**
     * Import CSV bank statement.
     */
    private function importCSV(BankReconciliation $reconciliation, $file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header row
        
        $entries = [];
        while (($row = fgetcsv($handle)) !== false) {
            // Assuming BTN format: Date, Description, Debit, Credit, Balance, Reference
            // Adjust column indexes based on actual bank format
            if (count($row) >= 5) {
                $entries[] = [
                    'reconciliation_id' => $reconciliation->id,
                    'transaction_date' => $this->parseDate($row[0] ?? ''),
                    'description' => $row[1] ?? '',
                    'debit_amount' => $this->parseAmount($row[2] ?? '0'),
                    'credit_amount' => $this->parseAmount($row[3] ?? '0'),
                    'running_balance' => $this->parseAmount($row[4] ?? '0'),
                    'reference_number' => $row[5] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        fclose($handle);

        if (!empty($entries)) {
            BankStatementEntry::insert($entries);
        }

        return count($entries);
    }

    /**
     * Import Excel bank statement (basic implementation).
     */
    private function importExcel(BankReconciliation $reconciliation, $file)
    {
        // For now, return message to use CSV
        // Full Excel implementation requires PhpSpreadsheet library
        throw new \Exception('Format Excel belum didukung. Silakan convert ke CSV terlebih dahulu.');
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($dateString)
    {
        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }

    /**
     * Parse amount from string (remove currency symbols, dots, commas).
     */
    private function parseAmount($amountString)
    {
        $cleaned = preg_replace('/[^0-9,.-]/', '', $amountString);
        $cleaned = str_replace(',', '.', $cleaned);
        return floatval($cleaned);
    }

    /**
     * Find exact match for a bank entry.
     */
    private function findExactMatch(BankStatementEntry $entry, BankReconciliation $reconciliation)
    {
        $amount = $entry->credit_amount > 0 ? $entry->credit_amount : $entry->debit_amount;
        $date = $entry->transaction_date;

        // Try to match with payments (income)
        if ($entry->credit_amount > 0) {
            $payment = ProjectPayment::where('bank_account_id', $reconciliation->cash_account_id)
                ->whereDate('payment_date', $date)
                ->where('amount', $amount)
                ->where('is_reconciled', false)
                ->first();

            if ($payment) {
                return ['type' => 'payment', 'id' => $payment->id];
            }

            // Try invoice payments
            $invoicePayment = PaymentSchedule::where('status', 'paid')
                ->whereDate('paid_date', $date)
                ->where('amount', $amount)
                ->where('is_reconciled', false)
                ->first();

            if ($invoicePayment) {
                return ['type' => 'invoice_payment', 'id' => $invoicePayment->id];
            }
        }

        // Try to match with expenses
        if ($entry->debit_amount > 0) {
            $expense = ProjectExpense::where('bank_account_id', $reconciliation->cash_account_id)
                ->whereDate('expense_date', $date)
                ->where('amount', $amount)
                ->where('is_reconciled', false)
                ->first();

            if ($expense) {
                return ['type' => 'expense', 'id' => $expense->id];
            }
        }

        return null;
    }
}
