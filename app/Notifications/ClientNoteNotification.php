<?php

namespace App\Notifications;

use App\Models\ApplicationNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientNoteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private ApplicationNote $note)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->note->application;
        $client = $application->client;
        $preview = \Str::limit($this->note->note, 100);
        
        return (new MailMessage)
            ->subject('Balasan Klien - ' . $application->application_number)
            ->greeting('Halo Admin,')
            ->line('Klien mengirim balasan pada permohonan izin:')
            ->line('**Nomor Permohonan:** ' . $application->application_number)
            ->line('**Klien:** ' . $client->name)
            ->line('**Pesan:**')
            ->line('"' . $preview . '"')
            ->action('Lihat Permohonan', route('admin.permit-applications.show', $application->id))
            ->line('Segera tindaklanjuti jika diperlukan.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'note_id' => $this->note->id,
            'application_id' => $this->note->application_id,
            'application_number' => $this->note->application->application_number,
            'client_name' => $this->note->application->client->name,
            'note_preview' => \Str::limit($this->note->note, 100),
            'url' => route('admin.permit-applications.show', $this->note->application_id),
        ];
    }
}