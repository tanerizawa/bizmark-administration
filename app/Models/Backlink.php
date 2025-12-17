<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'backlink_target_id',
        'source_url',
        'target_url',
        'anchor_text',
        'type',
        'status',
        'domain_authority',
        'page_authority',
        'is_indexed',
        'last_checked_at',
        'acquired_at',
        'notes',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'acquired_at' => 'datetime',
        'is_indexed' => 'boolean',
        'domain_authority' => 'integer',
        'page_authority' => 'integer',
    ];

    public function target()
    {
        return $this->belongsTo(BacklinkTarget::class, 'backlink_target_id');
    }
}
