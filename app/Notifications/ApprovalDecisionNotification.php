<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $itemType,
        protected string $itemLabel,
        protected string $action,
        protected ?string $note = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $actionLabel = $this->action === 'approved' ? 'disetujui' : 'ditolak';
        $actionColor = $this->action === 'approved' ? 'success' : 'error';
        $subject = '[Bizmark] '.ucfirst($this->itemType).' "'.$this->itemLabel.'" telah '.$actionLabel;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo, '.($notifiable->name ?? 'Tim'))
            ->line(ucfirst($this->itemType).' **"'.$this->itemLabel.'"** telah **'.$actionLabel.'**.');

        if ($this->note) {
            $mail->line('**Catatan:** '.$this->note);
        }

        return $mail
            ->line('Silakan login ke panel admin untuk melihat detail selengkapnya.')
            ->action('Buka Panel Admin', url('/admin/dashboard'))
            ->salutation('Salam, Tim Bizmark');
    }

    public function toArray(object $notifiable): array
    {
        $actionLabel = $this->action === 'approved' ? 'disetujui' : 'ditolak';

        return [
            'type' => 'approval_decision',
            'item_type' => $this->itemType,
            'item_label' => $this->itemLabel,
            'action' => $this->action,
            'message' => ucfirst($this->itemType)." \"{$this->itemLabel}\" telah {$actionLabel}.",
            'note' => $this->note,
        ];
    }
}
