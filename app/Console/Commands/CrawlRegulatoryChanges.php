<?php

namespace App\Console\Commands;

use App\Jobs\CrawlRegulatorySourcesJob;
use App\Models\RegulatoryChange;
use Illuminate\Console\Command;

/**
 * P7 — Artisan command to manually trigger regulatory crawl.
 */
class CrawlRegulatoryChanges extends Command
{
    protected $signature = 'regulatory:crawl {--dry-run : Preview without dispatching jobs}';

    protected $description = 'Crawl regulatory sources and analyze changes with AI';

    public function handle(): int
    {
        $total = RegulatoryChange::count();
        $this->info("Regulatory changes in DB: $total");

        if ($this->option('dry-run')) {
            $this->warn('Dry-run: job dispatch skipped.');

            return 0;
        }

        CrawlRegulatorySourcesJob::dispatch();
        $this->info('CrawlRegulatorySourcesJob dispatched to default queue.');

        return 0;
    }
}
