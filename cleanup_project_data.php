<?php

/**
 * Cleanup Script for Project Data and KBLI Cache
 * 
 * This script will:
 * 1. Clean all project-related data
 * 2. Clean KBLI recommendation cache
 * 3. Clean business contexts data
 * 4. Clean orphaned relationships
 * 
 * WARNING: This will delete data permanently!
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   CLEANUP PROJECT DATA & KBLI CACHE                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check current data counts
echo "📊 Current Data Status:\n";
echo str_repeat("─", 70) . "\n";

$counts = [
    'projects' => DB::table('projects')->count(),
    'project_permits' => DB::table('project_permits')->count(),
    'project_permit_dependencies' => DB::table('project_permit_dependencies')->count(),
    'project_payments' => DB::table('project_payments')->count(),
    'project_expenses' => DB::table('project_expenses')->count(),
    'project_logs' => DB::table('project_logs')->count(),
    'project_statuses' => DB::table('project_statuses')->count(),
    'invoices' => DB::table('invoices')->count(),
    'document_drafts' => DB::table('document_drafts')->count(),
    'ai_processing_logs' => DB::table('ai_processing_logs')->count(),
    'business_contexts' => DB::table('business_contexts')->count(),
    'kbli_permit_recommendations' => DB::table('kbli_permit_recommendations')->count(),
];

foreach ($counts as $table => $count) {
    printf("%-35s: %6d records\n", $table, $count);
}

echo str_repeat("─", 70) . "\n";
$totalRecords = array_sum($counts);
echo "Total records to be deleted: $totalRecords\n";
echo "\n";

// Ask for confirmation
echo "⚠️  WARNING: This will permanently delete all data listed above!\n";
echo "\n";
echo "Type 'YES' to proceed with cleanup: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if ($line !== 'YES') {
    echo "\n❌ Cleanup cancelled.\n\n";
    exit(0);
}

echo "\n🔄 Starting cleanup process...\n\n";

try {
    DB::beginTransaction();
    
    $deleted = [];
    
    // 1. Clean KBLI Permit Recommendations Cache
    echo "🗑️  Cleaning KBLI permit recommendations cache...\n";
    $deleted['kbli_permit_recommendations'] = DB::table('kbli_permit_recommendations')->delete();
    echo "   ✓ Deleted {$deleted['kbli_permit_recommendations']} KBLI recommendation cache records\n";
    
    // 2. Clean Business Contexts
    echo "🗑️  Cleaning business contexts...\n";
    $deleted['business_contexts'] = DB::table('business_contexts')->delete();
    echo "   ✓ Deleted {$deleted['business_contexts']} business context records\n";
    
    // 3. Clean AI Processing Logs (related to projects)
    echo "🗑️  Cleaning AI processing logs...\n";
    $deleted['ai_processing_logs'] = DB::table('ai_processing_logs')->delete();
    echo "   ✓ Deleted {$deleted['ai_processing_logs']} AI processing log records\n";
    
    // 4. Clean Document Drafts (related to projects)
    echo "🗑️  Cleaning document drafts...\n";
    $deleted['document_drafts'] = DB::table('document_drafts')->delete();
    echo "   ✓ Deleted {$deleted['document_drafts']} document draft records\n";
    
    // 5. Clean Invoices (will cascade to invoice_items)
    echo "🗑️  Cleaning invoices...\n";
    $deleted['invoices'] = DB::table('invoices')->delete();
    echo "   ✓ Deleted {$deleted['invoices']} invoice records\n";
    
    // 6. Clean Project Permit Dependencies
    echo "🗑️  Cleaning project permit dependencies...\n";
    $deleted['project_permit_dependencies'] = DB::table('project_permit_dependencies')->delete();
    echo "   ✓ Deleted {$deleted['project_permit_dependencies']} permit dependency records\n";
    
    // 7. Clean Project Permits
    echo "🗑️  Cleaning project permits...\n";
    $deleted['project_permits'] = DB::table('project_permits')->delete();
    echo "   ✓ Deleted {$deleted['project_permits']} project permit records\n";
    
    // 8. Clean Project Expenses
    echo "🗑️  Cleaning project expenses...\n";
    $deleted['project_expenses'] = DB::table('project_expenses')->delete();
    echo "   ✓ Deleted {$deleted['project_expenses']} project expense records\n";
    
    // 9. Clean Project Payments
    echo "🗑️  Cleaning project payments...\n";
    $deleted['project_payments'] = DB::table('project_payments')->delete();
    echo "   ✓ Deleted {$deleted['project_payments']} project payment records\n";
    
    // 10. Clean Project Logs
    echo "🗑️  Cleaning project logs...\n";
    $deleted['project_logs'] = DB::table('project_logs')->delete();
    echo "   ✓ Deleted {$deleted['project_logs']} project log records\n";
    
    // 11. Clean Project Statuses
    echo "🗑️  Cleaning project statuses...\n";
    $deleted['project_statuses'] = DB::table('project_statuses')->delete();
    echo "   ✓ Deleted {$deleted['project_statuses']} project status records\n";
    
    // 12. Finally, clean Projects table
    echo "🗑️  Cleaning projects...\n";
    $deleted['projects'] = DB::table('projects')->delete();
    echo "   ✓ Deleted {$deleted['projects']} project records\n";
    
    // 13. Check for orphaned data in permit_applications
    echo "🔍 Checking for orphaned permit application data...\n";
    $orphanedApps = DB::table('permit_applications')
        ->whereNotNull('project_id')
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('projects')
                  ->whereRaw('projects.id = permit_applications.project_id');
        })
        ->update(['project_id' => null]);
    echo "   ✓ Cleaned $orphanedApps orphaned project references in permit_applications\n";
    
    // Reset sequences for auto-increment IDs
    echo "🔄 Resetting auto-increment sequences...\n";
    $tables = [
        'projects',
        'project_permits',
        'project_permit_dependencies',
        'project_payments',
        'project_expenses',
        'project_logs',
        'project_statuses',
        'invoices',
        'document_drafts',
        'ai_processing_logs',
        'business_contexts',
        'kbli_permit_recommendations',
    ];
    
    foreach ($tables as $table) {
        DB::statement("ALTER SEQUENCE {$table}_id_seq RESTART WITH 1");
    }
    echo "   ✓ Sequences reset\n";
    
    DB::commit();
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   ✅ CLEANUP COMPLETED SUCCESSFULLY                            ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    echo "📊 Cleanup Summary:\n";
    echo str_repeat("─", 70) . "\n";
    $totalDeleted = 0;
    foreach ($deleted as $table => $count) {
        printf("%-35s: %6d deleted\n", $table, $count);
        $totalDeleted += $count;
    }
    echo str_repeat("─", 70) . "\n";
    echo "Total records deleted: $totalDeleted\n";
    echo "\n";
    
    echo "🎉 Database is now clean!\n";
    echo "\n";
    echo "What was cleaned:\n";
    echo "  ✓ All project data (projects, permits, payments, expenses, logs)\n";
    echo "  ✓ KBLI recommendation cache (AI-generated permit suggestions)\n";
    echo "  ✓ Business contexts data\n";
    echo "  ✓ Document drafts\n";
    echo "  ✓ AI processing logs\n";
    echo "  ✓ Invoices and related items\n";
    echo "  ✓ Orphaned references\n";
    echo "  ✓ Auto-increment sequences reset\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   ❌ ERROR DURING CLEANUP                                      ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\n";
    echo "⚠️  All changes have been rolled back.\n";
    echo "\n";
    
    exit(1);
}

echo "Done!\n\n";
