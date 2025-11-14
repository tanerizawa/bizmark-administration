<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailInbox extends Model
{
    protected $table = 'email_inbox';

    protected $fillable = [
        'message_id',
        'from_email',
        'from_name',
        'to_email',
        'subject',
        'body_html',
        'body_text',
        'attachments',
        'is_read',
        'is_starred',
        'category',
        'labels',
        'replied_to',
        'assigned_to',
        'received_at',
        // Multi-user email system fields
        'email_account_id',
        'department',
        'priority',
        'status',
        'first_responded_at',
        'resolved_at',
        'response_time_minutes',
        'resolution_time_minutes',
        'handled_by',
        'tags',
        'internal_notes',
        'sentiment',
    ];

    protected $casts = [
        'attachments' => 'array',
        'labels' => 'array',
        'tags' => 'array',
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'received_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'response_time_minutes' => 'integer',
        'resolution_time_minutes' => 'integer',
    ];

    // Relationships
    public function replyTo()
    {
        return $this->belongsTo(EmailInbox::class, 'replied_to');
    }

    public function replies()
    {
        return $this->hasMany(EmailInbox::class, 'replied_to');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Email account this inbox belongs to
     */
    public function emailAccount()
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * User handling this email
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopeInbox($query)
    {
        return $query->where('category', 'inbox');
    }

    public function scopeSent($query)
    {
        return $query->where('category', 'sent');
    }

    /**
     * Filter by email account
     */
    public function scopeForAccount($query, $emailAccountId)
    {
        return $query->where('email_account_id', $emailAccountId);
    }

    /**
     * Filter by department
     */
    public function scopeForDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Filter by priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter by handler
     */
    public function scopeHandledBy($query, $userId)
    {
        return $query->where('handled_by', $userId);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function markAsUnread()
    {
        $this->update(['is_read' => false]);
    }

    public function toggleStar()
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    public function moveToTrash()
    {
        $this->update(['category' => 'trash']);
    }

    public function moveToSpam()
    {
        $this->update(['category' => 'spam']);
    }

    public function addLabel($label)
    {
        $labels = $this->labels ?? [];
        if (!in_array($label, $labels)) {
            $labels[] = $label;
            $this->update(['labels' => $labels]);
        }
    }

    public function removeLabel($label)
    {
        $labels = $this->labels ?? [];
        $labels = array_filter($labels, fn($l) => $l !== $label);
        $this->update(['labels' => array_values($labels)]);
    }

    /**
     * Assign to handler
     */
    public function assignTo(User $user)
    {
        $this->update([
            'handled_by' => $user->id,
            'status' => 'open',
        ]);
    }

    /**
     * Mark as responded
     */
    public function markAsResponded()
    {
        if (!$this->first_responded_at) {
            $responseTime = now()->diffInMinutes($this->received_at);
            $this->update([
                'first_responded_at' => now(),
                'response_time_minutes' => $responseTime,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Mark as resolved
     */
    public function markAsResolved()
    {
        $resolutionTime = now()->diffInMinutes($this->received_at);
        $this->update([
            'resolved_at' => now(),
            'resolution_time_minutes' => $resolutionTime,
            'status' => 'resolved',
        ]);
    }

    /**
     * Reopen email
     */
    public function reopen()
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'resolution_time_minutes' => null,
        ]);
    }

    /**
     * Add tag
     */
    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    /**
     * Remove tag
     */
    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'primary',
            'open' => 'info',
            'pending' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }
}
