<?php

namespace App\Notifications;

use App\Models\PermitApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(PermitApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Permohonan Izin Berhasil Dikirim - ' . $this->application->application_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Terima kasih telah mengajukan permohonan izin melalui Bizmark.id')
            ->line('**Nomor Permohonan:** ' . $this->application->application_number)
            ->line('**Jenis Izin:** ' . $this->application->permitType->name)
            ->line('**Tanggal Pengajuan:** ' . $this->application->submitted_at->format('d F Y H:i'))
            ->line('Permohonan Anda sedang dalam proses review oleh tim kami. Kami akan mengirimkan quotation dalam waktu 1-2 hari kerja.')
            ->action('Lihat Detail Permohonan', url('/client/applications/' . $this->application->id))
            ->line('Jika ada pertanyaan, silakan hubungi kami.')
            ->salutation('Salam, Tim Bizmark.id');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_submitted',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'permit_type' => $this->application->permitType->name,
            'message' => 'Permohonan izin ' . $this->application->application_number . ' berhasil dikirim',
        ];
    }
}