<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // Only use WebPush channel for test notifications
        // Database channel disabled to avoid UUID/BIGINT conflict
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('🔔 Test Notifikasi')
            ->body('Notifikasi push Anda berfungsi dengan baik! Sistem siap mengirimkan pembaruan penting.')
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/badge-72x72.png')
            ->data([
                'url' => route('client.dashboard'),
                'type' => 'test',
                'timestamp' => now()->toIso8601String()
            ])
            ->tag('test-notification-' . now()->timestamp)
            ->vibrate([200, 100, 200, 100, 200])
            ->requireInteraction(true)
            ->renotify(true);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'test_notification',
            'message' => 'Ini adalah notifikasi test. Sistem notifikasi Anda berfungsi dengan baik!',
            'timestamp' => now()->toIso8601String(),
            'url' => route('client.dashboard')
        ];
    }
}
