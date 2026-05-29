<?php

namespace App\Notifications;

use App\Models\RegulatoryChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * P7 — Notify client about a new relevant regulatory change.
 */
class RegulatoryChangeAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private RegulatoryChange $change) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urgency = $this->change->isHighRelevance() ? '🔴 PENTING' : '🟡 Info';
        $categories = implode(', ', $this->change->affected_service_categories ?? []);

        return (new MailMessage)
            ->subject("[$urgency] Perubahan Regulasi: {$this->change->title}")
            ->greeting("Halo {$notifiable->name}!")
            ->line('Ada regulasi baru/perubahan yang relevan dengan perizinan bisnis Anda:')
            ->line("**{$this->change->title}**")
            ->when($this->change->document_number, fn ($m) => $m->line("Nomor: {$this->change->document_number}"))
            ->line("Dipublikasikan: {$this->change->published_at->format('d M Y')}")
            ->line($this->change->summary_id ?? '')
            ->when($categories, fn ($m) => $m->line("Layanan terdampak: $categories"))
            ->action('Lihat Detail', url('/client/regulatory-alerts'))
            ->line('Tim Bizmark akan segera memberikan panduan tindak lanjut.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'regulatory_change_alert',
            'regulatory_change_id' => $this->change->id,
            'title' => $this->change->title,
            'document_number' => $this->change->document_number,
            'published_at' => $this->change->published_at->toDateString(),
            'relevance_score' => $this->change->relevance_score,
            'summary_id' => $this->change->summary_id,
            'categories' => $this->change->affected_service_categories,
        ];
    }
}
