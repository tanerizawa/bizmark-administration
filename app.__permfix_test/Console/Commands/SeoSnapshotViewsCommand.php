<?php

namespace App\Console\Commands;

use App\Services\SeoReportService;
use Illuminate\Console\Command;

class SeoSnapshotViewsCommand extends Command
{
    protected $signature = 'seo:snapshot-views';

    protected $description = 'Snapshot daily article views for trend analysis';

    public function handle(SeoReportService $service): int
    {
        $count = $service->snapshotDailyViews();
        $this->info("📸 View snapshot complete: {$count} articles recorded");

        return self::SUCCESS;
    }
}
