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
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $actionLabel = $this->action === 'approved' ? 'disetujui' : 'ditolak';

        return [
            'type' => 'approval_decision',
            'item_type' => $this->itemType,
            'item_label' => $this->itemLabel,
            'action' => $this->action,
            'message' => ucfirst($this->itemType) . " \"{$this->itemLabel}\" telah {$actionLabel}.",
            'note' => $this->note,
        ];
    }
}
