<?php
/**
 * Script untuk upload dokumen template ke test UKL/UPL yang sudah dibuat
 * 
 * CARA PENGGUNAAN:
 * 1. Upload file UKL_UPL_Template_Broken.docx ke storage/app/test-templates/
 * 2. Jalankan: php update_ukl_upl_template.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TestTemplate;
use Illuminate\Support\Facades\Storage;

echo "\n";
echo "🔄 Update UKL/UPL Test Template Document\n";
echo "========================================\n\n";

// Find the template
$template = TestTemplate::where('title', 'Document Editing Test - UKL/UPL Environmental Specialist')->first();

if (!$template) {
    echo "❌ Template tidak ditemukan!\n";
    echo "   Run: php artisan db:seed --class=UklUplDocumentEditingTestSeeder\n\n";
    exit(1);
}

echo "✅ Template found: ID #{$template->id}\n";
echo "   Current status: " . ($template->is_active ? 'ACTIVE' : 'INACTIVE') . "\n";
echo "   Template file: " . ($template->template_file_path ?: 'NOT UPLOADED') . "\n\n";

// Check available files in test-templates directory
$files = Storage::disk('private')->files('test-templates');
echo "📁 Files in storage/app/test-templates/:\n";
if (empty($files)) {
    echo "   (kosong)\n";
} else {
    foreach ($files as $file) {
        $size = Storage::disk('private')->size($file);
        $sizeKB = round($size / 1024, 2);
        echo "   - " . basename($file) . " ({$sizeKB} KB)\n";
    }
}
echo "\n";

// Ask for filename
echo "📝 Masukkan nama file yang akan digunakan (tanpa path):\n";
echo "   Contoh: UKL_UPL_Template_Broken.docx\n";
echo "   Filename: ";
$filename = trim(fgets(STDIN));

if (empty($filename)) {
    echo "\n❌ Filename tidak boleh kosong!\n\n";
    exit(1);
}

// Build full path
$fullPath = 'test-templates/' . $filename;

// Check if file exists
if (!Storage::disk('private')->exists($fullPath)) {
    echo "\n❌ File tidak ditemukan: {$fullPath}\n";
    echo "   Upload file dulu ke: storage/app/test-templates/\n\n";
    exit(1);
}

// Get file info
$fileSize = Storage::disk('private')->size($fullPath);
$fileSizeMB = round($fileSize / (1024 * 1024), 2);

echo "\n";
echo "✅ File ditemukan!\n";
echo "   Path: {$fullPath}\n";
echo "   Size: {$fileSizeMB} MB\n\n";

// Ask for confirmation
echo "🔄 Update template dengan file ini? (y/n): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'y') {
    echo "\n❌ Dibatalkan.\n\n";
    exit(0);
}

// Update template
$template->template_file_path = $fullPath;
$template->is_active = true; // Activate template
$template->save();

echo "\n";
echo "✅ Template berhasil diupdate!\n";
echo "========================================\n";
echo "ID: {$template->id}\n";
echo "Title: {$template->title}\n";
echo "Template File: {$template->template_file_path}\n";
echo "Status: " . ($template->is_active ? 'ACTIVE ✅' : 'INACTIVE') . "\n";
echo "Duration: {$template->duration_minutes} minutes\n";
echo "Passing Score: {$template->passing_score}%\n";
echo "Total Criteria: " . count($template->evaluation_criteria['criteria']) . "\n";
echo "Total Points: {$template->evaluation_criteria['total_points']}\n";
echo "\n";
echo "🎯 Template siap digunakan!\n";
echo "🔗 View: https://bizmark.id/admin/recruitment/tests/{$template->id}\n";
echo "🔗 Edit: https://bizmark.id/admin/recruitment/tests/{$template->id}/edit\n";
echo "\n";
