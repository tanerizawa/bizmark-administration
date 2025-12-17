<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BacklinkTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_name',
        'website_url',
        'contact_email',
        'contact_name',
        'category',
        'domain_authority',
        'spam_score',
        'priority',
        'notes',
        'status',
        'last_contacted_at',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'domain_authority' => 'integer',
        'spam_score' => 'integer',
    ];

    public function outreaches()
    {
        return $this->hasMany(BacklinkOutreach::class);
    }

    public function backlinks()
    {
        return $this->hasMany(Backlink::class);
    }
}
