<?php

namespace App\Mail;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $previousStatus;
    public $newStatus;
    public $changedBy;
    public $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(
        JobApplication $application,
        string $previousStatus,
        string $newStatus,
        ?User $changedBy = null,
        ?string $notes = null
    ) {
        $this->application = $application;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
        $this->notes = $notes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $statusMessages = [
            'pending' => 'Lamaran Anda Sedang Diproses',
            'reviewed' => 'Lamaran Anda Telah Direview',
            'interview' => 'Undangan Interview',
            'accepted' => 'Selamat! Lamaran Anda Diterima',
            'rejected' => 'Update Status Lamaran Anda',
        ];

        $subject = sprintf(
            '[Bizmark.ID Career] %s - %s',
            $this->application->jobVacancy->title ?? 'Lamaran Kerja',
            $statusMessages[$this->newStatus] ?? 'Update Status'
        );

        return $this->subject($subject)
                    ->view('emails.job-application-status-changed')
                    ->with([
                        'application' => $this->application,
                        'previousStatus' => $this->previousStatus,
                        'newStatus' => $this->newStatus,
                        'changedBy' => $this->changedBy,
                        'notes' => $this->notes,
                    ]);
    }
}
