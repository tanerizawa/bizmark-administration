<?php

namespace App\Mail;

use App\Models\PermitApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged extends Mailable
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
        PermitApplication $application,
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
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusMessages = [
            'under_review' => 'Aplikasi Sedang Ditinjau',
            'document_incomplete' => 'Dokumen Perlu Dilengkapi',
            'quoted' => 'Penawaran Harga Tersedia',
            'quotation_accepted' => 'Penawaran Diterima',
            'payment_pending' => 'Menunggu Pembayaran',
            'payment_verified' => 'Pembayaran Terverifikasi',
            'in_progress' => 'Proses Perizinan Dimulai',
            'completed' => 'Izin Selesai Diproses',
            'cancelled' => 'Aplikasi Dibatalkan',
        ];

        $subject = sprintf(
            '[Bizmark.ID] %s - %s',
            $this->application->application_number,
            $statusMessages[$this->newStatus] ?? 'Update Status'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status-changed',
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
