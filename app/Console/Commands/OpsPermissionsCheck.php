<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OpsPermissionsCheck extends Command
{
    protected $signature = 'ops:permissions-check {--json : Output JSON} {--create : Create missing directories when possible}';

    protected $description = 'Check writability of critical runtime directories (storage/* and bootstrap/cache)';

    public function handle(): int
    {
        $compiledViews = (string) config('view.compiled', storage_path('framework/views'));

        $targets = [
            'view.compiled' => $compiledViews,
            'storage/framework/views' => storage_path('framework/views'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/testing' => storage_path('framework/testing'),
            'storage/logs' => storage_path('logs'),
            'storage/app' => storage_path('app'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $results = [];
        $allOk = true;

        foreach ($targets as $name => $path) {
            $results[$name] = $this->checkDirectory($path, $name, (bool) $this->option('create'));
            if (!($results[$name]['ok'] ?? false)) {
                $allOk = false;
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode([
                'ok' => $allOk,
                'results' => $results,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            foreach ($results as $name => $row) {
                $status = ($row['ok'] ?? false) ? 'OK' : 'FAIL';
                $this->line("{$status} {$name} {$row['path']}");
                if (!empty($row['error'])) {
                    $this->line('  ' . $row['error']);
                }
            }
        }

        if (!$allOk) {
            Log::error('Permission check failed for one or more runtime directories', [
                'results' => $results,
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function checkDirectory(string $path, string $name, bool $create): array
    {
        $row = [
            'path' => $path,
            'exists' => File::exists($path),
            'is_dir' => File::isDirectory($path),
            'writable' => is_writable($path),
            'ok' => false,
            'error' => null,
        ];

        if (!$row['exists'] || !$row['is_dir']) {
            if ($create) {
                try {
                    File::ensureDirectoryExists($path);
                    clearstatcache(true, $path);
                } catch (\Throwable $e) {
                    $row['error'] = "Cannot create directory: {$e->getMessage()}";
                    return $row;
                }

                $row['exists'] = File::exists($path);
                $row['is_dir'] = File::isDirectory($path);
                $row['writable'] = is_writable($path);
            } else {
                $row['error'] = 'Directory missing';
                return $row;
            }
        }

        if (!$row['writable']) {
            $row['error'] = 'Directory not writable';
            return $row;
        }

        try {
            $file = $path . DIRECTORY_SEPARATOR . '.permcheck-' . bin2hex(random_bytes(8));
            file_put_contents($file, "permcheck {$name} " . date(DATE_ATOM));
            @unlink($file);
        } catch (\Throwable $e) {
            $row['error'] = "Write test failed: {$e->getMessage()}";
            return $row;
        }

        $row['ok'] = true;
        return $row;
    }
}
