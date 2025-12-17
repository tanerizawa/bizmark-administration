<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BacklinkOutreach extends Model
{
    use HasFactory;

    protected $table = 'backlink_outreach';

    protected $fillable = [
        'backlink_target_id',
        'subject',
        'message',
        'type',
        'status',
        'sent_at',
        'opened_at',
        'responded_at',
        'response_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function target()
    {
        return $this->belongsTo(BacklinkTarget::class, 'backlink_target_id');
    }
}
