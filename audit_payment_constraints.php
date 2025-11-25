<?php

/**
 * COMPREHENSIVE PAYMENT CONSTRAINT AUDIT
 * 
 * Checks all payment-related code for database constraint violations
 * Run: php audit_payment_constraints.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║        🔍 COMPREHENSIVE PAYMENT CONSTRAINT AUDIT                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ============================================================================
// 1. DATABASE CONSTRAINTS ANALYSIS
// ============================================================================
echo "┌─ 1. DATABASE CONSTRAINTS ─────────────────────────────────────────────────┐\n";
echo "│                                                                            │\n";

$constraints = DB::select("
    SELECT 
        tc.constraint_name,
        cc.check_clause
    FROM information_schema.table_constraints tc
    LEFT JOIN information_schema.check_constraints cc 
        ON tc.constraint_name = cc.constraint_name
    WHERE tc.table_name = 'project_payments'
    AND tc.constraint_type = 'CHECK'
    AND tc.constraint_name LIKE '%payment_%'
");

foreach ($constraints as $constraint) {
    echo "│ Constraint: " . str_pad($constraint->constraint_name, 58) . "│\n";
    
    // Parse allowed values from check clause
    if (preg_match_all("/'([^']+)'::character varying/", $constraint->check_clause, $matches)) {
        echo "│ Allowed values: " . str_pad(implode(', ', $matches[1]), 55) . "│\n";
    }
    echo "│" . str_repeat(' ', 76) . "│\n";
}

echo "└" . str_repeat('─', 76) . "┘\n\n";

// ============================================================================
// 2. PAYMENT METHODS IN DATABASE
// ============================================================================
echo "┌─ 2. PAYMENT METHODS IN DATABASE ──────────────────────────────────────────┐\n";
echo "│                                                                            │\n";
echo "│ " . str_pad("ID", 4) . str_pad("Code", 18) . str_pad("Name", 22) . str_pad("Active", 10) . str_pad("Requires Acct", 15) . "│\n";
echo "│ " . str_repeat("─", 72) . "│\n";

$paymentMethods = PaymentMethod::orderBy('id')->get();
foreach ($paymentMethods as $pm) {
    $active = $pm->is_active ? '✓ Yes' : '✗ No';
    $reqAcct = $pm->requires_cash_account ? '✓ Yes' : '✗ No';
    echo "│ " . 
        str_pad($pm->id, 4) . 
        str_pad($pm->code, 18) . 
        str_pad(substr($pm->name, 0, 20), 22) . 
        str_pad($active, 10) . 
        str_pad($reqAcct, 15) . 
        "│\n";
}

echo "└" . str_repeat('─', 76) . "┘\n\n";

// ============================================================================
// 3. CODE ANALYSIS - Find all ProjectPayment creation points
// ============================================================================
echo "┌─ 3. CODE LOCATIONS CREATING ProjectPayment ───────────────────────────────┐\n";
echo "│                                                                            │\n";

$files = [
    'FinancialController' => 'app/Http/Controllers/FinancialController.php',
    'ProjectPaymentController' => 'app/Http/Controllers/ProjectPaymentController.php',
    'Mobile/FinancialController' => 'app/Http/Controllers/Mobile/FinancialController.php',
];

$issues = [];

foreach ($files as $name => $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (!file_exists($fullPath)) {
        echo "│ ⚠️  File not found: $file" . str_repeat(' ', 76 - strlen($file) - 20) . "│\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    echo "│ 📄 $name" . str_repeat(' ', 76 - strlen($name) - 4) . "│\n";
    
    // Check for payment_method assignment
    if (preg_match('/payment_method[\'"]?\s*=\s*\$paymentMethod->name/', $content)) {
        echo "│    ❌ ERROR: Uses ->name instead of ->code" . str_repeat(' ', 37) . "│\n";
        $issues[] = [
            'file' => $file,
            'issue' => 'Uses payment_method->name instead of ->code',
            'severity' => 'CRITICAL'
        ];
    } else if (preg_match('/payment_method[\'"]?\s*=\s*\$paymentMethod->code/', $content)) {
        echo "│    ✅ OK: Uses ->code (correct)" . str_repeat(' ', 44) . "│\n";
    } else if (preg_match('/payment_method[\'"]?\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        // Hardcoded value - check if it's valid
        $value = $matches[1];
        $validPaymentMethods = ['cash', 'bank_transfer', 'check', 'giro', 'other'];
        if (in_array($value, $validPaymentMethods)) {
            echo "│    ⚠️  HARDCODED: '{$value}' (valid but not flexible)" . str_repeat(' ', 76 - strlen($value) - 48) . "│\n";
        } else {
            echo "│    ❌ ERROR: Hardcoded invalid value '{$value}'" . str_repeat(' ', 76 - strlen($value) - 40) . "│\n";
            $issues[] = [
                'file' => $file,
                'issue' => "Hardcoded invalid payment_method value: '$value'",
                'severity' => 'CRITICAL'
            ];
        }
    }
    
    // Check payment_type assignment
    if (preg_match_all('/payment_type[\'"]?\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        $validPaymentTypes = ['dp', 'progress', 'final', 'other'];
        foreach ($matches[1] as $value) {
            if (!in_array($value, $validPaymentTypes)) {
                echo "│    ❌ ERROR: Invalid payment_type '{$value}'" . str_repeat(' ', 76 - strlen($value) - 39) . "│\n";
                $issues[] = [
                    'file' => $file,
                    'issue' => "Invalid payment_type value: '$value' (must be: dp, progress, final, other)",
                    'severity' => 'CRITICAL'
                ];
            } else {
                echo "│    ✅ payment_type: '{$value}' (valid)" . str_repeat(' ', 76 - strlen($value) - 36) . "│\n";
            }
        }
    }
    
    echo "│" . str_repeat(' ', 76) . "│\n";
}

echo "└" . str_repeat('─', 76) . "┘\n\n";

// ============================================================================
// 4. DATABASE DATA VALIDATION
// ============================================================================
echo "┌─ 4. EXISTING DATA VALIDATION ──────────────────────────────────────────────┐\n";
echo "│                                                                            │\n";

// Check for invalid payment_method values
$invalidPaymentMethods = DB::select("
    SELECT DISTINCT payment_method, COUNT(*) as count
    FROM project_payments
    WHERE payment_method NOT IN ('cash', 'bank_transfer', 'check', 'giro', 'other')
    GROUP BY payment_method
");

if (empty($invalidPaymentMethods)) {
    echo "│ ✅ All payment_method values are valid" . str_repeat(' ', 41) . "│\n";
} else {
    echo "│ ❌ INVALID payment_method values found in database:" . str_repeat(' ', 24) . "│\n";
    foreach ($invalidPaymentMethods as $row) {
        echo "│    - '{$row->payment_method}' ({$row->count} records)" . 
            str_repeat(' ', 76 - strlen($row->payment_method) - strlen($row->count) - 20) . "│\n";
        
        $issues[] = [
            'file' => 'DATABASE',
            'issue' => "Invalid payment_method in database: '{$row->payment_method}' ({$row->count} records)",
            'severity' => 'CRITICAL'
        ];
    }
}

// Check for invalid payment_type values
$invalidPaymentTypes = DB::select("
    SELECT DISTINCT payment_type, COUNT(*) as count
    FROM project_payments
    WHERE payment_type NOT IN ('dp', 'progress', 'final', 'other')
    GROUP BY payment_type
");

echo "│" . str_repeat(' ', 76) . "│\n";

if (empty($invalidPaymentTypes)) {
    echo "│ ✅ All payment_type values are valid" . str_repeat(' ', 43) . "│\n";
} else {
    echo "│ ❌ INVALID payment_type values found in database:" . str_repeat(' ', 26) . "│\n";
    foreach ($invalidPaymentTypes as $row) {
        echo "│    - '{$row->payment_type}' ({$row->count} records)" . 
            str_repeat(' ', 76 - strlen($row->payment_type) - strlen($row->count) - 20) . "│\n";
        
        $issues[] = [
            'file' => 'DATABASE',
            'issue' => "Invalid payment_type in database: '{$row->payment_type}' ({$row->count} records)",
            'severity' => 'CRITICAL'
        ];
    }
}

echo "└" . str_repeat('─', 76) . "┘\n\n";

// ============================================================================
// 5. SUMMARY & RECOMMENDATIONS
// ============================================================================
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  📊 AUDIT SUMMARY                                                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (empty($issues)) {
    echo "✅ NO ISSUES FOUND! All payment code complies with database constraints.\n";
} else {
    echo "❌ FOUND " . count($issues) . " ISSUE(S) THAT NEED FIXING:\n\n";
    
    foreach ($issues as $index => $issue) {
        echo ($index + 1) . ". [{$issue['severity']}] {$issue['file']}\n";
        echo "   {$issue['issue']}\n\n";
    }
    
    echo "┌─ RECOMMENDED FIXES ───────────────────────────────────────────────────────┐\n";
    echo "│                                                                            │\n";
    echo "│ 1. Code Issues:                                                            │\n";
    echo "│    • Change ->name to ->code when assigning payment_method                │\n";
    echo "│    • Update hardcoded values to use valid constraint values               │\n";
    echo "│    • Use 'other' instead of 'direct' for payment_type                     │\n";
    echo "│                                                                            │\n";
    echo "│ 2. Database Issues:                                                        │\n";
    echo "│    • Run migration/update script to fix invalid existing data             │\n";
    echo "│    • Map 'Transfer Bank' → 'bank_transfer'                                │\n";
    echo "│    • Map 'direct' → 'other' for payment_type                              │\n";
    echo "│                                                                            │\n";
    echo "└" . str_repeat('─', 76) . "┘\n";
}

echo "\n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo " Audit completed at: " . date('Y-m-d H:i:s') . "\n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "\n";

// Exit with error code if issues found
exit(empty($issues) ? 0 : 1);
