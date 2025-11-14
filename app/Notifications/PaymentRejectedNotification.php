<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $reason;

    public function __construct(Payment $payment, $reason)
    {
        $this->payment = $payment;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Ditolak - ' . $this->payment->payment_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Mohon maaf, bukti pembayaran Anda tidak dapat diverifikasi.')
            ->line('**Nomor Pembayaran:** ' . $this->payment->payment_number)
            ->line('**Alasan:** ' . $this->reason)
            ->action('Upload Ulang Bukti Pembayaran', url('/client/applications/' . $this->payment->quotation->application_id . '/payment'))
            ->line('Silakan upload ulang bukti pembayaran yang valid.')
            ->salutation('Salam, Tim Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_rejected',
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'reason' => $this->reason,
            'message' => 'Pembayaran ' . $this->payment->payment_number . ' ditolak',
        ];
    }
}