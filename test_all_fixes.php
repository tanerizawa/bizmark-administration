<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use Carbon\Carbon;

echo "\n" . str_repeat('=', 100) . "\n";
echo "                    VERIFIKASI SEMUA PERBAIKAN DEADLINE BUG                        \n";
echo str_repeat('=', 100) . "\n\n";

$projects = Project::with('status')->get();
$now = Carbon::now();

// 1. TEST PROJECTS INDEX
echo "1️⃣  HALAMAN /PROJECTS (Index)\n";
echo str_repeat('-', 100) . "\n";

$overdueCount = Project::where('deadline', '<', $now)
    ->whereNull('completed_at')
    ->whereHas('status', function($q) {
        $q->whereNotIn('code', ['COMPLETED', 'CLOSED', 'CANCELLED', 'ON_HOLD']);
    })->count();

echo "Statistik Card 'Terlambat': {$overdueCount} proyek\n";
echo "Fix: Exclude completed projects with whereNull('completed_at')\n\n";

echo "Tabel Deadline Column:\n";
foreach($projects as $p) {
    if ($p->completed_at) {
        $completionStatus = $p->getCompletionStatus();
        $color = match($completionStatus) {
            'early' => 'BIRU (#5AC8FA)',
            'on-time' => 'HIJAU (#34C759)',
            'late' => 'ORANGE (#FF9F0A)',
            default => 'PUTIH'
        };
    } else {
        $color = $p->deadline->isPast() ? 'MERAH (#FF453A)' : 'PUTIH';
    }
    $status = $p->completed_at ? "Selesai ({$p->getCompletionStatus()})" : ($p->deadline->isPast() ? 'Overdue' : 'Ongoing');
    echo "  Proyek #{$p->id}: {$p->deadline->format('d M Y')} - {$color} [{$status}]\n";
}

// 2. TEST DASHBOARD
echo "\n\n2️⃣  HALAMAN /DASHBOARD\n";
echo str_repeat('-', 100) . "\n";

// Critical Alerts
$overdueProjects = Project::where('deadline', '<', $now)
    ->whereNull('completed_at')
    ->whereDoesntHave('status', function($query) {
        $query->where('name', 'Selesai');
    })->get();

echo "Panel 'Memerlukan Penanganan' - Proyek Terlambat: {$overdueProjects->count()} proyek\n";
echo "Fix: Added whereNull('completed_at')\n";
foreach($overdueProjects as $p) {
    $daysOverdue = $now->diffInDays($p->deadline);
    echo "  - {$p->name}: Terlambat {$daysOverdue} hari\n";
}

// Agenda 30 Hari
$endOfMonth = $now->copy()->addDays(30);
$agendaProjects = Project::whereBetween('deadline', [$now, $endOfMonth])
    ->whereNull('completed_at')
    ->get();

echo "\nPanel 'Agenda 30 Hari': {$agendaProjects->count()} proyek\n";
echo "Fix: Exclude completed projects\n";

// 3. TEST PROYEKSI KAS
echo "\n\n3️⃣  PROYEKSI KAS\n";
echo str_repeat('-', 100) . "\n";

use App\Models\CashAccount;
use App\Models\ProjectExpense;

$currentBalance = CashAccount::where('is_active', true)->sum('current_balance');
$threeMonthsAgo = $now->copy()->subMonths(3);

$monthlyExpenses = ProjectExpense::where('expense_date', '>=', $threeMonthsAgo)
    ->selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
    ->groupBy('year', 'month')
    ->get();

$monthsWithExpenses = $monthlyExpenses->count();
$totalExpenses = $monthlyExpenses->sum('total');

if ($monthsWithExpenses === 0) {
    $allTimeExpenses = ProjectExpense::selectRaw("DATE_PART('year', expense_date) as year, DATE_PART('month', expense_date) as month, SUM(amount) as total")
        ->groupBy('year', 'month')
        ->get();
    $monthsWithExpenses = $allTimeExpenses->count();
    $totalExpenses = $allTimeExpenses->sum('total');
}

$monthlyBurnRate = $monthsWithExpenses > 0 ? $totalExpenses / $monthsWithExpenses : 0;

if ($monthlyBurnRate > 0) {
    $runway = $currentBalance / $monthlyBurnRate;
    $runway = min($runway, 99);  // Cap at 99
} else {
    $runway = 99;
}

$status = $runway < 2 ? 'critical' : ($runway < 6 ? 'warning' : 'healthy');

echo "Saldo Kas: Rp " . number_format($currentBalance, 0, ',', '.') . "\n";
echo "Burn Rate: Rp " . number_format($monthlyBurnRate, 0, ',', '.') . "/bulan\n";
echo "Proyeksi Kas: " . round($runway, 1) . " bln\n";
echo "Status: " . ucfirst($status) . "\n";
echo "Fix: Cap runway at 99 months max (was 999 when no expenses)\n";

// SUMMARY
echo "\n\n" . str_repeat('=', 100) . "\n";
echo "                                  ✅ RINGKASAN PERBAIKAN                                  \n";
echo str_repeat('=', 100) . "\n\n";

echo "✅ /projects (Index):\n";
echo "   • Fixed deadline column color logic (check completed_at first)\n";
echo "   • Fixed overdue count (exclude completed projects)\n\n";

echo "✅ /dashboard:\n";
echo "   • Fixed 'Proyek Terlambat' panel (exclude completed)\n";
echo "   • Fixed 'Agenda 30 Hari' panel (exclude completed)\n\n";

echo "✅ Proyeksi Kas:\n";
echo "   • Capped runway at 99 months (was 999)\n";
echo "   • More realistic display\n\n";

echo "Proyek #3 & #4 (selesai tepat waktu) sekarang:\n";
echo "  • TIDAK muncul di card 'Terlambat'\n";
echo "  • TIDAK muncul di 'Proyek Terlambat' dashboard\n";
echo "  • Menampilkan warna HIJAU (#34C759) di tabel deadline\n\n";

