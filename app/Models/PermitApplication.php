<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermitApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'application_number',
        'client_id',
        'permit_type_id',
        'status',
        'form_data',
        'notes',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'quoted_price',
        'quoted_at',
        'quotation_expires_at',
        'quotation_notes',
        'down_payment_amount',
        'down_payment_percentage',
        'payment_status',
        'project_id',
        'converted_at',
        'submitted_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'reviewed_at' => 'datetime',
        'quoted_at' => 'datetime',
        'quotation_expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'converted_at' => 'datetime',
        'quoted_price' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            if (!$application->application_number) {
                $application->application_number = self::generateApplicationNumber();
            }
        });
    }

    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $lastApplication = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastApplication ? 
            intval(substr($lastApplication->application_number, -3)) + 1 : 1;
        
        return sprintf('APP-%s-%03d', $year, $nextNumber);
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function permitType(): BelongsTo
    {
        return $this->belongsTo(PermitType::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Alias for reviewedBy (used in controller)
    public function reviewedBy(): BelongsTo
    {
        return $this->reviewer();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class, 'application_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'application_id');
    }

    // Alias for quotation (used in controller) - returns the latest quotation
    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'application_id')->latest();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class, 'application_id');
    }

    // Scopes
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review', 'document_incomplete']);
    }

    // Helper Methods
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'document_incomplete']);
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled', 'in_progress']);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => '#6B7280',
            'submitted' => '#3B82F6',
            'under_review' => '#F59E0B',
            'document_incomplete' => '#EF4444',
            'quoted' => '#8B5CF6',
            'quotation_accepted' => '#10B981',
            'payment_pending' => '#F59E0B',
            'payment_verified' => '#059669',
            'in_progress' => '#06B6D4',
            'completed' => '#22C55E',
            'cancelled' => '#6B7280',
            default => '#6B7280',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'under_review' => 'Sedang Direview',
            'document_incomplete' => 'Dokumen Kurang',
            'quoted' => 'Menunggu Persetujuan',
            'quotation_accepted' => 'Quotation Diterima',
            'payment_pending' => 'Menunggu Pembayaran',
            'payment_verified' => 'Pembayaran Terverifikasi',
            'in_progress' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
