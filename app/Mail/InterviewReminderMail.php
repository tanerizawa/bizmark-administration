<?php

namespace App\Mail;

use App\Models\InterviewSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public InterviewSchedule $interview
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Interview Tomorrow - ' . $this->interview->jobApplication->jobVacancy->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recruitment.interview-reminder',
            with: [
                'interview' => $this->interview,
                'candidate' => $this->interview->jobApplication,
                'vacancy' => $this->interview->jobApplication->jobVacancy,
                'interviewLink' => route('candidate.interview.show', $this->interview->id),
                'hoursUntil' => now()->diffInHours($this->interview->scheduled_at),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
