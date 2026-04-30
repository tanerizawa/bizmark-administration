<?php

namespace App\Listeners;

use App\Events\ArticlePublishedEvent;
use App\Services\SocialCaptionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateSocialCaptions implements ShouldQueue
{
    public int $delay = 60; // 1 minute after publish

    public function __construct(
        protected SocialCaptionService $captionService
    ) {}

    public function handle(ArticlePublishedEvent $event): void
    {
        if (! $event->isNew) {
            return;
        }

        try {
            $captions = $this->captionService->generateAll($event->article);

            // Cache captions for admin dashboard (7 days)
            Cache::put(
                "social_captions:{$event->article->id}",
                $captions,
                now()->addDays(7)
            );

            Log::info('Social captions generated', [
                'article' => $event->article->id,
                'platforms' => array_keys($captions),
            ]);
        } catch (\Exception $e) {
            Log::error('Social caption generation failed', [
                'article' => $event->article->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
