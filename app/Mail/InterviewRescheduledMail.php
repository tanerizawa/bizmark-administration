<?php

namespace App\Mail;

use App\Models\InterviewSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class InterviewRescheduledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public InterviewSchedule $interview,
        public ?\DateTime $oldDate = null,
        public ?string $reason = null
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interview Rescheduled - ' . $this->interview->jobApplication->jobVacancy->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recruitment.interview-rescheduled',
            with: [
                'interview' => $this->interview,
                'candidate' => $this->interview->jobApplication,
                'vacancy' => $this->interview->jobApplication->jobVacancy,
                'interviewLink' => route('candidate.interview.show', $this->interview->access_token),
                'oldDate' => $this->oldDate,
                'reason' => $this->reason ?? 'Schedule adjustment',
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
        try {
            // Create calendar event with updated date
            $event = Event::create($this->interview->jobApplication->jobVacancy->title)
                ->description(
                    "Interview Type: " . ucfirst($this->interview->interview_type) . "\n" .
                    "Duration: " . $this->interview->duration_minutes . " minutes\n" .
                    ($this->interview->meeting_link ? "Meeting Link: " . $this->interview->meeting_link : "")
                )
                ->startsAt($this->interview->scheduled_at)
                ->endsAt($this->interview->scheduled_at->copy()->addMinutes($this->interview->duration_minutes))
                ->address($this->interview->location ?? 'Online');

            $calendar = Calendar::create('Interview: ' . $this->interview->jobApplication->jobVacancy->title)
                ->event($event);

            // Save to temporary file
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $filename = 'interview_rescheduled_' . $this->interview->id . '_' . time() . '.ics';
            $filepath = $tempDir . '/' . $filename;
            file_put_contents($filepath, $calendar->get());

            return [
                Attachment::fromPath($filepath)
                    ->as('interview.ics')
                    ->withMime('text/calendar')
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate calendar attachment: ' . $e->getMessage());
            return [];
        }
    }
}
