<?php

namespace App\Mail;

use App\Models\ServiceCostRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceCostRequestAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceCostRequest $serviceRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lead Permohonan Baru - ' . $this->serviceRequest->request_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-cost-request-admin-notification',
            with: [
                'serviceRequest' => $this->serviceRequest,
                'resultUrl' => route('permohonan.result', $this->serviceRequest->request_number),
                'adminLeadsUrl' => route('admin.leads.index', ['tab' => 'service-cost-requests']),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
