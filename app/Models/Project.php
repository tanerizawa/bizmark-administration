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
        'actual_completion_date', // Tanggal aktual penyelesaian proyek
        'completed_at', // Tanggal aktual selesai (legacy)
        'completion_notes', // Catatan saat selesai
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
        'actual_completion_date' => 'date', // Cast sebagai date
        'completed_at' => 'datetime', // Cast sebagai datetime (legacy)
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

    /**
     * Calculate progress from task completion rate
     * Returns percentage (0-100)
     */
    public function calculateProgressFromTasks(): int
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return $this->progress_percentage ?? 0;
        }
        
        $completedTasks = $this->tasks()
            ->whereIn('status', ['done', 'completed', 'selesai', 'DONE', 'COMPLETED', 'SELESAI'])
            ->count();
        
        return round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Sync progress with task completion
     * Updates the progress_percentage field
     */
    public function syncProgressWithTasks(): bool
    {
        $calculatedProgress = $this->calculateProgressFromTasks();
        
        // Only update if there are tasks
        if ($this->tasks()->count() > 0) {
            $this->progress_percentage = $calculatedProgress;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Check if project is completed
     */
    public function isCompleted(): bool
    {
        return $this->progress_percentage >= 100 || 
               ($this->status && $this->status->is_final);
    }

    /**
     * Check if project is active (not cancelled, not completed)
     */
    public function isActive(): bool
    {
        if (!$this->status) {
            return true;
        }
        
        return !$this->status->is_final && 
               !in_array($this->status->code, ['CANCELLED', 'DIBATALKAN']);
    }

    /**
     * Check if project completed on time
     * Returns: 'on-time', 'late', 'early', or null if not completed
     */
    public function getCompletionStatus(): ?string
    {
        if (!$this->completed_at || !$this->deadline) {
            return null;
        }
        
        $completedDate = $this->completed_at->startOfDay();
        $deadlineDate = $this->deadline->startOfDay();
        
        if ($completedDate->equalTo($deadlineDate)) {
            return 'on-time';
        } elseif ($completedDate->lessThan($deadlineDate)) {
            return 'early';
        } else {
            return 'late';
        }
    }

    /**
     * Get days difference between completion and deadline
     * Positive = late, Negative = early, 0 = on-time
     */
    public function getDaysOffSchedule(): ?int
    {
        if (!$this->completed_at || !$this->deadline) {
            return null;
        }
        
        return $this->completed_at->startOfDay()->diffInDays($this->deadline->startOfDay(), false);
    }

    /**
     * Get formatted completion status message
     */
    public function getCompletionStatusMessage(): ?string
    {
        $status = $this->getCompletionStatus();
        $days = abs($this->getDaysOffSchedule() ?? 0);
        
        return match($status) {
            'on-time' => 'Selesai tepat waktu! ⏰',
            'early' => "Selesai lebih cepat {$days} hari! ⚡",
            'late' => "Terlambat {$days} hari ⚠️",
            default => null
        };
    }

    /**
     * Get completion status color
     */
    public function getCompletionStatusColor(): ?string
    {
        return match($this->getCompletionStatus()) {
            'on-time' => '#10B981', // Green
            'early' => '#3B82F6',   // Blue
            'late' => '#EF4444',    // Red
            default => null
        };
    }


}
