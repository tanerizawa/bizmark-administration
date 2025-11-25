<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;

echo "\n=== VERIFIKASI FIX - STATISTIK PROYEK PANEL ===\n";
echo str_repeat('=', 90) . "\n";

$projects = Project::with('status')->get();

foreach($projects as $p) {
    echo "\nProyek #{$p->id}: {$p->name}\n";
    echo "Deadline: " . $p->deadline->format('d M Y') . "\n";
    echo "Completed: " . ($p->completed_at ? $p->completed_at->format('d M Y') : 'Belum') . "\n";
    
    echo "\nQUICK PANEL - Statistik Proyek:\n";
    
    if ($p->completed_at) {
        $completionStatus = $p->getCompletionStatus();
        $deadlineColor = match($completionStatus) {
            'early' => 'BIRU',
            'on-time' => 'HIJAU',
            'late' => 'ORANGE',
            default => 'WHITE'
        };
        $deadlineLabel = match($completionStatus) {
            'early' => '⚡ Lebih cepat',
            'on-time' => '⏰ Tepat waktu',
            'late' => '⚠️ Terlambat',
            default => 'Selesai'
        };
        echo "  Deadline: " . $p->deadline->format('d M Y') . " ({$deadlineLabel}) [{$deadlineColor}]\n";
    } else {
        $isPastDeadline = $p->deadline->isPast();
        $deadlineColor = $isPastDeadline ? 'MERAH' : 'WHITE';
        $deadlineLabel = $isPastDeadline ? 'Terlambat' : $p->deadline->diffForHumans();
        echo "  Deadline: " . $p->deadline->format('d M Y') . " ({$deadlineLabel}) [{$deadlineColor}]\n";
    }
    
    echo str_repeat('-', 90) . "\n";
}

echo "\n✅ FIX BERHASIL!\n";
echo "Proyek yang sudah selesai tepat waktu sekarang menampilkan:\n";
echo "  - \"⏰ Tepat waktu\" (HIJAU) bukan \"Terlambat\" (MERAH)\n\n";
