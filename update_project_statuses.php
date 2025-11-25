<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProjectStatus;
use App\Models\Project;

echo "\n=== UPDATE PROJECT STATUSES - SIMPLIFY TO 'PROSES' ===\n\n";

// 1. Update status name dari "Proses di DLH" menjadi "Proses"
$statusProsesDLH = ProjectStatus::find(4);
if ($statusProsesDLH) {
    echo "1. Update status 'Proses di DLH' menjadi 'Proses':\n";
    $statusProsesDLH->name = 'Proses';
    $statusProsesDLH->code = 'PROSES';
    $statusProsesDLH->description = 'Proyek sedang dalam proses di instansi terkait (DLH, BPN, OSS, Notaris, dll)';
    $statusProsesDLH->save();
    echo "   ✅ Status updated: {$statusProsesDLH->name} (ID: {$statusProsesDLH->id})\n\n";
}

// 2. Pindahkan proyek yang menggunakan status lama ke status baru
$statusesToMerge = [5, 6, 7]; // BPN, OSS, Notaris
$movedCount = 0;

foreach ($statusesToMerge as $oldStatusId) {
    $oldStatus = ProjectStatus::find($oldStatusId);
    if ($oldStatus) {
        $projects = Project::where('status_id', $oldStatusId)->get();
        
        if ($projects->count() > 0) {
            echo "2. Pindahkan proyek dari '{$oldStatus->name}' ke 'Proses':\n";
            
            foreach ($projects as $project) {
                $project->status_id = 4; // ID status "Proses"
                $project->save();
                
                // Create log
                $project->logs()->create([
                    'action' => 'status_changed',
                    'description' => "Status otomatis diupdate dari '{$oldStatus->name}' ke 'Proses' (system merge)",
                    'old_values' => ['status_id' => $oldStatusId],
                    'new_values' => ['status_id' => 4],
                ]);
                
                echo "   ✅ Proyek #{$project->id}: {$project->name}\n";
                $movedCount++;
            }
        }
    }
}

if ($movedCount > 0) {
    echo "\n   Total proyek dipindahkan: {$movedCount}\n\n";
} else {
    echo "2. Tidak ada proyek yang perlu dipindahkan\n\n";
}

// 3. Nonaktifkan/hapus status lama (soft delete dengan is_active)
echo "3. Nonaktifkan status lama:\n";
foreach ($statusesToMerge as $oldStatusId) {
    $oldStatus = ProjectStatus::find($oldStatusId);
    if ($oldStatus) {
        // Check if projects still using this status
        $projectCount = Project::where('status_id', $oldStatusId)->count();
        
        if ($projectCount == 0) {
            $oldStatus->is_active = false;
            $oldStatus->save();
            echo "   ✅ Nonaktifkan: {$oldStatus->name} (ID: {$oldStatus->id})\n";
        } else {
            echo "   ⚠️  Masih ada {$projectCount} proyek menggunakan: {$oldStatus->name}\n";
        }
    }
}

echo "\n=== FINAL STATUS LIST ===\n";
ProjectStatus::where('is_active', true)
    ->orderBy('sort_order')
    ->get(['id', 'name', 'code', 'sort_order'])
    ->each(function($s) {
        echo "ID {$s->id} | Order {$s->sort_order} | {$s->name} ({$s->code})\n";
    });

echo "\n=== SELESAI ===\n";
