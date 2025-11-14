<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AIProcessingLog extends Model
{
    use HasFactory;

    protected $table = 'ai_processing_logs';

    protected $fillable = [
        'document_id',
        'template_id',
        'project_id',
        'operation_type',
        'status',
        'input_tokens',
        'output_tokens',
        'cost',
        'error_message',
        'metadata',
        'initiated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'cost' => 'decimal:6',
    ];

    /**
     * Get the document associated with this log (nullable)
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    /**
     * Get the template used
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Get the project associated
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the user who initiated this operation
     */
    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Get the document draft created from this processing
     */
    public function documentDraft(): HasOne
    {
        return $this->hasOne(DocumentDraft::class, 'ai_log_id');
    }

    /**
     * Scope completed logs
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pending/processing logs
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    /**
     * Get total tokens used
     */
    public function getTotalTokensAttribute(): int
    {
        return ($this->input_tokens ?? 0) + ($this->output_tokens ?? 0);
    }

    /**
     * Get cost in Rupiah (assuming 1 USD = 15,600 IDR)
     */
    public function getCostRupiahAttribute(): int
    {
        return round(($this->cost ?? 0) * 15600);
    }
}
