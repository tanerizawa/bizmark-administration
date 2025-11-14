<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'template_id',
        'content',
        'plain_content',
        'status',
        'recipient_type',
        'recipient_tags',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'created_by',
    ];

    protected $casts = [
        'recipient_tags' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    // Accessors
    public function getOpenRateAttribute()
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute()
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function getBounceRateAttribute()
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->bounced_count / $this->sent_count) * 100, 2);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    // Methods
    public function markAsSending()
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function incrementSentCount()
    {
        $this->increment('sent_count');
    }

    public function incrementOpenedCount()
    {
        $this->increment('opened_count');
    }

    public function incrementClickedCount()
    {
        $this->increment('clicked_count');
    }

    public function incrementBouncedCount()
    {
        $this->increment('bounced_count');
    }

    public function incrementUnsubscribedCount()
    {
        $this->increment('unsubscribed_count');
    }
}
