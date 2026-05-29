<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbliSemanticSearch extends Model
{
    protected $fillable = [
        'query',
        'results',
        'latency_ms',
        'ip_address',
    ];

    protected $casts = [
        'results' => 'array',
    ];
}
