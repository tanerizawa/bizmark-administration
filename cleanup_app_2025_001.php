<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cleanup APP-2025-001 ===" . PHP_EOL . PHP_EOL;

$appId = 2;
$appNumber = 'APP-2025-001';

try {
    DB::beginTransaction();
    
    // 1. Delete status logs
    echo "1. Deleting status logs..." . PHP_EOL;
    $logsDeleted = DB::table('application_status_logs')
        ->where('application_id', $appId)
        ->delete();
    echo "   ✓ Deleted {$logsDeleted} status logs" . PHP_EOL;
    
    // 2. Delete application
    echo "2. Deleting application..." . PHP_EOL;
    $appDeleted = DB::table('permit_applications')
        ->where('id', $appId)
        ->delete();
    echo "   ✓ Deleted application: {$appNumber}" . PHP_EOL;
    
    // 3. Reset sequence if needed
    echo "3. Checking application sequence..." . PHP_EOL;
    $maxId = DB::table('permit_applications')->max('id');
    if ($maxId === null) {
        // No applications left, reset sequence to 1
        DB::statement("ALTER SEQUENCE permit_applications_id_seq RESTART WITH 1");
        echo "   ✓ Reset sequence to 1 (no applications left)" . PHP_EOL;
    } else {
        // Set sequence to max ID + 1
        DB::statement("SELECT setval('permit_applications_id_seq', {$maxId})");
        echo "   ✓ Sequence set to " . ($maxId + 1) . PHP_EOL;
    }
    
    DB::commit();
    
    echo PHP_EOL . "=== Cleanup Summary ===" . PHP_EOL;
    echo "✓ Status logs deleted: {$logsDeleted}" . PHP_EOL;
    echo "✓ Application deleted: {$appNumber}" . PHP_EOL;
    echo "✓ Total records deleted: " . ($logsDeleted + $appDeleted) . PHP_EOL;
    
    echo PHP_EOL . "=== Verification ===" . PHP_EOL;
    $remaining = DB::table('permit_applications')->count();
    echo "Remaining applications: {$remaining}" . PHP_EOL;
    
    if ($remaining === 0) {
        echo "✓ All test applications cleaned!" . PHP_EOL;
    }
    
    echo PHP_EOL . "✅ Cleanup completed successfully!" . PHP_EOL;
    
} catch (\Exception $e) {
    DB::rollBack();
    echo PHP_EOL . "❌ Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
