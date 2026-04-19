<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'title',
        'description',
        'category',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'version',
        'parent_document_id',
        'is_latest_version',
        'status',
        'document_date',
        'submission_date',
        'approval_date',
        'uploaded_by',
        'is_confidential',
        'access_permissions',
        'notes',
        'download_count',
        'last_accessed_at',
    ];

    protected $casts = [
        'document_date' => 'date',
        'submission_date' => 'date',
        'approval_date' => 'date',
        'is_latest_version' => 'boolean',
        'is_confidential' => 'boolean',
        'access_permissions' => 'array',
        'last_accessed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function childDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function scopeLatestVersion($query)
    {
        return $query->where('is_latest_version', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
