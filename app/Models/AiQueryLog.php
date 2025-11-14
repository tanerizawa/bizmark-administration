<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiQueryLog extends Model
{
    const UPDATED_AT = null; // Only created_at, no updated_at

    protected $fillable = [
        'client_id',
        'kbli_code',
        'business_context',
        'prompt_text',
        'response_text',
        'tokens_used',
        'response_time_ms',
        'status',
        'error_message',
        'ai_model',
        'api_cost',
    ];

    protected $casts = [
        'business_context' => 'array',
        'tokens_used' => 'integer',
        'response_time_ms' => 'integer',
        'api_cost' => 'decimal:6',
        'created_at' => 'datetime',
    ];

    /**
     * Get the client that made this query
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope to filter successful queries
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to filter failed queries
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['error', 'timeout']);
    }

    /**
     * Scope to get queries for a specific KBLI
     */
    public function scopeForKbli($query, string $kbliCode)
    {
        return $query->where('kbli_code', $kbliCode);
    }

    /**
     * Scope to get queries within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get total API cost for a period
     */
    public static function totalCost($startDate = null, $endDate = null)
    {
        $query = static::successful();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->sum('api_cost');
    }

    /**
     * Get average response time
     */
    public static function averageResponseTime($startDate = null, $endDate = null)
    {
        $query = static::successful();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->avg('response_time_ms');
    }

    /**
     * Get total tokens used
     */
    public static function totalTokens($startDate = null, $endDate = null)
    {
        $query = static::successful();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->sum('tokens_used');
    }
}
