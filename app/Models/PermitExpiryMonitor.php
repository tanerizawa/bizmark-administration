<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermitExpiryMonitor extends Model
{
    protected $fillable = [
        'project_id',
        'project_permit_id',
        'client_id',
        'permit_type',
        'permit_number',
        'issued_at',
        'expires_at',
        'status',
        'notified_90',
        'notified_30',
        'notified_7',
        'last_notified_at',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'notified_90' => 'boolean',
        'notified_30' => 'boolean',
        'notified_7' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectPermit(): BelongsTo
    {
        return $this->belongsTo(ProjectPermit::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Sisa hari hingga expire.
     */
    public function daysUntilExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->expires_at, false);
    }

    /**
     * Persentase progress (0-100) menuju expire date dari issued_at.
     */
    public function progressPercent(): int
    {
        if (! $this->issued_at) {
            return 50;
        }

        $total = $this->issued_at->diffInDays($this->expires_at);
        if ($total <= 0) {
            return 100;
        }

        $elapsed = $this->issued_at->diffInDays(now());

        return (int) min(100, max(0, ($elapsed / $total) * 100));
    }
}
