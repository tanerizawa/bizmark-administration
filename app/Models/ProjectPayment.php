<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $fillable = [
        'project_id',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_type',
        'payment_method',
        'bank_account_id',
        'reference_number',
        'description',
        'receipt_file',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(CashAccount::class, 'bank_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Events - Auto-update project totals
    protected static function booted()
    {
        static::created(function ($payment) {
            $payment->project->updatePaymentReceived();
            
            // Update invoice payment if linked
            if ($payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoice->recordPayment($payment->amount);
                }
            }
            
            // Update cash account balance
            if ($payment->bank_account_id) {
                $account = CashAccount::find($payment->bank_account_id);
                if ($account) {
                    $account->current_balance += $payment->amount;
                    $account->save();
                }
            }
        });

        static::updated(function ($payment) {
            $oldAmount = $payment->getOriginal('amount');
            $oldBankAccountId = $payment->getOriginal('bank_account_id');
            $oldInvoiceId = $payment->getOriginal('invoice_id');
            
            $payment->project->updatePaymentReceived();
            
            // Revert old invoice payment
            if ($oldInvoiceId && $oldInvoiceId != $payment->invoice_id) {
                $oldInvoice = Invoice::find($oldInvoiceId);
                if ($oldInvoice) {
                    $oldInvoice->paid_amount -= $oldAmount;
                    $oldInvoice->remaining_amount = $oldInvoice->total_amount - $oldInvoice->paid_amount;
                    if ($oldInvoice->paid_amount <= 0) {
                        $oldInvoice->status = 'sent';
                    } elseif ($oldInvoice->paid_amount > 0 && $oldInvoice->remaining_amount > 0) {
                        $oldInvoice->status = 'partial';
                    }
                    $oldInvoice->save();
                }
            }
            
            // Update new invoice payment
            if ($payment->invoice_id) {
                $newInvoice = Invoice::find($payment->invoice_id);
                if ($newInvoice) {
                    // If same invoice, adjust the difference
                    if ($oldInvoiceId == $payment->invoice_id) {
                        $difference = $payment->amount - $oldAmount;
                        $newInvoice->recordPayment($difference);
                    } else {
                        // New invoice, add full amount
                        $newInvoice->recordPayment($payment->amount);
                    }
                }
            }
            
            // Revert old account balance
            if ($oldBankAccountId) {
                $oldAccount = CashAccount::find($oldBankAccountId);
                if ($oldAccount) {
                    $oldAccount->current_balance -= $oldAmount;
                    $oldAccount->save();
                }
            }
            
            // Update new account balance
            if ($payment->bank_account_id) {
                $newAccount = CashAccount::find($payment->bank_account_id);
                if ($newAccount) {
                    $newAccount->current_balance += $payment->amount;
                    $newAccount->save();
                }
            }
        });

        static::deleted(function ($payment) {
            $payment->project->updatePaymentReceived();
            
            // Revert invoice payment
            if ($payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoice->paid_amount -= $payment->amount;
                    $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
                    if ($invoice->paid_amount <= 0) {
                        $invoice->status = 'sent';
                    } elseif ($invoice->paid_amount > 0 && $invoice->remaining_amount > 0) {
                        $invoice->status = 'partial';
                    }
                    $invoice->save();
                }
            }
            
            // Revert cash account balance
            if ($payment->bank_account_id) {
                $account = CashAccount::find($payment->bank_account_id);
                if ($account) {
                    $account->current_balance -= $payment->amount;
                    $account->save();
                }
            }
        });
    }

    // Helper Methods
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getPaymentTypeNameAttribute()
    {
        return match($this->payment_type) {
            'dp' => 'Down Payment (DP)',
            'progress' => 'Progress Payment',
            'final' => 'Final Payment',
            default => 'Other',
        };
    }
}
