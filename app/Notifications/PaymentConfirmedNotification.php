<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->payment->quotation?->application;
        $appNumber = $application?->application_number ?? '-';
        $amount = number_format($this->payment->amount, 0, ',', '.') ?? '-';
        $type = $this->payment->payment_type === 'down_payment' ? 'Uang Muka (DP)' : 'Pelunasan';

        return (new MailMessage)
            ->subject('Pembayaran Dikonfirmasi — ' . $this->payment->payment_number)
            ->greeting('Halo,')
            ->line("Pembayaran Anda telah berhasil dikonfirmasi.")
            ->line('**No. Pembayaran:** ' . $this->payment->payment_number)
            ->line('**No. Aplikasi:** ' . $appNumber)
            ->line('**Jenis:** ' . $type)
            ->line('**Jumlah:** Rp ' . $amount)
            ->line('**Tanggal:** ' . $this->payment->paid_at?->format('d M Y H:i'))
            ->action('Lihat Status Aplikasi', url('/client/applications/' . ($application?->id ?? '')))
            ->line('Tim kami akan segera memproses aplikasi Anda. Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmed',
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'amount' => $this->payment->amount,
            'payment_type' => $this->payment->payment_type,
        ];
    }
}
