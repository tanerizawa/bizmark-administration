<?php
/**
 * Script to create test transactions for Bank Reconciliation testing
 * Run with: docker compose exec app php create_test_transactions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Project;
use App\Models\CashAccount;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use Carbon\Carbon;

echo "🚀 Creating Test Transactions for Bank Reconciliation\n";
echo str_repeat('=', 60) . "\n\n";

// Get BTN account (ID: 13)
$btnAccount = CashAccount::find(13);
if (!$btnAccount) {
    echo "❌ BTN account not found!\n";
    exit(1);
}

// Get a project
$project = Project::first();
if (!$project) {
    echo "❌ No projects found!\n";
    exit(1);
}

echo "✅ Using BTN Account (ID: {$btnAccount->id})\n";
echo "✅ Using Project: {$project->project_name} (ID: {$project->id})\n\n";

// Create Income Transactions (to match CSV)
echo "📥 Creating Income Transactions...\n";

$incomes = [
    [
        'date' => '2025-10-02',
        'amount' => 15000000,
        'description' => 'Transfer dari Client A',
        'reference' => 'TRF20251002001',
    ],
    [
        'date' => '2025-10-03',
        'amount' => 5000000,
        'description' => 'Pembayaran Invoice #001',
        'reference' => 'INV001-PAY',
    ],
    [
        'date' => '2025-10-10',
        'amount' => 10000000,
        'description' => 'Transfer dari Client B',
        'reference' => 'TRF20251010001',
    ],
    [
        'date' => '2025-10-18',
        'amount' => 20000000,
        'description' => 'Transfer dari Client C',
        'reference' => 'TRF20251018001',
    ],
];

foreach ($incomes as $income) {
    $payment = ProjectPayment::create([
        'project_id' => $project->id,
        'bank_account_id' => $btnAccount->id,
        'payment_date' => $income['date'],
        'amount' => $income['amount'],
        'payment_type' => 'progress',
        'payment_method' => 'bank_transfer',
        'reference_number' => $income['reference'],
        'description' => $income['description'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "  ✓ Created income: {$income['date']} - Rp " . number_format($income['amount'], 0, ',', '.') . " - {$income['reference']}\n";
}

echo "\n📤 Creating Expense Transactions...\n";

$expenses = [
    [
        'date' => '2025-10-05',
        'amount' => 56500,
        'description' => 'Biaya Admin Bank',
        'reference' => 'ADM202510',
        'category' => 'operational',
        'vendor' => 'Bank BTN',
    ],
    [
        'date' => '2025-10-07',
        'amount' => 3000000,
        'description' => 'Pembayaran ke Supplier X',
        'reference' => 'PMT20251007001',
        'category' => 'vendor',
        'vendor' => 'Supplier X',
    ],
    [
        'date' => '2025-10-15',
        'amount' => 5000000,
        'description' => 'Pembayaran Gaji Tim - Draft',
        'reference' => 'SAL202510-DRF',
        'category' => 'operational',
        'vendor' => 'Tim Internal',
    ],
    [
        'date' => '2025-10-20',
        'amount' => 8000000,
        'description' => 'Pembayaran ke Supplier Y',
        'reference' => 'PMT20251020001',
        'category' => 'vendor',
        'vendor' => 'Supplier Y',
    ],
];

foreach ($expenses as $expense) {
    $projectExpense = ProjectExpense::create([
        'project_id' => $project->id,
        'bank_account_id' => $btnAccount->id,
        'expense_date' => $expense['date'],
        'amount' => $expense['amount'],
        'category' => $expense['category'],
        'vendor_name' => $expense['vendor'],
        'description' => $expense['description'],
        'payment_method' => 'bank_transfer',
        'is_billable' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "  ✓ Created expense: {$expense['date']} - Rp " . number_format($expense['amount'], 0, ',', '.') . " - {$expense['reference']}\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "✅ Test transactions created successfully!\n\n";

echo "📊 Summary:\n";
echo "  - Income transactions: " . count($incomes) . " (Total: Rp " . number_format(array_sum(array_column($incomes, 'amount')), 0, ',', '.') . ")\n";
echo "  - Expense transactions: " . count($expenses) . " (Total: Rp " . number_format(array_sum(array_column($expenses, 'amount')), 0, ',', '.') . ")\n";
echo "  - Net cash flow: Rp " . number_format(array_sum(array_column($incomes, 'amount')) - array_sum(array_column($expenses, 'amount')), 0, ',', '.') . "\n\n";

echo "🎯 Next Steps:\n";
echo "  1. Navigate to: /reconciliations\n";
echo "  2. Click 'Mulai Rekonsiliasi Baru'\n";
echo "  3. Select BTN account\n";
echo "  4. Period: 2025-10-01 to 2025-10-20\n";
echo "  5. Opening balance: Rp 50,000,000\n";
echo "  6. Closing balance: Rp 83,943,500\n";
echo "  7. Upload: storage/app/sample_bank_statement_october.csv\n";
echo "  8. Click 'Auto-Match' to match transactions!\n\n";
