<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'client_id', // Added client_id
        'permit_application_id', // Phase 5: Link to source permit application
        'client_name',
        'client_contact',
        'client_address',
        'status_id',
        'institution_id',
        'start_date',
        'deadline',
        'progress_percentage',
        'budget',
        'actual_cost',
        'notes',
        // Financial fields (Phase 1)
        'contract_value',
        'down_payment',
        'payment_received',
        'total_expenses',
        'profit_margin',
        'payment_terms',
        'payment_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percentage' => 'integer',
        // Financial casts (Phase 1)
        'contract_value' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'payment_received' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function permitApplication(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProjectLog::class);
    }

    // AI Document Paraphrasing relationships
    public function documentDrafts(): HasMany
    {
        return $this->hasMany(DocumentDraft::class);
    }

    public function aiProcessingLogs(): HasMany
    {
        return $this->hasMany(AIProcessingLog::class);
    }

    // Financial relationships (Phase 1)
    public function payments(): HasMany
    {
        return $this->hasMany(ProjectPayment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    // Phase 2A: Permit relationships
    public function permits(): HasMany
    {
        return $this->hasMany(ProjectPermit::class)->orderBy('sequence_order');
    }

    public function goalPermit()
    {
        return $this->hasOne(ProjectPermit::class)->where('is_goal_permit', true);
    }

    // Sprint 6: Financial Tab relationships
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    // Financial calculation methods (called by model events)
    public function updatePaymentReceived(): void
    {
        $this->payment_received = $this->payments()->sum('amount');
        $this->updatePaymentStatus();
        $this->updateProfitMargin();
        $this->save();
    }

    public function updateTotalExpenses(): void
    {
        $this->total_expenses = $this->expenses()->sum('amount');
        $this->updateProfitMargin();
        $this->save();
    }

    public function updateProfitMargin(): void
    {
        if ($this->payment_received > 0) {
            $netProfit = $this->payment_received - $this->total_expenses;
            $this->profit_margin = ($netProfit / $this->payment_received) * 100;
        } else {
            $this->profit_margin = 0;
        }
    }

    public function updatePaymentStatus(): void
    {
        if (!$this->contract_value || $this->contract_value == 0) {
            $this->payment_status = 'unpaid';
            return;
        }

        $percentagePaid = ($this->payment_received / $this->contract_value) * 100;

        $this->payment_status = match (true) {
            $percentagePaid == 0 => 'unpaid',
            $percentagePaid >= 100 => 'paid',
            default => 'partial'
        };
    }

    // Computed properties
    public function getOutstandingReceivableAttribute(): float
    {
        return max(0, ($this->contract_value ?? 0) - ($this->payment_received ?? 0));
    }

    public function getNetProfitAttribute(): float
    {
        return ($this->payment_received ?? 0) - ($this->total_expenses ?? 0);
    }

    public function getProfitPercentageAttribute(): float
    {
        if (($this->payment_received ?? 0) > 0) {
            return ($this->net_profit / $this->payment_received) * 100;
        }
        return 0;
    }

    public function getFormattedContractValueAttribute(): string
    {
        return 'Rp ' . number_format($this->contract_value ?? 0, 0, ',', '.');
    }

    public function getFormattedPaymentReceivedAttribute(): string
    {
        return 'Rp ' . number_format($this->payment_received ?? 0, 0, ',', '.');
    }

    public function getFormattedOutstandingReceivableAttribute(): string
    {
        return 'Rp ' . number_format($this->outstanding_receivable, 0, ',', '.');
    }

    public function getFormattedNetProfitAttribute(): string
    {
        return 'Rp ' . number_format($this->net_profit, 0, ',', '.');
    }


}
