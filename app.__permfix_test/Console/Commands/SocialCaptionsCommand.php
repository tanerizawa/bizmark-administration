<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\SocialCaptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SocialCaptionsCommand extends Command
{
    protected $signature = 'content:social-captions
        {--article= : Specific article ID}
        {--limit=3 : Max articles to generate captions for}
        {--platform= : Show only specific platform caption}';

    protected $description = 'Generate AI-powered social media captions for articles';

    public function handle(SocialCaptionService $service): int
    {
        $this->info('📱 Social Caption Generator');
        $this->newLine();

        if ($articleId = $this->option('article')) {
            $article = Article::find($articleId);
            if (! $article) {
                $this->error("Article #{$articleId} not found");

                return self::FAILURE;
            }

            return $this->generateForArticle($service, $article);
        }

        // Batch: generate for recent articles without cached captions
        $limit = (int) $this->option('limit');
        $articles = Article::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit($limit * 3)
            ->get()
            ->filter(fn ($a) => ! Cache::has("social_captions:{$a->id}"))
            ->take($limit);

        if ($articles->isEmpty()) {
            $this->warn('All recent articles already have cached captions');

            return self::SUCCESS;
        }

        foreach ($articles as $article) {
            $this->generateForArticle($service, $article);
            $this->newLine();
        }

        $this->info('✅ Social captions generated');

        return self::SUCCESS;
    }

    protected function generateForArticle(SocialCaptionService $service, Article $article): int
    {
        $this->info("📄 {$article->title}");
        $this->line("   ID: {$article->id} | Views: {$article->views_count}");
        $this->newLine();

        $captions = $service->generateAll($article);

        // Cache for dashboard
        Cache::put("social_captions:{$article->id}", $captions, now()->addDays(7));

        $platform = $this->option('platform');

        foreach ($captions as $platformName => $caption) {
            if ($platform && $platformName !== $platform) {
                continue;
            }

            $this->line('   ━━━ '.strtoupper($platformName).' ━━━');
            // Truncate display for readability
            $display = mb_strlen($caption) > 300
                ? mb_substr($caption, 0, 300).'...'
                : $caption;
            $this->line('   '.str_replace("\n", "\n   ", $display));
            $this->newLine();
        }

        // Show schedule info
        $this->info('   ⏰ Optimal posting times:');
        $schedule = $service->getOptimalSchedule();
        foreach ($schedule as $p => $times) {
            if ($platform && $p !== $platform) {
                continue;
            }
            $this->line("      {$p}: ".implode(', ', $times).' WIB');
        }

        return self::SUCCESS;
    }
}
