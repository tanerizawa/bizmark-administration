<?php

namespace App\Notifications;

use App\Models\ApplicationNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNoteNotification extends Notification implements ShouldQueue
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
        $preview = \Str::limit($this->note->note, 100);
        
        return (new MailMessage)
            ->subject('Pesan Baru dari Admin - ' . $application->application_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda menerima pesan baru dari admin terkait permohonan izin Anda:')
            ->line('**Nomor Permohonan:** ' . $application->application_number)
            ->line('**Dari:** ' . $this->note->author->name)
            ->line('**Pesan:**')
            ->line('"' . $preview . '"')
            ->action('Lihat Permohonan', route('client.applications.show', $application->id))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'note_id' => $this->note->id,
            'application_id' => $this->note->application_id,
            'application_number' => $this->note->application->application_number,
            'author_name' => $this->note->author->name,
            'note_preview' => \Str::limit($this->note->note, 100),
            'url' => route('client.applications.show', $this->note->application_id),
        ];
    }
}