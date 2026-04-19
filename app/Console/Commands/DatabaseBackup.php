<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

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

        $connectionName = (string) config('database.default', 'mysql');
        $connection = (array) config("database.connections.{$connectionName}", []);

        $env = [
            'DB_CONNECTION' => $connectionName,
            'DB_HOST' => (string) Arr::get($connection, 'host', '127.0.0.1'),
            'DB_PORT' => (string) Arr::get($connection, 'port', '3306'),
            'DB_DATABASE' => (string) Arr::get($connection, 'database', ''),
            'DB_USERNAME' => (string) Arr::get($connection, 'username', 'root'),
            'DB_PASSWORD' => (string) Arr::get($connection, 'password', ''),
        ];

        $process = new Process(['bash', $scriptPath, (string) $type], base_path(), $env);
        $process->setTimeout(null);
        $process->run(function ($outputType, $buffer) {
            $this->output->write($buffer);
        });

        if ($process->getExitCode() !== 0) {
            $this->error('');
            $this->error('❌ Backup failed!');
            return Command::FAILURE;
        }

        if ($this->option('verify')) {
            $verify = new Process(['bash', $scriptPath, 'verify'], base_path(), $env);
            $verify->setTimeout(null);
            $verify->run(function ($outputType, $buffer) {
                $this->output->write($buffer);
            });

            if ($verify->getExitCode() !== 0) {
                $this->error('');
                $this->error('❌ Backup verification failed!');
                return Command::FAILURE;
            }
        }

        if ($process->getExitCode() === 0) {
            $this->info('');
            $this->info('✅ Backup completed successfully!');
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
