<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupShapefiles extends Command
{
    protected $signature = 'shapefiles:cleanup {--hours=24 : Delete files older than this many hours}';

    protected $description = 'Clean up old generated shapefile ZIP files and temporary directories';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $storagePath = config('shapefile.path', 'shapefiles');
        $dir = storage_path("app/{$storagePath}");

        if (!is_dir($dir)) {
            $this->info('Shapefile directory does not exist. Nothing to clean.');
            return self::SUCCESS;
        }

        $cutoff = now()->subHours($hours)->timestamp;
        $deletedFiles = 0;
        $deletedDirs = 0;
        $freedBytes = 0;

        // Clean up ZIP files
        $files = glob($dir . '/*.zip');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                $freedBytes += filesize($file);
                unlink($file);
                $deletedFiles++;
            }
        }

        // Clean up leftover temp directories (from failed generations)
        $dirs = glob($dir . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $subdir) {
            if (filemtime($subdir) < $cutoff) {
                $this->removeDirectory($subdir);
                $deletedDirs++;
            }
        }

        $freedMB = round($freedBytes / 1024 / 1024, 2);
        $this->info("Cleaned up {$deletedFiles} ZIP files and {$deletedDirs} temp directories ({$freedMB} MB freed).");

        return self::SUCCESS;
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
