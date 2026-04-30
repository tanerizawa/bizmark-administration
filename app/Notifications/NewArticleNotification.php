<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewArticleNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Article $article
    ) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $title = $this->article->title;
        $body = $this->article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($this->article->content), 120);
        $url = config('app.url').'/blog/'.$this->article->slug;

        return (new WebPushMessage)
            ->title("📄 Artikel Baru: {$title}")
            ->body($body)
            ->icon('/images/favicon.svg')
            ->badge('/icons/badge-72x72.png')
            ->data([
                'url' => $url,
                'type' => 'new_article',
                'article_id' => $this->article->id,
            ])
            ->tag('article-'.$this->article->id)
            ->vibrate([200, 100, 200])
            ->requireInteraction(false);
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_article',
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'url' => config('app.url').'/blog/'.$this->article->slug,
        ];
    }
}
