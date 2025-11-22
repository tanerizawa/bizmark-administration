<?php

namespace App\Observers;

use App\Models\PermitApplication;
use App\Models\Notification;

class PermitApplicationObserver
{
    /**
     * Handle the PermitApplication "created" event.
     */
    public function created(PermitApplication $permit): void
    {
        // Notify admin about new permit application
        $clientName = $permit->client->name ?? 'Klien';
        
        Notification::createNotification(
            'permit',
            'Permohonan Izin Baru',
            "Permohonan izin baru dari {$clientName} - {$permit->application_number}",
            1, // Admin user ID
            route('admin.permit-applications.show', $permit->id)
        );
    }

    /**
     * Handle the PermitApplication "updated" event.
     */
    public function updated(PermitApplication $permit): void
    {
        // If status changed
        if ($permit->isDirty('status')) {
            $clientName = $permit->client->name ?? 'Klien';
            
            Notification::createNotification(
                'permit',
                'Status Perizinan Diupdate',
                "Status perizinan {$permit->application_number} untuk {$clientName} telah diubah menjadi: " . ucfirst($permit->status),
                1, // Admin user ID
                route('admin.permit-applications.show', $permit->id)
            );
        }
    }
}
