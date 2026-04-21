<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminClientActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $action,
        protected string $applicationNumber,
        protected int $applicationId,
        protected string $clientName,
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = [
            'quotation_accepted' => 'Quotation Diterima',
            'quotation_rejected' => 'Quotation Ditolak',
            'revision_approved' => 'Revisi Paket Disetujui',
            'revision_rejected' => 'Revisi Paket Ditolak',
        ];

        $label = $labels[$this->action] ?? $this->action;

        $mail = (new MailMessage)
            ->subject("[Action Required] {$label} — {$this->applicationNumber}")
            ->greeting('Halo Admin,')
            ->line("Klien **{$this->clientName}** telah melakukan tindakan: **{$label}**")
            ->line("No. Aplikasi: **{$this->applicationNumber}**");

        if ($this->reason) {
            $mail->line("Alasan: {$this->reason}");
        }

        return $mail
            ->action('Lihat Aplikasi', url("/dashboard/applications/{$this->applicationId}"))
            ->line('Silakan tindak lanjuti sesuai kebutuhan.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'client_action',
            'action' => $this->action,
            'application_id' => $this->applicationId,
            'application_number' => $this->applicationNumber,
            'client_name' => $this->clientName,
            'reason' => $this->reason,
        ];
    }
}
