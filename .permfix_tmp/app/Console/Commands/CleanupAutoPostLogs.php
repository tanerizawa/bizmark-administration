<?php

namespace App\Console\Commands;

use App\Models\AutoPostLog;
use Illuminate\Console\Command;

class CleanupAutoPostLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:cleanup-logs {--days=90 : Keep logs from last N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old auto-post logs to keep database clean';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("🗑️  Cleaning up logs older than {$days} days (before {$cutoff->toDateString()})...");

        // Count before deletion
        $count = AutoPostLog::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('✅ No old logs to delete.');
            return 0;
        }

        $this->warn("   Found {$count} log entries to delete.");

        if (!$this->confirm('   Proceed with deletion?', true)) {
            $this->info('   Cancelled.');
            return 0;
        }

        // Delete old logs
        $deleted = AutoPostLog::where('created_at', '<', $cutoff)->delete();

        $this->info("✅ Deleted {$deleted} log entries.");
        $this->line("   Logs kept: Last {$days} days");

        return 0;
    }
}
