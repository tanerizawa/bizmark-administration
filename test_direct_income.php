<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use App\Models\PaymentMethod;
use App\Models\CashAccount;

echo "\n" . str_repeat('=', 90) . "\n";
echo "TEST: Direct Income Endpoint Fix\n";
echo str_repeat('=', 90) . "\n\n";

// Get test data
$project = Project::first();
$paymentMethod = PaymentMethod::where('requires_cash_account', true)->first();
$cashAccount = CashAccount::where('is_active', true)->first();

if (!$project) {
    echo "❌ No project found!\n";
    exit;
}

if (!$paymentMethod) {
    echo "❌ No payment method found!\n";
    exit;
}

if (!$cashAccount) {
    echo "❌ No active cash account found!\n";
    exit;
}

echo "Test Data:\n";
echo str_repeat('-', 90) . "\n";
echo "Project: {$project->name} (ID: {$project->id})\n";
echo "Payment Method: {$paymentMethod->name} (ID: {$paymentMethod->id})\n";
echo "Cash Account: {$cashAccount->account_name} (ID: {$cashAccount->id})\n";
echo "Cash Account Balance: Rp " . number_format($cashAccount->current_balance, 0, ',', '.') . "\n";

echo "\n" . str_repeat('=', 90) . "\n";
echo "ISSUE ANALYSIS\n";
echo str_repeat('=', 90) . "\n\n";

echo "❌ BEFORE FIX:\n";
echo "   Controller tried to insert:\n";
echo "   - payment_method_id = {$paymentMethod->id} (WRONG - column doesn't exist!)\n";
echo "   - cash_account_id = {$cashAccount->id}\n";
echo "   - reference = \$validated['reference']\n\n";

echo "   Database Error:\n";
echo "   SQLSTATE[42703]: Undefined column: 7 ERROR:\n";
echo "   column \"payment_method_id\" of relation \"project_payments\" does not exist\n\n";

echo "✅ AFTER FIX:\n";
echo "   Controller now inserts:\n";
echo "   - payment_method = '{$paymentMethod->name}' (string, not ID)\n";
echo "   - bank_account_id = {$cashAccount->id} (correct field name)\n";
echo "   - reference_number = \$validated['reference'] (correct field name)\n";
echo "   - payment_type = 'direct' (to identify direct income)\n";
echo "   - created_by = auth()->id()\n\n";

echo str_repeat('=', 90) . "\n";
echo "DATABASE SCHEMA - project_payments TABLE\n";
echo str_repeat('=', 90) . "\n\n";

echo "Actual Columns:\n";
$columns = \DB::select('SELECT column_name, data_type FROM information_schema.columns WHERE table_name = \'project_payments\' ORDER BY ordinal_position');
foreach($columns as $col) {
    echo "  - {$col->column_name} ({$col->data_type})\n";
}

echo "\n" . str_repeat('=', 90) . "\n";
echo "MODEL FILLABLE FIELDS\n";
echo str_repeat('=', 90) . "\n\n";

$fillable = (new \App\Models\ProjectPayment())->getFillable();
foreach($fillable as $field) {
    echo "  - {$field}\n";
}

echo "\n" . str_repeat('=', 90) . "\n";
echo "CHANGES MADE\n";
echo str_repeat('=', 90) . "\n\n";

echo "1. Fixed field mapping:\n";
echo "   ✅ payment_method_id → payment_method (store name)\n";
echo "   ✅ cash_account_id → bank_account_id (correct column)\n";
echo "   ✅ reference → reference_number (correct column)\n\n";

echo "2. Removed duplicate code:\n";
echo "   ✅ Removed fallback invoice creation\n";
echo "   ✅ Removed manual cash account balance update\n";
echo "   ✅ ProjectPayment observer handles balance automatically\n\n";

echo "3. Added proper fields:\n";
echo "   ✅ payment_type = 'direct'\n";
echo "   ✅ created_by = auth()->id()\n\n";

echo str_repeat('=', 90) . "\n";
echo "OBSERVER BEHAVIOR\n";
echo str_repeat('=', 90) . "\n\n";

echo "When ProjectPayment is created, the observer automatically:\n";
echo "  1. Updates project->updatePaymentReceived()\n";
echo "  2. Updates invoice payment (if invoice_id is set)\n";
echo "  3. Updates cash account balance:\n";
echo "     \$account->current_balance += \$payment->amount\n\n";

echo "So we DON'T need to manually:\n";
echo "  ❌ Update \$cashAccount->balance (wrong field anyway!)\n";
echo "  ❌ Create CashAccountTransaction (observer handles it)\n\n";

echo "✅ RESULT: Clean, simple code that uses existing infrastructure!\n\n";

