<?php

namespace App\Mail;

use App\Models\ServiceCostRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceCostRequestQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceCostRequest $serviceRequest,
        public string $subjectLine,
        public string $bodyText,
        public string $htmlBody,
        public array $signature = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@bizmark.id', 'Tim Konsultan'),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-cost-request-quote',
            with: [
                'serviceRequest' => $this->serviceRequest,
                'bodyText' => $this->bodyText,
                'htmlBody' => $this->htmlBody,
                'signature' => $this->signature,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
