<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->invoice->project;

        return (new MailMessage)
            ->subject('Invoice '.$this->invoice->invoice_number.' dari Bizmark.id')
            ->greeting('Halo '.$this->invoice->client_name.',')
            ->line('Berikut adalah invoice untuk layanan yang kami berikan.')
            ->line('**Nomor Invoice:** '.$this->invoice->invoice_number)
            ->line('**Proyek:** '.($project?->name ?? '-'))
            ->line('**Total:** Rp '.number_format($this->invoice->total_amount, 0, ',', '.'))
            ->line('**Jatuh Tempo:** '.$this->invoice->due_date->format('d F Y'))
            ->action('Lihat Invoice', route('invoices.show', $this->invoice))
            ->line('Silakan hubungi kami jika ada pertanyaan mengenai invoice ini.')
            ->salutation('Salam, Tim Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_sent',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total_amount' => $this->invoice->total_amount,
            'message' => 'Invoice '.$this->invoice->invoice_number.' telah dikirim.',
        ];
    }
}
