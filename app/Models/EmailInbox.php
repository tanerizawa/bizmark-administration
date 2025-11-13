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
    ];

    protected $casts = [
        'attachments' => 'array',
        'labels' => 'array',
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'received_at' => 'datetime',
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
}
