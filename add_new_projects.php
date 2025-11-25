<?php

/**
 * Script untuk menambahkan 4 Proyek Baru
 * Tanggal: 22 November 2025
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectStatus;
use Carbon\Carbon;

echo "🚀 Menambahkan 4 Proyek Baru...\n\n";

// Get clients
$clients = [
    'asiacon' => Client::where('company_name', 'PT Asiacon Cipta Prima')->first(),
    'maulida' => Client::where('company_name', 'PT Maulida Indo Property')->first(),
    'putra_jaya' => Client::where('company_name', 'PT Putra Jaya Laksana Utama')->first(),
    'rindu_alam' => Client::where('company_name', 'PT Rindu Alam Sejahtera')->first(),
];

// Get statuses
$statusKontrak = ProjectStatus::where('name', 'Kontrak')->first();
$statusSKTerbit = ProjectStatus::where('name', 'SK Terbit')->first();

if (!$statusKontrak || !$statusSKTerbit) {
    echo "❌ Error: Status 'Kontrak' atau 'SK Terbit' tidak ditemukan!\n";
    exit(1);
}

$projects = [
    // Project 1: PT Asiacon Cipta Prima - Paket Pekerjaan
    [
        'name' => 'Paket Pekerjaan Perizinan PT Asiacon Cipta Prima',
        'description' => "LINGKUP LAYANAN:\n" .
                        "1. Pengurusan PKKPR OSS (Kesesuaian Kegiatan Pemanfaatan Ruang)\n" .
                        "2. Pengajuan Pertimbangan Teknis Pertanahan ke ATR/BPN\n" .
                        "3. Penyusunan Site Plan Arsitektur & Infrastruktur dan Peil Banjir\n" .
                        "4. Penyusunan dan pengesahan UKL-UPL lengkap dengan uji laboratorium lingkungan\n" .
                        "5. Persetujuan Bangunan Gedung (PBG)\n" .
                        "6. Rekomendasi Analisis Dampak Lalu Lintas (Andalalin)",
        'client_id' => $clients['asiacon']->id,
        'client_name' => $clients['asiacon']->company_name,
        'client_contact' => $clients['asiacon']->phone,
        'client_address' => $clients['asiacon']->address . ', ' . $clients['asiacon']->city . ', ' . $clients['asiacon']->province,
        'status_id' => $statusKontrak->id,
        'start_date' => Carbon::parse('2025-09-01'),
        'deadline' => Carbon::parse('2026-01-28'),
        'contract_value' => 180000000, // 180 juta
        'down_payment' => 90000000, // Termin 1: 90 juta
        'payment_received' => 0,
        'payment_terms' => 'Termin 1: Rp 90.000.000',
        'payment_status' => 'partial',
        'progress_percentage' => 0,
        'notes' => 'Paket pekerjaan mencakup 6 layanan perizinan lengkap. Termin tahap 1: 90 juta.',
    ],
    
    // Project 2: PT Maulida Indo Property - UKL-UPL
    [
        'name' => 'Penyusunan UKL-UPL PT Maulida Indo Property',
        'description' => 'Pekerjaan penyusunan dan pengesahan dokumen UKL-UPL (Upaya Pengelolaan Lingkungan Hidup dan Upaya Pemantauan Lingkungan Hidup) untuk PT Maulida Indo Property.',
        'client_id' => $clients['maulida']->id,
        'client_name' => $clients['maulida']->company_name,
        'client_contact' => $clients['maulida']->phone,
        'client_address' => $clients['maulida']->address . ', ' . $clients['maulida']->city . ', ' . $clients['maulida']->province,
        'status_id' => $statusSKTerbit->id,
        'start_date' => Carbon::parse('2025-09-01'),
        'deadline' => Carbon::parse('2025-11-20'),
        'contract_value' => 50000000, // 50 juta
        'down_payment' => 25000000, // Termin 1: 10 juta + Termin 2: 15 juta
        'payment_received' => 25000000,
        'payment_terms' => 'Termin 1: Rp 10.000.000, Termin 2: Rp 15.000.000, Termin 3: Rp 25.000.000',
        'payment_status' => 'partial',
        'progress_percentage' => 100,
        'notes' => 'Status: SK Terbit (Selesai). Total termin: 10 juta + 15 juta + 25 juta (sisa) = 50 juta.',
    ],
    
    // Project 3: PT Putra Jaya Laksana Utama - UKL-UPL
    [
        'name' => 'Penyusunan UKL-UPL PT Putra Jaya Laksana Utama',
        'description' => 'Pekerjaan penyusunan dan pengesahan dokumen UKL-UPL (Upaya Pengelolaan Lingkungan Hidup dan Upaya Pemantauan Lingkungan Hidup) untuk PT Putra Jaya Laksana Utama.',
        'client_id' => $clients['putra_jaya']->id,
        'client_name' => $clients['putra_jaya']->company_name,
        'client_contact' => $clients['putra_jaya']->phone,
        'client_address' => $clients['putra_jaya']->address . ', ' . $clients['putra_jaya']->city . ', ' . $clients['putra_jaya']->province,
        'status_id' => $statusSKTerbit->id,
        'start_date' => Carbon::parse('2025-09-01'),
        'deadline' => Carbon::parse('2025-11-20'),
        'contract_value' => 45000000, // 45 juta
        'down_payment' => 30000000, // Termin 1: 10 juta + Termin 2: 10 juta + Termin 3: 10 juta
        'payment_received' => 30000000,
        'payment_terms' => 'Termin 1: Rp 10.000.000, Termin 2: Rp 10.000.000, Termin 3: Rp 10.000.000, Termin 4: Rp 15.000.000',
        'payment_status' => 'partial',
        'progress_percentage' => 100,
        'notes' => 'Status: SK Terbit (Selesai). Total termin: 10 juta + 10 juta + 10 juta + 15 juta (sisa) = 45 juta.',
    ],
    
    // Project 4: PT Rindu Alam Sejahtera - Paket Pekerjaan
    [
        'name' => 'Paket Pekerjaan Perizinan PT Rindu Alam Sejahtera',
        'description' => "LINGKUP LAYANAN:\n" .
                        "1. Penyusunan Pertek BPN\n" .
                        "2. Pembuatan Siteplan\n" .
                        "3. Penyusunan PKKPR\n" .
                        "4. Penyusunan PBG (Persetujuan Bangunan Gedung)\n" .
                        "5. Uji Laboratorium Udara (ambient, emisi)\n" .
                        "6. Uji Laboratorium Air Tanah\n" .
                        "7. Penyusunan Pertek Limbah B3\n" .
                        "8. Penyusunan Rintek Limbah B3\n" .
                        "9. Penyusunan UKL/UPL",
        'client_id' => $clients['rindu_alam']->id,
        'client_name' => $clients['rindu_alam']->company_name,
        'client_contact' => $clients['rindu_alam']->phone,
        'client_address' => $clients['rindu_alam']->address . ', ' . $clients['rindu_alam']->city . ', ' . $clients['rindu_alam']->province,
        'status_id' => $statusKontrak->id,
        'start_date' => Carbon::parse('2025-11-20'),
        'deadline' => Carbon::parse('2026-01-20'),
        'contract_value' => 220000000, // 220 juta
        'down_payment' => 50000000, // Termin 1: 50 juta
        'payment_received' => 0,
        'payment_terms' => 'Termin 1: Rp 50.000.000',
        'payment_status' => 'partial',
        'progress_percentage' => 0,
        'notes' => 'Paket pekerjaan mencakup 9 layanan perizinan lengkap termasuk Limbah B3. Termin tahap 1: 50 juta.',
    ],
];

$added = 0;
$skipped = 0;

foreach ($projects as $index => $projectData) {
    try {
        $num = $index + 1;
        
        // Check if project already exists by name and client
        $existing = Project::where('name', $projectData['name'])
                          ->where('client_id', $projectData['client_id'])
                          ->first();
        
        if ($existing) {
            echo "⚠️  Proyek #" . $num . " - {$projectData['name']} sudah ada (ID: {$existing->id})\n";
            $skipped++;
            continue;
        }
        
        // Create project
        $project = Project::create($projectData);
        
        echo "✅ Proyek #" . $num . " - {$projectData['name']} berhasil ditambahkan (ID: {$project->id})\n";
        echo "   👤 Client: {$projectData['client_name']}\n";
        echo "   💰 Nilai Kontrak: Rp " . number_format($projectData['contract_value'], 0, ',', '.') . "\n";
        echo "   📅 Periode: {$projectData['start_date']->format('d M Y')} - {$projectData['deadline']->format('d M Y')}\n";
        echo "   📊 Status: " . ($projectData['status_id'] == $statusKontrak->id ? 'Kontrak' : 'SK Terbit') . "\n";
        echo "   💵 Down Payment: Rp " . number_format($projectData['down_payment'], 0, ',', '.') . "\n\n";
        
        $added++;
        
    } catch (\Exception $e) {
        echo "❌ Error menambahkan {$projectData['name']}: {$e->getMessage()}\n\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "✅ Berhasil ditambahkan: {$added} proyek\n";
echo "⚠️  Dilewati (sudah ada): {$skipped} proyek\n";
echo "📝 Total proyek dalam database: " . Project::count() . " proyek\n";
echo "💰 Total nilai kontrak baru: Rp " . number_format(
    ($added > 0 ? 180000000 + 50000000 + 45000000 + 220000000 : 0), 
    0, ',', '.'
) . "\n";
echo str_repeat("=", 60) . "\n\n";

echo "✨ Selesai!\n";
echo "💡 Catatan: Data proyek dapat dikelola melalui halaman /projects di admin panel.\n";
