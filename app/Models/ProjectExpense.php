<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    public const CATEGORY_DEFINITIONS = [
        // SDM & Personel
        'personnel' => [
            'label' => 'Gaji & Honor',
            'icon' => 'briefcase',
            'group' => 'SDM & Personel',
        ],
        'commission' => [
            'label' => 'Komisi',
            'icon' => 'handshake',
            'group' => 'SDM & Personel',
        ],
        'allowance' => [
            'label' => 'Tunjangan & Bonus',
            'icon' => 'money-bill-wave',
            'group' => 'SDM & Personel',
        ],

        // Rekanan & Subkontraktor
        'subcontractor' => [
            'label' => 'Subkontraktor',
            'icon' => 'hard-hat',
            'group' => 'Rekanan & Subkontraktor',
        ],
        'consultant' => [
            'label' => 'Konsultan Eksternal',
            'icon' => 'user-tie',
            'group' => 'Rekanan & Subkontraktor',
        ],
        'supplier' => [
            'label' => 'Rekanan/Partner',
            'icon' => 'handshake',
            'group' => 'Rekanan & Subkontraktor',
        ],

        // Layanan Teknis
        'laboratory' => [
            'label' => 'Laboratorium',
            'icon' => 'microscope',
            'group' => 'Layanan Teknis',
        ],
        'survey' => [
            'label' => 'Survey & Pengukuran',
            'icon' => 'ruler-combined',
            'group' => 'Layanan Teknis',
        ],
        'testing' => [
            'label' => 'Testing & Inspeksi',
            'icon' => 'vial',
            'group' => 'Layanan Teknis',
        ],
        'certification' => [
            'label' => 'Sertifikasi',
            'icon' => 'certificate',
            'group' => 'Layanan Teknis',
        ],

        // Peralatan & Perlengkapan
        'equipment_rental' => [
            'label' => 'Sewa Alat',
            'icon' => 'truck-moving',
            'group' => 'Peralatan & Perlengkapan',
        ],
        'equipment_purchase' => [
            'label' => 'Pembelian Alat',
            'icon' => 'tools',
            'group' => 'Peralatan & Perlengkapan',
        ],
        'materials' => [
            'label' => 'Perlengkapan & Supplies',
            'icon' => 'box',
            'group' => 'Peralatan & Perlengkapan',
        ],
        'maintenance' => [
            'label' => 'Maintenance & Perbaikan',
            'icon' => 'wrench',
            'group' => 'Peralatan & Perlengkapan',
        ],

        // Operasional
        'travel' => [
            'label' => 'Perjalanan Dinas',
            'icon' => 'plane',
            'group' => 'Operasional',
        ],
        'accommodation' => [
            'label' => 'Akomodasi',
            'icon' => 'hotel',
            'group' => 'Operasional',
        ],
        'transportation' => [
            'label' => 'Transportasi',
            'icon' => 'car',
            'group' => 'Operasional',
        ],
        'communication' => [
            'label' => 'Komunikasi & Internet',
            'icon' => 'phone',
            'group' => 'Operasional',
        ],
        'office_supplies' => [
            'label' => 'ATK & Supplies',
            'icon' => 'file-alt',
            'group' => 'Operasional',
        ],
        'printing' => [
            'label' => 'Printing & Dokumen',
            'icon' => 'print',
            'group' => 'Operasional',
        ],

        // Legal & Administrasi
        'permit' => [
            'label' => 'Perizinan',
            'icon' => 'file-contract',
            'group' => 'Legal & Administrasi',
        ],
        'insurance' => [
            'label' => 'Asuransi',
            'icon' => 'shield-alt',
            'group' => 'Legal & Administrasi',
        ],
        'tax' => [
            'label' => 'Pajak & Retribusi',
            'icon' => 'dollar-sign',
            'group' => 'Legal & Administrasi',
        ],
        'legal' => [
            'label' => 'Legal & Notaris',
            'icon' => 'balance-scale',
            'group' => 'Legal & Administrasi',
        ],
        'administration' => [
            'label' => 'Administrasi',
            'icon' => 'clipboard-list',
            'group' => 'Legal & Administrasi',
        ],

        // Marketing & Lainnya
        'marketing' => [
            'label' => 'Marketing & Promosi',
            'icon' => 'bullhorn',
            'group' => 'Marketing & Lainnya',
        ],
        'entertainment' => [
            'label' => 'Entertainment & Jamuan',
            'icon' => 'utensils',
            'group' => 'Marketing & Lainnya',
        ],
        'donation' => [
            'label' => 'Donasi & CSR',
            'icon' => 'gift',
            'group' => 'Marketing & Lainnya',
        ],
        'other' => [
            'label' => 'Lainnya',
            'icon' => 'ellipsis-h',
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
        return self::CATEGORY_DEFINITIONS[$this->category]['icon'] ?? 'ellipsis-h';
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
        $icon = self::CATEGORY_DEFINITIONS[$category]['icon'] ?? 'ellipsis-h';
        $class = 'fas fa-' . $icon . ($additionalClass ? ' ' . $additionalClass : '');
        return '<i class="' . $class . '"></i>';
    }
}

