<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number',
        'application_id',
        'client_id',
        'base_price',
        'additional_fees',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'down_payment_percentage',
        'down_payment_amount',
        'payment_terms',
        'valid_until',
        'terms_and_conditions',
        'status',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'additional_fees' => 'array',
        'base_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($quotation) {
            if (!$quotation->quotation_number) {
                $quotation->quotation_number = self::generateQuotationNumber();
            }
        });
    }

    public static function generateQuotationNumber(): string
    {
        $year = date('Y');
        $lastQuotation = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastQuotation ? 
            intval(substr($lastQuotation->quotation_number, -3)) + 1 : 1;
        
        return sprintf('QUO-%s-%03d', $year, $nextNumber);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function getRemainingPaymentAttribute(): float
    {
        return $this->total_amount - $this->down_payment_amount;
    }
}
