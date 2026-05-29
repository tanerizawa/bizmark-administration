<?php

namespace App\Notifications;

use App\Models\PermitExpiryMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PermitExpiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly PermitExpiryMonitor $monitor,
        public readonly int $daysLeft
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', WebPushChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $urgency = $this->urgencyLabel();
        $clientName = $notifiable->name ?? $notifiable->company_name ?? 'Klien';
        $renewalUrl = url('/client/compliance-monitor');

        return (new MailMessage)
            ->subject("{$urgency} Izin \"{$this->monitor->permit_type}\" akan expire dalam {$this->daysLeft} hari")
            ->greeting("Halo, {$clientName}!")
            ->line("Izin **{$this->monitor->permit_type}** (No: ".($this->monitor->permit_number ?? '-').") terkait proyek Anda akan kedaluwarsa pada **{$this->monitor->expires_at->format('d M Y')}**.")
            ->line("Sisa waktu: **{$this->daysLeft} hari** lagi.")
            ->action('Lihat Status Compliance', $renewalUrl)
            ->line('Segera hubungi tim Bizmark.id untuk proses perpanjangan izin sebelum terlambat.')
            ->salutation('Tim Bizmark.id');
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->urgencyEmoji().' Izin Akan Expired')
            ->body("{$this->monitor->permit_type} — {$this->daysLeft} hari lagi")
            ->icon('/images/favicon.svg')
            ->badge('/images/favicon.svg')
            ->data(['url' => '/client/compliance-monitor'])
            ->tag('permit-expiry-'.$this->monitor->id)
            ->requireInteraction(true);
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'permit_expiry',
            'monitor_id' => $this->monitor->id,
            'permit_type' => $this->monitor->permit_type,
            'permit_number' => $this->monitor->permit_number,
            'expires_at' => $this->monitor->expires_at->toDateString(),
            'days_left' => $this->daysLeft,
            'project_id' => $this->monitor->project_id,
        ];
    }

    private function urgencyLabel(): string
    {
        return match (true) {
            $this->daysLeft <= 7 => '[MENDESAK]',
            $this->daysLeft <= 30 => '[PERHATIAN]',
            default => '[PENGINGAT]',
        };
    }

    private function urgencyEmoji(): string
    {
        return match (true) {
            $this->daysLeft <= 7 => '🔴',
            $this->daysLeft <= 30 => '🟡',
            default => '🔵',
        };
    }
}
