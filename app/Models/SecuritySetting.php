<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    protected $fillable = [
        'min_password_length',
        'require_special_char',
        'require_number',
        'require_mixed_case',
        'enforce_password_expiration',
        'password_expiration_days',
        'session_timeout_minutes',
        'allow_concurrent_sessions',
        'two_factor_enabled',
        'activity_log_enabled',
    ];

    protected $casts = [
        'min_password_length' => 'integer',
        'require_special_char' => 'boolean',
        'require_number' => 'boolean',
        'require_mixed_case' => 'boolean',
        'enforce_password_expiration' => 'boolean',
        'password_expiration_days' => 'integer',
        'session_timeout_minutes' => 'integer',
        'allow_concurrent_sessions' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'activity_log_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::create();
    }
}
