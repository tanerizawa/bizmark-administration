<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'permit_type',
        'description',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'page_count',
        'required_fields',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all AI processing logs for this template
     */
    public function aiProcessingLogs(): HasMany
    {
        return $this->hasMany(AIProcessingLog::class, 'template_id');
    }

    /**
     * Get all document drafts created from this template
     */
    public function documentDrafts(): HasMany
    {
        return $this->hasMany(DocumentDraft::class, 'template_id');
    }

    /**
     * Scope active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by permit type
     */
    public function scopeForPermitType($query, string $permitType)
    {
        return $query->where('permit_type', $permitType);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
