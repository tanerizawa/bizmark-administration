<?php

namespace App\Listeners;

use App\Events\ArticlePublishedEvent;
use App\Models\User;
use App\Notifications\NewArticleNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendArticlePushNotification
{
    public function handle(ArticlePublishedEvent $event): void
    {
        if (! $event->isNew) {
            return;
        }

        try {
            // Get all users with push subscriptions
            $users = User::whereHas('pushSubscriptions')->get();

            if ($users->isEmpty()) {
                Log::info('No push subscribers for article notification', ['article' => $event->article->id]);

                return;
            }

            Notification::send($users, new NewArticleNotification($event->article));

            Log::info('Push notifications sent for article', [
                'article' => $event->article->id,
                'recipients' => $users->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Push notification failed', [
                'article' => $event->article->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
