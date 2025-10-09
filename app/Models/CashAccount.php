<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_type',
        'account_number',
        'bank_name',
        'account_holder',
        'current_balance',
        'initial_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBankAccounts($query)
    {
        return $query->where('account_type', 'bank');
    }

    public function scopeCash($query)
    {
        return $query->where('account_type', 'cash');
    }

    // Relationships
    public function payments()
    {
        return $this->hasMany(ProjectPayment::class, 'bank_account_id');
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class, 'bank_account_id');
    }

    // Helper Methods
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }
}
