<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CashAccount;
use App\Models\BankReconciliation;
use App\Models\BankStatementEntry;
use App\Http\Controllers\BankReconciliationController;
use Illuminate\Http\UploadedFile;

echo "🔧 Testing BCA CSV Import\n";
echo str_repeat('=', 60) . "\n\n";

// Test file path
$file = '/home/bizmark/bizmark.id/storage/app/test_bca_statement.csv';
if (!file_exists($file)) {
    echo "❌ File not found: {$file}\n";
    exit(1);
}
echo "✅ CSV File found: " . basename($file) . "\n\n";

// Find or create BCA cash account
$cashAccount = CashAccount::where('account_name', 'like', '%BCA%')->first();
if (!$cashAccount) {
    echo "Creating test BCA account...\n";
    $cashAccount = CashAccount::create([
        'account_name' => 'BCA - 1091806504 (ODANG RODIANA)',
        'account_number' => '1091806504',
        'bank_name' => 'BCA',
        'account_type' => 'bank',
        'initial_balance' => 1408847.23,
        'current_balance' => 178447.23,
        'is_active' => true,
        'created_by' => 1
    ]);
}
echo "📊 Cash Account: {$cashAccount->account_name} (ID: {$cashAccount->id})\n";
echo "   Account Number: {$cashAccount->account_number}\n";
echo "   Initial Balance: Rp " . number_format($cashAccount->initial_balance, 2) . "\n\n";

// Create test reconciliation
echo "Creating reconciliation record...\n";
$reconciliation = BankReconciliation::create([
    'cash_account_id' => $cashAccount->id,
    'reconciliation_date' => '2025-09-29',
    'start_date' => '2025-09-07',
    'end_date' => '2025-09-29',
    // Required balance fields
    'opening_balance_book' => $cashAccount->initial_balance,
    'opening_balance_bank' => 1408847.23,
    'closing_balance_book' => $cashAccount->current_balance,
    'closing_balance_bank' => 178447.23,
    'adjusted_bank_balance' => 178447.23,
    'adjusted_book_balance' => $cashAccount->current_balance,
    'difference' => 0,
    'status' => 'in_progress',
    'bank_statement_format' => 'BCA'
]);
echo "✅ Reconciliation ID: {$reconciliation->id}\n";
echo "   Period: {$reconciliation->start_date} to {$reconciliation->end_date}\n";
echo "   Format: BCA\n\n";

// Test import using reflection to access private method
echo "Importing CSV file...\n";
$controller = new BankReconciliationController();
$reflector = new ReflectionClass($controller);
$method = $reflector->getMethod('importCSV');
$method->setAccessible(true);

$uploadedFile = new UploadedFile($file, 'test.csv', 'text/csv', null, true);

try {
    $count = $method->invoke($controller, $reconciliation, $uploadedFile);
    echo "✅ Import completed: {$count} entries imported\n\n";
} catch (\Exception $e) {
    echo "❌ Import failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// Analyze imported data
echo str_repeat('=', 60) . "\n";
echo "📊 IMPORT ANALYSIS\n";
echo str_repeat('=', 60) . "\n\n";

$entries = BankStatementEntry::where('reconciliation_id', $reconciliation->id)
    ->orderBy('transaction_date')
    ->get();

$totalDebit = $entries->sum('debit_amount');
$totalCredit = $entries->sum('credit_amount');
$netChange = $totalCredit - $totalDebit;

echo "Total Transactions: " . $entries->count() . "\n";
echo "Total Debit:  Rp " . number_format($totalDebit, 2) . "\n";
echo "Total Credit: Rp " . number_format($totalCredit, 2) . "\n";
echo "Net Change:   Rp " . number_format($netChange, 2) . "\n\n";

echo "Expected (from CSV footer):\n";
echo "Total Debit:  Rp 5,030,400.00\n";
echo "Total Credit: Rp 3,800,000.00\n";
echo "Net Change:   Rp -1,230,400.00\n\n";

// Validation
$debitMatch = abs($totalDebit - 5030400) < 1;
$creditMatch = abs($totalCredit - 3800000) < 1;

if ($debitMatch && $creditMatch) {
    echo "✅ VALIDATION PASSED: Amounts match CSV footer!\n\n";
} else {
    echo "⚠️  VALIDATION WARNING: Amounts don't match exactly\n";
    echo "   Debit difference:  Rp " . number_format($totalDebit - 5030400, 2) . "\n";
    echo "   Credit difference: Rp " . number_format($totalCredit - 3800000, 2) . "\n\n";
}

// Show sample transactions
echo str_repeat('-', 60) . "\n";
echo "SAMPLE TRANSACTIONS (First 5)\n";
echo str_repeat('-', 60) . "\n\n";

foreach ($entries->take(5) as $idx => $entry) {
    $type = $entry->credit_amount > 0 ? 'income' : 'expense';
    echo ($idx + 1) . ". Date: {$entry->transaction_date}\n";
    echo "   Description: " . substr($entry->description, 0, 60) . "...\n";
    echo "   Type: {$type}\n";
    echo "   Debit:  Rp " . number_format($entry->debit_amount, 2) . "\n";
    echo "   Credit: Rp " . number_format($entry->credit_amount, 2) . "\n";
    echo "   Balance: Rp " . number_format($entry->running_balance, 2) . "\n\n";
}

// Date analysis
echo str_repeat('-', 60) . "\n";
echo "DATE RANGE ANALYSIS\n";
echo str_repeat('-', 60) . "\n\n";

$minDate = $entries->min('transaction_date');
$maxDate = $entries->max('transaction_date');
echo "First Transaction: {$minDate}\n";
echo "Last Transaction:  {$maxDate}\n";
echo "Expected Range:    2025-09-07 to 2025-09-29\n\n";

$dateMatch = (strpos($minDate, '2025-09') !== false && strpos($maxDate, '2025-09') !== false);
if ($dateMatch) {
    echo "✅ Date range matches expected period\n\n";
} else {
    echo "⚠️  Date range outside expected period\n\n";
}

// Type distribution
echo str_repeat('-', 60) . "\n";
echo "TRANSACTION TYPE DISTRIBUTION\n";
echo str_repeat('-', 60) . "\n\n";

$income = $entries->filter(function($entry) { return $entry->credit_amount > 0; });
$expense = $entries->filter(function($entry) { return $entry->debit_amount > 0; });

echo "Income transactions:  " . $income->count() . " (Rp " . number_format($income->sum('credit_amount'), 2) . ")\n";
echo "Expense transactions: " . $expense->count() . " (Rp " . number_format($expense->sum('debit_amount'), 2) . ")\n\n";

echo str_repeat('=', 60) . "\n";
echo "✅ BCA CSV Import Test Completed\n";
echo str_repeat('=', 60) . "\n";
