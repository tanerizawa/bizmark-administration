<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DatabaseMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:monitor 
                            {--alert : Send alerts for anomalies}
                            {--report : Generate full report}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor database health, backup status, and data integrity';

    /**
     * Critical tables to monitor.
     */
    protected array $criticalTables = [
        'users',
        'articles',
        'clients',
        'projects',
        'documents',
        'invoices',
        'tasks',
    ];

    /**
     * Baseline counts for anomaly detection.
     */
    protected string $baselinePath;

    /**
     * Alert thresholds.
     */
    protected array $thresholds = [
        'data_loss_percent' => 10, // Alert if any table loses > 10% records
        'backup_age_hours' => 26, // Alert if backup older than 26 hours
        'db_size_drop_percent' => 15, // Alert if DB size drops > 15%
    ];

    public function __construct()
    {
        parent::__construct();
        $this->baselinePath = storage_path('app/db-baseline.json');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('╔═══════════════════════════════════════════════════════════════╗');
        $this->info('║  📊 DATABASE MONITORING REPORT                                 ║');
        $this->info('╚═══════════════════════════════════════════════════════════════╝');
        $this->info('');

        $alerts = [];
        $connectionName = (string) config('database.default');
        $connectionConfig = (array) config("database.connections.{$connectionName}", []);
        $report = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'connection_name' => $connectionName,
            'driver' => $connectionConfig['driver'] ?? null,
            'database' => $connectionConfig['database'] ?? null,
        ];

        // 1. Check database connectivity
        $this->info('🔌 Database Connection:');
        if (!$this->checkConnection()) {
            $this->error('   ❌ FAILED - Cannot connect to database!');
            $alerts[] = 'CRITICAL: Database connection failed!';
            return $this->finalize($alerts, $report);
        }
        $this->line('   ✅ Connected');
        $report['connection'] = 'ok';

        // 2. Current table statistics
        $this->info('');
        $this->info('📈 Table Statistics:');
        $currentCounts = $this->getTableCounts();
        $report['tables'] = $currentCounts;

        foreach ($currentCounts as $table => $count) {
            $this->line("   • {$table}: " . number_format($count) . " records");
        }

        // 3. Check for data anomalies
        $this->info('');
        $this->info('🔍 Anomaly Detection:');
        $baseline = $this->loadBaseline();
        $anomalies = $this->detectAnomalies($currentCounts, $baseline);

        if (empty($anomalies)) {
            $this->line('   ✅ No anomalies detected');
        } else {
            foreach ($anomalies as $anomaly) {
                $this->warn("   ⚠️  {$anomaly}");
                $alerts[] = $anomaly;
            }
        }
        $report['anomalies'] = $anomalies;

        // Update baseline with current counts
        $this->updateBaseline($currentCounts);

        // 4. Check backup status
        $this->info('');
        $this->info('💾 Backup Status:');
        $backupStatus = $this->checkBackupStatus();
        $report['backup'] = $backupStatus;

        if ($backupStatus['healthy']) {
            $this->line('   ✅ Backups are healthy');
            $this->line("   Last backup: {$backupStatus['last_backup']}");
            $this->line("   Age: {$backupStatus['age_hours']} hours");
            $this->line("   Size: {$backupStatus['size']}");
        } else {
            $this->error("   ❌ {$backupStatus['issue']}");
            $alerts[] = "BACKUP: {$backupStatus['issue']}";
        }

        // 5. Database size
        $this->info('');
        $this->info('💿 Database Size:');
        $dbSize = $this->getDatabaseSize();
        $this->line("   Total: {$dbSize['total']}");
        $this->line("   Tables: {$dbSize['tables']}");
        $this->line("   Indexes: {$dbSize['indexes']}");
        $report['size'] = $dbSize;

        // 6. Connection pool status (PostgreSQL specific)
        $this->info('');
        $this->info('🔗 Connection Pool:');
        $poolStatus = $this->getConnectionPoolStatus();
        $this->line("   Active connections: {$poolStatus['active']}");
        $this->line("   Max connections: {$poolStatus['max']}");
        $this->line("   Usage: {$poolStatus['usage_percent']}%");
        $report['connections'] = $poolStatus;

        if ($poolStatus['usage_percent'] > 80) {
            $alerts[] = "HIGH CONNECTION USAGE: {$poolStatus['usage_percent']}%";
        }

        // Finalize
        return $this->finalize($alerts, $report);
    }

    /**
     * Check database connection.
     */
    protected function checkConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error('Database connection check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get record counts for critical tables.
     */
    protected function getTableCounts(): array
    {
        $counts = [];

        $tables = $this->option('report')
            ? $this->listTables()
            : $this->criticalTables;

        foreach ($tables as $table) {
            try {
                $counts[$table] = DB::table($table)->count();
            } catch (\Exception $e) {
                $counts[$table] = -1; // Error indicator
            }
        }

        return $counts;
    }

    protected function listTables(): array
    {
        $driver = DB::connection()->getDriverName();

        try {
            if ($driver === 'sqlite') {
                $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
                return array_values(array_filter(array_map(fn ($r) => $r->name ?? null, $rows)));
            }

            if ($driver === 'pgsql') {
                $rows = DB::select("SELECT tablename AS name FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
                return array_values(array_filter(array_map(fn ($r) => $r->name ?? null, $rows)));
            }

            $rows = DB::select('SHOW TABLES');
            $tables = [];
            foreach ($rows as $row) {
                $arr = (array) $row;
                $tables[] = array_values($arr)[0] ?? null;
            }
            return array_values(array_filter($tables));
        } catch (\Exception $e) {
            return $this->criticalTables;
        }
    }

    /**
     * Load baseline counts from storage.
     */
    protected function loadBaseline(): array
    {
        if (File::exists($this->baselinePath)) {
            $data = json_decode(File::get($this->baselinePath), true);
            return $data['counts'] ?? [];
        }
        
        return [];
    }

    /**
     * Update baseline with current counts.
     */
    protected function updateBaseline(array $counts): void
    {
        $data = [
            'updated_at' => now()->toIso8601String(),
            'counts' => $counts,
        ];

        File::ensureDirectoryExists(dirname($this->baselinePath));
        File::put($this->baselinePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Detect data anomalies.
     */
    protected function detectAnomalies(array $current, array $baseline): array
    {
        $anomalies = [];

        if (empty($baseline)) {
            return $anomalies; // First run, no baseline to compare
        }

        foreach ($current as $table => $count) {
            if (!isset($baseline[$table])) {
                continue;
            }

            $baselineCount = $baseline[$table];
            
            if ($baselineCount > 0) {
                $changePercent = (($count - $baselineCount) / $baselineCount) * 100;

                // Detect significant data loss
                if ($changePercent < -$this->thresholds['data_loss_percent']) {
                    $anomalies[] = "DATA LOSS: {$table} dropped from {$baselineCount} to {$count} (" . round($changePercent, 1) . "%)";
                }
            }

            // Detect if table became empty
            if ($baselineCount > 10 && $count === 0) {
                $anomalies[] = "CRITICAL: {$table} is now EMPTY (was {$baselineCount} records)!";
            }
        }

        return $anomalies;
    }

    /**
     * Check backup status.
     */
    protected function checkBackupStatus(): array
    {
        $backupDir = storage_path('app/backups');
        $status = [
            'healthy' => false,
            'last_backup' => null,
            'age_hours' => null,
            'size' => null,
            'path' => null,
            'issue' => null,
        ];

        if (!is_dir($backupDir)) {
            $status['issue'] = 'Backup directory not found';
            return $status;
        }

        $files = array_values(array_filter(glob("{$backupDir}/*") ?: [], fn ($p) => is_file($p)));

        if (empty($files)) {
            $status['issue'] = 'No backups found';
            return $status;
        }

        // Sort by modification time, newest first
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        $latestBackup = $files[0];

        $backupTime = Carbon::createFromTimestamp(filemtime($latestBackup));
        $ageHours = $backupTime->diffInHours(now());
        $size = $this->formatBytes(filesize($latestBackup));

        $status['last_backup'] = $backupTime->format('Y-m-d H:i:s');
        $status['age_hours'] = $ageHours;
        $status['size'] = $size;
        $status['path'] = $latestBackup;

        if ($ageHours > $this->thresholds['backup_age_hours']) {
            $status['issue'] = "Backup is {$ageHours} hours old (threshold: {$this->thresholds['backup_age_hours']}h)";
            return $status;
        }

        // Verify backup is not suspiciously small
        $driver = DB::connection()->getDriverName();
        $minBytes = ($driver === 'sqlite') ? 1024 : 10000;
        if (filesize($latestBackup) < $minBytes) {
            $status['issue'] = 'Latest backup is suspiciously small';
            return $status;
        }

        $status['healthy'] = true;
        return $status;
    }

    /**
     * Get database size information.
     */
    protected function getDatabaseSize(): array
    {
        try {
            $conn = DB::connection();
            $driver = $conn->getDriverName();
            $connectionName = (string) config('database.default');
            $connectionConfig = (array) config("database.connections.{$connectionName}", []);

            if ($driver === 'sqlite') {
                $path = (string) ($connectionConfig['database'] ?? '');
                $bytes = (is_string($path) && $path !== '' && file_exists($path)) ? filesize($path) : 0;

                return [
                    'total' => $this->formatBytes((int) $bytes),
                    'tables' => 'unknown',
                    'indexes' => 'unknown',
                ];
            }

            if ($driver === 'pgsql') {
                $dbName = (string) ($connectionConfig['database'] ?? '');
                $total = DB::selectOne("SELECT pg_size_pretty(pg_database_size(?)) as size", [$dbName])->size;

                $tables = DB::selectOne("
                    SELECT pg_size_pretty(sum(pg_table_size(quote_ident(tablename)::regclass))) as size
                    FROM pg_tables
                    WHERE schemaname = 'public'
                ")->size ?? '0 bytes';

                $indexes = DB::selectOne("
                    SELECT pg_size_pretty(sum(pg_indexes_size(quote_ident(tablename)::regclass))) as size
                    FROM pg_tables
                    WHERE schemaname = 'public'
                ")->size ?? '0 bytes';

                return [
                    'total' => $total,
                    'tables' => $tables,
                    'indexes' => $indexes,
                ];
            }

            $dbName = (string) ($connectionConfig['database'] ?? '');
            $row = DB::selectOne("
                SELECT
                    COALESCE(SUM(data_length),0) AS tables_bytes,
                    COALESCE(SUM(index_length),0) AS indexes_bytes,
                    COALESCE(SUM(data_length + index_length),0) AS total_bytes
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName]);

            return [
                'total' => $this->formatBytes((int) ($row->total_bytes ?? 0)),
                'tables' => $this->formatBytes((int) ($row->tables_bytes ?? 0)),
                'indexes' => $this->formatBytes((int) ($row->indexes_bytes ?? 0)),
            ];
        } catch (\Exception $e) {
            return [
                'total' => 'unknown',
                'tables' => 'unknown',
                'indexes' => 'unknown',
            ];
        }
    }

    /**
     * Get connection pool status.
     */
    protected function getConnectionPoolStatus(): array
    {
        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'pgsql') {
                $active = (int) (DB::selectOne("SELECT count(*) as count FROM pg_stat_activity")->count ?? 0);
                $max = (int) (DB::selectOne("SHOW max_connections")->max_connections ?? 0);
                $usage = ($max > 0) ? round(($active / $max) * 100, 1) : 0;

                return [
                    'active' => $active,
                    'max' => $max,
                    'usage_percent' => $usage,
                ];
            }

            if ($driver === 'mysql' || $driver === 'mariadb') {
                $activeRow = DB::selectOne("SHOW STATUS LIKE 'Threads_connected'");
                $maxRow = DB::selectOne("SHOW VARIABLES LIKE 'max_connections'");

                $active = (int) (($activeRow->Value ?? $activeRow->value ?? 0));
                $max = (int) (($maxRow->Value ?? $maxRow->value ?? 0));
                $usage = ($max > 0) ? round(($active / $max) * 100, 1) : 0;

                return [
                    'active' => $active,
                    'max' => $max,
                    'usage_percent' => $usage,
                ];
            }

            return [
                'active' => 'n/a',
                'max' => 'n/a',
                'usage_percent' => 0,
            ];

        } catch (\Exception $e) {
            return [
                'active' => 'unknown',
                'max' => 'unknown',
                'usage_percent' => 0,
            ];
        }
    }

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Finalize the monitoring run.
     */
    protected function finalize(array $alerts, array $report): int
    {
        $this->info('');

        // Save report
        $reportPath = storage_path('logs/db-monitor-' . now()->format('Y-m-d') . '.json');
        File::put($reportPath, json_encode($report, JSON_PRETTY_PRINT));

        // Handle alerts
        if (!empty($alerts) && $this->option('alert')) {
            $this->error('╔═══════════════════════════════════════════════════════════════╗');
            $this->error('║  ⚠️  ALERTS DETECTED!                                          ║');
            $this->error('╚═══════════════════════════════════════════════════════════════╝');
            
            foreach ($alerts as $alert) {
                $this->error("   • {$alert}");
                Log::warning('Database Monitor Alert', ['alert' => $alert]);
            }

            // Send email alert
            $this->sendEmailAlert($alerts);
        }

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));
        }

        $this->info('');
        $this->info("Report saved: {$reportPath}");
        $this->info('');

        return empty($alerts) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Send email alert for critical issues.
     */
    protected function sendEmailAlert(array $alerts): void
    {
        try {
            $email = config('mail.from.address', 'cs@bizmark.id');
            $subject = '⚠️ [BIZMARK] Database Alert - ' . now()->format('Y-m-d H:i');
            
            $body = "Database monitoring detected the following issues:\n\n";
            foreach ($alerts as $alert) {
                $body .= "• {$alert}\n";
            }
            $body .= "\n\nEnvironment: " . app()->environment();
            $body .= "\nServer: " . gethostname();
            $body .= "\nTime: " . now()->toIso8601String();

            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });

            $this->line('📧 Alert email sent');
        } catch (\Exception $e) {
            Log::error('Failed to send database alert email', ['error' => $e->getMessage()]);
        }
    }
}
