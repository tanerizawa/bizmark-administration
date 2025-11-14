<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->quotation->application;
        
        return (new MailMessage)
            ->subject('Quotation Siap - ' . $application->application_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Quotation untuk permohonan izin Anda sudah siap!')
            ->line('**Nomor Quotation:** ' . $this->quotation->quotation_number)
            ->line('**Jenis Izin:** ' . $application->permitType->name)
            ->line('**Total Biaya:** Rp ' . number_format($this->quotation->total_amount, 0, ',', '.'))
            ->line('**Uang Muka:** Rp ' . number_format($this->quotation->down_payment_amount, 0, ',', '.') . ' (' . $this->quotation->down_payment_percentage . '%)')
            ->line('**Berlaku Hingga:** ' . $this->quotation->expires_at->format('d F Y'))
            ->action('Lihat Quotation', url('/client/applications/' . $application->id . '/quotation'))
            ->line('Silakan review dan terima quotation untuk melanjutkan ke pembayaran.')
            ->salutation('Salam, Tim Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quotation_created',
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'total_amount' => $this->quotation->total_amount,
            'message' => 'Quotation ' . $this->quotation->quotation_number . ' sudah siap',
        ];
    }
}