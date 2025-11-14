<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'phone',
        'status',
        'source',
        'tags',
        'custom_fields',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Relationships
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'subscriber_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Methods
    public function unsubscribe($reason = null)
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
            'unsubscribe_reason' => $reason,
        ]);
    }

    public function markAsBounced()
    {
        $this->update(['status' => 'bounced']);
    }
}
