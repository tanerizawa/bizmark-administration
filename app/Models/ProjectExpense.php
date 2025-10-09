<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    public const CATEGORY_DEFINITIONS = [
        // SDM & Personel
        'personnel' => [
            'label' => 'Gaji & Honor',
            'icon' => '💼',
            'group' => 'SDM & Personel',
        ],
        'commission' => [
            'label' => 'Komisi',
            'icon' => '🤝',
            'group' => 'SDM & Personel',
        ],
        'allowance' => [
            'label' => 'Tunjangan & Bonus',
            'icon' => '💰',
            'group' => 'SDM & Personel',
        ],

        // Vendor & Subkontraktor
        'subcontractor' => [
            'label' => 'Subkontraktor',
            'icon' => '🏗️',
            'group' => 'Vendor & Subkontraktor',
        ],
        'consultant' => [
            'label' => 'Konsultan Eksternal',
            'icon' => '👨‍💼',
            'group' => 'Vendor & Subkontraktor',
        ],
        'supplier' => [
            'label' => 'Supplier/Vendor',
            'icon' => '📦',
            'group' => 'Vendor & Subkontraktor',
        ],

        // Layanan Teknis
        'laboratory' => [
            'label' => 'Laboratorium',
            'icon' => '🔬',
            'group' => 'Layanan Teknis',
        ],
        'survey' => [
            'label' => 'Survey & Pengukuran',
            'icon' => '📐',
            'group' => 'Layanan Teknis',
        ],
        'testing' => [
            'label' => 'Testing & Inspeksi',
            'icon' => '🧪',
            'group' => 'Layanan Teknis',
        ],
        'certification' => [
            'label' => 'Sertifikasi',
            'icon' => '📋',
            'group' => 'Layanan Teknis',
        ],

        // Peralatan & Material
        'equipment_rental' => [
            'label' => 'Sewa Alat',
            'icon' => '🚜',
            'group' => 'Peralatan & Material',
        ],
        'equipment_purchase' => [
            'label' => 'Pembelian Alat',
            'icon' => '🛠️',
            'group' => 'Peralatan & Material',
        ],
        'materials' => [
            'label' => 'Bahan & Material',
            'icon' => '📦',
            'group' => 'Peralatan & Material',
        ],
        'maintenance' => [
            'label' => 'Maintenance & Perbaikan',
            'icon' => '🔧',
            'group' => 'Peralatan & Material',
        ],

        // Operasional
        'travel' => [
            'label' => 'Perjalanan Dinas',
            'icon' => '✈️',
            'group' => 'Operasional',
        ],
        'accommodation' => [
            'label' => 'Akomodasi',
            'icon' => '🏨',
            'group' => 'Operasional',
        ],
        'transportation' => [
            'label' => 'Transportasi',
            'icon' => '🚗',
            'group' => 'Operasional',
        ],
        'communication' => [
            'label' => 'Komunikasi & Internet',
            'icon' => '📞',
            'group' => 'Operasional',
        ],
        'office_supplies' => [
            'label' => 'ATK & Supplies',
            'icon' => '📝',
            'group' => 'Operasional',
        ],
        'printing' => [
            'label' => 'Printing & Dokumen',
            'icon' => '🖨️',
            'group' => 'Operasional',
        ],

        // Legal & Administrasi
        'permit' => [
            'label' => 'Perizinan',
            'icon' => '📜',
            'group' => 'Legal & Administrasi',
        ],
        'insurance' => [
            'label' => 'Asuransi',
            'icon' => '🛡️',
            'group' => 'Legal & Administrasi',
        ],
        'tax' => [
            'label' => 'Pajak & Retribusi',
            'icon' => '💵',
            'group' => 'Legal & Administrasi',
        ],
        'legal' => [
            'label' => 'Legal & Notaris',
            'icon' => '⚖️',
            'group' => 'Legal & Administrasi',
        ],
        'administration' => [
            'label' => 'Administrasi',
            'icon' => '📋',
            'group' => 'Legal & Administrasi',
        ],

        // Marketing & Lainnya
        'marketing' => [
            'label' => 'Marketing & Promosi',
            'icon' => '📢',
            'group' => 'Marketing & Lainnya',
        ],
        'entertainment' => [
            'label' => 'Entertainment & Jamuan',
            'icon' => '🍽️',
            'group' => 'Marketing & Lainnya',
        ],
        'donation' => [
            'label' => 'Donasi & CSR',
            'icon' => '🎁',
            'group' => 'Marketing & Lainnya',
        ],
        'other' => [
            'label' => 'Lainnya',
            'icon' => '📌',
            'group' => 'Marketing & Lainnya',
        ],
    ];

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

    public static function categories(): array
    {
        return self::CATEGORY_DEFINITIONS;
    }

    public static function categoryKeys(): array
    {
        return array_keys(self::CATEGORY_DEFINITIONS);
    }

    public static function categoriesByGroup(): array
    {
        $grouped = [];

        foreach (self::CATEGORY_DEFINITIONS as $value => $definition) {
            $group = $definition['group'] ?? 'Lainnya';
            $grouped[$group][$value] = $definition;
        }

        return $grouped;
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
        return self::CATEGORY_DEFINITIONS[$this->category]['label'] ?? 'Lainnya';
    }

    public function getCategoryIconAttribute()
    {
        return self::CATEGORY_DEFINITIONS[$this->category]['icon'] ?? '📌';
    }
}
