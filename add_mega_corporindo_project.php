<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectStatus;
use Carbon\Carbon;

echo "\n=== Menambahkan Proyek PT Mega Corporindo Mandiri ===\n\n";

// Cari client PT Mega Corporindo Mandiri
$client = Client::where('company_name', 'PT Mega Corporindo Mandiri')->first();

if (!$client) {
    echo "❌ Client PT Mega Corporindo Mandiri tidak ditemukan!\n";
    exit(1);
}

echo "✅ Client ditemukan: {$client->company_name} (ID: {$client->id})\n";

// Cari status "Kontrak"
$status = ProjectStatus::where('name', 'Kontrak')->first();

if (!$status) {
    echo "❌ Status 'Kontrak' tidak ditemukan!\n";
    exit(1);
}

echo "✅ Status ditemukan: {$status->name} (ID: {$status->id})\n\n";

// Data proyek
$projectData = [
    'name' => 'Perizinan Pengumpulan Limbah B3',
    'description' => 'Pekerjaan perizinan pengumpulan limbah B3 mencakup:
1. Penyusunan dokumen perizinan pengumpulan limbah B3
2. Konsultasi dan koordinasi dengan instansi terkait
3. Pengurusan izin operasional pengumpulan limbah B3
4. Pendampingan hingga izin terbit',
    'client_id' => $client->id,
    'client_name' => $client->company_name,
    'client_contact' => $client->phone,
    'client_address' => $client->address,
    'status_id' => $status->id,
    'start_date' => Carbon::parse('2025-11-01'),
    'deadline' => Carbon::parse('2025-12-30'),
    'contract_value' => 90000000,
    'down_payment' => 45000000,
    'payment_received' => 45000000,
    'payment_terms' => 'Termin 1: Rp 45.000.000 (50% - sudah dibayar)
Termin 2: Rp 45.000.000 (50% - sisa pembayaran)',
    'payment_status' => 'partial',
    'progress_percentage' => 50,
    'notes' => 'Proyek perizinan pengumpulan limbah B3. Termin 1 sebesar 45 juta sudah dibayar. Progress disesuaikan dengan pembayaran.',
];

// Cek duplikasi
$existing = Project::where('name', $projectData['name'])
    ->where('client_id', $client->id)
    ->first();

if ($existing) {
    echo "⚠️ Proyek '{$projectData['name']}' untuk client ini sudah ada (ID: {$existing->id})\n";
    exit(0);
}

// Buat proyek baru
try {
    $project = Project::create($projectData);
    
    echo "✅ PROYEK BERHASIL DITAMBAHKAN!\n\n";
    echo "ID Proyek: {$project->id}\n";
    echo "Nama: {$project->name}\n";
    echo "Client: {$project->client_name}\n";
    echo "Status: {$status->name}\n";
    echo "Nilai Kontrak: Rp " . number_format($project->contract_value, 0, ',', '.') . "\n";
    echo "Periode: " . $project->start_date->format('d M Y') . " - " . $project->deadline->format('d M Y') . "\n";
    echo "Durasi: " . $project->start_date->diffInDays($project->deadline) . " hari\n";
    echo "Termin 1 (Dibayar): Rp " . number_format($project->down_payment, 0, ',', '.') . "\n";
    echo "Sisa Pembayaran: Rp " . number_format($project->contract_value - $project->payment_received, 0, ',', '.') . "\n";
    echo "Progress: {$project->progress_percentage}%\n";
    echo "\n=== SUKSES ===\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
