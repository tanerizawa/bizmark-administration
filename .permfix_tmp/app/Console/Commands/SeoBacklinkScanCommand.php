<?php

namespace App\Console\Commands;

use App\Services\InternalLinkService;
use Illuminate\Console\Command;

class SeoBacklinkScanCommand extends Command
{
    protected $signature = 'seo:backlink-scan {--limit=50 : Max articles to scan}';

    protected $description = 'Scan published articles and inject bidirectional internal links';

    public function handle(InternalLinkService $linkService): int
    {
        try {
        $limit = (int) $this->option('limit');

        $this->info("🔗 Starting backlink scan (limit: {$limit})...");

        $stats = $linkService->batchBacklinkScan($limit);

        $this->info("✅ Backlink scan complete:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Articles Scanned', $stats['scanned']],
                ['Articles Updated', $stats['updated']],
                ['Links Injected', $stats['links_injected']],
            ]
        );

        return Command::SUCCESS;
        } catch (\Exception $e) {
            \Log::error('Backlink scan failed: ' . $e->getMessage());
            $this->error('Scan failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
