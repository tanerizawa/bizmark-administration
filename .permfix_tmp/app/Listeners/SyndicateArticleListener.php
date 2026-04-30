<?php

namespace App\Listeners;

use App\Events\ArticlePublishedEvent;
use App\Services\ContentSyndicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SyndicateArticleListener implements ShouldQueue
{
    public int $delay = 300; // 5 minutes after publish

    public function __construct(
        protected ContentSyndicationService $syndicationService
    ) {}

    public function handle(ArticlePublishedEvent $event): void
    {
        if (!$event->isNew) {
            return;
        }

        try {
            $results = $this->syndicationService->syndicateArticle($event->article);

            Log::info('Article syndication completed', [
                'article' => $event->article->id,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Article syndication failed', [
                'article' => $event->article->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
