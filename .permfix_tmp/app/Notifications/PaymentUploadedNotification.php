<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bukti Pembayaran Baru - ' . $this->payment->payment_number)
            ->greeting('Halo Admin,')
            ->line('Bukti pembayaran baru telah diupload dan menunggu verifikasi.')
            ->line('**Nomor Pembayaran:** ' . $this->payment->payment_number)
            ->line('**Klien:** ' . $this->payment->quotation->application->client->name)
            ->line('**Jumlah:** Rp ' . number_format($this->payment->paid_amount, 0, ',', '.'))
            ->action('Verifikasi Pembayaran', url('/payments/' . $this->payment->id . '/verify'))
            ->salutation('Sistem Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_uploaded',
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'client_name' => $this->payment->quotation->application->client->name,
            'amount' => $this->payment->paid_amount,
        ];
    }
}