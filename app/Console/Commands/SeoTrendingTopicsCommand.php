<?php

namespace App\Console\Commands;

use App\Services\TrendingTopicService;
use Illuminate\Console\Command;

class SeoTrendingTopicsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'seo:trending-topics
        {--category= : Specific category to discover (umkm, perizinan, legal, marketing, technology, finance)}
        {--cleanup : Clean up expired topics instead of discovering}
        {--convert : Convert high-priority trending topics to article topics}
        {--min-score=60 : Minimum trend score for conversion (used with --convert)}
        {--limit=5 : Max topics to convert (used with --convert)}
        {--summary : Show trending topics summary only}';

    /**
     * The console command description.
     */
    protected $description = 'Discover trending topics via SearXNG for content generation';

    /**
     * Execute the console command.
     */
    public function handle(TrendingTopicService $service): int
    {
        // Summary mode
        if ($this->option('summary')) {
            return $this->showSummary($service);
        }

        // Cleanup mode
        if ($this->option('cleanup')) {
            return $this->cleanupExpired($service);
        }

        // Convert mode
        if ($this->option('convert')) {
            return $this->convertToArticleTopics($service);
        }

        // Discovery mode
        return $this->discoverTopics($service);
    }

    /**
     * Discover trending topics
     */
    protected function discoverTopics(TrendingTopicService $service): int
    {
        $category = $this->option('category');

        if ($category) {
            $this->info("Discovering trending topics for category: {$category}");
        } else {
            $this->info('Discovering trending topics across all categories...');
        }

        $startTime = now();

        try {
            if ($category) {
                // Single category discovery
                $seeds = $this->getCategorySeeds($category);
                if (empty($seeds)) {
                    $this->error("Unknown category: {$category}");
                    return self::FAILURE;
                }

                $topics = $service->discoverForCategory($category, $seeds);
                $summary = [
                    'discovered' => count($topics),
                    'categories' => [$category => count($topics)],
                    'errors' => [],
                ];
            } else {
                // All categories
                $summary = $service->discoverTrendingTopics();
            }

            $duration = now()->diffInSeconds($startTime);

            // Display results
            $this->newLine();

            if ($summary['discovered'] > 0) {
                $this->info("✓ Discovered {$summary['discovered']} trending topics in {$duration}s");

                $this->table(
                    ['Category', 'Topics Found'],
                    collect($summary['categories'])
                        ->map(fn($count, $cat) => [$cat, $count])
                        ->values()
                        ->toArray()
                );
            } else {
                $this->warn('No new trending topics discovered.');
            }

            if (!empty($summary['errors'])) {
                $this->newLine();
                $this->warn('Errors encountered:');
                foreach ($summary['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }

            // Show summary
            $this->newLine();
            $this->showSummary($service);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("Discovery failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Show trending topics summary
     */
    protected function showSummary(TrendingTopicService $service): int
    {
        $summary = $service->getTrendingSummary();

        $this->info('📊 Trending Topics Summary');
        $this->newLine();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Active', $summary['total_active']],
                ['Unprocessed', $summary['unprocessed']],
                ['High Priority (70+)', $summary['high_priority']],
                ['Recent (7 days)', $summary['recent_7d']],
            ]
        );

        if (!empty($summary['by_category'])) {
            $this->newLine();
            $this->info('By Category:');
            $this->table(
                ['Category', 'Count', 'Avg Score'],
                collect($summary['by_category'])
                    ->map(fn($data, $cat) => [
                        $cat,
                        $data['count'],
                        round($data['avg_score'], 1),
                    ])
                    ->values()
                    ->toArray()
            );
        }

        if (!empty($summary['top_topics'])) {
            $this->newLine();
            $this->info('Top 5 Unprocessed Topics:');
            $this->table(
                ['Topic', 'Category', 'Score', 'Discovered'],
                collect($summary['top_topics'])
                    ->map(fn($t) => [
                        \Illuminate\Support\Str::limit($t['topic'], 40),
                        $t['category'],
                        $t['trend_score'],
                        \Carbon\Carbon::parse($t['discovered_at'])->diffForHumans(),
                    ])
                    ->toArray()
            );
        }

        return self::SUCCESS;
    }

    /**
     * Clean up expired topics
     */
    protected function cleanupExpired(TrendingTopicService $service): int
    {
        $this->info('Cleaning up expired trending topics...');

        $deleted = $service->cleanupExpired();

        if ($deleted > 0) {
            $this->info("✓ Deleted {$deleted} expired topics.");
        } else {
            $this->info('No expired topics to clean up.');
        }

        return self::SUCCESS;
    }

    /**
     * Convert trending topics to article topics for auto-posting
     */
    protected function convertToArticleTopics(TrendingTopicService $service): int
    {
        $limit = (int) $this->option('limit');
        $minScore = (int) $this->option('min-score');

        $this->info("Converting trending topics to article topics (min score: {$minScore}, limit: {$limit})...");

        $result = $service->convertToArticleTopics($limit, $minScore);

        if ($result['converted'] > 0) {
            $this->info("✓ Converted {$result['converted']} trending topics to article topics.");

            $this->table(
                ['Trending ID', 'Topic ID', 'Title', 'Score', 'Priority'],
                collect($result['topics'])
                    ->map(fn($t) => [
                        $t['trending_id'],
                        $t['article_topic_id'],
                        \Illuminate\Support\Str::limit($t['title'], 50),
                        $t['score'],
                        $t['priority'],
                    ])
                    ->toArray()
            );
        } else {
            $this->warn('No trending topics eligible for conversion.');
        }

        if ($result['skipped'] > 0) {
            $this->line("  Skipped {$result['skipped']} (similar topics already exist).");
        }

        return self::SUCCESS;
    }

    /**
     * Get category seed keywords
     */
    protected function getCategorySeeds(string $category): array
    {
        $seeds = [
            'umkm' => [
                'UMKM terbaru 2025',
                'bisnis kecil Indonesia',
                'bantuan usaha kecil',
                'modal usaha UMKM',
                'digitalisasi UMKM',
            ],
            'perizinan' => [
                'perizinan usaha terbaru',
                'NIB OSS',
                'peraturan izin usaha',
                'izin usaha online',
                'kemudahan berusaha',
            ],
            'legal' => [
                'hukum bisnis Indonesia',
                'peraturan perusahaan',
                'PT perseroan perorangan',
                'akta notaris usaha',
                'legalitas usaha',
            ],
            'marketing' => [
                'digital marketing Indonesia',
                'strategi pemasaran UMKM',
                'social media marketing',
                'e-commerce Indonesia',
                'marketplace seller',
            ],
            'technology' => [
                'teknologi bisnis',
                'aplikasi usaha',
                'AI untuk bisnis',
                'software usaha kecil',
                'transformasi digital',
            ],
            'finance' => [
                'pajak UMKM 2025',
                'kredit usaha rakyat',
                'fintech Indonesia',
                'akuntansi usaha kecil',
                'pinjaman modal usaha',
            ],
        ];

        return $seeds[$category] ?? [];
    }
}
