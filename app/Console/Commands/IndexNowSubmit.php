<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\IndexNowService;
use Illuminate\Console\Command;

class IndexNowSubmit extends Command
{
    protected $signature = 'seo:index-now
                            {--all : Submit all published article URLs}
                            {--recent=0 : Submit articles published within N days}
                            {--url= : Submit a specific URL}';

    protected $description = 'Submit URLs to search engines via IndexNow protocol for instant indexing';

    public function handle(IndexNowService $indexNow): int
    {
        if ($url = $this->option('url')) {
            $this->info("Submitting: {$url}");
            $result = $indexNow->submitUrl($url);
            $this->info($result ? 'Submitted successfully.' : 'Submission failed.');
            return $result ? 0 : 1;
        }

        $appUrl = config('app.url');
        $urls = [];

        // Always include key pages
        $urls[] = $appUrl;
        $urls[] = $appUrl . '/blog';

        if ($this->option('all')) {
            $articles = Article::published()->get(['slug']);
            $this->info("Collecting all {$articles->count()} published article URLs...");
        } elseif ((int) $this->option('recent') > 0) {
            $days = (int) $this->option('recent');
            $articles = Article::published()
                ->where('created_at', '>=', now()->subDays($days))
                ->get(['slug']);
            $this->info("Collecting {$articles->count()} articles from last {$days} days...");
        } else {
            // Default: last 7 days
            $articles = Article::published()
                ->where('created_at', '>=', now()->subDays(7))
                ->get(['slug']);
            $this->info("Collecting {$articles->count()} articles from last 7 days...");
        }

        foreach ($articles as $article) {
            $urls[] = $appUrl . '/blog/' . $article->slug;
        }

        // Add category pages
        $categories = Article::published()
            ->select('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $cat) {
            $urls[] = $appUrl . '/blog/kategori/' . $cat;
        }

        $urls = array_unique($urls);
        $this->info("Submitting " . count($urls) . " URLs to IndexNow...");

        $bar = $this->output->createProgressBar(count($urls));

        // Submit in batches of 100
        $chunks = array_chunk($urls, 100);
        $success = 0;

        foreach ($chunks as $chunk) {
            if ($indexNow->submitBatch($chunk)) {
                $success += count($chunk);
            }
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$success}/" . count($urls) . " URLs submitted.");

        return 0;
    }
}
