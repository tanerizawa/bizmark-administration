<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = [
        'application_id',
        'revision_id',
        'item_type',
        'permit_type_id',
        'item_name',
        'description',
        'unit_price',
        'quantity',
        'subtotal',
        'service_type',
        'estimated_days',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'estimated_days' => 'integer',
    ];

    /**
     * Boot method to auto-calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->unit_price * $item->quantity;
        });
    }

    /**
     * Relationship to application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    /**
     * Relationship to revision
     */
    public function revision(): BelongsTo
    {
        return $this->belongsTo(ApplicationRevision::class, 'revision_id');
    }

    /**
     * Relationship to permit type
     */
    public function permitType(): BelongsTo
    {
        return $this->belongsTo(PermitType::class, 'permit_type_id');
    }

    /**
     * Get item type label in Indonesian
     */
    public function getItemTypeLabelAttribute(): string
    {
        $labels = [
            'permit' => 'Izin',
            'consultation' => 'Konsultasi',
            'survey' => 'Survei',
            'processing' => 'Pengurusan',
            'other' => 'Lainnya',
        ];

        return $labels[$this->item_type] ?? $this->item_type;
    }

    /**
     * Get service type label in Indonesian
     */
    public function getServiceTypeLabelAttribute(): string
    {
        $labels = [
            'bizmark' => 'BizMark',
            'owned' => 'Milik Sendiri',
            'self' => 'Urus Sendiri',
        ];

        return $labels[$this->service_type] ?? $this->service_type;
    }
}
