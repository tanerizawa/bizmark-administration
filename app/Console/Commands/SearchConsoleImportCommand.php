<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SearchConsoleService;

class SearchConsoleImportCommand extends Command
{
    protected $signature = 'seo:gsc-import {--days=7 : Days of data to import}';
    protected $description = 'Import Google Search Console data (simulated until API credentials configured)';

    public function handle(SearchConsoleService $service): int
    {
        $days = (int) $this->option('days');
        $this->info("📊 Importing Search Console data ({$days} days)...");

        $result = $service->importFromGSC($days);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Records Imported', $result['imported']],
                ['Data Source', $result['source']],
            ]
        );

        // Show quick summary
        $summary = $service->getSummary($days);
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Clicks', number_format($summary['total_clicks'])],
                ['Total Impressions', number_format($summary['total_impressions'])],
                ['Avg CTR', $summary['avg_ctr'] . '%'],
                ['Avg Position', $summary['avg_position']],
                ['Unique Queries', $summary['unique_queries']],
                ['Unique Pages', $summary['unique_pages']],
            ]
        );

        // Show opportunities
        $opportunities = $service->getOpportunities($days);
        if ($opportunities->count() > 0) {
            $this->newLine();
            $this->info('🎯 Quick Win Opportunities (high impressions, low CTR):');
            $rows = $opportunities->take(5)->map(fn($o) => [
                \Illuminate\Support\Str::limit($o->query, 40),
                $o->total_impressions,
                $o->total_clicks,
                round($o->avg_ctr, 1) . '%',
                round($o->avg_position, 1),
            ])->toArray();

            $this->table(['Query', 'Impressions', 'Clicks', 'CTR', 'Position'], $rows);
        }

        $this->info("✅ GSC import complete");
        return 0;
    }
}
