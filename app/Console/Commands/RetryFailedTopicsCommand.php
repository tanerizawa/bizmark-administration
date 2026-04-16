<?php

namespace App\Console\Commands;

use App\Models\ArticleTopic;
use Illuminate\Console\Command;

class RetryFailedTopicsCommand extends Command
{
    protected $signature = 'topics:retry-failed
        {--limit=10 : Maximum number of failed topics to retry}
        {--all : Retry all failed topics}
        {--dry-run : Show what would be retried without making changes}';

    protected $description = 'Reset failed ArticleTopics back to pending so they can be re-processed';

    public function handle(): int
    {
        $query = ArticleTopic::where('status', 'failed');
        $totalFailed = $query->count();

        if ($totalFailed === 0) {
            $this->info('No failed topics found.');
            return self::SUCCESS;
        }

        $this->info("Found {$totalFailed} failed topics.");

        $limit = $this->option('all') ? $totalFailed : (int) $this->option('limit');

        $topics = ArticleTopic::where('status', 'failed')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        if ($this->option('dry-run')) {
            $this->table(
                ['ID', 'Title', 'Category', 'Language', 'Failed At'],
                $topics->map(fn ($t) => [
                    $t->id,
                    mb_substr($t->title, 0, 50),
                    $t->category,
                    $t->language,
                    $t->updated_at->format('Y-m-d H:i'),
                ])
            );
            $this->info("[DRY RUN] Would retry {$topics->count()} topics.");
            return self::SUCCESS;
        }

        $retried = 0;
        foreach ($topics as $topic) {
            $topic->update([
                'status' => 'pending',
                'scheduled_for' => null,
                'generation_notes' => null,
            ]);
            $retried++;
        }

        $this->info("✅ Reset {$retried} failed topics back to pending.");
        $this->info('Remaining failed: ' . ArticleTopic::where('status', 'failed')->count());

        return self::SUCCESS;
    }
}
