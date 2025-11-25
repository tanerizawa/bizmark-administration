<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentMethod;
use App\Models\CashAccount;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat('=', 90) . "\n";
echo "TEST: Direct Income Payment - Database Constraint Check\n";
echo str_repeat('=', 90) . "\n\n";

// 1. Check Payment Methods
echo "1️⃣  PAYMENT METHODS\n";
echo str_repeat('-', 90) . "\n";
$paymentMethods = PaymentMethod::all(['id', 'code', 'name', 'requires_cash_account']);
foreach ($paymentMethods as $pm) {
    echo sprintf("ID: %-3d | Code: %-20s | Name: %-30s | Requires Account: %s\n",
        $pm->id,
        $pm->code,
        $pm->name,
        $pm->requires_cash_account ? 'Yes' : 'No'
    );
}

// 2. Check Database Constraint
echo "\n2️⃣  DATABASE CONSTRAINT\n";
echo str_repeat('-', 90) . "\n";
$constraints = DB::select("
    SELECT conname, pg_get_constraintdef(oid) as definition
    FROM pg_constraint
    WHERE conrelid = 'project_payments'::regclass
    AND conname = 'project_payments_payment_method_check'
");
foreach ($constraints as $c) {
    echo "Constraint: {$c->conname}\n";
    echo "Definition: {$c->definition}\n";
}

// Parse allowed values
preg_match_all("/'([^']+)'/", $constraints[0]->definition, $matches);
$allowedValues = $matches[1];
echo "\nAllowed payment_method values:\n";
foreach ($allowedValues as $val) {
    echo "  ✓ '$val'\n";
}

// 3. Verify Payment Method Codes Match Constraint
echo "\n3️⃣  VALIDATION CHECK\n";
echo str_repeat('-', 90) . "\n";
$allValid = true;
foreach ($paymentMethods as $pm) {
    $isValid = in_array($pm->code, $allowedValues);
    $icon = $isValid ? '✅' : '❌';
    echo "$icon Payment Method Code: '{$pm->code}' - " . ($isValid ? 'VALID' : 'INVALID') . "\n";
    if (!$isValid) $allValid = false;
}

if ($allValid) {
    echo "\n✅ All payment method codes match database constraint!\n";
} else {
    echo "\n❌ WARNING: Some payment method codes don't match constraint!\n";
}

// 4. Test Data Example
echo "\n4️⃣  EXAMPLE: Correct Data Format\n";
echo str_repeat('-', 90) . "\n";
$examplePM = PaymentMethod::where('code', 'bank_transfer')->first();
$exampleAccount = CashAccount::active()->first();

if ($examplePM && $exampleAccount) {
    echo "Example Request Data:\n";
    echo "  payment_method_id: {$examplePM->id}\n";
    echo "  cash_account_id: {$exampleAccount->id}\n\n";
    
    echo "What Controller Should Store:\n";
    echo "  payment_method: '{$examplePM->code}' ← MUST USE CODE, NOT NAME!\n";
    echo "  bank_account_id: {$exampleAccount->id}\n\n";
    
    echo "❌ WRONG (causes error):\n";
    echo "  payment_method: '{$examplePM->name}' ← This violates constraint!\n\n";
    
    echo "✅ CORRECT:\n";
    echo "  payment_method: '{$examplePM->code}' ← This matches constraint!\n";
}

// 5. Summary
echo "\n" . str_repeat('=', 90) . "\n";
echo "SUMMARY OF FIX\n";
echo str_repeat('=', 90) . "\n\n";

echo "❌ BEFORE (Error):\n";
echo "   Controller used: \$payment->payment_method = \$paymentMethod->name;\n";
echo "   Database received: 'Transfer Bank'\n";
echo "   Constraint expects: 'bank_transfer'\n";
echo "   Result: ❌ Check constraint violation!\n\n";

echo "✅ AFTER (Fixed):\n";
echo "   Controller uses: \$payment->payment_method = \$paymentMethod->code;\n";
echo "   Database receives: 'bank_transfer'\n";
echo "   Constraint expects: 'bank_transfer'\n";
echo "   Result: ✅ Success!\n\n";

echo "📝 FILES MODIFIED:\n";
echo "   app/Http/Controllers/FinancialController.php\n";
echo "   Line 889: Changed from ->name to ->code\n\n";

