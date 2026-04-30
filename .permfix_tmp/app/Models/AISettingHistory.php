<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AISettingHistory extends Model
{
    protected $table = 'ai_setting_history';

    protected $fillable = [
        'setting_id',
        'key',
        'old_value',
        'new_value',
        'changed_by_name',
        'changed_by',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the setting that was changed
     */
    public function setting()
    {
        return $this->belongsTo(AISetting::class, 'setting_id');
    }

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get formatted display of the change
     */
    public function getChangeDescriptionAttribute(): string
    {
        $old = $this->old_value ? "'{$this->old_value}'" : 'null';
        $new = "'{$this->new_value}'";
        
        return "Changed from {$old} to {$new}";
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for changes by specific user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('changed_by', $userId);
    }

    /**
     * Scope for changes to specific setting
     */
    public function scopeForSetting($query, int $settingId)
    {
        return $query->where('setting_id', $settingId);
    }
}

