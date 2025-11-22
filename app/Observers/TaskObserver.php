<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\Notification;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Notify assigned user
        if ($task->assigned_user_id) {
            Notification::createNotification(
                'task',
                'Task Baru Ditugaskan',
                "Anda mendapatkan task baru: {$task->title}",
                $task->assigned_user_id,
                route('tasks.show', $task->id)
            );
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // If task status changed to completed
        if ($task->isDirty('status') && $task->status === 'completed') {
            // Notify project owner or admin
            if ($task->project && $task->project->client_id) {
                Notification::createNotification(
                    'task',
                    'Task Selesai',
                    "Task '{$task->title}' telah diselesaikan",
                    1, // Admin user ID, adjust as needed
                    route('tasks.show', $task->id)
                );
            }
        }

        // If assigned user changed
        if ($task->isDirty('assigned_user_id') && $task->assigned_user_id) {
            Notification::createNotification(
                'task',
                'Task Ditugaskan Ulang',
                "Task '{$task->title}' telah ditugaskan kepada Anda",
                $task->assigned_user_id,
                route('tasks.show', $task->id)
            );
        }
    }
}
