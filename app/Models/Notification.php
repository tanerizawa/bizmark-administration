<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'icon',
        'related_type',
        'related_id',
        'action_url',
        'read_at',
        'data',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get the notifiable entity (polymorphic)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('notifiable_type', User::class)
                     ->where('notifiable_id', $userId);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Check if notification is read
     */
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get link/url for notification
     */
    public function getLinkAttribute()
    {
        return $this->action_url;
    }

    /**
     * Get color for notification type
     */
    public function getColorAttribute()
    {
        $colors = [
            'permit' => 'apple-blue',
            'email' => 'apple-purple',
            'task' => 'apple-orange',
            'project' => 'apple-green',
            'payment' => 'apple-red',
            'document' => 'apple-blue',
            'client' => 'apple-purple',
            'system' => 'apple-orange',
        ];

        return $colors[$this->type] ?? 'apple-blue';
    }

    /**
     * Create notification helper
     */
    public static function createNotification($type, $title, $message, $userId = null, $actionUrl = null, $data = [])
    {
        $icons = [
            'permit' => 'fa-file-certificate',
            'email' => 'fa-envelope',
            'task' => 'fa-tasks',
            'project' => 'fa-project-diagram',
            'payment' => 'fa-credit-card',
            'document' => 'fa-file-alt',
            'client' => 'fa-users',
            'system' => 'fa-cog',
        ];

        return self::create([
            'notifiable_type' => User::class,
            'notifiable_id' => $userId ?? auth()->id(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $data['icon'] ?? $icons[$type] ?? 'fa-bell',
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }
}
