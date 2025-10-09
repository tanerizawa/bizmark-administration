<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
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
        return match($this->category) {
            // SDM & Personel
            'personnel' => 'Gaji & Honor',
            'commission' => 'Komisi',
            'allowance' => 'Tunjangan & Bonus',
            
            // Vendor & Subkontraktor
            'subcontractor' => 'Subkontraktor',
            'consultant' => 'Konsultan Eksternal',
            'supplier' => 'Supplier/Vendor',
            
            // Layanan Teknis
            'laboratory' => 'Laboratorium',
            'survey' => 'Survey & Pengukuran',
            'testing' => 'Testing & Inspeksi',
            'certification' => 'Sertifikasi',
            
            // Peralatan & Material
            'equipment_rental' => 'Sewa Alat',
            'equipment_purchase' => 'Pembelian Alat',
            'materials' => 'Bahan & Material',
            'maintenance' => 'Maintenance & Perbaikan',
            
            // Operasional
            'travel' => 'Perjalanan Dinas',
            'accommodation' => 'Akomodasi',
            'transportation' => 'Transportasi',
            'communication' => 'Komunikasi & Internet',
            'office_supplies' => 'ATK & Supplies',
            'printing' => 'Printing & Dokumen',
            
            // Legal & Administrasi
            'permit' => 'Perizinan',
            'insurance' => 'Asuransi',
            'tax' => 'Pajak & Retribusi',
            'legal' => 'Legal & Notaris',
            'administration' => 'Administrasi',
            
            // Marketing & Lainnya
            'marketing' => 'Marketing & Promosi',
            'entertainment' => 'Entertainment & Jamuan',
            'donation' => 'Donasi & CSR',
            
            default => 'Lainnya',
        };
    }
    
    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'personnel' => '💼',
            'commission' => '🤝',
            'allowance' => '💰',
            'subcontractor' => '🏗️',
            'consultant' => '👨‍💼',
            'supplier' => '📦',
            'laboratory' => '🔬',
            'survey' => '📐',
            'testing' => '🧪',
            'certification' => '📋',
            'equipment_rental' => '🚜',
            'equipment_purchase' => '🛠️',
            'materials' => '📦',
            'maintenance' => '🔧',
            'travel' => '✈️',
            'accommodation' => '🏨',
            'transportation' => '🚗',
            'communication' => '📞',
            'office_supplies' => '📝',
            'printing' => '🖨️',
            'permit' => '📜',
            'insurance' => '🛡️',
            'tax' => '💵',
            'legal' => '⚖️',
            'administration' => '📋',
            'marketing' => '📢',
            'entertainment' => '🍽️',
            'donation' => '🎁',
            default => '📌',
        };
    }
}
