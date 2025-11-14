<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAssignment extends Model
{
    protected $fillable = [
        'email_account_id',
        'user_id',
        'role',
        'can_send',
        'can_receive',
        'can_delete',
        'can_assign_others',
        'notify_on_receive',
        'notify_on_reply',
        'notify_on_mention',
        'is_active',
        'priority',
        'assigned_by',
    ];

    protected $casts = [
        'can_send' => 'boolean',
        'can_receive' => 'boolean',
        'can_delete' => 'boolean',
        'can_assign_others' => 'boolean',
        'notify_on_receive' => 'boolean',
        'notify_on_reply' => 'boolean',
        'notify_on_mention' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // ==================== Relationships ====================

    /**
     * The email account this assignment belongs to
     */
    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * The user assigned to the email account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who made this assignment
     */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // ==================== Scopes ====================

    /**
     * Only active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Primary role assignments
     */
    public function scopePrimary($query)
    {
        return $query->where('role', 'primary');
    }

    /**
     * Backup role assignments
     */
    public function scopeBackup($query)
    {
        return $query->where('role', 'backup');
    }

    /**
     * Viewer role assignments
     */
    public function scopeViewer($query)
    {
        return $query->where('role', 'viewer');
    }

    /**
     * For a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * For a specific email account
     */
    public function scopeForEmail($query, $emailAccountId)
    {
        return $query->where('email_account_id', $emailAccountId);
    }

    // ==================== Permission Methods ====================

    /**
     * Check if has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match($permission) {
            'send' => $this->can_send,
            'receive' => $this->can_receive,
            'delete' => $this->can_delete,
            'assign' => $this->can_assign_others,
            default => false,
        };
    }

    /**
     * Can send emails from this account?
     */
    public function canSend(): bool
    {
        return $this->is_active && $this->can_send;
    }

    /**
     * Can receive/view emails for this account?
     */
    public function canReceive(): bool
    {
        return $this->is_active && $this->can_receive;
    }

    /**
     * Can delete emails?
     */
    public function canDelete(): bool
    {
        return $this->is_active && $this->can_delete;
    }

    /**
     * Can assign other users to this email?
     */
    public function canAssign(): bool
    {
        return $this->is_active && $this->can_assign_others;
    }

    /**
     * Is this a primary role?
     */
    public function isPrimary(): bool
    {
        return $this->role === 'primary';
    }

    /**
     * Is this a backup role?
     */
    public function isBackup(): bool
    {
        return $this->role === 'backup';
    }

    /**
     * Is this a viewer role?
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /**
     * Should receive notification for new emails?
     */
    public function shouldNotifyOnReceive(): bool
    {
        return $this->is_active && $this->notify_on_receive;
    }

    /**
     * Should receive notification for replies?
     */
    public function shouldNotifyOnReply(): bool
    {
        return $this->is_active && $this->notify_on_reply;
    }

    /**
     * Should receive notification when mentioned?
     */
    public function shouldNotifyOnMention(): bool
    {
        return $this->is_active && $this->notify_on_mention;
    }

    // ==================== Attributes ====================

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'primary' => 'Primary Handler',
            'backup' => 'Backup Handler',
            'viewer' => 'Viewer',
        ];

        return $labels[$this->role] ?? ucfirst($this->role);
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'primary' => 'success',
            'backup' => 'warning',
            'viewer' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get permissions summary
     */
    public function getPermissionsSummaryAttribute(): array
    {
        return [
            'send' => $this->can_send,
            'receive' => $this->can_receive,
            'delete' => $this->can_delete,
            'assign' => $this->can_assign_others,
        ];
    }
}
