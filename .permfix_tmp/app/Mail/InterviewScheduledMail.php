<?php

namespace App\Mail;

use App\Models\InterviewSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class InterviewScheduledMail extends Mailable implements ShouldQueue
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
            subject: 'Interview Scheduled - ' . $this->interview->jobApplication->jobVacancy->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recruitment.interview-scheduled',
            with: [
                'interview' => $this->interview,
                'candidate' => $this->interview->jobApplication,
                'vacancy' => $this->interview->jobApplication->jobVacancy,
                'interviewLink' => route('candidate.interview.show', $this->interview->id),
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
        $attachments = [];

        // Generate .ics calendar file
        try {
            $calendar = Calendar::create()
                ->event(
                    Event::create()
                        ->name('Interview: ' . $this->interview->jobApplication->jobVacancy->title)
                        ->description(
                            "Interview with " . config('app.name') . "\n\n" .
                            "Type: " . $this->interview->getMeetingTypeLabel() . "\n" .
                            "Duration: " . $this->interview->duration_minutes . " minutes\n\n" .
                            ($this->interview->meeting_link ? "Meeting Link: " . $this->interview->meeting_link : "")
                        )
                        ->startsAt($this->interview->scheduled_at)
                        ->endsAt($this->interview->scheduled_at->copy()->addMinutes($this->interview->duration_minutes))
                        ->address($this->interview->location ?? 'Online')
                );

            $icsContent = $calendar->get();
            $icsPath = storage_path('app/temp/interview-' . $this->interview->id . '.ics');
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            file_put_contents($icsPath, $icsContent);

            $attachments[] = Attachment::fromPath($icsPath)
                ->as('interview.ics')
                ->withMime('text/calendar');
        } catch (\Exception $e) {
            // If calendar generation fails, continue without attachment
            \Log::error('Failed to generate calendar attachment: ' . $e->getMessage());
        }

        return $attachments;
    }
}
