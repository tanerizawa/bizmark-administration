<?php

namespace App\Console\Commands;

use App\Services\SeoScoringService;
use Illuminate\Console\Command;

class SeoScoreArticlesCommand extends Command
{
    protected $signature = 'seo:score-articles
        {--limit=0 : Limit articles to score (0=all)}
        {--article= : Score specific article ID}';

    protected $description = 'Run SEO audit scoring on published articles (0-100)';

    public function handle(SeoScoringService $service): int
    {
        $this->info('🎯 SEO Article Scoring Engine');
        $this->newLine();

        if ($articleId = $this->option('article')) {
            $article = \App\Models\Article::find($articleId);
            if (! $article) {
                $this->error("Article #{$articleId} not found");

                return self::FAILURE;
            }

            $score = $service->scoreArticle($article);
            $this->info("📄 {$article->title}");
            $this->info("   Score: {$score->total_score}/100 (Grade: {$score->grade})");
            $this->newLine();

            foreach ($score->factors as $key => $factor) {
                $icon = $factor['score'] >= $factor['max'] * 0.7 ? '✅' : '⚠️';
                $this->line("   {$icon} {$key}: {$factor['score']}/{$factor['max']}");
            }

            if (! empty($score->recommendations)) {
                $this->newLine();
                $this->info('   💡 Rekomendasi:');
                foreach ($score->recommendations as $rec) {
                    $this->line("      • {$rec}");
                }
            }

            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $this->info('Scoring '.($limit > 0 ? "{$limit}" : 'all').' published articles...');

        $results = $service->scoreAll($limit);

        $this->newLine();
        $this->table(
            ['ID', 'Title', 'Score', 'Grade'],
            collect($results['scores'])->map(fn ($s) => [
                $s['id'],
                \Illuminate\Support\Str::limit($s['title'], 50),
                $s['score'],
                $s['grade'],
            ])->toArray()
        );

        $this->newLine();
        $this->info("✅ Scored {$results['scored']} articles | Average: {$results['avg_score']}/100");

        return self::SUCCESS;
    }
}
