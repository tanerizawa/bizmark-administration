<?php

namespace App\Console\Commands;

use App\Services\ContentRefreshService;
use Illuminate\Console\Command;

class ContentRefreshCommand extends Command
{
    protected $signature = 'seo:refresh-content
                            {--older-than=90 : Refresh articles older than N days}
                            {--limit=2 : Max articles to refresh per run}
                            {--dry-run : Show which articles would be refreshed without making changes}';

    protected $description = 'AI-powered content refresh for stale articles to boost freshness signals';

    public function handle(ContentRefreshService $service): int
    {
        $days = (int) $this->option('older-than');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info("Finding articles not updated in {$days}+ days...");

        $staleArticles = $service->getStaleArticles($days, $limit);

        if ($staleArticles->isEmpty()) {
            $this->info('No stale articles found. All content is fresh!');

            return 0;
        }

        $this->info("Found {$staleArticles->count()} stale articles.");

        if ($dryRun) {
            $this->table(
                ['ID', 'Title', 'Last Updated', 'Days Stale'],
                $staleArticles->map(fn ($a) => [
                    $a->id,
                    mb_substr($a->title, 0, 50),
                    $a->updated_at->format('Y-m-d'),
                    $a->updated_at->diffInDays(now()),
                ])->toArray()
            );

            return 0;
        }

        $refreshed = 0;
        $errors = 0;

        foreach ($staleArticles as $article) {
            $this->line("Refreshing: [{$article->id}] {$article->title}");

            $result = $service->refreshArticle($article);

            if ($result['status'] === 'refreshed') {
                $this->info('  ✓ Refreshed: '.implode(', ', $result['changes']));
                $refreshed++;
            } elseif ($result['status'] === 'error') {
                $this->error('  ✗ Error: '.($result['error'] ?? 'unknown'));
                $errors++;
            } else {
                $this->warn('  - No changes needed');
            }
        }

        $this->newLine();
        $this->info("Done. Refreshed: {$refreshed}, Errors: {$errors}, Total: {$staleArticles->count()}");

        return $errors > 0 ? 1 : 0;
    }
}
