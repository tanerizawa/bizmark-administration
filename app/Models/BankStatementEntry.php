<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementEntry extends Model
{
    protected $fillable = [
        'reconciliation_id',
        'transaction_date',
        'description',
        'debit_amount',
        'credit_amount',
        'running_balance',
        'reference_number',
        'is_matched',
        'matched_transaction_type',
        'matched_transaction_id',
        'match_confidence',
        'match_notes',
        'unmatch_reason',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'is_matched' => 'boolean',
    ];

    /**
     * Get the reconciliation that owns the entry.
     */
    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class, 'reconciliation_id');
    }

    /**
     * Get the matched transaction (polymorphic-like approach).
     */
    public function getMatchedTransaction()
    {
        if (!$this->is_matched || !$this->matched_transaction_id) {
            return null;
        }

        switch ($this->matched_transaction_type) {
            case 'payment':
                return ProjectPayment::find($this->matched_transaction_id);
            case 'expense':
                return ProjectExpense::find($this->matched_transaction_id);
            case 'invoice_payment':
                return PaymentSchedule::find($this->matched_transaction_id);
            default:
                return null;
        }
    }

    /**
     * Get transaction amount (credit for income, debit for expense).
     */
    public function getAmountAttribute(): float
    {
        return $this->credit_amount > 0 ? $this->credit_amount : $this->debit_amount;
    }

    /**
     * Get transaction type (income or expense).
     */
    public function getTypeAttribute(): string
    {
        return $this->credit_amount > 0 ? 'income' : 'expense';
    }

    /**
     * Scope to filter matched entries.
     */
    public function scopeMatched($query)
    {
        return $query->where('is_matched', true);
    }

    /**
     * Scope to filter unmatched entries.
     */
    public function scopeUnmatched($query)
    {
        return $query->where('is_matched', false);
    }

    /**
     * Scope to filter by transaction type.
     */
    public function scopeIncome($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    /**
     * Scope to filter by transaction type.
     */
    public function scopeExpense($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    /**
     * Match this entry with a system transaction.
     */
    public function matchWith($transactionType, $transactionId, $confidence = 'manual', $notes = null)
    {
        $this->update([
            'is_matched' => true,
            'matched_transaction_type' => $transactionType,
            'matched_transaction_id' => $transactionId,
            'match_confidence' => $confidence,
            'match_notes' => $notes,
            'unmatch_reason' => null,
        ]);

        // Update the system transaction
        $transaction = $this->getMatchedTransaction();
        if ($transaction) {
            $transaction->update([
                'is_reconciled' => true,
                'reconciled_at' => now(),
                'reconciliation_id' => $this->reconciliation_id,
            ]);
        }
    }

    /**
     * Unmatch this entry.
     */
    public function unmatch($reason = null)
    {
        // Update the system transaction first
        $transaction = $this->getMatchedTransaction();
        if ($transaction) {
            $transaction->update([
                'is_reconciled' => false,
                'reconciled_at' => null,
                'reconciliation_id' => null,
            ]);
        }

        // Update this entry
        $this->update([
            'is_matched' => false,
            'matched_transaction_type' => null,
            'matched_transaction_id' => null,
            'match_confidence' => null,
            'match_notes' => null,
            'unmatch_reason' => $reason,
        ]);
    }
}
