<?php

namespace App\Notifications;

use App\Models\EmailAccount;
use App\Models\EmailInbox;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewEmailReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected EmailInbox $inbox,
        protected EmailAccount $emailAccount
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_email_received',
            'inbox_id' => $this->inbox->id,
            'from_email' => $this->inbox->from_email,
            'from_name' => $this->inbox->from_name,
            'subject' => $this->inbox->subject,
            'email_account' => $this->emailAccount->email,
            'priority' => $this->inbox->priority,
            'message' => "Email baru ({$this->inbox->priority}) dari {$this->inbox->from_email}: {$this->inbox->subject}",
        ];
    }
}
