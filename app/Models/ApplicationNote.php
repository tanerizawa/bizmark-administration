<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationNote extends Model
{
    protected $fillable = [
        'application_id',
        'author_type',
        'author_id',
        'note',
        'is_internal',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    /**
     * Get the author dynamically based on author_type
     * This uses accessor pattern to handle polymorphic-like behavior
     */
    public function getAuthorAttribute()
    {
        // If already loaded, return it
        if (array_key_exists('author', $this->relations)) {
            return $this->relations['author'];
        }

        // Load based on author_type
        if ($this->author_type === 'admin' && $this->attributes['author_id']) {
            return $this->relations['author'] = User::find($this->attributes['author_id']);
        } elseif ($this->author_type === 'client' && $this->attributes['author_id']) {
            return $this->relations['author'] = Client::find($this->attributes['author_id']);
        }
        
        return null;
    }

    /**
     * Scopes
     */
    public function scopeVisibleToClient($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByAdmin($query)
    {
        return $query->where('author_type', 'admin');
    }

    public function scopeByClient($query)
    {
        return $query->where('author_type', 'client');
    }

    /**
     * Mark note as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
