<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoPostSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'scheduled_at',
        'status',
        'error_message',
        'attempts',
        'started_at',
        'completed_at',
        'generation_time_seconds',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'attempts' => 'integer',
        'generation_time_seconds' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function topic()
    {
        return $this->belongsTo(ArticleTopic::class, 'topic_id');
    }

    public function logs()
    {
        return $this->hasMany(AutoPostLog::class, 'schedule_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueForProcessing($query, $windowMinutes = 15)
    {
        return $query->where('status', 'pending')
            ->where('scheduled_at', '<=', now()->addMinutes($windowMinutes))
            ->where('scheduled_at', '>=', now()->subMinutes($windowMinutes));
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-white">Pending</span>',
            'processing' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-500 text-white">Processing</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">Completed</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-500 text-white">Failed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-500 text-white">Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Helper Methods
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
            'attempts' => $this->attempts + 1,
        ]);
    }

    public function markAsCompleted($generationTime = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'generation_time_seconds' => $generationTime ?? (now()->diffInSeconds($this->started_at)),
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function canRetry($maxAttempts = 3)
    {
        return $this->isFailed() && $this->attempts < $maxAttempts;
    }

    public function retry()
    {
        if ($this->canRetry()) {
            $this->update(['status' => 'pending']);
            return true;
        }
        return false;
    }
}
