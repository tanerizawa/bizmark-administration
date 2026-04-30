<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Client $client,
        protected string $plainPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat Datang di Bizmark.id — Akun Anda Siap')
            ->greeting('Halo '.$this->client->name.',')
            ->line('Akun klien Anda telah berhasil dibuat. Berikut detail login Anda:')
            ->line('**Email:** '.$this->client->email)
            ->line('**Password:** '.$this->plainPassword)
            ->action('Login ke Portal Klien', url('/client/login'))
            ->line('Segera ubah password Anda setelah login pertama kali.')
            ->line('Jika ada pertanyaan, silakan hubungi tim kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'client_welcome',
            'client_id' => $this->client->id,
            'message' => 'Akun klien berhasil dibuat untuk '.$this->client->email,
        ];
    }
}
