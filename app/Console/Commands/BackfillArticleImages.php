<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\PexelsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillArticleImages extends Command
{
    protected $signature = 'articles:backfill-images
        {--limit=0 : Max articles to process (0 = all)}
        {--dry-run : Preview without downloading}';

    protected $description = 'Fetch featured images from Pexels for articles that have none';

    public function handle(PexelsService $pexels): int
    {
        // 1. Articles with no featured_image in DB
        $query = Article::where(function ($q) {
            $q->whereNull('featured_image')->orWhere('featured_image', '');
        })->orderBy('id');

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $articles = $query->get();

        // 2. Articles where DB has path but file is missing on disk
        $orphaned = Article::whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->orderBy('id')
            ->get()
            ->filter(function (Article $article) {
                return ! \Storage::disk('public')->exists($article->featured_image);
            });

        if ($orphaned->isNotEmpty()) {
            $this->warn("Found {$orphaned->count()} articles with missing image files on disk.");
            // Clear the stale DB path so they are treated as needing a new image
            foreach ($orphaned as $article) {
                $article->update(['featured_image' => null]);
            }
            // Merge into the articles collection
            $articles = $articles->merge($orphaned)->unique('id');
            if ($limit > 0) {
                $articles = $articles->take($limit);
            }
        }

        if ($articles->isEmpty()) {
            $this->info('All articles already have featured images.');

            return 0;
        }

        $this->info("Found {$articles->count()} articles without featured image.");
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN — no images will be downloaded.');
        }

        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($articles as $article) {
            $queries = $this->buildSearchQueries($article);

            if ($dryRun) {
                $this->newLine();
                $this->line("  #{$article->id} \"{$article->title}\" → queries: ".implode(' | ', $queries));
                $bar->advance();

                continue;
            }

            $imagePath = $this->searchAndDownload($pexels, $queries);

            if ($imagePath) {
                $article->update(['featured_image' => $imagePath]);
                $success++;
            } else {
                $this->newLine();
                $this->warn("  #{$article->id}: no image found for \"{$article->title}\"");
                $failed++;
            }

            $bar->advance();

            // Respect Pexels rate limit (200 req/hour ≈ 1 req/18s, conservative)
            usleep(500_000); // 0.5s between articles
        }

        $bar->finish();
        $this->newLine(2);

        if (! $dryRun) {
            $this->info("Done: {$success} images added, {$failed} not found.");
        }

        return 0;
    }

    private function buildSearchQueries(Article $article): array
    {
        $queries = [];

        // 1. Tags (most specific)
        $tags = $article->tags;
        if (is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        if (! empty($tags)) {
            $queries[] = implode(' ', array_slice((array) $tags, 0, 2));
        }

        // 2. Meta keywords (first 2-3)
        if (! empty($article->meta_keywords)) {
            $kw = array_map('trim', explode(',', $article->meta_keywords));
            $queries[] = implode(' ', array_slice($kw, 0, 2));
        }

        // 3. Cleaned title (remove years/numbers, keep 3 key words)
        $clean = preg_replace('/\b\d{4}\b/', '', $article->title);
        $clean = preg_replace('/\b\d+\b/', '', $clean);
        $clean = preg_replace('/[^\w\s]/u', '', $clean);
        $words = array_filter(explode(' ', trim($clean)));
        if (count($words) > 3) {
            $words = array_slice($words, 0, 3);
        }
        $queries[] = implode(' ', $words);

        // 4. Category fallback
        $categoryMap = [
            'tips' => 'business office professional',
            'regulation' => 'government legal document',
            'general' => 'business Indonesia office',
            'case-study' => 'business success team',
            'news' => 'business news Indonesia',
        ];
        $queries[] = $categoryMap[$article->category] ?? 'business professional office';

        return array_values(array_unique(array_filter($queries)));
    }

    private function searchAndDownload(PexelsService $pexels, array $queries): ?string
    {
        foreach ($queries as $query) {
            try {
                $results = $pexels->searchPhotos($query, 3, 1, [
                    'orientation' => 'landscape',
                    'size' => 'medium',
                ]);

                if (! empty($results['photos'])) {
                    $photo = $results['photos'][0];
                    $url = $photo['src']['large2x'] ?? $photo['src']['large'] ?? $photo['src']['original'];

                    return $pexels->downloadAndSavePhoto(
                        $url,
                        $photo['photographer'] ?? 'Unknown',
                        $photo['id']
                    );
                }
            } catch (\Exception $e) {
                Log::warning('Backfill image search failed', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }
}
