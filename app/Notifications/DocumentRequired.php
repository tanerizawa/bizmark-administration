<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DocumentRequired extends Notification
{
    use Queueable;

    protected $application;
    protected $documentName;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $documentName = null)
    {
        $this->application = $application;
        $this->documentName = $documentName;
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
        $message = $this->documentName 
            ? "Dokumen diperlukan: {$this->documentName}"
            : "Dokumen tambahan diperlukan";

        return (new WebPushMessage)
            ->title('📄 Dokumen Diperlukan')
            ->body("{$this->application->application_number}: {$message}")
            ->icon('/icons/icon-192x192.png')
            ->badge('/icons/badge-72x72.png')
            ->data([
                'url' => route('client.applications.show', $this->application->id),
                'application_id' => $this->application->id,
                'document' => $this->documentName
            ])
            ->tag('document-required-' . $this->application->id)
            ->requireInteraction(true)
            ->vibrate([200, 100, 200, 100, 200]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $message = $this->documentName
            ? "Dokumen {$this->documentName} diperlukan untuk izin {$this->application->application_number}"
            : "Dokumen tambahan diperlukan untuk izin {$this->application->application_number}";

        return [
            'type' => 'document_required',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'document_name' => $this->documentName,
            'message' => $message,
            'url' => route('client.applications.show', $this->application->id)
        ];
    }
}
