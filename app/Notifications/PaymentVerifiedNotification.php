<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $project;

    public function __construct(Payment $payment, $project = null)
    {
        $this->payment = $payment;
        $this->project = $project;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->payment->quotation->application;
        
        $message = (new MailMessage)
            ->subject('Pembayaran Terverifikasi - ' . $this->payment->payment_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pembayaran Anda telah terverifikasi!')
            ->line('**Nomor Pembayaran:** ' . $this->payment->payment_number)
            ->line('**Jumlah:** Rp ' . number_format($this->payment->amount, 0, ',', '.'))
            ->line('**Tanggal Verifikasi:** ' . $this->payment->verified_at->format('d F Y H:i'));

        if ($this->project) {
            $message->line('**Project telah dibuat:** ' . $this->project->name)
                ->action('Lihat Project', url('/client/projects/' . $this->project->id))
                ->line('Project Anda sudah aktif dan dalam proses pengerjaan.');
        } else {
            $message->action('Lihat Detail', url('/client/applications/' . $application->id))
                ->line('Terima kasih atas kepercayaan Anda menggunakan layanan kami.');
        }

        return $message->salutation('Salam, Tim Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_verified',
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'amount' => $this->payment->amount,
            'project_id' => $this->project?->id,
            'message' => 'Pembayaran ' . $this->payment->payment_number . ' telah diverifikasi',
        ];
    }
}
