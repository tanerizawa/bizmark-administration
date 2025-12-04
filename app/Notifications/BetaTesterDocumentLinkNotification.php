<?php

namespace App\Notifications;

use App\Models\BetaTester;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BetaTesterDocumentLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $betaTester;
    protected $isResend;

    /**
     * Create a new notification instance.
     */
    public function __construct(BetaTester $betaTester, bool $isResend = false)
    {
        $this->betaTester = $betaTester;
        $this->isResend = $isResend;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dashboardUrl = route('beta-tester.dashboard', ['token' => $this->betaTester->access_token]);
        
        $subject = $this->isResend 
            ? 'Link Dokumen Beta Tester - Dikirim Ulang' 
            : 'Selamat Datang di Program Beta Tester Bizmark.ID';

        $greeting = $this->isResend
            ? 'Halo kembali, ' . $this->betaTester->full_name
            : 'Halo ' . $this->betaTester->full_name . ', Selamat Datang!';

        $intro = $this->isResend
            ? 'Berikut adalah link akses dashboard Anda yang dikirim ulang:'
            : 'Terima kasih telah mendaftar sebagai Beta Tester Bizmark.ID! Kami sangat antusias untuk bekerja sama dengan Anda.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($intro)
            ->line('**Nomor Registrasi:** ' . $this->betaTester->registration_number)
            ->line('Silakan klik tombol di bawah ini untuk mengakses dashboard dan menandatangani dokumen yang diperlukan:')
            ->action('Akses Dashboard', $dashboardUrl)
            ->line('**Penting:**')
            ->line('• Link ini bersifat rahasia dan personal untuk Anda')
            ->line('• Token akses berlaku hingga: ' . $this->betaTester->access_token_expires_at->format('d F Y'))
            ->line('• Jangan bagikan link ini kepada siapa pun')
            ->line('• Segera tanda tangani dokumen yang tersedia')
            ->line('Jika Anda mengalami kesulitan, silakan hubungi kami melalui email ini.')
            ->salutation('Hormat kami, Tim Bizmark.ID');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'beta_tester_id' => $this->betaTester->id,
            'registration_number' => $this->betaTester->registration_number,
            'is_resend' => $this->isResend,
        ];
    }
}
