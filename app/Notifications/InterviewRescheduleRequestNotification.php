<?php

namespace App\Notifications;

use App\Models\InterviewSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewRescheduleRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected InterviewSchedule $interview) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = $this->interview->jobApplication->full_name ?? 'Kandidat';
        $vacancy = $this->interview->jobApplication->jobVacancy->title ?? '-';
        $scheduled = $this->interview->scheduled_at->format('d M Y H:i');
        $reason = $this->interview->reschedule_request['reason'] ?? '-';

        return (new MailMessage)
            ->subject("[Reschedule Request] Interview {$applicantName} — {$vacancy}")
            ->greeting('Halo HR Admin,')
            ->line("Kandidat **{$applicantName}** mengajukan reschedule interview.")
            ->line("**Posisi:** {$vacancy}")
            ->line("**Jadwal semula:** {$scheduled}")
            ->line("**Alasan:** {$reason}")
            ->action('Lihat Detail Interview', url('/dashboard/interviews/'.$this->interview->id))
            ->line('Silakan atur ulang jadwal sesuai permintaan kandidat.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'interview_reschedule_request',
            'interview_id' => $this->interview->id,
            'applicant_name' => $this->interview->jobApplication->full_name ?? null,
            'scheduled_at' => $this->interview->scheduled_at->toISOString(),
            'reason' => $this->interview->reschedule_request['reason'] ?? null,
        ];
    }
}
