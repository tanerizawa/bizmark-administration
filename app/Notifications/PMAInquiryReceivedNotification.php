<?php

namespace App\Notifications;

use App\Models\ServiceInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PMAInquiryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ServiceInquiry $inquiry) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formData = $this->inquiry->form_data ?? [];

        return (new MailMessage)
            ->subject('[PMA Inquiry] ' . $this->inquiry->company_name . ' — ' . $this->inquiry->inquiry_number)
            ->greeting('Halo Admin,')
            ->line('Inquiry PMA baru telah masuk.')
            ->line('**No. Inquiry:** ' . $this->inquiry->inquiry_number)
            ->line('**Perusahaan:** ' . $this->inquiry->company_name)
            ->line('**Kontak:** ' . $this->inquiry->contact_person . ' (' . $this->inquiry->email . ')')
            ->line('**Negara:** ' . ($formData['country'] ?? '-'))
            ->line('**Investasi:** ' . ($formData['investment_amount_usd'] ?? '-'))
            ->action('Lihat Inquiry', url('/dashboard/service-inquiries/' . $this->inquiry->id))
            ->line('Segera tindak lanjuti dalam 1×24 jam.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pma_inquiry_received',
            'inquiry_id' => $this->inquiry->id,
            'inquiry_number' => $this->inquiry->inquiry_number,
            'company_name' => $this->inquiry->company_name,
            'email' => $this->inquiry->email,
        ];
    }
}
