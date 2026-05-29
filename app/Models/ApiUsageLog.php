<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'api_key_id',
        'endpoint',
        'status_code',
        'latency_ms',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'latency_ms' => 'float',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }
}
