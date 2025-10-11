<?php
/**
 * Dashboard Verification Script
 * Checks for data integrity issues in dashboard calculations
 * Run with: docker compose exec app php verify_dashboard_data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Project;
use App\Models\Task;
use App\Models\CashAccount;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "🔍 DASHBOARD DATA VERIFICATION\n";
echo str_repeat('=', 70) . "\n\n";

// ============================================
// 1. CHECK CASH BALANCE
// ============================================
echo "1️⃣  CASH BALANCE VERIFICATION\n";
echo str_repeat('-', 70) . "\n";

$cashAccounts = CashAccount::where('is_active', true)->get();
$totalCash = $cashAccounts->sum('current_balance');

echo "Active Cash Accounts:\n";
foreach ($cashAccounts as $account) {
    $balance = number_format($account->current_balance, 0, ',', '.');
    echo "  • {$account->account_name}: Rp {$balance}\n";
}
echo "\n";
echo "Total Cash Balance: Rp " . number_format($totalCash, 0, ',', '.') . "\n";

if ($totalCash < 0) {
    echo "⚠️  WARNING: Negative cash balance detected!\n";
}

echo "\n";

// ============================================
// 2. CHECK FOR PAYMENT DOUBLE COUNTING
// ============================================
echo "2️⃣  PAYMENT DOUBLE COUNTING CHECK\n";
echo str_repeat('-', 70) . "\n";

// Check if there are project_payments linked to invoices
$linkedPayments = ProjectPayment::whereNotNull('invoice_id')->count();
echo "Project Payments linked to invoices: {$linkedPayments}\n";

// Check payment_schedules
$paidSchedules = DB::table('payment_schedules')
    ->where('status', 'paid')
    ->whereNotNull('paid_date')
    ->count();
echo "Paid payment schedules: {$paidSchedules}\n";

// Check for potential duplicates (same invoice_id and similar amounts)
$potentialDuplicates = DB::select("
    SELECT 
        pp.id as payment_id,
        pp.invoice_id,
        pp.amount as payment_amount,
        pp.payment_date,
        ps.id as schedule_id,
        ps.amount as schedule_amount,
        ps.paid_date
    FROM project_payments pp
    INNER JOIN payment_schedules ps ON pp.invoice_id = ps.invoice_id
    WHERE pp.invoice_id IS NOT NULL
    AND ps.status = 'paid'
    AND ABS(pp.amount - ps.amount) < 1000
    AND MONTH(pp.payment_date) = MONTH(ps.paid_date)
    AND YEAR(pp.payment_date) = YEAR(ps.paid_date)
    LIMIT 10
");

if (count($potentialDuplicates) > 0) {
    echo "\n🔴 CRITICAL: Found " . count($potentialDuplicates) . " potential duplicate payments!\n";
    echo "\nSample duplicates:\n";
    foreach ($potentialDuplicates as $dup) {
        echo "  • Invoice #{$dup->invoice_id}: ";
        echo "Payment #{$dup->payment_id} (Rp " . number_format($dup->payment_amount) . ") ";
        echo "= Schedule #{$dup->schedule_id} (Rp " . number_format($dup->schedule_amount) . ")\n";
    }
    
    $totalDuplicateAmount = array_sum(array_column($potentialDuplicates, 'payment_amount'));
    echo "\nTotal potentially double-counted: Rp " . number_format($totalDuplicateAmount, 0, ',', '.') . "\n";
} else {
    echo "✅ No duplicate payments found\n";
}

echo "\n";

// ============================================
// 3. VERIFY THIS MONTH INCOME/EXPENSES
// ============================================
echo "3️⃣  THIS MONTH FINANCIAL VERIFICATION\n";
echo str_repeat('-', 70) . "\n";

$thisMonth = Carbon::now();

// Method 1: Payment schedules
$invoicePaymentsThisMonth = DB::table('payment_schedules')
    ->where('status', 'paid')
    ->whereNotNull('paid_date')
    ->whereYear('paid_date', $thisMonth->year)
    ->whereMonth('paid_date', $thisMonth->month)
    ->sum('amount');

// Method 2: Direct payments (no invoice)
$directPaymentsThisMonth = ProjectPayment::whereNull('invoice_id')
    ->whereYear('payment_date', $thisMonth->year)
    ->whereMonth('payment_date', $thisMonth->month)
    ->sum('amount');

// Method 3: All project payments (for comparison)
$allPaymentsThisMonth = ProjectPayment::whereYear('payment_date', $thisMonth->year)
    ->whereMonth('payment_date', $thisMonth->month)
    ->sum('amount');

echo "Income This Month (" . $thisMonth->format('F Y') . "):\n";
echo "  • From payment schedules: Rp " . number_format($invoicePaymentsThisMonth, 0, ',', '.') . "\n";
echo "  • Direct payments (no invoice): Rp " . number_format($directPaymentsThisMonth, 0, ',', '.') . "\n";
echo "  • Combined total (used in dashboard): Rp " . number_format($invoicePaymentsThisMonth + $directPaymentsThisMonth, 0, ',', '.') . "\n";
echo "  • All project payments (for comparison): Rp " . number_format($allPaymentsThisMonth, 0, ',', '.') . "\n";

if ($allPaymentsThisMonth > ($invoicePaymentsThisMonth + $directPaymentsThisMonth)) {
    $difference = $allPaymentsThisMonth - ($invoicePaymentsThisMonth + $directPaymentsThisMonth);
    echo "\n⚠️  WARNING: Missing Rp " . number_format($difference, 0, ',', '.') . " in calculations!\n";
    echo "   This could be due to payments with invoice_id that don't have matching payment_schedules.\n";
}

// Expenses
$expensesThisMonth = ProjectExpense::whereYear('expense_date', $thisMonth->year)
    ->whereMonth('expense_date', $thisMonth->month)
    ->sum('amount');

echo "\nExpenses This Month:\n";
echo "  • Total: Rp " . number_format($expensesThisMonth, 0, ',', '.') . "\n";

$netThisMonth = ($invoicePaymentsThisMonth + $directPaymentsThisMonth) - $expensesThisMonth;
echo "\nNet This Month: Rp " . number_format($netThisMonth, 0, ',', '.') . " ";
echo $netThisMonth >= 0 ? "✅ (Profit)\n" : "⚠️  (Loss)\n";

echo "\n";

// ============================================
// 4. VERIFY BURN RATE CALCULATION
// ============================================
echo "4️⃣  BURN RATE VERIFICATION\n";
echo str_repeat('-', 70) . "\n";

$threeMonthsAgo = Carbon::now()->subMonths(3);

// Current method (simple average)
$totalExpensesLast3Months = ProjectExpense::where('expense_date', '>=', $threeMonthsAgo)
    ->sum('amount');
$currentBurnRate = $totalExpensesLast3Months / 3;

echo "Current burn rate calculation (simple divide by 3):\n";
echo "  • Total expenses last 3 months: Rp " . number_format($totalExpensesLast3Months, 0, ',', '.') . "\n";
echo "  • Monthly burn rate: Rp " . number_format($currentBurnRate, 0, ',', '.') . "\n";

// Improved method (actual monthly average)
$monthlyExpenses = ProjectExpense::where('expense_date', '>=', $threeMonthsAgo)
    ->selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month, SUM(amount) as total')
    ->groupBy('year', 'month')
    ->get();

echo "\nImproved burn rate calculation (actual monthly average):\n";
foreach ($monthlyExpenses as $month) {
    $monthName = Carbon::create($month->year, $month->month, 1)->format('F Y');
    echo "  • {$monthName}: Rp " . number_format($month->total, 0, ',', '.') . "\n";
}

$monthsWithExpenses = $monthlyExpenses->count();
$improvedBurnRate = $monthsWithExpenses > 0 ? $totalExpensesLast3Months / $monthsWithExpenses : 0;

echo "\n  • Months with expenses: {$monthsWithExpenses}\n";
echo "  • Improved burn rate: Rp " . number_format($improvedBurnRate, 0, ',', '.') . "\n";

if (abs($currentBurnRate - $improvedBurnRate) > 1000000) {
    echo "\n⚠️  WARNING: Significant difference (Rp " . number_format(abs($currentBurnRate - $improvedBurnRate), 0, ',', '.') . ") between methods!\n";
}

// Calculate runway
if ($improvedBurnRate > 0) {
    $runway = $totalCash / $improvedBurnRate;
    echo "\nCash Runway: " . round($runway, 1) . " months\n";
    
    if ($runway < 2) {
        echo "🔴 CRITICAL: Less than 2 months runway!\n";
    } elseif ($runway < 6) {
        echo "⚠️  WARNING: Less than 6 months runway\n";
    } else {
        echo "✅ Healthy runway (>6 months)\n";
    }
}

echo "\n";

// ============================================
// 5. VERIFY OVERDUE ITEMS
// ============================================
echo "5️⃣  OVERDUE ITEMS VERIFICATION\n";
echo str_repeat('-', 70) . "\n";

$today = Carbon::today();

// Overdue projects
$overdueProjects = Project::where('deadline', '<', $today)
    ->whereDoesntHave('status', function($query) {
        $query->where('name', 'Selesai');
    })
    ->count();

echo "Overdue Projects: {$overdueProjects}\n";

// Overdue tasks
$overdueTasks = Task::where('due_date', '<', $today)
    ->where('status', '!=', 'done')
    ->count();

echo "Overdue Tasks: {$overdueTasks}\n";

// Due today
$projectsDueToday = Project::whereDate('deadline', $today)
    ->whereDoesntHave('status', function($query) {
        $query->where('name', 'Selesai');
    })
    ->count();

$tasksDueToday = Task::whereDate('due_date', $today)
    ->where('status', '!=', 'done')
    ->count();

echo "Due Today (Projects): {$projectsDueToday}\n";
echo "Due Today (Tasks): {$tasksDueToday}\n";

$totalUrgent = $overdueProjects + $overdueTasks + $projectsDueToday + $tasksDueToday;
echo "\nTotal Urgent Items: {$totalUrgent}\n";

if ($totalUrgent > 10) {
    echo "⚠️  WARNING: High number of urgent items!\n";
}

echo "\n";

// ============================================
// 6. VERIFY BUDGET UTILIZATION
// ============================================
echo "6️⃣  BUDGET UTILIZATION VERIFICATION\n";
echo str_repeat('-', 70) . "\n";

// Method 1: Current method (contract_value only)
$totalBudgetMethod1 = Project::selectRaw('SUM(COALESCE(NULLIF(contract_value, 0), budget)) as total')
    ->value('total') ?? 0;

// Method 2: Include all projects
$totalBudgetMethod2 = Project::selectRaw('SUM(COALESCE(NULLIF(contract_value, 0), NULLIF(budget, 0), 0)) as total')
    ->value('total') ?? 0;

// Total spent
$totalSpent = ProjectExpense::sum('amount');

// Projects with contract_value
$projectsWithContract = Project::whereNotNull('contract_value')
    ->where('contract_value', '>', 0)
    ->count();

// All projects
$allProjects = Project::count();

echo "Budget Calculation:\n";
echo "  • Method 1 (current - contract_value only): Rp " . number_format($totalBudgetMethod1, 0, ',', '.') . "\n";
echo "  • Method 2 (include legacy budget): Rp " . number_format($totalBudgetMethod2, 0, ',', '.') . "\n";
echo "  • Projects with contract_value: {$projectsWithContract}/{$allProjects}\n";

echo "\nExpenses:\n";
echo "  • Total spent (all projects): Rp " . number_format($totalSpent, 0, ',', '.') . "\n";

$utilization1 = $totalBudgetMethod1 > 0 ? round(($totalSpent / $totalBudgetMethod1) * 100, 1) : 0;
$utilization2 = $totalBudgetMethod2 > 0 ? round(($totalSpent / $totalBudgetMethod2) * 100, 1) : 0;

echo "\nUtilization:\n";
echo "  • Method 1: {$utilization1}%\n";
echo "  • Method 2: {$utilization2}%\n";

if (abs($utilization1 - $utilization2) > 10) {
    echo "\n⚠️  WARNING: Significant difference between methods!\n";
    echo "   Recommendation: Include all projects in budget calculation\n";
}

echo "\n";

// ============================================
// 7. SUMMARY
// ============================================
echo str_repeat('=', 70) . "\n";
echo "📊 VERIFICATION SUMMARY\n";
echo str_repeat('=', 70) . "\n\n";

$issues = [];

// Check for issues
if ($totalCash < 0) {
    $issues[] = "Negative cash balance";
}

if (count($potentialDuplicates) > 0) {
    $issues[] = "Potential payment double counting";
}

if ($allPaymentsThisMonth > ($invoicePaymentsThisMonth + $directPaymentsThisMonth + 1000)) {
    $issues[] = "Missing payments in calculation";
}

if (abs($currentBurnRate - $improvedBurnRate) > 1000000) {
    $issues[] = "Burn rate calculation inaccuracy";
}

if (abs($utilization1 - $utilization2) > 10) {
    $issues[] = "Budget utilization scope mismatch";
}

if ($totalUrgent > 10) {
    $issues[] = "High number of urgent items";
}

if (isset($runway) && $runway < 2) {
    $issues[] = "Critical cash runway (<2 months)";
}

if (empty($issues)) {
    echo "✅ All verifications passed! Dashboard data looks accurate.\n\n";
} else {
    echo "⚠️  Found " . count($issues) . " issue(s):\n\n";
    foreach ($issues as $i => $issue) {
        echo "  " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
    echo "Recommendation: Review DASHBOARD_ANALYSIS_REPORT.md for detailed fixes.\n\n";
}

// Key metrics summary
echo "Key Metrics:\n";
echo "  • Total Cash: Rp " . number_format($totalCash, 0, ',', '.') . "\n";
echo "  • Monthly Burn: Rp " . number_format($improvedBurnRate, 0, ',', '.') . "\n";
if (isset($runway)) {
    echo "  • Cash Runway: " . round($runway, 1) . " months\n";
}
echo "  • This Month Income: Rp " . number_format($invoicePaymentsThisMonth + $directPaymentsThisMonth, 0, ',', '.') . "\n";
echo "  • This Month Expenses: Rp " . number_format($expensesThisMonth, 0, ',', '.') . "\n";
echo "  • This Month Net: Rp " . number_format($netThisMonth, 0, ',', '.') . "\n";
echo "  • Budget Utilization: {$utilization2}%\n";
echo "  • Urgent Items: {$totalUrgent}\n";

echo "\n";
echo "✅ Verification complete!\n";
echo "See DASHBOARD_ANALYSIS_REPORT.md for recommended fixes.\n\n";
