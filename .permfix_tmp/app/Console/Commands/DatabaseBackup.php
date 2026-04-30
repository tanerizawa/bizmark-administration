<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup 
                            {type=daily : Backup type (daily, weekly, monthly, full)}
                            {--verify : Verify backup after creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $scriptPath = base_path('scripts/db-backup.sh');

        if (!File::exists($scriptPath)) {
            $this->error('Backup script not found!');
            return Command::FAILURE;
        }

        $this->info("Creating {$type} backup...");

        $command = "bash {$scriptPath} {$type}";
        
        if ($this->option('verify')) {
            $command .= ' && bash ' . escapeshellarg($scriptPath) . ' verify';
        }

        passthru($command, $returnCode);

        if ($returnCode === 0) {
            $this->info('');
            $this->info('✅ Backup completed successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('');
            $this->error('❌ Backup failed!');
            return Command::FAILURE;
        }
    }
}
