<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'notes',
        'client_name',
        'client_address',
        'client_tax_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * Get the project that owns the invoice.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order');
    }

    /**
     * Get the payment schedules for the invoice.
     */
    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(ProjectPayment::class);
    }

    /**
     * Generate unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "INV-{$year}{$month}";
        
        // Include soft deleted invoices to avoid duplicate numbers
        $lastInvoice = self::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}-{$newNumber}";
    }

    /**
     * Calculate total amount from items.
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->items->sum('amount');
        $this->tax_amount = ($this->subtotal * $this->tax_rate) / 100;
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    /**
     * Record payment for the invoice.
     */
    public function recordPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        
        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }
        
        $this->save();
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft' => ['color' => 'gray', 'label' => 'Draft'],
            'sent' => ['color' => 'blue', 'label' => 'Sent'],
            'partial' => ['color' => 'yellow', 'label' => 'Partial'],
            'paid' => ['color' => 'green', 'label' => 'Paid'],
            'overdue' => ['color' => 'red', 'label' => 'Overdue'],
            'cancelled' => ['color' => 'gray', 'label' => 'Cancelled'],
            default => ['color' => 'gray', 'label' => 'Unknown'],
        };
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' 
            && $this->status !== 'cancelled' 
            && $this->due_date->isPast();
    }

    /**
     * Update overdue status.
     */
    public function updateOverdueStatus(): void
    {
        if ($this->isOverdue()) {
            $this->status = 'overdue';
            $this->save();
        }
    }
}
