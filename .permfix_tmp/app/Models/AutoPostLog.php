<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoPostLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Only created_at

    protected $fillable = [
        'schedule_id',
        'article_id',
        'topic_id',
        'level',
        'event',
        'message',
        'context',
        'word_count',
        'reading_time',
        'internal_links',
        'ai_cost',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'word_count' => 'integer',
        'reading_time' => 'integer',
        'internal_links' => 'integer',
        'ai_cost' => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Boot method - Auto-set created_at
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->created_at)) {
                $log->created_at = now();
            }
        });
    }

    /**
     * Relationships
     */
    public function schedule()
    {
        return $this->belongsTo(AutoPostSchedule::class, 'schedule_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function topic()
    {
        return $this->belongsTo(ArticleTopic::class, 'topic_id');
    }

    /**
     * Scopes
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeErrors($query)
    {
        return $query->where('level', 'error');
    }

    public function scopeSuccesses($query)
    {
        return $query->where('level', 'success');
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Accessors
     */
    public function getLevelBadgeAttribute()
    {
        $badges = [
            'info' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-500 text-white">Info</span>',
            'warning' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-white">Warning</span>',
            'error' => '<span class="px-2 py-1 text-xs rounded-full bg-red-500 text-white">Error</span>',
            'success' => '<span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">Success</span>',
        ];

        return $badges[$this->level] ?? $badges['info'];
    }

    /**
     * Static factory methods for common log types
     */
    public static function logInfo($event, $message, $data = [])
    {
        return static::create(array_merge([
            'level' => 'info',
            'event' => $event,
            'message' => $message,
        ], $data));
    }

    public static function logSuccess($event, $message, $data = [])
    {
        return static::create(array_merge([
            'level' => 'success',
            'event' => $event,
            'message' => $message,
        ], $data));
    }

    public static function logWarning($event, $message, $data = [])
    {
        return static::create(array_merge([
            'level' => 'warning',
            'event' => $event,
            'message' => $message,
        ], $data));
    }

    public static function logError($event, $message, $data = [])
    {
        return static::create(array_merge([
            'level' => 'error',
            'event' => $event,
            'message' => $message,
        ], $data));
    }
}
