<?php

namespace App\Notifications;

use App\Models\ApplicationRevision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageRevisionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ApplicationRevision $revision) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->revision->application;

        return (new MailMessage)
            ->subject('Revisi Paket Layanan - ' . ($application?->application_number ?? ''))
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Admin telah membuat revisi paket layanan untuk permohonan Anda.')
            ->line('**Nomor Permohonan:** ' . ($application?->application_number ?? '-'))
            ->line('**Alasan Revisi:** ' . ($this->revision->revision_reason ?? '-'))
            ->action('Lihat Revisi', url('/client/applications/' . ($application?->id ?? '')))
            ->line('Silakan review dan berikan persetujuan Anda.')
            ->salutation('Salam, Tim Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'package_revision_created',
            'revision_id' => $this->revision->id,
            'application_id' => $this->revision->application_id,
            'application_number' => $this->revision->application?->application_number,
            'message' => 'Revisi paket layanan baru menunggu persetujuan Anda.',
        ];
    }
}
