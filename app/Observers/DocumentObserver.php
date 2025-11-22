<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\Notification;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        // Notify relevant users about new document
        if ($document->project_id && $document->project) {
            $projectName = $document->project->name ?? 'Proyek';
            
            // Notify admin or project manager (user ID 1 for now)
            Notification::createNotification(
                'document',
                'Dokumen Baru Diupload',
                "Dokumen '{$document->title}' diupload ke {$projectName}",
                1,
                route('documents.show', $document->id)
            );
        }
    }
}
