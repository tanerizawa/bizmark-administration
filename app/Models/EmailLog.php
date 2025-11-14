<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'recipient_email',
        'subject',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'tracking_id',
        'error_message',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(EmailSubscriber::class, 'subscriber_id');
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->tracking_id)) {
                $log->tracking_id = Str::random(32);
            }
        });
    }

    // Methods
    public function markAsOpened($ipAddress = null, $userAgent = null)
    {
        if (!$this->opened_at) {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Update campaign stats
            if ($this->campaign) {
                $this->campaign->incrementOpenedCount();
            }
        }
    }

    public function markAsClicked($ipAddress = null, $userAgent = null)
    {
        $this->update([
            'status' => 'clicked',
            'clicked_at' => now(),
            'ip_address' => $ipAddress ?? $this->ip_address,
            'user_agent' => $userAgent ?? $this->user_agent,
        ]);

        // Update campaign stats
        if ($this->campaign) {
            $this->campaign->incrementClickedCount();
        }
    }

    public function markAsBounced($errorMessage = null)
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $errorMessage,
        ]);

        // Update campaign stats
        if ($this->campaign) {
            $this->campaign->incrementBouncedCount();
        }

        // Mark subscriber as bounced
        if ($this->subscriber) {
            $this->subscriber->markAsBounced();
        }
    }
}
