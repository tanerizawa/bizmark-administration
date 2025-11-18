<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ApplicationLegalityDocument extends Model
{
    protected $fillable = [
        'application_id',
        'document_category',
        'document_name',
        'is_available',
        'document_number',
        'issued_date',
        'expiry_date',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Relationship to application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return null;
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        if ($this->expiry_date) {
            return $this->expiry_date->isPast();
        }

        return false;
    }

    /**
     * Get category label in Indonesian
     */
    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            'land_ownership' => 'Kepemilikan Tanah',
            'company_legal' => 'Legalitas Perusahaan',
            'existing_permits' => 'Izin Yang Ada',
            'power_of_attorney' => 'Surat Kuasa',
            'technical' => 'Dokumen Teknis',
            'other' => 'Lainnya',
        ];

        return $labels[$this->document_category] ?? $this->document_category;
    }
}
