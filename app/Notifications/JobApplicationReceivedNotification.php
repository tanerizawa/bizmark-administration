<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected JobApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Lamaran Baru] ' . ($this->application->jobVacancy->title ?? 'Posisi Baru') . ' — ' . $this->application->full_name)
            ->greeting('Halo Admin,')
            ->line('Lamaran baru telah masuk untuk posisi **' . ($this->application->jobVacancy->title ?? '-') . '**.')
            ->line('**Nama:** ' . $this->application->full_name)
            ->line('**Email:** ' . $this->application->email)
            ->line('**Telepon:** ' . $this->application->phone)
            ->action('Lihat Lamaran', url('/dashboard/job-applications/' . $this->application->id))
            ->line('Segera tinjau dan proses lamaran ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_application_received',
            'application_id' => $this->application->id,
            'applicant_name' => $this->application->full_name,
            'vacancy_title' => $this->application->jobVacancy->title ?? null,
        ];
    }
}
