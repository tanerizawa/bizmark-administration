<?php

namespace App\Mail;

use App\Models\ServiceCostRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceCostRequestUserConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceCostRequest $serviceRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Diterima - ' . $this->serviceRequest->request_number . ' | Bizmark.ID',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-cost-request-user-confirmation',
            with: [
                'serviceRequest' => $this->serviceRequest,
                'resultUrl' => route('permohonan.result', $this->serviceRequest->request_number),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
