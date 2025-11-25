<?php
/**
 * Quick Test Script for General Transactions Feature
 * Run: php test_general_transactions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\CashAccount;

echo "=== Testing General Transactions Feature ===\n\n";

// Test 1: Check if project_id is nullable in project_payments
echo "Test 1: Verify project_id is nullable in project_payments\n";
try {
    $tableSchema = DB::select("
        SELECT column_name, is_nullable 
        FROM information_schema.columns 
        WHERE table_name = 'project_payments' 
        AND column_name = 'project_id'
    ");
    $isNullable = $tableSchema[0]->is_nullable ?? 'NO';
    echo "  ✓ project_id nullable: " . $isNullable . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check general income count
echo "\nTest 2: Count general income (project_id = NULL)\n";
try {
    $generalIncomeCount = ProjectPayment::whereNull('project_id')
                                       ->whereNull('invoice_id')
                                       ->count();
    echo "  ✓ General income records: " . $generalIncomeCount . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check general expenses count
echo "\nTest 3: Count general expenses (project_id = NULL)\n";
try {
    $generalExpensesCount = ProjectExpense::whereNull('project_id')->count();
    echo "  ✓ General expense records: " . $generalExpensesCount . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check expense categories
echo "\nTest 4: Load expense categories\n";
try {
    $categories = ProjectExpense::categoriesByGroup();
    $categoryCount = collect($categories)->flatten(1)->count();
    echo "  ✓ Expense categories loaded: " . $categoryCount . " categories\n";
    echo "  Groups: " . collect($categories)->keys()->implode(', ') . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check active cash accounts
echo "\nTest 5: Load active cash accounts\n";
try {
    $activeAccounts = CashAccount::where('is_active', true)->count();
    echo "  ✓ Active cash accounts: " . $activeAccounts . "\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Test 6: Verify routes exist
echo "\nTest 6: Verify routes are registered\n";
$routes = [
    'general-transactions.income.store',
    'general-transactions.income.show',
    'general-transactions.income.update',
    'general-transactions.income.destroy',
    'general-transactions.expense.store',
    'general-transactions.expense.show',
    'general-transactions.expense.update',
    'general-transactions.expense.destroy',
];

foreach ($routes as $routeName) {
    try {
        $url = route($routeName, ['id' => 1], false);
        echo "  ✓ Route exists: {$routeName}\n";
    } catch (\Exception $e) {
        echo "  ✗ Route missing: {$routeName}\n";
    }
}

echo "\n=== All Tests Completed ===\n";
