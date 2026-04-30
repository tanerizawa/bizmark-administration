<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SafeMigrateFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe-fresh 
                            {--force : Force the operation in production}
                            {--backup : Create backup before operation}
                            {--seed : Run seeders after migration}
                            {--really-sure : Additional confirmation flag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely run migrate:fresh with mandatory backups and confirmations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->error('');
        $this->error('╔═══════════════════════════════════════════════════════════════╗');
        $this->error('║  ⚠️  DANGER: DESTRUCTIVE OPERATION DETECTED!                   ║');
        $this->error('╚═══════════════════════════════════════════════════════════════╝');
        $this->error('');

        // Check environment
        $env = app()->environment();
        $this->warn("Environment: {$env}");

        if (in_array($env, ['production', 'prod'])) {
            $this->error('');
            $this->error('🚫 BLOCKED: migrate:fresh is DISABLED in production!');
            $this->error('');
            $this->info('If you really need to reset the database:');
            $this->line('  1. Create a backup: php artisan db:backup');
            $this->line('  2. Use the restore script: scripts/db-restore.sh');
            $this->line('  3. Contact the system administrator');
            $this->error('');

            return Command::FAILURE;
        }

        // Show current data stats
        $this->warn('');
        $this->warn('Current database statistics:');

        $tables = $this->getTableStats();
        $totalRecords = 0;

        foreach ($tables as $table => $count) {
            if ($count > 0) {
                $this->line("  • {$table}: {$count} records");
                $totalRecords += $count;
            }
        }

        $this->warn('');
        $this->warn("Total records that will be DELETED: {$totalRecords}");
        $this->warn('');

        // Multiple confirmations
        if (! $this->option('really-sure')) {
            $this->error('Missing --really-sure flag');

            return Command::FAILURE;
        }

        if (! $this->confirm('Do you understand this will DELETE ALL DATA?', false)) {
            $this->info('Operation cancelled.');

            return Command::FAILURE;
        }

        $typedConfirm = $this->ask('Type "DELETE-ALL-DATA" to confirm');
        if ($typedConfirm !== 'DELETE-ALL-DATA') {
            $this->info('Operation cancelled.');

            return Command::FAILURE;
        }

        // Create mandatory backup
        $this->info('');
        $this->info('Creating mandatory backup before reset...');

        $backupPath = $this->createBackup();

        if (! $backupPath) {
            $this->error('Backup failed! Aborting operation.');

            return Command::FAILURE;
        }

        $this->info("Backup created: {$backupPath}");
        $this->info('');

        // Final warning
        $this->error('');
        $this->error('FINAL WARNING: All data will be deleted in 10 seconds...');
        $this->error('Press Ctrl+C to cancel!');

        for ($i = 10; $i > 0; $i--) {
            $this->line("  {$i}...");
            sleep(1);
        }

        // Run migrate:fresh
        $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => $this->option('seed'),
        ]);

        $this->info('');
        $this->info('Migration complete. Backup available at:');
        $this->line("  {$backupPath}");
        $this->info('');

        return Command::SUCCESS;
    }

    /**
     * Get record counts for important tables.
     */
    private function getTableStats(): array
    {
        $tables = ['users', 'articles', 'projects', 'clients', 'documents', 'tasks', 'invoices'];
        $stats = [];

        foreach ($tables as $table) {
            try {
                $stats[$table] = DB::table($table)->count();
            } catch (\Exception $e) {
                $stats[$table] = 0;
            }
        }

        return $stats;
    }

    /**
     * Create a backup before destructive operation.
     */
    private function createBackup(): ?string
    {
        $scriptPath = base_path('scripts/db-backup.sh');

        if (File::exists($scriptPath)) {
            exec("bash {$scriptPath} backup 2>&1", $output, $returnCode);

            if ($returnCode === 0) {
                // Find the latest backup
                $backupDir = storage_path('backups/daily');
                $files = glob("{$backupDir}/bizmark_daily_*.sql.gz");

                if (! empty($files)) {
                    usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));

                    return $files[0];
                }
            }
        }

        // Fallback: use pg_dump directly
        $timestamp = now()->format('Ymd_His');
        $backupPath = storage_path("backups/pre-fresh/pre_fresh_{$timestamp}.sql.gz");

        File::ensureDirectoryExists(dirname($backupPath));

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s | gzip > %s',
            escapeshellarg(config('database.connections.pgsql.password')),
            escapeshellarg(config('database.connections.pgsql.host')),
            escapeshellarg(config('database.connections.pgsql.port')),
            escapeshellarg(config('database.connections.pgsql.username')),
            escapeshellarg(config('database.connections.pgsql.database')),
            escapeshellarg($backupPath)
        );

        exec($command, $output, $returnCode);

        return $returnCode === 0 ? $backupPath : null;
    }
}
