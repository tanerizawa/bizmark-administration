<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class PermitStatusUpdated extends Notification
{
    use Queueable;

    protected $application;

    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
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
        $statusText = $this->getStatusText();
        
        return (new WebPushMessage)
            ->title('📋 Status Izin Diperbarui')
            ->body("Izin {$this->application->application_number}: {$statusText}")
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/badge-72x72.png')
            ->data([
                'url' => route('client.applications.show', $this->application->id),
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'status' => $this->application->status
            ])
            ->tag('permit-status-' . $this->application->id)
            ->requireInteraction(true)
            ->vibrate([200, 100, 200]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'permit_status_updated',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'status' => $this->application->status,
            'message' => "Status izin {$this->application->application_number} telah diperbarui menjadi: " . $this->getStatusText(),
            'url' => route('client.applications.show', $this->application->id)
        ];
    }

    /**
     * Get human-readable status text
     */
    private function getStatusText()
    {
        $statusMap = [
            'pending' => 'Menunggu Review',
            'in_review' => 'Sedang Direview',
            'approved' => 'Disetujui ✅',
            'rejected' => 'Ditolak ❌',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $statusMap[$this->application->status] ?? $this->application->status;
    }
}
