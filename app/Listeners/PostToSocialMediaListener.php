<?php

namespace App\Listeners;

use App\Events\ArticlePublishedEvent;
use App\Services\SocialPostingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PostToSocialMediaListener implements ShouldQueue
{
    public int $delay = 600; // 10 minutes after publish (after syndication)

    public function __construct(
        protected SocialPostingService $postingService
    ) {}

    public function handle(ArticlePublishedEvent $event): void
    {
        if (!$event->isNew) {
            return;
        }

        try {
            $results = $this->postingService->postToAll($event->article);

            $posted = collect($results)->where('status', 'posted')->count();
            $pending = collect($results)->where('status', 'pending')->count();

            Log::info('Social media posting completed', [
                'article' => $event->article->id,
                'posted' => $posted,
                'pending' => $pending,
            ]);
        } catch (\Exception $e) {
            Log::error('Social media posting failed', [
                'article' => $event->article->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
