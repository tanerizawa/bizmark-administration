<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProjectExpense extends Model
{
    protected static ?Collection $categoryCache = null;

    protected $fillable = [
        'project_id',
        'expense_date',
        'category',
        'vendor_name',
        'amount',
        'payment_method',
        'bank_account_id',
        'description',
        'receipt_file',
        'is_billable',
        'is_receivable',
        'receivable_from',
        'receivable_status',
        'receivable_paid_amount',
        'receivable_notes',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'is_billable' => 'boolean',
        'is_receivable' => 'boolean',
        'receivable_paid_amount' => 'decimal:2',
    ];

    protected static function categoryDefinitions(): Collection
    {
        if (self::$categoryCache === null) {
            self::$categoryCache = ExpenseCategory::options()->keyBy('slug');
        }

        return self::$categoryCache;
    }

    public static function clearCategoryCache(): void
    {
        self::$categoryCache = null;
    }

    public static function categories(): array
    {
        return self::categoryDefinitions()
            ->map(fn ($category) => [
                'label' => $category->name,
                'icon' => $category->icon,
                'group' => $category->group,
            ])
            ->toArray();
    }

    public static function categoryKeys(): array
    {
        return self::categoryDefinitions()->keys()->all();
    }

    public static function categoriesByGroup(): array
    {
        return self::categoryDefinitions()
            ->groupBy(fn ($category) => $category->group ?? 'Lainnya')
            ->map(function ($items) {
                return $items->map(function ($category) {
                    return [
                        'label' => $category->name,
                        'icon' => $category->icon,
                        'group' => $category->group,
                    ];
                })->toArray();
            })
            ->toArray();
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
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
        static::created(function ($expense) {
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
            
            // Update cash account balance
            if ($expense->bank_account_id) {
                $account = CashAccount::find($expense->bank_account_id);
                if ($account) {
                    $account->current_balance -= $expense->amount;
                    $account->save();
                }
            }
        });

        static::updated(function ($expense) {
            $oldAmount = $expense->getOriginal('amount');
            $oldBankAccountId = $expense->getOriginal('bank_account_id');
            
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
            
            // Revert old account balance
            if ($oldBankAccountId) {
                $oldAccount = CashAccount::find($oldBankAccountId);
                if ($oldAccount) {
                    $oldAccount->current_balance += $oldAmount;
                    $oldAccount->save();
                }
            }
            
            // Update new account balance
            if ($expense->bank_account_id) {
                $newAccount = CashAccount::find($expense->bank_account_id);
                if ($newAccount) {
                    $newAccount->current_balance -= $expense->amount;
                    $newAccount->save();
                }
            }
        });

        static::deleted(function ($expense) {
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
            
            // Revert cash account balance
            if ($expense->bank_account_id) {
                $account = CashAccount::find($expense->bank_account_id);
                if ($account) {
                    $account->current_balance += $expense->amount;
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

    public function getCategoryNameAttribute()
    {
        $category = self::categoryDefinitions()->get($this->category);

        return $category?->name ?? 'Lainnya';
    }

    public function getCategoryIconAttribute()
    {
        $category = self::categoryDefinitions()->get($this->category);

        return $category?->icon ?? 'ellipsis-h';
    }

    /**
     * Render category icon as Font Awesome HTML
     */
    public function getCategoryIconHtmlAttribute()
    {
        $icon = $this->category_icon;
        return '<i class="fas fa-' . $icon . '" style="color: rgba(235, 235, 245, 0.4);"></i>';
    }

    /**
     * Static method to render icon HTML for a category
     */
    public static function renderCategoryIcon(string $category, string $additionalClass = ''): string
    {
        $definition = self::categoryDefinitions()->get($category);
        $icon = $definition?->icon ?? 'ellipsis-h';
        $class = 'fas fa-' . $icon . ($additionalClass ? ' ' . $additionalClass : '');
        return '<i class="' . $class . '"></i>';
    }
}
