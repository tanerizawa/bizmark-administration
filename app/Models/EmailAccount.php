<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'name',
        'type',
        'department',
        'description',
        'is_active',
        'auto_reply_enabled',
        'auto_reply_message',
        'signature',
        'assigned_users',
        'total_received',
        'total_sent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_reply_enabled' => 'boolean',
        'assigned_users' => 'array',
        'total_received' => 'integer',
        'total_sent' => 'integer',
    ];

    // ==================== Relationships ====================

    /**
     * Email assignments for this account
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(EmailAssignment::class);
    }

    /**
     * Users assigned to this email account via assignments table
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'email_assignments')
            ->withPivot(['role', 'can_send', 'can_receive', 'can_delete', 'can_assign_others', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Active users only
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivot('is_active', true);
    }

    /**
     * Primary handlers for this email
     */
    public function primaryUsers(): BelongsToMany
    {
        return $this->activeUsers()->wherePivot('role', 'primary');
    }

    /**
     * Backup handlers
     */
    public function backupUsers(): BelongsToMany
    {
        return $this->activeUsers()->wherePivot('role', 'backup');
    }

    /**
     * Emails in inbox for this account
     */
    public function inbox(): HasMany
    {
        return $this->hasMany(EmailInbox::class);
    }

    // ==================== Scopes ====================

    /**
     * Only active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by department
     */
    public function scopeDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Shared emails only
     */
    public function scopeShared($query)
    {
        return $query->where('type', 'shared');
    }

    /**
     * Personal emails only
     */
    public function scopePersonal($query)
    {
        return $query->where('type', 'personal');
    }

    // ==================== Methods ====================

    /**
     * Assign user to this email account
     */
    public function assignUser(User $user, array $options = [])
    {
        $defaults = [
            'role' => 'primary',
            'can_send' => true,
            'can_receive' => true,
            'can_delete' => false,
            'can_assign_others' => false,
            'notify_on_receive' => true,
            'notify_on_reply' => false,
            'notify_on_mention' => true,
            'is_active' => true,
        ];

        $data = array_merge($defaults, $options);

        return EmailAssignment::updateOrCreate(
            [
                'email_account_id' => $this->id,
                'user_id' => $user->id,
            ],
            $data
        );
    }

    /**
     * Remove user from this email account
     */
    public function removeUser(User $user)
    {
        return EmailAssignment::where('email_account_id', $this->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    /**
     * Check if user has access to this email
     */
    public function hasUser(User $user): bool
    {
        return $this->activeUsers()->where('users.id', $user->id)->exists();
    }

    /**
     * Get primary handler
     */
    public function getPrimaryHandler()
    {
        return $this->primaryUsers()->first();
    }

    /**
     * Get all handlers (primary + backup)
     */
    public function getHandlers()
    {
        return $this->activeUsers()
            ->whereIn('email_assignments.role', ['primary', 'backup'])
            ->get();
    }

    /**
     * Update statistics
     */
    public function incrementReceived()
    {
        $this->increment('total_received');
    }

    public function incrementSent()
    {
        $this->increment('total_sent');
    }

    /**
     * Get today's email count
     */
    public function getTodayEmailCount()
    {
        return $this->inbox()->whereDate('received_at', today())->count();
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        return $this->inbox()->where('is_read', false)->count();
    }

    /**
     * Check if should send auto-reply
     */
    public function shouldAutoReply(): bool
    {
        return $this->is_active && $this->auto_reply_enabled && !empty($this->auto_reply_message);
    }

    /**
     * Get display name with email
     */
    public function getDisplayName(): string
    {
        return "{$this->name} <{$this->email}>";
    }

    // ==================== Attributes ====================

    /**
     * Get type badge color
     */
    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'shared' ? 'primary' : 'success';
    }

    /**
     * Get department label
     */
    public function getDepartmentLabelAttribute(): string
    {
        $labels = [
            'general' => 'General',
            'cs' => 'Customer Service',
            'sales' => 'Sales',
            'support' => 'Technical Support',
            'finance' => 'Finance',
            'technical' => 'Technical',
        ];

        return $labels[$this->department] ?? ucfirst($this->department);
    }
}
