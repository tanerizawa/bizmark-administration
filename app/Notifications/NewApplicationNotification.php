<?php

namespace App\Notifications;

use App\Models\PermitApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    public function __construct(PermitApplication $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formData = is_string($this->application->form_data) 
            ? json_decode($this->application->form_data, true) 
            : $this->application->form_data;
        
        $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
        
        if ($isPackage) {
            $projectName = $formData['project_name'] ?? 'N/A';
            $permitCount = count($formData['selected_permits'] ?? []);
            $bizmarkCount = $formData['permits_by_service']['bizmark'] ?? 0;
            
            return (new MailMessage)
                ->subject('Paket Perizinan Baru - ' . $this->application->application_number)
                ->greeting('Halo Admin,')
                ->line('Permohonan paket perizinan baru telah diterima.')
                ->line('**Nomor Aplikasi:** ' . $this->application->application_number)
                ->line('**Klien:** ' . $this->application->client->name)
                ->line('**Nama Proyek:** ' . $projectName)
                ->line('**Total Izin:** ' . $permitCount . ' izin (' . $bizmarkCount . ' dikelola BizMark.ID)')
                ->line('**Nilai Investasi:** Rp ' . number_format($formData['investment_value'] ?? 0, 0, ',', '.'))
                ->action('Review Paket', url('/admin/permit-applications/' . $this->application->id))
                ->salutation('Sistem Bizmark.id');
        }
        
        $permitName = $this->application->permitType 
            ? $this->application->permitType->name 
            : ($formData['permit_name'] ?? 'N/A');
            
        return (new MailMessage)
            ->subject('Aplikasi Perizinan Baru - ' . $this->application->application_number)
            ->greeting('Halo Admin,')
            ->line('Aplikasi perizinan baru telah diterima.')
            ->line('**Nomor Aplikasi:** ' . $this->application->application_number)
            ->line('**Klien:** ' . $this->application->client->name)
            ->line('**Jenis Perizinan:** ' . $permitName)
            ->action('Review Aplikasi', url('/admin/permit-applications/' . $this->application->id))
            ->salutation('Sistem Bizmark.id');
    }

    public function toArray(object $notifiable): array
    {
        $formData = is_string($this->application->form_data) 
            ? json_decode($this->application->form_data, true) 
            : $this->application->form_data;
        
        $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
        
        if ($isPackage) {
            $projectName = $formData['project_name'] ?? 'N/A';
            $permitCount = count($formData['selected_permits'] ?? []);
            $bizmarkCount = $formData['permits_by_service']['bizmark'] ?? 0;
            
            return [
                'type' => 'new_package_application',
                'application_id' => $this->application->id,
                'application_number' => $this->application->application_number,
                'client_name' => $this->application->client->name,
                'project_name' => $projectName,
                'total_permits' => $permitCount,
                'bizmark_permits' => $bizmarkCount,
                'is_package' => true,
            ];
        }
        
        $permitName = $this->application->permitType 
            ? $this->application->permitType->name 
            : ($formData['permit_name'] ?? 'N/A');
            
        return [
            'type' => 'new_application',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'client_name' => $this->application->client->name,
            'permit_type' => $permitName,
            'is_package' => false,
        ];
    }
}