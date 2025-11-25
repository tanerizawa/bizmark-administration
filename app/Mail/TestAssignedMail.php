<?php

namespace App\Mail;

use App\Models\TestSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public TestSession $testSession
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $jobTitle = $this->testSession->jobApplication?->jobVacancy?->title ?? 'Job Position';
        
        return new Envelope(
            subject: 'Assessment Invitation - ' . $jobTitle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $hoursUntilExpiry = now()->diffInHours($this->testSession->expires_at);
        $testLink = route('candidate.test.show', $this->testSession->session_token);

        $questions = $this->testSession->testTemplate->questions['questions'] ?? [];
        $totalQuestions = is_array($questions) ? count($questions) : 0;

        return new Content(
            view: 'emails.recruitment.test-assigned',
            with: [
                'testSession' => $this->testSession,
                'template' => $this->testSession->testTemplate,
                'candidate' => $this->testSession->jobApplication,
                'vacancy' => $this->testSession->jobApplication?->jobVacancy,
                'testLink' => $testLink,
                'hoursUntilExpiry' => $hoursUntilExpiry,
                'expiresAt' => $this->testSession->expires_at,
                'testType' => ucfirst(str_replace('-', ' ', $this->testSession->testTemplate->test_type ?? 'general')),
                'duration' => $this->testSession->testTemplate->time_limit ?? 60,
                'totalQuestions' => $totalQuestions,
                'passingScore' => $this->testSession->testTemplate->passing_score ?? 70,
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
