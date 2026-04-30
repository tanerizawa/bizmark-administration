<?php

namespace App\Console\Commands;

use App\Services\SmartMetaOptimizerService;
use Illuminate\Console\Command;

class MetaOptimizeCommand extends Command
{
    protected $signature = 'seo:optimize-meta
        {--limit=5 : Number of articles to optimize}
        {--article= : Optimize a specific article by ID}
        {--language=id : Language (id/en)}';

    protected $description = 'AI-powered meta tag optimization for articles';

    public function handle(SmartMetaOptimizerService $optimizer): int
    {
        try {
        if ($articleId = $this->option('article')) {
            $article = \App\Models\Article::find($articleId);
            if (!$article) {
                $this->error("Article #{$articleId} not found.");
                return 1;
            }

            $this->info("🔧 Optimizing meta for: {$article->title}");
            $result = $optimizer->optimizeArticle($article);

            if ($result['status'] === 'optimized') {
                $this->info("✓ Optimized: " . implode(', ', $result['changes']));
            } else {
                $this->warn("Status: {$result['status']}");
            }

            return 0;
        }

        $limit = (int) $this->option('limit');
        $language = $this->option('language');

        $this->info("🔧 Optimizing meta tags for {$limit} articles...");
        $results = $optimizer->optimizeBatch($limit, $language);

        $tableData = collect($results)->map(fn($r) => [
            $r['article_id'],
            mb_substr($r['title'], 0, 40),
            $r['status'],
            implode(', ', $r['changes'] ?? []),
        ])->toArray();

        $this->table(['ID', 'Title', 'Status', 'Changes'], $tableData);

        $optimized = collect($results)->where('status', 'optimized')->count();
        $this->info("Done. Optimized: {$optimized}/{$limit}");

        return 0;
        } catch (\Exception $e) {
            \Log::error('Meta optimization failed: ' . $e->getMessage());
            $this->error('Optimization failed: ' . $e->getMessage());
            return 1;
        }
    }
}
