<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DeadlineReminder extends Notification
{
    use Queueable;

    protected $application;
    protected $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $daysRemaining = null)
    {
        $this->application = $application;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $message = $this->daysRemaining 
            ? "Deadline dalam {$this->daysRemaining} hari"
            : "Deadline mendekati";

        return (new WebPushMessage)
            ->title('⏰ Pengingat Deadline')
            ->body("{$this->application->application_number}: {$message}")
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/badge-72x72.png')
            ->data([
                'url' => route('client.applications.show', $this->application->id),
                'application_id' => $this->application->id,
                'days_remaining' => $this->daysRemaining
            ])
            ->tag('deadline-' . $this->application->id)
            ->requireInteraction(true)
            ->vibrate([300, 100, 300]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $message = $this->daysRemaining
            ? "Deadline izin {$this->application->application_number} dalam {$this->daysRemaining} hari"
            : "Deadline izin {$this->application->application_number} sudah dekat";

        return [
            'type' => 'deadline_reminder',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'days_remaining' => $this->daysRemaining,
            'message' => $message,
            'url' => route('client.applications.show', $this->application->id)
        ];
    }
}
