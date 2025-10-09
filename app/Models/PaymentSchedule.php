<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'project_id',
        'invoice_id',
        'description',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Get the project that owns the payment schedule.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the invoice that owns the payment schedule.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(string $paymentMethod = null, string $referenceNumber = null): void
    {
        $this->status = 'paid';
        $this->paid_date = now();
        $this->payment_method = $paymentMethod;
        $this->reference_number = $referenceNumber;
        $this->save();

        // Update invoice if linked
        if ($this->invoice_id) {
            $this->invoice->recordPayment($this->amount);
        }
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'yellow', 'label' => 'Pending'],
            'paid' => ['color' => 'green', 'label' => 'Paid'],
            'overdue' => ['color' => 'red', 'label' => 'Overdue'],
            'cancelled' => ['color' => 'gray', 'label' => 'Cancelled'],
            default => ['color' => 'gray', 'label' => 'Unknown'],
        };
    }

    /**
     * Check if payment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
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

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update overdue status when retrieving
        static::retrieved(function ($schedule) {
            $schedule->updateOverdueStatus();
        });
    }
}
