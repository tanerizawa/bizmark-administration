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
        \Log::info("=== RECONCILIATION STORE STARTED ===", [
            'user_id' => auth()->id(),
            'request_data' => $request->except(['bank_statement', '_token']),
            'has_file' => $request->hasFile('bank_statement'),
        ]);

        $validated = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'opening_balance_bank' => 'required|numeric|min:0',
            'closing_balance_bank' => 'required|numeric|min:0',
            'bank_statement' => 'required|file|mimetypes:text/plain,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/csv,text/comma-separated-values|max:5120',
        ]);

        \Log::info("Validation passed", ['validated' => $validated]);

        try {
            DB::beginTransaction();

            $cashAccount = CashAccount::findOrFail($request->cash_account_id);

            // Get opening balance from books (system balance at start date)
            // Use day before start_date to get accurate opening balance
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            
            $openingBalanceBook = $this->getSystemBalance(
                $cashAccount->id, 
                $startDate->copy()->subDay()->toDateString()
            );
            
            $closingBalanceBook = $this->getSystemBalance(
                $cashAccount->id, 
                $endDate->toDateString()
            );

            \Log::info("Reconciliation balances calculated", [
                'account_id' => $cashAccount->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'opening_balance_book' => $openingBalanceBook,
                'closing_balance_book' => $closingBalanceBook,
                'opening_balance_bank' => $request->opening_balance_bank,
                'closing_balance_bank' => $request->closing_balance_bank,
            ]);

            // Store bank statement file
            $file = $request->file('bank_statement');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bank-statements', $filename, 'public');

            \Log::info("File uploaded", ['path' => $path, 'filename' => $filename]);

            // Prepare data array for create
            $reconciliationData = [
                'cash_account_id' => $request->cash_account_id,
                'reconciliation_date' => now()->toDateString(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                
                // Balances (required)
                'opening_balance_book' => $openingBalanceBook ?? 0,
                'opening_balance_bank' => $request->opening_balance_bank,
                'closing_balance_book' => $closingBalanceBook ?? 0,
                'closing_balance_bank' => $request->closing_balance_bank,
                
                // Adjustments (with defaults, but set explicitly)
                'total_deposits_in_transit' => 0,
                'total_outstanding_checks' => 0,
                'total_bank_charges' => 0,
                'total_bank_credits' => 0,
                
                // Results (required)
                'adjusted_bank_balance' => $request->closing_balance_bank,
                'adjusted_book_balance' => $closingBalanceBook ?? 0,
                'difference' => ($closingBalanceBook ?? 0) - $request->closing_balance_bank,
                
                // Status & metadata
                'status' => 'in_progress',
                'bank_statement_file' => $path,
                'bank_statement_format' => $this->detectBankFormat($file),
                'reconciled_by' => auth()->id(),
            ];

            \Log::info("About to create reconciliation with data:", [
                'field_count' => count($reconciliationData),
                'has_opening_balance_book' => isset($reconciliationData['opening_balance_book']),
                'has_closing_balance_book' => isset($reconciliationData['closing_balance_book']),
                'opening_balance_book_value' => $reconciliationData['opening_balance_book'] ?? 'NOT SET',
                'closing_balance_book_value' => $reconciliationData['closing_balance_book'] ?? 'NOT SET',
            ]);

            // Create reconciliation with all required fields
            $reconciliation = BankReconciliation::create($reconciliationData);

            \Log::info("Reconciliation #{$reconciliation->id} created successfully");

            // Import bank statement with error handling
            try {
                $importedCount = $this->importBankStatement($reconciliation, $file);
                \Log::info("Reconciliation #{$reconciliation->id}: Imported {$importedCount} entries");
                
                if ($importedCount === 0) {
                    DB::rollBack();
                    Storage::disk('public')->delete($path);
                    return back()
                        ->withInput()
                        ->with('error', 'Tidak ada transaksi valid yang dapat diimport dari file. Periksa format CSV Anda.');
                }
            } catch (\Exception $importError) {
                DB::rollBack();
                Storage::disk('public')->delete($path);
                \Log::error("Import failed for reconciliation #{$reconciliation->id}: " . $importError->getMessage());
                \Log::error($importError->getTraceAsString());
                return back()
                    ->withInput()
                    ->with('error', 'Gagal mengimport file: ' . $importError->getMessage());
            }

            DB::commit();

            return redirect()
                ->route('reconciliations.match', $reconciliation)
                ->with('success', 'Rekonsiliasi berhasil dimulai. ' . $importedCount . ' transaksi berhasil diimport.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Validation failed in reconciliation store", [
                'errors' => $e->errors(),
                'request_data' => $request->except(['bank_statement', '_token']),
            ]);
            throw $e; // Re-throw to show validation errors to user
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Reconciliation creation failed: " . $e->getMessage(), [
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            \Log::error($e->getTraceAsString());
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
    /**
     * Get system balance at specific date.
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
     * Detect bank CSV format.
     */
    private function detectBankFormat($file)
    {
        try {
            $handle = fopen($file->getRealPath(), 'r');
            $firstLine = fgets($handle);
            fclose($handle);
            
            // BCA format has "Account No." in first line
            if (stripos($firstLine, 'Account No') !== false) {
                return 'BCA';
            }
            
            // Check for other bank formats
            if (stripos($firstLine, 'Mandiri') !== false) {
                return 'Mandiri';
            }
            
            if (stripos($firstLine, 'BNI') !== false) {
                return 'BNI';
            }
            
            if (stripos($firstLine, 'BRI') !== false) {
                return 'BRI';
            }
            
            // Default to extension-based format
            return strtoupper($file->getClientOriginalExtension());
        } catch (\Exception $e) {
            return 'Unknown';
        }
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
     * 
     * Supports multiple bank formats:
     * 1. BTN Format: Date, Description, Debit, Credit, Balance, Reference
     * 2. BCA Format: Date, Description, Branch, Amount, DB/CR, Balance
     */
    private function importCSV(BankReconciliation $reconciliation, $file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        // Read first line to detect format
        $firstLine = fgets($handle);
        rewind($handle);
        
        // Detect BCA format (has "Account No." header)
        $isBCAFormat = stripos($firstLine, 'Account No') !== false;
        
        if ($isBCAFormat) {
            return $this->importBCAFormat($reconciliation, $handle);
        } else {
            return $this->importStandardFormat($reconciliation, $handle);
        }
    }
    
    /**
     * Import BCA CSV format.
     * 
     * Format BCA:
     * Header: Account No.,=,'1091806504
     *         Name,=,ODANG RODIANA
     *         Currency,=,IDR
     *         (blank line)
     *         Date,Description,Branch,Amount,,Balance
     * Data:   '07/09,KARTU DEBIT IDM...,'0998,511400.00,DB,897447.23
     * Footer: Starting Balance,=,1408847.23
     *         Credit,=,3800000.00
     *         Debet,=,5030400.00
     *         Ending Balance,=,178447.23
     */
    private function importBCAFormat(BankReconciliation $reconciliation, $handle)
    {
        $entries = [];
        $rowNumber = 0;
        $inDataSection = false;
        $currentYear = date('Y'); // Use current year for DD/MM dates
        
        \Log::info("Detected BCA CSV format for reconciliation #{$reconciliation->id}");
        
        while (($line = fgets($handle)) !== false) {
            $rowNumber++;
            
            // Parse CSV line manually to handle BCA format properly
            $row = str_getcsv($line);
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Detect header section (skip)
            if (stripos($row[0], 'Account No') !== false || 
                stripos($row[0], 'Name') !== false || 
                stripos($row[0], 'Currency') !== false) {
                \Log::info("Row {$rowNumber}: Header row - {$row[0]}");
                continue;
            }
            
            // Detect data header (Date,Description,Branch,Amount,,Balance)
            if (stripos($row[0], 'Date') !== false && 
                stripos($line, 'Description') !== false && 
                stripos($line, 'Balance') !== false) {
                $inDataSection = true;
                \Log::info("Row {$rowNumber}: Data section header detected");
                continue;
            }
            
            // Detect footer section (stop processing)
            if (stripos($row[0], 'Starting Balance') !== false || 
                stripos($row[0], 'Credit') !== false || 
                stripos($row[0], 'Debet') !== false || 
                stripos($row[0], 'Ending Balance') !== false) {
                \Log::info("Row {$rowNumber}: Footer detected - {$row[0]}");
                break; // Stop processing at footer
            }
            
            // Process data rows
            if ($inDataSection && count($row) >= 6) {
                try {
                    // BCA Format columns:
                    // [0] = Date ('07/09)
                    // [1] = Description
                    // [2] = Branch ('0998)
                    // [3] = Amount (511400.00)
                    // [4] = DB/CR indicator
                    // [5] = Balance (897447.23)
                    
                    $dateStr = trim($row[0]);
                    $description = trim($row[1]);
                    $branch = trim($row[2]);
                    $amount = $this->parseAmount($row[3]);
                    $dbCrIndicator = strtoupper(trim($row[4]));
                    $runningBalance = $this->parseAmount($row[5]);
                    
                    // Parse date - BCA uses 'DD/MM format
                    // Remove leading quote if present
                    $dateStr = ltrim($dateStr, "'");
                    
                    // Parse DD/MM and add current year
                    if (preg_match('/^(\d{1,2})\/(\d{1,2})$/', $dateStr, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                        
                        // If month > current month, assume previous year
                        $transactionDate = "{$currentYear}-{$month}-{$day}";
                        $transMonth = (int)$month;
                        $currentMonth = (int)date('m');
                        
                        if ($transMonth > $currentMonth) {
                            $transactionDate = ($currentYear - 1) . "-{$month}-{$day}";
                        }
                    } else {
                        $transactionDate = $this->parseDate($dateStr);
                    }
                    
                    // Skip if amount is zero
                    if ($amount == 0) {
                        \Log::info("Skipping row {$rowNumber}: Zero amount");
                        continue;
                    }
                    
                    // Determine debit or credit based on indicator
                    $debitAmount = 0;
                    $creditAmount = 0;
                    
                    if ($dbCrIndicator === 'DB' || $dbCrIndicator === 'DEBIT') {
                        $debitAmount = $amount;
                    } elseif ($dbCrIndicator === 'CR' || $dbCrIndicator === 'CREDIT') {
                        $creditAmount = $amount;
                    } else {
                        // Default: if no indicator, check description
                        if (stripos($description, 'TRANSFER') !== false || 
                            stripos($description, 'TRSF') !== false ||
                            stripos($description, 'BI-FAST CR') !== false ||
                            stripos($description, 'SWITCHING CR') !== false) {
                            $creditAmount = $amount;
                        } else {
                            $debitAmount = $amount;
                        }
                    }
                    
                    // Add branch to description for reference
                    $fullDescription = $description;
                    if (!empty($branch) && $branch !== "'") {
                        $branchClean = ltrim($branch, "'");
                        if (!empty($branchClean)) {
                            $fullDescription .= " [Branch: {$branchClean}]";
                        }
                    }
                    
                    $entries[] = [
                        'reconciliation_id' => $reconciliation->id,
                        'transaction_date' => $transactionDate,
                        'description' => substr($fullDescription, 0, 500),
                        'debit_amount' => $debitAmount,
                        'credit_amount' => $creditAmount,
                        'running_balance' => $runningBalance,
                        'reference_number' => null, // BCA doesn't provide reference in this format
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    \Log::info("BCA Row {$rowNumber} parsed: Date={$transactionDate}, Debit={$debitAmount}, Credit={$creditAmount}, Balance={$runningBalance}");
                    
                } catch (\Exception $e) {
                    \Log::error("Error parsing BCA row {$rowNumber}: " . $e->getMessage());
                    \Log::error("Row data: " . json_encode($row));
                    continue;
                }
            }
        }
        fclose($handle);

        if (!empty($entries)) {
            $chunks = array_chunk($entries, 100);
            foreach ($chunks as $chunk) {
                BankStatementEntry::insert($chunk);
            }
            \Log::info("Successfully imported " . count($entries) . " BCA entries for reconciliation #{$reconciliation->id}");
        } else {
            \Log::warning("No valid BCA entries found in CSV file for reconciliation #{$reconciliation->id}");
        }

        return count($entries);
    }
    
    /**
     * Import standard BTN/Generic CSV format.
     */
    private function importStandardFormat(BankReconciliation $reconciliation, $handle)
    {
        // Read and skip header row
        $header = fgetcsv($handle);
        
        $entries = [];
        $rowNumber = 1;
        
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            try {
                // Assuming BTN format: Date, Description, Debit, Credit, Balance, Reference
                if (count($row) >= 5) {
                    $transactionDate = $this->parseDate($row[0] ?? '');
                    $description = trim($row[1] ?? 'Unknown Transaction');
                    $debitAmount = $this->parseAmount($row[2] ?? '0');
                    $creditAmount = $this->parseAmount($row[3] ?? '0');
                    $runningBalance = $this->parseAmount($row[4] ?? '0');
                    $referenceNumber = isset($row[5]) ? trim($row[5]) : null;
                    
                    // Skip if all amounts are zero
                    if ($debitAmount == 0 && $creditAmount == 0 && $runningBalance == 0) {
                        \Log::info("Skipping row {$rowNumber}: All amounts are zero");
                        continue;
                    }
                    
                    // Validate amounts
                    if ($debitAmount < 0 || $creditAmount < 0) {
                        \Log::warning("Row {$rowNumber}: Negative amount detected. Taking absolute values.");
                        $debitAmount = abs($debitAmount);
                        $creditAmount = abs($creditAmount);
                    }
                    
                    $entries[] = [
                        'reconciliation_id' => $reconciliation->id,
                        'transaction_date' => $transactionDate,
                        'description' => substr($description, 0, 500),
                        'debit_amount' => $debitAmount,
                        'credit_amount' => $creditAmount,
                        'running_balance' => $runningBalance,
                        'reference_number' => $referenceNumber ? substr($referenceNumber, 0, 100) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    \Log::info("Row {$rowNumber} parsed: Date={$transactionDate}, Debit={$debitAmount}, Credit={$creditAmount}");
                }
            } catch (\Exception $e) {
                \Log::error("Error parsing row {$rowNumber}: " . $e->getMessage());
                continue;
            }
        }
        fclose($handle);

        if (!empty($entries)) {
            $chunks = array_chunk($entries, 100);
            foreach ($chunks as $chunk) {
                BankStatementEntry::insert($chunk);
            }
            \Log::info("Successfully imported " . count($entries) . " standard entries for reconciliation #{$reconciliation->id}");
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
     * Parse amount from string (handle Indonesian format: 1.234.567,89).
     */
    private function parseAmount($amountString)
    {
        // Remove all whitespace
        $cleaned = trim($amountString);
        
        // Handle empty or invalid input
        if (empty($cleaned) || $cleaned === '-' || $cleaned === '0') {
            return 0;
        }
        
        // Check for scientific notation BEFORE any other processing
        if (stripos($cleaned, 'E') !== false || stripos($cleaned, 'e') !== false) {
            $value = floatval($cleaned);
            // Scientific notation values are usually too large - return 0
            \Log::warning("Scientific notation detected: {$amountString} = {$value}. Setting to 0 to prevent overflow.");
            return 0;
        }
        
        // Remove currency symbols and other non-numeric characters except dots, commas, and minus
        $cleaned = preg_replace('/[^0-9,.-]/', '', $cleaned);
        
        // Detect format: Indonesian (1.234.567,89) vs International (1,234,567.89)
        // Count dots and commas to determine format
        $dotCount = substr_count($cleaned, '.');
        $commaCount = substr_count($cleaned, ',');
        
        if ($dotCount > 0 && $commaCount > 0) {
            // Both present - determine which is decimal separator
            $lastDotPos = strrpos($cleaned, '.');
            $lastCommaPos = strrpos($cleaned, ',');
            
            if ($lastCommaPos > $lastDotPos) {
                // Indonesian format: 1.234.567,89
                $cleaned = str_replace('.', '', $cleaned); // Remove thousand separator
                $cleaned = str_replace(',', '.', $cleaned); // Convert decimal separator
            } else {
                // International format: 1,234,567.89
                $cleaned = str_replace(',', '', $cleaned); // Remove thousand separator
            }
        } elseif ($commaCount > 0) {
            // Only comma present
            if ($commaCount > 1) {
                // Multiple commas = thousand separators (1,234,567)
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                // Single comma - check position to determine if decimal or thousand
                $parts = explode(',', $cleaned);
                if (isset($parts[1]) && strlen($parts[1]) <= 2) {
                    // Likely decimal: 1234,89
                    $cleaned = str_replace(',', '.', $cleaned);
                } else {
                    // Likely thousand: 1,234
                    $cleaned = str_replace(',', '', $cleaned);
                }
            }
        } elseif ($dotCount > 1) {
            // Multiple dots = Indonesian thousand separator (1.234.567)
            $cleaned = str_replace('.', '', $cleaned);
        }
        // Single dot with no comma = decimal point (123.45) - leave as is
        
        // Convert to float
        $value = floatval($cleaned);
        
        // Validate range for PostgreSQL NUMERIC(15,2)
        // Maximum absolute value: 9,999,999,999,999.99 (10^13 - 0.01)
        $maxValue = 9999999999999.99;
        
        if (abs($value) > $maxValue) {
            // Log warning and return 0 to prevent database error
            \Log::warning("Amount value too large: {$amountString} (parsed as {$value}). Setting to 0.");
            return 0;
        }
        
        return $value;
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
