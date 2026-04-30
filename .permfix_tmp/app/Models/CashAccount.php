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

    /**
     * Internal flag to allow balance updates during recalculation
     * @var bool
     */
    public $allowBalanceUpdate = false;

    /**
     * Boot the model and protect current_balance from manual changes
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent manual balance changes
        static::updating(function ($account) {
            if ($account->isDirty('current_balance') && !$account->allowBalanceUpdate) {
                throw new \Exception(
                    'Current balance cannot be changed manually. ' .
                    'Use recalculateBalance() method or create transactions. ' .
                    'Account: ' . $account->account_name
                );
            }
        });
    }

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

    public function reconciliations()
    {
        return $this->hasMany(BankReconciliation::class, 'cash_account_id');
    }

    // Helper Methods
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    /**
     * Recalculate balance from all transactions
     * This is the ONLY proper way to update current_balance
     * 
     * @param string|null $changeType Type of change: income, expense, adjustment, reconciliation, recalculation
     * @param int|null $referenceId ID of related transaction
     * @param string|null $referenceType Model class name
     * @param string|null $description Description of change
     * @return void
     */
    public function recalculateBalance(
        string $changeType = 'recalculation',
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?string $description = null
    ) {
        $oldBalance = $this->current_balance;

        // Calculate income from payments
        $totalIncome = $this->payments()->sum('amount');

        // Calculate expenses
        $totalExpense = $this->expenses()->sum('amount');

        // Calculate new balance
        $newBalance = $this->initial_balance + $totalIncome - $totalExpense;
        $changeAmount = $newBalance - $oldBalance;

        // Allow balance update
        $this->allowBalanceUpdate = true;
        $this->current_balance = $newBalance;
        $this->save();
        $this->allowBalanceUpdate = false;

        // Log to balance history
        if ($oldBalance != $newBalance) {
            $this->logBalanceChange(
                $oldBalance,
                $newBalance,
                $changeAmount,
                $changeType,
                $referenceId,
                $referenceType,
                $description ?? "Balance recalculated: Income {$totalIncome} - Expense {$totalExpense}"
            );
        }

        \Log::info("Cash Account Balance Recalculated", [
            'account_id' => $this->id,
            'account_name' => $this->account_name,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'change' => $changeAmount,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
        ]);
    }

    /**
     * Log balance change to history table
     * 
     * @param float $oldBalance
     * @param float $newBalance
     * @param float $changeAmount
     * @param string $changeType
     * @param int|null $referenceId
     * @param string|null $referenceType
     * @param string|null $description
     * @return void
     */
    public function logBalanceChange(
        float $oldBalance,
        float $newBalance,
        float $changeAmount,
        string $changeType,
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?string $description = null
    ) {
        \DB::table('cash_account_balance_history')->insert([
            'cash_account_id' => $this->id,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'change_amount' => $changeAmount,
            'change_type' => $changeType,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Get balance history
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function balanceHistory(int $limit = 50)
    {
        return \DB::table('cash_account_balance_history')
            ->where('cash_account_id', $this->id)
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
