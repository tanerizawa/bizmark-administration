<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationRevision extends Model
{
    protected $fillable = [
        'application_id',
        'revision_number',
        'revision_type',
        'revision_reason',
        'revised_by_id',
        'permits_data',
        'project_data',
        'total_cost',
        'status',
        'client_approved_at',
    ];

    protected $casts = [
        'permits_data' => 'array',
        'project_data' => 'array',
        'total_cost' => 'decimal:2',
        'client_approved_at' => 'datetime',
    ];

    /**
     * Relationship to application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    /**
     * Relationship to user who revised
     */
    public function revisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by_id');
    }

    /**
     * Relationship to quotation items
     */
    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'revision_id');
    }

    /**
     * Check if revision is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_client_approval';
    }

    /**
     * Check if revision is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if revision is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
