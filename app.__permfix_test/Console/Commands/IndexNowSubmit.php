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
                            {--url= : Submit a specific URL}
                            {--pseo : Submit all pSEO city×service pages}';

    protected $description = 'Submit URLs to search engines via IndexNow protocol for instant indexing';

    public function handle(IndexNowService $indexNow): int
    {
        if ($url = $this->option('url')) {
            $this->info("Submitting: {$url}");
            $result = $indexNow->submitUrl($url);
            $this->info($result ? 'Submitted successfully.' : 'Submission failed.');

            return $result ? 0 : 1;
        }

        // pSEO pages mode
        if ($this->option('pseo')) {
            return $this->submitPseoPages($indexNow);
        }

        $appUrl = config('app.url');
        $urls = [];

        // Always include key pages
        $urls[] = $appUrl;
        $urls[] = $appUrl.'/blog';

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
            $urls[] = $appUrl.'/blog/'.$article->slug;
        }

        // Add category pages
        $categories = Article::published()
            ->select('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $cat) {
            $urls[] = $appUrl.'/blog/kategori/'.$cat;
        }

        $urls = array_unique($urls);
        $this->info('Submitting '.count($urls).' URLs to IndexNow...');

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
        $this->info("Done. {$success}/".count($urls).' URLs submitted.');

        return 0;
    }

    /**
     * Submit all pSEO (city×service) pages to IndexNow
     */
    protected function submitPseoPages(IndexNowService $indexNow): int
    {
        $appUrl = config('app.url');
        $cities = config('programmatic_seo.cities', []);
        $serviceKeys = config('programmatic_seo.services', []);
        $urls = [];

        foreach ($cities as $citySlug => $city) {
            // City index page
            $urls[] = $appUrl.'/layanan/kota/'.$citySlug;

            // Service × city combos
            foreach ($serviceKeys as $serviceSlug) {
                $urls[] = $appUrl.'/layanan/'.$serviceSlug.'/'.$citySlug;
            }
        }

        $this->info('Submitting '.count($urls).' pSEO pages ('.count($cities).' cities × '.count($serviceKeys).' services) to IndexNow...');

        $bar = $this->output->createProgressBar(count($urls));
        $success = 0;

        $chunks = array_chunk($urls, 100);
        foreach ($chunks as $chunk) {
            if ($indexNow->submitBatch($chunk)) {
                $success += count($chunk);
            }
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$success}/".count($urls).' pSEO URLs submitted.');

        return 0;
    }
}
