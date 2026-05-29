<?php

namespace App\Notifications;

use App\Models\OssPermitStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class OssStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly OssPermitStatus $status,
        private readonly string $previousStatusLabel
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update Status OSS: '.$this->status->permit_type)
            ->greeting('Halo, '.($notifiable->name ?? 'Klien').'!')
            ->line('Ada perubahan status pada permohonan OSS Anda:')
            ->line('**Jenis Izin:** '.$this->status->permit_type)
            ->line('**Nomor Permohonan:** '.($this->status->application_number ?? '-'))
            ->line('**Status Sebelumnya:** '.$this->previousStatusLabel)
            ->line('**Status Terbaru:** '.$this->status->status_label)
            ->action('Lihat Detail di Portal', url('/client/oss-tracker'))
            ->line('Tim Bizmark siap membantu jika ada pertanyaan.');
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Status OSS Diperbarui')
            ->icon('/images/logo-192.png')
            ->body($this->status->permit_type.': '.$this->status->status_label)
            ->action('Lihat Detail', '/client/oss-tracker');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'oss_status_changed',
            'oss_permit_status_id' => $this->status->id,
            'permit_type' => $this->status->permit_type,
            'previous_status' => $this->previousStatusLabel,
            'new_status' => $this->status->status_label,
            'application_number' => $this->status->application_number,
        ];
    }
}
