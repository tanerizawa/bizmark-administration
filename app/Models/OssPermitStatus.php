<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OssPermitStatus extends Model
{
    protected $fillable = [
        'client_id',
        'project_id',
        'oss_nib',
        'permit_type',
        'application_number',
        'status_code',
        'status_label',
        'raw_response',
        'last_checked_at',
        'status_changed_at',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'last_checked_at' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project::class);
    }
}
