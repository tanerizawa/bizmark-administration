<?php

namespace App\Notifications;

use App\Models\ComplianceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * P10 — Notify client when compliance report PDF is ready.
 */
class ComplianceReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private ComplianceReport $report) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Laporan {$this->report->template->name} Siap Diunduh")
            ->greeting("Halo {$notifiable->name}!")
            ->line('Laporan compliance Anda telah selesai dibuat dan siap diunduh.')
            ->line("**{$this->report->template->name}**")
            ->line("Periode: {$this->report->period_start->format('d M Y')} – {$this->report->period_end->format('d M Y')}")
            ->action('Unduh PDF', url('/client/compliance-reports/'.$this->report->id.'/download'))
            ->line('Laporan ini dibuat dengan AI dan perlu direview sebelum disubmit ke instansi terkait.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'compliance_report_ready',
            'report_id' => $this->report->id,
            'title' => $this->report->template->name,
            'status' => 'ready',
            'pdf_path' => $this->report->pdf_path,
        ];
    }
}
