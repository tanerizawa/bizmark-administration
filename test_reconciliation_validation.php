<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST RECONCILIATION VALIDATION ===\n\n";

// Simulate request data (what should be sent from form)
$testData = [
    'cash_account_id' => 1, // BCA account
    'start_date' => '2025-10-01',
    'end_date' => '2025-10-31',
    'opening_balance_bank' => 178447.23,
    'closing_balance_bank' => 34299629.23,
];

echo "Test Data:\n";
print_r($testData);
echo "\n";

// Create validator
$validator = Illuminate\Support\Facades\Validator::make($testData, [
    'cash_account_id' => 'required|exists:cash_accounts,id',
    'start_date' => 'required|date',
    'end_date' => 'required|date|after_or_equal:start_date',
    'opening_balance_bank' => 'required|numeric|min:0',
    'closing_balance_bank' => 'required|numeric|min:0',
    'bank_statement' => 'required|file|mimes:csv,xlsx,xls|max:5120',
]);

if ($validator->fails()) {
    echo "❌ VALIDATION FAILED!\n\n";
    echo "Errors:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "✅ ALL VALIDATIONS PASSED (except file)\n";
}

echo "\n";

// Check if cash_account exists
$cashAccount = \App\Models\CashAccount::find(1);
if ($cashAccount) {
    echo "✅ Cash Account #1 exists: {$cashAccount->account_name}\n";
} else {
    echo "❌ Cash Account #1 NOT FOUND\n";
}
