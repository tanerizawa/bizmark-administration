<?php

/**
 * Complete Cleanup - Remove Permit Types & Check All Data
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   COMPLETE CLEANUP - PERMIT TYPES & REMAINING DATA            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    DB::beginTransaction();
    
    // 1. Check and show permit types before deletion
    echo "📋 Current Permit Types:\n";
    echo str_repeat("─", 70) . "\n";
    $permitTypes = DB::table('permit_types')->select('id', 'code', 'name')->get();
    foreach ($permitTypes as $pt) {
        echo "  ID: {$pt->id} - {$pt->code} - {$pt->name}\n";
    }
    echo str_repeat("─", 70) . "\n";
    echo "Total: " . $permitTypes->count() . " permit types\n\n";
    
    // 2. Delete permit types (will cascade to related data if any)
    echo "🗑️  Deleting permit types...\n";
    $deletedPermitTypes = DB::table('permit_types')->delete();
    echo "   ✓ Deleted {$deletedPermitTypes} permit types\n\n";
    
    // 3. Check permit applications for any permit_type_id references
    echo "🔍 Checking permit applications...\n";
    $appsWithPermitType = DB::table('permit_applications')
        ->whereNotNull('permit_type_id')
        ->count();
    
    if ($appsWithPermitType > 0) {
        echo "   Found {$appsWithPermitType} applications with permit_type_id\n";
        echo "   Nullifying permit_type_id references...\n";
        DB::table('permit_applications')
            ->whereNotNull('permit_type_id')
            ->update(['permit_type_id' => null]);
        echo "   ✓ Cleaned up permit_type_id references\n";
    } else {
        echo "   ✓ No permit_type_id references to clean\n";
    }
    echo "\n";
    
    // 4. Reset permit_types sequence
    echo "🔄 Resetting permit_types sequence...\n";
    try {
        DB::statement("ALTER SEQUENCE permit_types_id_seq RESTART WITH 1");
        echo "   ✓ Sequence reset to start from 1\n";
    } catch (\Exception $e) {
        echo "   ℹ️  Sequence reset skipped (might not exist)\n";
    }
    echo "\n";
    
    // 5. Final verification
    echo "📊 Final Database Status:\n";
    echo str_repeat("─", 70) . "\n";
    
    $finalCounts = [
        'permit_types' => DB::table('permit_types')->count(),
        'permit_applications' => DB::table('permit_applications')->count(),
        'projects' => DB::table('projects')->count(),
        'business_contexts' => DB::table('business_contexts')->count(),
        'kbli_permit_recommendations' => DB::table('kbli_permit_recommendations')->count(),
    ];
    
    foreach ($finalCounts as $table => $count) {
        printf("%-35s: %6d records\n", $table, $count);
    }
    echo str_repeat("─", 70) . "\n\n";
    
    DB::commit();
    
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   ✅ CLEANUP COMPLETED                                         ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    echo "Summary:\n";
    echo "  ✓ Deleted {$deletedPermitTypes} permit types\n";
    echo "  ✓ Cleaned permit_type_id references in applications\n";
    echo "  ✓ Reset permit_types sequence\n";
    echo "\n";
    echo "Next Steps:\n";
    echo "  • Permit types will be created from AI recommendations\n";
    echo "  • Applications will work with KBLI-based recommendations\n";
    echo "  • Clean slate for AI-driven permit management\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   ❌ ERROR                                                     ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n";
    
    exit(1);
}
