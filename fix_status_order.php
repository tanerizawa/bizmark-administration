<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProjectStatus;

echo "\n=== PERBAIKI URUTAN STATUS PROYEK ===\n\n";

// Definisi urutan yang benar secara teknis
$correctOrder = [
    1 => ['id' => 1, 'name' => 'Penawaran', 'sort_order' => 1],
    2 => ['id' => 2, 'name' => 'Kontrak', 'sort_order' => 2],
    3 => ['id' => 3, 'name' => 'Pengumpulan Dokumen', 'sort_order' => 3],
    4 => ['id' => 4, 'name' => 'Proses', 'sort_order' => 4],
    5 => ['id' => 8, 'name' => 'Menunggu Persetujuan', 'sort_order' => 5], // Pindah dari order 8 ke 5
    6 => ['id' => 9, 'name' => 'SK Terbit', 'sort_order' => 6], // Pindah dari order 9 ke 6
    // Status khusus (tidak dalam workflow normal)
    98 => ['id' => 11, 'name' => 'Ditunda', 'sort_order' => 98],
    99 => ['id' => 10, 'name' => 'Dibatalkan', 'sort_order' => 99],
];

echo "Urutan Status yang Benar (Workflow):\n";
echo str_repeat('-', 70) . "\n";

foreach ($correctOrder as $order) {
    $status = ProjectStatus::find($order['id']);
    
    if ($status) {
        $oldOrder = $status->sort_order;
        $status->sort_order = $order['sort_order'];
        $status->save();
        
        $arrow = $oldOrder != $order['sort_order'] ? " (dari order {$oldOrder})" : "";
        echo sprintf("Order %-3d | %-25s | ID: %-2d%s\n", 
            $order['sort_order'], 
            $status->name, 
            $status->id,
            $arrow
        );
    }
}

echo "\n=== WORKFLOW DIAGRAM ===\n\n";
echo "1. Penawaran (Quote/Proposal ke client)\n";
echo "    ↓\n";
echo "2. Kontrak (Deal closed, kontrak ditandatangani)\n";
echo "    ↓\n";
echo "3. Pengumpulan Dokumen (Persiapan: kumpulkan dokumen dari client)\n";
echo "    ↓\n";
echo "4. Proses (Submit ke DLH/BPN/OSS/Notaris - detail di notes)\n";
echo "    ↓\n";
echo "5. Menunggu Persetujuan (Menunggu hasil review dari instansi)\n";
echo "    ↓\n";
echo "6. SK Terbit (SELESAI - Izin/SK sudah diterbitkan)\n";
echo "\n";
echo "Status Khusus:\n";
echo "  - Ditunda (Proyek ditunda sementara)\n";
echo "  - Dibatalkan (Proyek dibatalkan)\n";

echo "\n=== VERIFIKASI ===\n";
ProjectStatus::where('is_active', true)
    ->orderBy('sort_order')
    ->get(['id', 'name', 'code', 'sort_order', 'is_final'])
    ->each(function($s) {
        $final = $s->is_final ? ' [FINAL]' : '';
        echo sprintf("%-3d | %-25s | %s%s\n", $s->sort_order, $s->name, $s->code, $final);
    });

echo "\n=== SELESAI ===\n";
