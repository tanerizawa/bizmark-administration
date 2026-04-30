<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\ContentSyndicationService;
use Illuminate\Console\Command;

class ContentSyndicateCommand extends Command
{
    protected $signature = 'content:syndicate
        {--limit=5 : Max articles to syndicate}
        {--article= : Specific article ID}';

    protected $description = 'Syndicate articles to external platforms (Medium, LinkedIn, Dev.to)';

    public function handle(ContentSyndicationService $service): int
    {
        $this->info('🚀 Content Syndication Engine');
        $this->newLine();

        if ($articleId = $this->option('article')) {
            $article = Article::find($articleId);
            if (! $article) {
                $this->error("Article #{$articleId} not found");

                return self::FAILURE;
            }

            $this->info("Syndicating: {$article->title}");
            $results = $service->syndicateArticle($article);
            $this->displayResults($article, $results);

            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $this->info("Syndicating up to {$limit} unsyndicated articles...");
        $this->newLine();

        $results = $service->syndicateBatch($limit);

        if (empty($results)) {
            $this->warn('No articles to syndicate (all recent articles already syndicated)');

            return self::SUCCESS;
        }

        foreach ($results as $articleId => $platformResults) {
            $article = Article::find($articleId);
            if ($article) {
                $this->displayResults($article, $platformResults);
            }
        }

        $this->newLine();
        $this->info('✅ Syndication complete');

        return self::SUCCESS;
    }

    protected function displayResults(Article $article, array $results): void
    {
        $this->info("📄 {$article->title}");
        foreach ($results as $platform => $result) {
            $status = $result['status'] ?? 'unknown';
            $icon = $status === 'published' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
            $extra = $result['url'] ?? $result['message'] ?? $result['error'] ?? '';
            $this->line("   {$icon} {$platform}: {$status} {$extra}");
        }
        $this->newLine();
    }
}
