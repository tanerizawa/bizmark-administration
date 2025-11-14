<?php

namespace App\Notifications;

use App\Models\PermitApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public function __construct(PermitApplication $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Aplikasi Perizinan Baru - ' . $this->application->application_number)
            ->greeting('Halo Admin,')
            ->line('Aplikasi perizinan baru telah diterima.')
            ->line('**Nomor Aplikasi:** ' . $this->application->application_number)
            ->line('**Klien:** ' . $this->application->client->name)
            ->line('**Jenis Perizinan:** ' . $this->application->permitType->name)
            ->action('Review Aplikasi', url('/applications/' . $this->application->id))
            ->salutation('Sistem Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'client_name' => $this->application->client->name,
            'permit_type' => $this->application->permitType->name,
        ];
    }
}