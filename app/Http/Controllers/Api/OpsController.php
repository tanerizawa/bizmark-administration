<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OpsController
{
    public function permissions(Request $request)
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
            $results[$name] = $this->checkDirectory((string) $path, $name, true);
            if (! ($results[$name]['ok'] ?? false)) {
                $allOk = false;
            }
        }

        return response()->json([
            'ok' => $allOk,
            'php' => [
                'sapi' => PHP_SAPI,
                'uid' => function_exists('posix_geteuid') ? posix_geteuid() : null,
                'gid' => function_exists('posix_getegid') ? posix_getegid() : null,
                'user' => function_exists('posix_getpwuid') && function_exists('posix_geteuid') ? (posix_getpwuid(posix_geteuid())['name'] ?? null) : null,
                'group' => function_exists('posix_getgrgid') && function_exists('posix_getegid') ? (posix_getgrgid(posix_getegid())['name'] ?? null) : null,
            ],
            'view' => [
                'compiled' => (string) config('view.compiled', ''),
            ],
            'results' => $results,
        ]);
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

        if (! $row['exists'] || ! $row['is_dir']) {
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

        if (! $row['writable']) {
            $row['error'] = 'Directory not writable';

            return $row;
        }

        try {
            $file = $path.DIRECTORY_SEPARATOR.'.permcheck-'.bin2hex(random_bytes(8));
            file_put_contents($file, "permcheck {$name} ".date(DATE_ATOM));
            @unlink($file);
        } catch (\Throwable $e) {
            $row['error'] = "Write test failed: {$e->getMessage()}";

            return $row;
        }

        $row['ok'] = true;

        return $row;
    }
}
