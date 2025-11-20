<?php

/**
 * Database Cleanup Script
 * Removes orphaned data and test/dummy records
 * 
 * Run: php cleanup_database.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DATABASE CLEANUP SCRIPT ===\n\n";

$dryRun = true; // Set to false to actually delete
if (isset($argv[1]) && $argv[1] === '--execute') {
    $dryRun = false;
    echo "⚠️  EXECUTION MODE - Changes will be applied!\n\n";
} else {
    echo "ℹ️  DRY RUN MODE - No changes will be made\n";
    echo "   Run with --execute flag to apply changes\n\n";
}

$stats = [
    'deleted_clients_removed' => 0,
    'test_clients_removed' => 0,
    'orphaned_applications_removed' => 0,
    'failed_jobs_cleared' => 0,
    'old_sessions_cleared' => 0,
];

// 1. Remove soft-deleted clients permanently (only if no data attached)
echo "1. Checking soft-deleted clients...\n";
$deletedClients = DB::table('clients')
    ->whereNotNull('deleted_at')
    ->get();

foreach ($deletedClients as $client) {
    $projectCount = DB::table('projects')->where('client_id', $client->id)->count();
    $appCount = DB::table('permit_applications')->where('client_id', $client->id)->count();
    
    if ($projectCount == 0 && $appCount == 0) {
        echo "   - Removing client ID {$client->id}: {$client->name} (deleted at {$client->deleted_at})\n";
        if (!$dryRun) {
            DB::table('clients')->where('id', $client->id)->delete();
        }
        $stats['deleted_clients_removed']++;
    } else {
        echo "   - Keeping client ID {$client->id}: {$client->name} (has {$projectCount} projects, {$appCount} applications)\n";
    }
}

// 2. Remove test/dummy clients
echo "\n2. Checking test/dummy clients...\n";
$testClients = DB::table('clients')
    ->where(function($query) {
        $query->where('name', 'ILIKE', '%test%')
              ->orWhere('name', 'ILIKE', '%dummy%')
              ->orWhere('email', 'ILIKE', '%test%')
              ->orWhere('email', 'ILIKE', '%dummy%');
    })
    ->whereNull('deleted_at')
    ->get();

foreach ($testClients as $client) {
    $projectCount = DB::table('projects')->where('client_id', $client->id)->count();
    
    if ($projectCount == 0) {
        echo "   - Removing test client ID {$client->id}: {$client->name} ({$client->email})\n";
        if (!$dryRun) {
            DB::table('clients')->where('id', $client->id)->delete();
        }
        $stats['test_clients_removed']++;
    } else {
        echo "   ⚠️  Test client ID {$client->id} has {$projectCount} projects - manual review needed\n";
    }
}

// 3. Remove orphaned permit applications (no client)
echo "\n3. Checking orphaned permit applications...\n";
$orphanedApps = DB::table('permit_applications')
    ->whereNull('client_id')
    ->get();

foreach ($orphanedApps as $app) {
    echo "   - Application ID {$app->id} ({$app->application_number}) - Status: {$app->status}\n";
    echo "     Created: {$app->created_at}\n";
    
    // Check if it has any important data
    $docCount = DB::table('application_documents')->where('application_id', $app->id)->count();
    $noteCount = DB::table('application_notes')->where('application_id', $app->id)->count();
    
    echo "     Documents: {$docCount}, Notes: {$noteCount}\n";
    
    if ($app->status === 'draft' || $app->status === 'submitted') {
        echo "     → Will be removed (early stage, no client)\n";
        if (!$dryRun) {
            DB::table('application_documents')->where('application_id', $app->id)->delete();
            DB::table('application_notes')->where('application_id', $app->id)->delete();
            DB::table('application_status_logs')->where('application_id', $app->id)->delete();
            DB::table('permit_applications')->where('id', $app->id)->delete();
        }
        $stats['orphaned_applications_removed']++;
    } else {
        echo "     ℹ️  Advanced stage - keeping for records\n";
    }
}

// 4. Clear failed jobs
echo "\n4. Checking failed jobs...\n";
$failedJobsCount = DB::table('failed_jobs')->count();
echo "   - Found {$failedJobsCount} failed jobs\n";
if ($failedJobsCount > 0) {
    echo "     → Will be cleared\n";
    if (!$dryRun) {
        DB::table('failed_jobs')->truncate();
    }
    $stats['failed_jobs_cleared'] = $failedJobsCount;
}

// 5. Clear old sessions (older than 30 days)
echo "\n5. Checking old sessions...\n";
$thirtyDaysAgo = now()->subDays(30)->timestamp;
$oldSessionsCount = DB::table('sessions')
    ->where('last_activity', '<', $thirtyDaysAgo)
    ->count();
    
echo "   - Found {$oldSessionsCount} sessions older than 30 days\n";
if ($oldSessionsCount > 0) {
    echo "     → Will be cleared\n";
    if (!$dryRun) {
        DB::table('sessions')->where('last_activity', '<', $thirtyDaysAgo)->delete();
    }
    $stats['old_sessions_cleared'] = $oldSessionsCount;
}

// 6. Clear cache table (it will be rebuilt automatically)
echo "\n6. Checking cache...\n";
$cacheCount = DB::table('cache')->count();
echo "   - Found {$cacheCount} cache entries\n";
if ($cacheCount > 0) {
    echo "     → Will be cleared (will rebuild automatically)\n";
    if (!$dryRun) {
        DB::table('cache')->truncate();
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Deleted clients removed:       {$stats['deleted_clients_removed']}\n";
echo "Test clients removed:          {$stats['test_clients_removed']}\n";
echo "Orphaned applications removed: {$stats['orphaned_applications_removed']}\n";
echo "Failed jobs cleared:           {$stats['failed_jobs_cleared']}\n";
echo "Old sessions cleared:          {$stats['old_sessions_cleared']}\n";
echo str_repeat("=", 50) . "\n";

if ($dryRun) {
    echo "\nℹ️  This was a DRY RUN. No changes were made.\n";
    echo "   Run with --execute flag to apply these changes.\n";
} else {
    echo "\n✅ Cleanup completed successfully!\n";
}

echo "\n";
