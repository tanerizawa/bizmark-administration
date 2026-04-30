<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    protected $fillable = [
        'cash_account_id',
        'reconciliation_date',
        'start_date',
        'end_date',
        'opening_balance_book',
        'opening_balance_bank',
        'closing_balance_book',
        'closing_balance_bank',
        'total_deposits_in_transit',
        'total_outstanding_checks',
        'total_bank_charges',
        'total_bank_credits',
        'adjusted_bank_balance',
        'adjusted_book_balance',
        'difference',
        'status',
        'bank_statement_file',
        'bank_statement_format',
        'notes',
        'reconciled_by',
        'reviewed_by',
        'approved_by',
        'completed_at',
        'reviewed_at',
        'approved_at',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'opening_balance_book' => 'decimal:2',
        'opening_balance_bank' => 'decimal:2',
        'closing_balance_book' => 'decimal:2',
        'closing_balance_bank' => 'decimal:2',
        'total_deposits_in_transit' => 'decimal:2',
        'total_outstanding_checks' => 'decimal:2',
        'total_bank_charges' => 'decimal:2',
        'total_bank_credits' => 'decimal:2',
        'adjusted_bank_balance' => 'decimal:2',
        'adjusted_book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the cash account that owns the reconciliation.
     */
    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    /**
     * Get the bank statement entries for the reconciliation.
     */
    public function bankStatementEntries(): HasMany
    {
        return $this->hasMany(BankStatementEntry::class, 'reconciliation_id');
    }

    /**
     * Get the user who reconciled.
     */
    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    /**
     * Get the user who reviewed.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who approved.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get matched payments for this reconciliation.
     */
    public function matchedPayments(): HasMany
    {
        return $this->hasMany(ProjectPayment::class, 'reconciliation_id');
    }

    /**
     * Get matched expenses for this reconciliation.
     */
    public function matchedExpenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class, 'reconciliation_id');
    }

    /**
     * Get matched invoice payments for this reconciliation.
     */
    public function matchedInvoicePayments(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class, 'reconciliation_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by cash account.
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('cash_account_id', $accountId);
    }

    /**
     * Check if reconciliation is complete (difference = 0).
     */
    public function isBalanced(): bool
    {
        return $this->difference == 0;
    }

    /**
     * Calculate matching statistics.
     */
    public function getMatchingStats(): array
    {
        $entries = $this->bankStatementEntries;
        $total = $entries->count();
        $matched = $entries->where('is_matched', true)->count();
        
        return [
            'total' => $total,
            'matched' => $matched,
            'unmatched' => $total - $matched,
            'match_rate' => $total > 0 ? round(($matched / $total) * 100, 2) : 0,
        ];
    }
}
