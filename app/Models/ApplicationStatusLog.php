<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by_type',
        'changed_by_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    public function changedBy()
    {
        if ($this->changed_by_type === 'client') {
            return $this->belongsTo(Client::class, 'changed_by_id');
        }
        return $this->belongsTo(User::class, 'changed_by_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }

    public function getChangedByAttribute()
    {
        if ($this->changed_by_type === 'client') {
            return Client::find($this->changed_by_id);
        }
        return User::find($this->changed_by_id);
    }
}
