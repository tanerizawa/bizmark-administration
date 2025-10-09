<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get the invoice that owns the item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate amount from quantity and unit price.
     */
    public function calculateAmount(): void
    {
        $this->amount = $this->quantity * $this->unit_price;
        $this->save();
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate amount when creating
        static::creating(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
        });

        // Recalculate invoice total after save
        static::saved(function ($item) {
            $item->invoice->calculateTotal();
        });

        // Recalculate invoice total after delete
        static::deleted(function ($item) {
            $item->invoice->calculateTotal();
        });
    }
}
